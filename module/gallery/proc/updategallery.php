<?php if(!defined('__AFOX__')) exit();

function proc($data)
{
	if(empty($data['md_id'])||!preg_match('/'._AF_PATTERN_ID_.'/', $data['md_id'])){
		return set_error(getLang('invalid_value', ['id']), 2001);
	}

	global $_MEMBER;

	if(empty($_MEMBER)) {
		$data['mb_nick'] = empty($data['mb_nick'])?'':$data['mb_nick'];
		if(empty($data['mb_nick']) || empty($data['mb_password'])) {
			return set_error(getLang('request_input', [getLang('%s, %s', ['id', 'password'])]), 1);
		}
		$data['mb_srl'] = 0;
		$data['mb_rank'] = 0;
		$encrypt_password = createHash($data['mb_password']);
	} else {
		$data['mb_srl'] = $_MEMBER['mb_srl'];
		$data['mb_rank'] = $_MEMBER['mb_rank'];
		$data['mb_nick'] = $_MEMBER['mb_nick'];
		$encrypt_password = null;
	}

	$is_admin = isAdmin();
	$md_id = $data['md_id'];

	$module = getModule($md_id);
	if(empty($module)) return set_error(getLang('error_founded'), 4201);

	$data['wr_category'] = '';
	if(!empty($module['md_category'])) {
		if(empty($data['wr_tags'])) return set_error(getLang('request_input',['category']), 1);
		$md_categorys = explode(',', $module['md_category']);
		foreach ($data['wr_tags'] as $value) {
			if(!in_array($value, $md_categorys)) {
				return set_error(getLang('invalid_value', ['category']), 2001);
			}
		}
	}
	$data['wr_tags'] = empty($data['wr_tags'])?'':implode(',',$data['wr_tags']);

	// 파일이 업로드되면 최대 수 체크
	$files = empty($_FILES['upload_files']['tmp_name'])?null:$_FILES['upload_files'];
	$upload_count = $files&&!empty($files['tmp_name'][0])?count($files['tmp_name']):0;

	$file_max = $is_admin ? 999999 : (int) $module['md_file_max'];
	$file_max_size = $is_admin ? 0 : (int) $module['md_file_size'];
	$file_accept = $is_admin ? '' : str_replace(',', '|', $module['md_file_accept']);

	$to_files = [];
	$new_files = [];
	$unlink_files = [];
	$file_types = array('binary'=>0, 'image' => 1, 'video' => 2, 'audio' => 3);

	DB::transaction();

	try {

		if(!empty($data['remove_files'])) {
			foreach ($data['remove_files'] as $val) {
				$file = DB::get(_AF_FILE_TABLE_, [
					'md_id'=>$md_id,
					'mf_srl'=>$val
				]);
				if(!empty($file) && true === DB::delete(_AF_FILE_TABLE_, [
					'md_id'=>$md_id,
					'mf_srl'=>$val])
				) {
					$ftype = explode('/', strtolower($file['mf_type']));
					$ftype = empty($file_types[$ftype[0]]) ? 'binary' : $ftype[0];
					$unlink_files[] = _AF_ATTACH_DATA_.$ftype.'/'.$md_id.'/'.$file['mf_target'].'/'.$file['mf_upload_name'];
				}
			}
		}

		if($upload_count > 0) {
			// 권한 체크
			if(!isGrant('upload', $md_id)) throw new Exception(getLang('warn_not_allowable', ['upload']), 3505);
			if($file_max < ($upload_count+$file_count)) throw new Exception(getLang('error_upload(-3)'), 10487);
			$exif = function_exists('exif_read_data');

			for ($i=0; $i < $upload_count; $i++) {
				// 빈 파일 넘김
				if(empty($files['tmp_name'][$i])) continue;

				$file = [
					'tmp_name' => $files['tmp_name'][$i],
					'name' => $files['name'][$i],
					'type' => $files['type'][$i],
					'size' => $files['size'][$i],
					'date' => date('Y-m-d H:i:s', time()),
					'error' => $files['error'][$i]
				];

				if($file_accept && !preg_match('/('.($file_accept).')$/i', $file['name'])) {
					throw new Exception(getLang('warn_allowable', [
						str_replace('.', '', $module['md_file_accept'])
					]), 3503);
				}
				// 실행 가능한 파일 못하게 처리
				$file['name'] = preg_replace('/\.(php|phtm|phar|html?|cgi|pl|exe|[aj]sp|inc)/i', '$0-x', $file['name']);
				$fname = md5($i.$file['name'].time());
				$ftype = explode('/', strtolower($file['type']));
				$ftype = empty($file_types[$ftype[0]]) ? 'binary' : $ftype[0];

				$to_files[$i] = _AF_ATTACH_DATA_ . $ftype . '/' . $md_id . '/1/' . $fname;

				$ret = moveUploadedFile($file, $to_files[$i], $file_max_size);
				if(!empty($ret['error'])) throw new Exception($ret['message'], $ret['error']);

				if($exif && $ftype == 'image' && ($ifd0=@exif_read_data($to_files[$i]))){
					$DateTime = trim(empty($ifd0['DateTimeOriginal']) ? '' : $ifd0['DateTimeOriginal']);
					if($DateTime) $file['date'] = date('Y-m-d H:i:s', strtotime($DateTime));
					if($Model = (trim(empty($ifd0['Make']) ? 'Unavailable' : $ifd0['Make']))) {
						$Model .= ' - '.trim(empty($ifd0['Model']) ? 'Unavailable' : $ifd0['Model']);
						$file['name'] = $Model;
					}
				}

				DB::insert(_AF_FILE_TABLE_, [
					'md_id'=>$md_id,
					'mf_target'=>1,
					'mf_name'=>$file['name'],
					'mf_upload_name'=>$fname,
					'mf_type'=>$file['type'],
					'mf_about'=>$data['wr_tags'],
					'mf_size'=>$file['size'],
					'mb_srl'=>$data['mb_srl'],
					'mb_ipaddress'=>$_SERVER['REMOTE_ADDR'],
					'mf_regdate'=>$file['date']
				]);

				$file['mf_srl'] = $mf_srl = DB::insertId();
				$new_files[] = $file;
				$file_count++;
			}
		}

		/*// 포인트 사용중이면
		if($point=(int)$module['point_write']){
			if(setHistory('mf_upload::'.$md_id, $point)){
				if(($_r=setPoint($point)) && !empty($_r['error'])){
					// TODO 포인트 에러시...
				}
			}
		}
		//*/

		// 모두 완료 되면 지워진 파일 완전 삭제
		foreach ($unlink_files as $val) @unlinkFile($val);

		// 썸네일 제거
		unlinkAll(_AF_ATTACH_DATA_.'thumbnail/'.$md_id.'/');

	} catch (Exception $ex) {
		DB::rollback();

		// Engine == MyISAM 트랜잭션을 지원 안한다.
		if (DB::engine(_AF_FILE_TABLE_) == 'myisam') {
			if(count($new_files)>0) {
				$nfile_srls = [];
				foreach ($new_files as $file) $nfile_srls[] = $file['mf_srl'];
				@DB::delete(_AF_FILE_TABLE_, ['mf_srl{IN}'=>implode(',', $nfile_srls)]);
			}
			// 트랜잭션을 지원 안하면 삭제된 파일은 그냥 지움
			foreach ($unlink_files as $val) @unlinkFile($val);
		}

		// 실패시 업로드 된 파일 삭제
		foreach ($to_files as $val) @unlinkFile($val);
		return set_error($ex->getMessage(),$ex->getCode());
	}

	DB::commit();

	return ['error'=>0, 'message'=>getLang('success_saved')];
}

/* End of file updategallery.php */
/* Location: ./module/board/proc/updategallery.php */
