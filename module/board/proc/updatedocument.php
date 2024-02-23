<?php if(!defined('__AFOX__')) exit();

function proc($data)
{
	if(empty($data['md_id'])||!preg_match('/'._AF_PATTERN_ID_.'/', $data['md_id'])){
		return set_error(getLang('invalid_value', ['id']), 2001);
	}

	$data['wr_title'] = trim(strip_tags($data['wr_title']));
	if(empty($data['wr_title'])) return set_error(getLang('request_input', ['title']),1);

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
	$wr_srl = (int) abs(empty($data['wr_srl']) ? 0 : $data['wr_srl']);
	$new_insert = empty($wr_srl);

	$doc = DB::get(_AF_DOCUMENT_TABLE_, 'md_id, mb_srl, mb_password', ['wr_srl'=>$wr_srl]);
	if($ex=DB::error()) return set_error($ex->getMessage(), $ex->getCode());
	if(!empty($wr_srl) && (empty($doc['md_id']) || $doc['md_id'] != $md_id)) {
		return set_error(getLang('error_request'),4303);
	}

	// 권한 체크
	if ($new_insert) {
		if(!isGrant('write', $md_id)) {
			return set_error(getLang('error_permitted'),4501);
		}
	} else {
		if(empty($_MEMBER)) {
			if(empty($doc['mb_password']) || !checkPassword($data['mb_password'], $doc['mb_password'])) {
				return set_error(getLang('error_permitted'),4501);
			}
		} else if(!isManager($md_id)) {
			if($_MEMBER['mb_srl'] != $doc['mb_srl']) {
				return set_error(getLang('error_permitted'),4501);
			}
		}
	}

	$module = getModule($md_id);
	if(empty($module)) return set_error(getLang('error_founded'), 4201);

	// use_type 값이 1~6 사이이면 모듈에 설정된 값으로 강제 설정
	if(!empty($module['use_type']) && $module['use_type'] < 7) $data['wr_type'] = ((int)$module['use_type'])-1;
	$wr_secret = empty($data['wr_secret']) ? 0 : ($data['wr_secret'] == 'true' ||  $data['wr_secret'] === 1);
	$wr_secret = empty($module['use_secret']) ? (int)$wr_secret : ((int)$module['use_secret'])-1;

	if(!empty($module['md_category'])) {
		if(empty($data['wr_category'])) return set_error(getLang('request_input',['category']), 1);
		if(!in_array($data['wr_category'], explode(',', $module['md_category']))) {
			return set_error(getLang('invalid_value', ['category']), 2001);
		}
	} else {
		$data['wr_category'] = '';
	}

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

	$wr_extra = [];

	if(!empty($module['md_extra']) && !is_array($module['md_extra'])) {
		$module['md_extra'] = unserialize($module['md_extra']);
		// 확장 변수 키값이 있으면 해당 값을 입력
		if (!empty($module['md_extra']['keys'])) {
			$values = [];
			foreach($module['md_extra']['keys'] as $ex_key=>$ex_caption){
				$value = @$data['wr_extra_'.$ex_key];
				$value = trim(is_array($value) ? implode(',', $value) : $value);
				if(empty($value) && substr($ex_caption,-1,1) === '*') {
					return set_error(getLang('request_input',[substr($ex_caption,0,-1)]), 1);
				}
				$values[$ex_key] = cutstr($value,255,'');
			}
			if(!empty($values)) $wr_extra['values'] = $values;
		}
	}

	$data['wr_type'] = @$data['wr_type'] ? $data['wr_type'] : 0;
	if($data['wr_type'] > 1) $data['wr_type'] = 1; // 저장은 MD 타입으로 저장 (db 가벼워져서)

	$data['wr_tags'] = empty($data['wr_tags'])?getHashtags($data['wr_content']):implode(',',$data['wr_tags']);
	$data['wr_content'] = xssClean($data['wr_content'], $module['use_type'] == '9' || $module['use_type'] == '3');

	DB::transaction();

	try {
		if ($new_insert) {

			// 문서 번호를 얻기 위해 먼저 추가
			if(true === DB::insert(
				_AF_DOCUMENT_TABLE_,
				[
					'md_id'=>'_TMP_DOC_',
					'wr_title'=>'tmp',
					'mb_srl'=>$data['mb_srl'],
					'mb_rank'=>$data['mb_rank'],
					'mb_nick'=>$data['mb_nick'],
					'mb_password'=>$encrypt_password,
					'mb_ipaddress'=>$_SERVER['REMOTE_ADDR'],
					'wr_regdate(=)'=>'NOW()',
					'wr_update(=)'=>'NOW()'
				]
			)) {
				$wr_srl = DB::insertId();
			}
			if (empty($wr_srl)) {
				throw new Exception(getLang('error_occured'), 4001);
			}
		}

		if(!empty($data['remove_files'])) {
			foreach ($data['remove_files'] as $val) {
				$file = DB::get(_AF_FILE_TABLE_, [
					'md_id'=>$md_id,
					'mf_target'=>$wr_srl,
					'mf_srl'=>$val
				]);
				if(!empty($file) && true === DB::delete(_AF_FILE_TABLE_, [
					'md_id'=>$md_id,
					'mf_target'=>$wr_srl,
					'mf_srl'=>$val])
				) {
					$ftype = explode('/', strtolower($file['mf_type']));
					$ftype = empty($file_types[$ftype[0]]) ? 'binary' : $ftype[0];
					$unlink_files[] = _AF_ATTACH_DATA_.$ftype.'/'.$md_id.'/'.$wr_srl.'/'.$file['mf_upload_name'];
				}
			}
		}

		// 첨부 파일 수 계산을 위해 미리 가져 오기
		$file_count = DB::count(_AF_FILE_TABLE_, ['md_id'=>$md_id,'mf_target'=>$wr_srl]);

		if($upload_count > 0) {
			// 권한 체크
			if(!isGrant('upload', $md_id)) throw new Exception(getLang('warn_not_allowable', ['upload']), 3505);
			if($file_max < ($upload_count+$file_count)) throw new Exception(getLang('error_upload(-3)'), 10487);

			for ($i=0; $i < $upload_count; $i++) {
				// 빈 파일 넘김
				if(empty($files['tmp_name'][$i])) continue;

				//$iinfo = getimagesize($files['tmp_name'][$i]);
				$file = [
					'tmp_name' => $files['tmp_name'][$i],
					'name' => $files['name'][$i],
					'type' => $files['type'][$i],
					//'bits' => $iinfo['bits'],
					//'width' => $iinfo[0],
					//'height' => $iinfo[1],
					'size' => $files['size'][$i],
					'error' => $files['error'][$i]
				];

				if($file_accept && !preg_match('/\.('.($file_accept).')$/i', $file['name'])) {
					throw new Exception(getLang('warn_allowable', [$module['md_file_accept']]), 3503);
				}
				// 실행 가능한 파일 못하게 처리
				$file['name'] = preg_replace('/\.(php|phtm|phar|html?|cgi|pl|exe|[aj]sp|inc)/i', '$0-x', $file['name']);

				$fname = md5($i.$file['name'].time());
				$ftype = explode('/', strtolower($file['type']));
				$ftype = empty($file_types[$ftype[0]]) ? 'binary' : $ftype[0];

				$to_files[$i] = _AF_ATTACH_DATA_ . $ftype . '/' . $md_id . '/' . $wr_srl . '/' . $fname;

				$ret = moveUploadedFile($file, $to_files[$i], $file_max_size);
				if(!empty($ret['error'])) throw new Exception($ret['message'], $ret['error']);

				DB::insert(_AF_FILE_TABLE_, [
					'md_id'=>$md_id,
					'mf_target'=>$wr_srl,
					'mf_name'=>$file['name'],
					'mf_upload_name'=>$fname,
					'mf_size'=>$file['size'],
					'mf_type'=>$file['type'],
					'mb_srl'=>$data['mb_srl'],
					'mb_ipaddress'=>$_SERVER['REMOTE_ADDR'],
					'mf_regdate(=)'=>'NOW()'
				]);

				$file['mf_srl'] = $mf_srl = DB::insertId();
				$new_files[] = $file;
				$file_count++;
			}

			//$patterns = '/\[blob-([0-9]+)\]\(([^\)\"]+)(?:(?(R)\"|\s\"([^\"]+)\"\))|\))/Us';
			$patterns = '/\(blob[^\)\"]+\s\"blob-([0-9]+)\"\)/Us';
			$data['wr_content'] = preg_replace_callback(
				$patterns,
				function ($matches) use($new_files) {
					$file = $new_files[(int)$matches[1]];
					$es_name = escapeHTML($file['name']);
					$isimg = substr($file['type'], 0, 5) == 'image';
					return sprintf('(%s "%s")',
						'./?file='.$file['mf_srl'],
						escapeHTML($file['type'])
					);
				},
				$data['wr_content']
			);
		}

		DB::update(_AF_DOCUMENT_TABLE_,
			[
				'md_id'=>$md_id,
				'wr_secret'=>$wr_secret,
				'wr_type'=>$data['wr_type'],
				'wr_category'=>trim($data['wr_category']),
				'wr_title'=>$data['wr_title'],
				'wr_content'=>$data['wr_content'],
				'wr_tags'=>substr($data['wr_tags'], 0, 255),
				'wr_file'=>$file_count,
				'wr_extra'=>empty($wr_extra)?'':serialize($wr_extra),
				'wr_update(=)'=>'NOW()'
			], [
				'wr_srl'=>$wr_srl
			]
		);

		// 포인트 사용중이고 새글이면
		if($new_insert && ($point=(int)$module['point_write'])){
			if(setHistory('wr_document::'.$wr_srl, $point)){
				if(($_r=setPoint($point)) && !empty($_r['error'])){
					// TODO 포인트 에러시...
				}
			}
		}

		// 모두 완료 되면 지워진 파일 완전 삭제
		foreach ($unlink_files as $val) @unlinkFile($val);

		// 비회원이면 비밀번호 다시 안묻기위해 임시권한주기
		if(empty($_MEMBER)) {
			$PERMIT_KEY = md5($md_id.'_'.$wr_srl . '_' . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
			set_session('_AF_SECRET_DOCUMENT_'.$PERMIT_KEY, true);
		}

		// 썸네일 제거
		unlinkAll(_AF_ATTACH_DATA_.'thumbnail/'.$md_id.'/'.$wr_srl.'/');

	} catch (Exception $ex) {
		DB::rollback();

		// Engine == MyISAM 트랜잭션을 지원 안한다.
		if (DB::engine(_AF_DOCUMENT_TABLE_) == 'myisam') {
			if($new_insert && !empty($wr_srl)) {
				@DB::delete(_AF_DOCUMENT_TABLE_, ['wr_srl'=>$wr_srl]);
				@DB::delete(_AF_FILE_TABLE_, ['md_id'=>$md_id,'mf_target'=>$wr_srl]);
			} else if(count($new_files)>0) {
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

	$success_url = $data['success_url'].'&srl='.$wr_srl;
	return ['error'=>0, 'message'=>getLang('success_saved'), 'wr_srl'=>$wr_srl, 'success_url'=>$success_url];
}

/* End of file updatedocument.php */
/* Location: ./module/board/proc/updatedocument.php */
