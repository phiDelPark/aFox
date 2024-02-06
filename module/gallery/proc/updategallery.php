<?php if(!defined('__AFOX__')) exit();
include_once dirname(__FILE__) . '/../patterns.php';

function proc($data) {
	if(empty($data['md_id'])) return set_error(getLang('error_request'),4303);
	if(!preg_match('/'._AF_PATTERN_ID_.'/', $data['md_id'])) return set_error(getLang('invalid_value', ['id']),2001);

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

	$module['use_style'] = abs($module['use_style']);
	$use_style = ['list','review','gallery','timeline'];
	$use_style = $use_style[$module['use_style']>3?0:$module['use_style']];

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

	$files = null;
	$upload_count = 0;
	$new_files = [];

	// 파일이 업로드되면 최대 수 체크
	if(!empty($_FILES['upload_files']['tmp_name'])) {
		$files = $_FILES['upload_files'];
		$upload_count = count($files['tmp_name']);
		// 빈파일만 넘어오면 변수 삭제
		if($upload_count > 0 && empty($files['tmp_name'][0])) {
			$upload_count = 0;
		}
	}

	// 관리자는 제한 없음
	if($is_admin) {
		$file_max = 99999999;
		$file_max_size = 0;
		$file_accept = '';
	} else {
		$file_max = (int) empty($module['md_file_max']) ? 0 : $module['md_file_max'];
		$file_max_size = (int) $module['md_file_size'];
		$file_accept = str_replace(',', '|', $module['md_file_accept']);
	}

	$to_files = [];
	$unlink_files = [];
	$file_types = array('binary'=>0, 'image' => 1, 'video' => 2, 'audio' => 3);

	$wr_extra = [];

	if(!empty($module['md_extra']) && !is_array($module['md_extra'])) {
		$module['md_extra'] = unserialize($module['md_extra']);
		// 확장 변수 키값이 있으면 해당 값을 입력
		if (!empty($module['md_extra']['keys'])) {
			$values = [];
			foreach($module['md_extra']['keys'] as $ex_key=>$ex_caption){
				$value = $data['wr_extra_'.$ex_key];
				$value = trim(is_array($value) ? implode(',', $value) : $value);
				if(empty($value) && substr($ex_caption,-1,1) === '*') {
					return set_error(getLang('request_input',[substr($ex_caption,0,-1)]), 1);
				}
				$values[$ex_key] = cutstr($value,255,'');
			}
			if(!empty($values)) $wr_extra['values'] = $values;
		}
	}

	$data['wr_content'] = xssClean($data['wr_content']);
	$data['wr_tags'] = empty($data['wr_tags'])?getHashtags($data['wr_content']):implode(',',$data['wr_tags']);

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
					'^wr_regdate'=>'NOW()',
					'^wr_update'=>'NOW()'
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
			if(!isGrant('upload', $md_id)) throw new Exception(getLang('warning_not_allowable', ['upload']), 3505);
			if($file_max < ($upload_count+$file_count)) throw new Exception(getLang('UPLOAD_ERR_CODE(-3)'), 10487);
			$exif = $use_style == 'gallery' && function_exists('exif_read_data');

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

				$ftype = explode('/', strtolower($file['type']));
				$ftype = empty($file_types[$ftype[0]]) ? 'binary' : $ftype[0];
				$f_ext = explode('.', $file['name']);
				$f_ext = count($f_ext) === 1 ? 'none' : $f_ext[count($f_ext)-1]; //array_pop

				if($file_accept && !preg_match('/('.($file_accept).')$/i', $filename)) {
					throw new Exception(getLang('warning_allowable', [
						str_replace('.', '', $module['md_file_accept'])
					]), 3503);
				}

				// 실행 가능한 파일 못하게 처리
				$f_ext = preg_replace('/\.(php|phtm|phar|html?|cgi|pl|exe|[aj]sp|inc)/i', '$0-x', ('.'.$f_ext));

				$filename = md5($i.$file['name'].time());
				$to_files[$i] = _AF_ATTACH_DATA_ . $ftype . '/' . $md_id . '/' . $wr_srl . '/' . $filename;

				$ret = moveUpFile($file, $to_files[$i], $file_max_size);
				if(!empty($ret['error'])) throw new Exception($ret['message'], $ret['error']);

				if($exif && $ftype == 'image' && ($ifd0=@exif_read_data($to_files[$i]))){
					$DateTime = trim(empty($ifd0['DateTimeOriginal']) ? '' : $ifd0['DateTimeOriginal']);
					if($DateTime) $file['date'] = date('Y-m-d H:i:s', strtotime($DateTime));
					if($Model = (trim(empty($ifd0['Make']) ? 'Unavailable' : $ifd0['Make']))) {
						$Model .= ' - '.trim(empty($ifd0['Model']) ? 'Unavailable' : $ifd0['Model']);
						$file['name'] = str_replace(array('\\','/',':','*','?','"','<','>','|'),' ',$Model);
					}
				}else{
					$file['date'] = 'NOW()';
				}

				DB::insert(_AF_FILE_TABLE_, [
					'md_id'=>$md_id,
					'mf_target'=>$wr_srl,
					'mf_name'=>$file['name'],
					'mf_upload_name'=>$filename,
					'mf_size'=>$file['size'],
					'mf_type'=>$file['type'],
					'mb_srl'=>$data['mb_srl'],
					'mb_ipaddress'=>$_SERVER['REMOTE_ADDR'],
					($file['date']=='NOW()'?'^':'').'mf_regdate'=>$file['date']
				]);

				$file['mf_srl'] = $mf_srl = DB::insertId();
				$new_files[] = $file;
				$file_count++;
			}

			$patterns = '/<[img|a][^>]+[src|href]=[\\"\']+blob\:[^>\\"\']+[\\"\']?[^>]*target=[\\"\']+([0-9])+[\\"\']?[^>]*>/is';

			$data['wr_content'] = preg_replace_callback(
				$patterns,
				function ($matches) use($new_files) {
					$file = $new_files[(int)$matches[1]];
					$es_name = escapeHTML($file['name']);
					$isimg = substr($file['type'], 0, 5) == 'image';
					return sprintf(
						$isimg ? '<img src="%s" class="%s" alt="%s">'
							: '<a href="%s" class="%s" title="%s" target="_file">',
						'./?file='.$file['mf_srl'],
						escapeHTML($file['type']),
						$es_name.' ('.shortFileSize($file['size']). ')'
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
				'wr_tags'=>$data['wr_tags'],
				'wr_file'=>$file_count,
				'wr_extra'=>empty($wr_extra)?'':serialize($wr_extra),
				'^wr_update'=>'NOW()'
			], [
				'wr_srl'=>$wr_srl
			]
		);

		// 포인트 사용중이고 새글이면
		$point = (int)$module['point_write'];
		if($new_insert && $point !== 0){
			$_r = setPoint($point);
			if(!empty($_r['error'])) {
				//TODO 에러시 메세지 보냄
			}
			setHistory('wr_document::'.$wr_srl, $point);
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
				@DB::delete(_AF_FILE_TABLE_, ['mf_srl{IN}'=>implode(',', $new_files)]);
			}
			// 트랜잭션을 지원 안하면 삭제된 파일은 그냥 지움
			foreach ($unlink_files as $val) @unlinkFile($val);
		}

		// 실패시 업로드 된 파일 삭제
		foreach ($to_files as $val) @unlinkFile($val);

		return set_error($ex->getMessage(),$ex->getCode());
	}

	DB::commit();

	return ['error'=>0, 'message'=>getLang('success_saved'), 'wr_srl'=>$wr_srl];
}

/* End of file updategallery.php */
/* Location: ./module/board/proc/updategallery.php */