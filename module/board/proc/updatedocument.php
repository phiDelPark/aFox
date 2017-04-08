<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	if(empty($data['md_id'])) return set_error(getLang('msg_invalid_request'),303);
	$data['wr_title'] = trim(strip_tags($data['wr_title']));
	if(empty($data['wr_title'])) return set_error(getLang('warn_input', ['title']));

	global $_MEMBER;

	$module = getModule($data['md_id']);
	if(!empty($module['error'])) return set_error($module['message'],$module['error']);
	if(empty($module['md_id'])) return set_error(getLang('msg_invalid_request'),303);

	$is_admin = !empty($_MEMBER) && $_MEMBER['mb_rank'] == 's';

	if(!empty($module['use_type'])) $data['wr_type'] = ((int)$module['use_type'])-1;
	if(!empty($module['use_secret'])) $data['wr_secret'] = ((int)$module['use_secret'])-1;

	$files = null;
	$upload_count = 0;

	// 파일이 업로드되면 최대 수 체크
	if(!empty($_FILES['upload_files']['tmp_name'])) {
		$files = $_FILES['upload_files'];
		$upload_count = count($files['tmp_name']);
		// 빈파일만 넘어오면 변수 삭제
		if($upload_count > 0 && empty($files['tmp_name'][0])) {
			$upload_count = 0;
			$files = null;
			unset($_FILES);
		}
	}

	DB::transaction();

	// 관리자는 제한 없음
	if($is_admin) {
		$file_exts = '';
		$file_max = 99999999;
		$file_max_size = 0;
	} else {
		$file_exts = $module['md_file_ext'] == '*' ? '' : $module['md_file_ext'];
		$file_max = (int) empty($module['md_file_max']) ? 0 : $module['md_file_max'];
		$file_max_size = (int) $module['md_file_size'];
	}

	$unlink_files = [];
	$unlink_thumbs = [];
	$file_dests = [];
	$_file_types = array('binary'=>0, 'image' => 1, 'video' => 2, 'audio' => 3);

	$wr_srl = (int) abs(empty($data['wr_srl']) ? 0 : $data['wr_srl']);
	$md_id = $module['md_id'];

	try {
		$doc = getDBItem(_AF_DOCUMENT_TABLE_, ['wr_srl'=>$wr_srl], 'md_id, mb_srl, mb_password');

		if(!empty($doc['error'])) throw new Exception($doc['message'], $doc['error']);
		if(!empty($wr_srl) && (empty($doc['md_id']) || $doc['md_id'] != $md_id)) {
			throw new Exception(getLang('msg_invalid_request'), 303);
		}

		if(!empty($module['md_category'])) {
			if(empty($data['wr_category'])) {
				throw new Exception(getLang('warn_input',['category']), 3);
			}
			if(preg_match('/[\x{21}-\x{2b}\x{2d}-\x{2f}\x{3a}-\x{40}\x{5b}-\x{60}]+/', $data['wr_category'])) {
				throw new Exception(getLang('invalid_value', ['category']), 701);
			}
		} else {
			$data['wr_category'] = '';
		}

		$data['wr_content'] = xssClean($data['wr_content']);
		$data['wr_tags'] = getHashtags($data['wr_content']);
		$data['mb_ipaddress'] = $_SERVER['REMOTE_ADDR'];

		if(empty($_MEMBER)) {
			$data['mb_nick'] = trim(empty($data['mb_nick'])?'':strip_tags($data['mb_nick']));
			if(empty($data['mb_nick']) || empty($data['mb_password'])) {
				throw new Exception(getLang('warn_input', [getLang('%s, %s', ['id', 'password'])]), 3);
			}
			$data['mb_srl'] = 0;
			$data['mb_rank'] = 0;
			$encrypt_password = encryptString($data['mb_password']);
		} else {
			$data['mb_srl'] = $_MEMBER['mb_srl'];
			$data['mb_rank'] = $_MEMBER['mb_rank'];
			$data['mb_nick'] = $_MEMBER['mb_nick'];
			$encrypt_password = null;
		}

		$new_insert = empty($wr_srl);
		$new_files = [];

		// 권한 체크, 파일 첨부 때문에 먼저 함
		if ($new_insert) {
			if(!isGrant($md_id, 'write')) {
				throw new Exception(getLang('msg_not_permitted'), 901);
			}

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
					'mb_ipaddress'=>$data['mb_ipaddress'],
					'(wr_regdate)'=>'NOW()'
				]
			)) {
				$wr_srl = DB::insertId();
			}
			if (empty($wr_srl)) {
				throw new Exception(getLang('msg_error_occured'), 101);
			}

			// 포인트 사용중이면
			$_r = setPoint((int)$module['point_write']);
			if(!empty($_r['error'])) throw new Exception($_r['message'], $_r['error']);

		} else {
			if(empty($_MEMBER)) {
				if(empty($doc['mb_password']) || !verifyEncrypt($data['mb_password'], $doc['mb_password'])) {
					throw new Exception(getLang('msg_not_permitted'), 901);
				}
			} else if(!isManager($md_id)) {
				if($_MEMBER['mb_srl'] != $doc['mb_srl']) {
					throw new Exception(getLang('msg_not_permitted'), 901);
				}
			}
		}

		if(!empty($data['remove_files'])) {

			foreach ($data['remove_files'] as $val) {
				$out = DB::select(_AF_FILE_TABLE_, [
					'md_id'=>$md_id,
					'mf_target'=>$wr_srl,
					'mf_srl'=>$val
				]);

				$file = DB::assoc($out);
				if(!empty($file) && true === DB::delete(_AF_FILE_TABLE_, [
					'md_id'=>$md_id,
					'mf_target'=>$wr_srl,
					'mf_srl'=>$val])
				) {
					$filetype = strtolower(array_shift(explode('/', $file['mf_type'])));
					$filetype = empty($_file_types[$filetype]) ? 'binary' : $filetype;
					$unlink_files[] = _AF_ATTACH_DATA_.$filetype.'/'.$md_id.'/'.$wr_srl.'/'.$file['mf_upload_name'];
					if($filetype=='image') {
						$unlink_thumbs[$file['mf_srl']] = $md_id.'/'.$wr_srl;
					}
				}
			}
		}

		// 첨부 파일 수 계산을 위해 미리 가져 오기
		$file_count = DB::count(_AF_FILE_TABLE_, ['md_id'=>$md_id,'mf_target'=>$wr_srl]);

		// 이전 수 + 업로드 수 체크
		if($upload_count > 0 && (($upload_count+$file_count) > $file_max)) {
			throw new Exception(getLang('UPLOAD_ERR_CODE(-3)'), 1487);
		}

		if($upload_count>0) {
			// 권한 체크
			if(!isGrant($md_id, 'upload')) throw new Exception(getLang('warn_not_permit', ['upload']), 901);
			if($file_max < $upload_count) throw new Exception(getLang('UPLOAD_ERR_CODE(-3)'), 1487);

			for ($i=0; $i < $upload_count; $i++) {
				// 빈 파일 넘김
				if(empty($files['tmp_name'][$i])) continue;

				$file = [
					'name' => $files['name'][$i],'type' => $files['type'][$i],
					'tmp_name' => $files['tmp_name'][$i],'error' => $files['error'][$i],
					'size' => $files['size'][$i]
				];

				$filetype = strtolower(array_shift(explode('/', $file['type'])));
				$filetype = empty($_file_types[$filetype]) ? 'binary' : $filetype;
				$filename = $file['name'];
				$fileext = explode('.', $filename);
				$fileext = count($fileext)===1 ? 'none' : $fileext[count($fileext)-1]; //array_pop

				if($file_exts && !preg_match('/\.('.($file_exts).')$/i', $filename)) {
					throw new Exception(getLang('warn_permit', [$file_exts])."\n", 303);
				}

				$filename = md5($filename.time().$i) . '.' . $fileext;
				$file_dests[$i] = _AF_ATTACH_DATA_ . $filetype . '/' . $md_id . '/' . $wr_srl . '/' . $filename;

				$ret = moveUpFile($file, $file_dests[$i], $file_max_size);
				if(!empty($ret['error'])) {
					throw new Exception($ret['message'], $ret['error']);
				}

				DB::insert(_AF_FILE_TABLE_, [
					'md_id'=>$md_id,
					'mf_target'=>$wr_srl,
					'mf_name'=>$file['name'],
					'mf_upload_name'=>$filename,
					'mf_size'=>$file['size'],
					'mf_type'=>$file['type'],
					'mb_srl'=>$data['mb_srl'],
					'mb_ipaddress'=>$data['mb_ipaddress'],
					'(mf_regdate)'=>'NOW()'
				]);

				$new_files[] = $mf_srl = DB::insertId();
				$file_count++;

				if($data['wr_type'] == 2) {
					$patterns = '/(<[a|img|source][^>]*)([src|href])(=[\"\']?[^>\"\']+[\"\']?)([^>]*data-af-editor-tmpfile=[\"\']?'.$i.'[\"\']?)([^>]*>)/i';
					$replacement = "\\1\\2=\""._AF_URL_."?file={$mf_srl}\"\\5";
				} else {
					$patterns = '/(\[.+\]\()(af-editor-tmpfile='.$i.')(\s?"?[^\)"]*"?\))/i';
					$replacement = "\\1"._AF_URL_."?file={$mf_srl}\\3";
				}
				$data['wr_content'] = preg_replace($patterns, $replacement, $data['wr_content']);
			}
		}

		DB::update(_AF_DOCUMENT_TABLE_,
			[
				'md_id'=>$md_id,
				'wr_secret'=>$data['wr_secret'],
				'wr_type'=>$data['wr_type'],
				'wr_category'=>trim($data['wr_category']),
				'wr_title'=>$data['wr_title'],
				'wr_content'=>$data['wr_content'],
				'wr_tags'=>$data['wr_tags'],
				'wr_file'=>$file_count,
				'(wr_update)'=>'NOW()'
			], [
				'wr_srl'=>$wr_srl
			]
		);

		// 모두 완료 되면 지워진 파일 완전 삭제
		foreach ($unlink_files as $val) @unlinkFile($val);

		// 썸네일 제거
		foreach ($unlink_thumbs as $key => $val) {
			$directory = _AF_ATTACH_DATA_ . 'thumbnail/' . $val . '/';
			if(is_dir($directory)){
				$handle = @opendir($directory); // 절대경로
				while ($file = readdir($handle)) {
					if (strpos($file, $key.'_') === false) continue;
					@unlinkFile($directory.$file);
				}
				closedir($handle);
				//@unlinkDir($directory);
			}
		}

	} catch (Exception $ex) {
		DB::rollback();

		// myisam면 rollback 수동으로 해야됨
		if(DB::engine(_AF_DOCUMENT_TABLE_) === 'myisam') {
			if($new_insert && !empty($wr_srl)) {
				@DB::delete(_AF_DOCUMENT_TABLE_, ['wr_srl'=>$wr_srl]);
				@DB::delete(_AF_FILE_TABLE_, ['md_id'=>$md_id,'mf_target'=>$wr_srl]);
			} else if(count($new_files)>0) {
				@DB::delete(_AF_FILE_TABLE_, ['mf_srl{IN}'=>implode(',', $new_files)]);
			}
			// myisam면 삭제된 파일은 그냥 지움
			foreach ($unlink_files as $val) @unlinkFile($val);
		}

		// 실패시 업로드 된 파일 삭제
		foreach ($file_dests as $val) @unlinkFile($val);

		return set_error($ex->getMessage(),$ex->getCode());
	}

	DB::commit();

	return ['error'=>0, 'message'=>getLang('success_saved'), 'wr_srl'=>$wr_srl];
}

/* End of file updatedocument.php */
/* Location: ./module/board/proc/updatedocument.php */