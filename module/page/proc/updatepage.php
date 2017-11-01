<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	global $_MEMBER;

	if (isset($data['new_md_id'])) $data['md_id'] = $data['new_md_id'];
	if (empty($data['md_id'])) return set_error(getLang('error_request'),4303);

	if (!preg_match('/^[a-zA-Z]+\w{2,}$/', $data['md_id'])) {
		return set_error(getLang('invalid_value', ['id']),701);
	}

	$upload_count = 0;
	$files = null;

	// 파일이 업로드되면 최대 수 체크
	if (!empty($_FILES['upload_files']['tmp_name'])) {
		$files = $_FILES['upload_files'];
		$upload_count = count($files['tmp_name']);
		// 빈파일만 넘어오면 변수 삭제
		if ($upload_count > 0 && empty($files['tmp_name'][0])) {
			$upload_count = 0;
			$files = null;
			unset($_FILES);
		}
	}

	$data['md_title'] = trim(strip_tags($data['md_title']));
	$data['pg_content'] = xssClean($data['pg_content']);

	$unlink_files = [];
	$file_dests = [];
	$file_types = array('binary'=>0, 'image' => 1, 'video' => 2, 'audio' => 3);

	DB::transaction();

	try {

		$md_id = $data['md_id'];
		$module = getModule($md_id);

		$new_insert = empty($module['md_id']);
		$new_files = [];

		// 권한 체크, 파일 첨부 때문에 먼저 함
		if ($new_insert) {
			if (!isset($data['new_md_id'])) {
				throw new Exception(getLang('error_request'),4303);
			}

			// id 를 얻기 위해 먼저 추가
			DB::insert(_AF_MODULE_TABLE_,
				[
					'md_id'=>$md_id,
					'md_key'=>'page',
					'md_title'=>$data['md_title'],
					'(md_regdate)'=>'NOW()'
				]
			);

			DB::insert(_AF_PAGE_TABLE_,['md_id'=>$md_id,'(pg_regdate)'=>'NOW()']);

			$module = getModule($md_id);
		} else {
			if (isset($data['new_md_id'])) {
				throw new Exception(getLang('error_exists'), 4251);
			}
		}

		// 관리자만 권한 설정 가능
		if (empty($_MEMBER) || $_MEMBER['mb_rank'] != 's') {
			$data['grant_view'] = $module['grant_view'];
			$data['grant_reply'] = $module['grant_reply'];
			$data['grant_download'] = $module['grant_download'];
		}

		if (!empty($data['remove_files'])) {

			foreach ($data['remove_files'] as $val) {
				$out = DB::select(_AF_FILE_TABLE_, [
					'md_id'=>$md_id,
					'mf_target'=>1,
					'mf_srl'=>$val
				]);

				$file = DB::assoc($out);
				if (!empty($file) && true === DB::delete(_AF_FILE_TABLE_, [
					'md_id'=>$md_id,
					'mf_target'=>1,
					'mf_srl'=>$val])
				) {
					$filetype = strtolower(array_shift(explode('/', $file['mf_type'])));
					$filetype = empty($file_types[$filetype]) ? 'binary' : $filetype;
					$unlink_files[] = _AF_ATTACH_DATA_.$filetype.'/'.$md_id.'/1/'.$file['mf_upload_name'];
				}
			}
		}

		// 첨부 파일 수 계산을 위해 미리 가져 오기
		$file_count = DB::count(_AF_FILE_TABLE_, ['md_id'=>$md_id,'mf_target'=>1]);

		if ($upload_count>0) {

			$chk_ext = '';
			$mb_ipaddress = $_SERVER['REMOTE_ADDR'];

			for ($i=0; $i < $upload_count; $i++) {
				// 빈 파일 넘김
				if (empty($files['tmp_name'][$i])) continue;

				$file = [
					'name' => $files['name'][$i],'type' => $files['type'][$i],
					'tmp_name' => $files['tmp_name'][$i],'error' => $files['error'][$i],
					'size' => $files['size'][$i]
				];

				$filetype = strtolower(array_shift(explode('/', $file['type'])));
				$filetype = empty($file_types[$filetype]) ? 'binary' : $filetype;
				$filename = $file['name'];
				$fileext = explode('.', $filename);
				$fileext = count($fileext)===1 ? 'none' : $fileext[count($fileext)-1]; //array_pop

				if ($chk_ext && !preg_match('/\.('.($chk_ext).')$/i', $filename)) {
					throw new Exception(getLang('warning_allowable', [$chk_ext])."\n", 2501);
				}

				// 실행 가능한 파일 못하게 처리
				$fileext = preg_replace('/\.(php|phtm|phar|html?|cgi|pl|exe|jsp|asp|inc)/i', '$0-x', ('.'.$fileext));

				$filename = md5($filename.time().$i) . '.' . $fileext;
				$file_dests[$i] = _AF_ATTACH_DATA_ . $filetype . '/' . $md_id . '/1/' . $filename;

				$ret = moveUpFile($file, $file_dests[$i], 0);
				if (!empty($ret['error'])) {
					throw new Exception($ret['message'], $ret['error']);
				}

				DB::insert(_AF_FILE_TABLE_, [
					'md_id'=>$md_id,
					'mf_target'=>1,
					'mf_name'=>$file['name'],
					'mf_upload_name'=>$filename,
					'mf_size'=>$file['size'],
					'mf_type'=>$file['type'],
					'mb_srl'=>0,
					'mb_ipaddress'=>$mb_ipaddress,
					'(mf_regdate)'=>'NOW()'
				]);

				$new_files[] = $mf_srl = DB::insertId();
				$file_count++;

				if ($data['pg_type'] == 2) {
					$patterns = '/(<[a|img|source][^>]*)([src|href])(=[\"\']?[^>\"\']+[\"\']?)([^>]*data-af-editor-tmpfile=[\"\']?'.$i.'[\"\']?)([^>]*>)/i';
					$replacement = "\\1\\2=\""._AF_URL_."?file={$mf_srl}\"\\5";
				} else {
					$patterns = '/(\[.+\]\()(af-editor-tmpfile='.$i.')(\s?"?[^\)"]*"?\))/i';
					$replacement = "\\1"._AF_URL_."?file={$mf_srl}\\3";
				}
				$data['pg_content'] = preg_replace($patterns, $replacement, $data['pg_content']);
			}
		}

		DB::update(_AF_MODULE_TABLE_,
			[
				'md_key'=>'page',
				'md_title'=>$data['md_title'],
				'md_description'=>$data['md_description'],
				'md_manager'=>0,
				'md_file_max'=>9999,
				'md_file_size'=>0,
				'grant_list'=>'0',
				'grant_view'=>isset($data['grant_view'])?$data['grant_view']:$module['grant_view'],
				'grant_write'=>'m',
				'grant_reply'=>isset($data['grant_reply'])?$data['grant_reply']:$module['grant_reply'],
				'grant_upload'=>'m',
				'grant_download'=>isset($data['grant_download'])?$data['grant_download']:$module['grant_download']
			], [
				'md_id'=>$md_id
			]
		);

		DB::update(_AF_PAGE_TABLE_,
			[
				'pg_type'=>$data['pg_type'],
				'pg_content'=>$data['pg_content'],
				'pg_file'=>$file_count,
				'(pg_update)'=>'NOW()'
			], [
				'md_id'=>$md_id
			]
		);

		// 모두 완료 되면 지워진 파일 완전 삭제
		foreach ($unlink_files as $val) @unlinkFile($val);

		// 썸네일 제거
		unlinkAll(_AF_ATTACH_DATA_.'thumbnail/'.$md_id.'/');

	} catch (Exception $ex) {
		DB::rollback();

		// myisam면 rollback 수동으로 해야됨
		if (DB::engine(_AF_PAGE_TABLE_) === 'myisam') {
			if ($new_insert && !empty($wr_srl)) {
				@DB::delete(_AF_PAGE_TABLE_, ['md_id'=>$md_id]);
				@DB::delete(_AF_MODULE_TABLE_, ['md_id'=>$md_id]);
				@DB::delete(_AF_FILE_TABLE_, ['md_id'=>$md_id,'mf_target'=>1]);
			} else if (count($new_files)>0) {
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

	return ['error'=>0, 'message'=>getLang('success_saved')];
}

/* End of file updatepage.php */
/* Location: ./module/page/proc/updatepage.php */
