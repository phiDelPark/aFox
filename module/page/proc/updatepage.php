<?php if(!defined('__AFOX__')) exit();

function proc($data)
{
	if (isset($data['new_md_id'])) $data['md_id'] = $data['new_md_id'];
	if(empty($data['md_id'])||!preg_match('/^[a-zA-Z]+\w{2,}$/', $data['md_id'])){
		return set_error(getLang('invalid_value', ['id']), 2001);
	}

	global $_MEMBER;
	$md_id = $data['md_id'];

	// 파일이 업로드되면 최대 수 체크
	$files = empty($_FILES['upload_files']['tmp_name'])?null:$_FILES['upload_files'];
	$upload_count = $files&&!empty($files['tmp_name'][0])?count($files['tmp_name']):0;

	$to_files = [];
	$new_files = [];
	$unlink_files = [];
	$file_types = array('binary'=>0, 'image' => 1, 'video' => 2, 'audio' => 3);

	$module = getModule($md_id);
	$new_insert = empty($module['md_id']);

	$data['pg_type'] = @$data['pg_type'] ? $data['pg_type'] : 0;
	if($data['pg_type'] > 1) $data['pg_type'] = 1; // 저장은 MD 타입으로 저장 (db 가벼워져서)

	$data['pg_content'] = xssClean($data['pg_content']);

	DB::transaction();

	try {
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
					'md_title'=>trim(strip_tags($data['md_title'])),
					'md_extra'=>'',
					'md_regdate(=)'=>'NOW()'
				]
			);

			DB::insert(_AF_PAGE_TABLE_,['md_id'=>$md_id,'pg_regdate(=)'=>'NOW()','pg_update(=)'=>'NOW()']);

			$module = getModule($md_id);
		} else {
			if (isset($data['new_md_id']) || $module['md_key'] != 'page') {
				throw new Exception(getLang('warn_exists', ['id']), 3103);
			}

			// 수정시 페이지 정보 넘어왔는지 체크 (관리자 모드에서 수정 안하면 일부 항목이 없음)
			if(!isset($data['md_title'])) $data['md_title'] = $module['md_title'];
			else $data['md_title'] = trim(strip_tags($data['md_title']));
			if(!isset($data['md_about'])) $data['md_about'] = $module['md_about'];
			if(!isset($data['grant_view'])) $data['grant_view'] = $module['grant_view'];
			if(!isset($data['grant_reply'])) $data['grant_reply'] = $module['grant_reply'];
			if(!isset($data['grant_download'])) $data['grant_download'] = $module['grant_download'];
		}

		// 관리자만 권한 설정 가능
		/*
		if (!isAdmin()) {
			$data['grant_view'] = $module['grant_view'];
			$data['grant_reply'] = $module['grant_reply'];
			$data['grant_download'] = $module['grant_download'];
		}
		*/

		if (!empty($data['remove_files'])) {

			foreach ($data['remove_files'] as $val) {
				$file = DB::get(_AF_FILE_TABLE_, [
					'md_id'=>$md_id,
					'mf_srl'=>$val
				]);
				if (!empty($file) && true === DB::delete(_AF_FILE_TABLE_, [
					'md_id'=>$md_id,
					'mf_srl'=>$val])
				) {
					$ftype = explode('/', strtolower($file['mf_type']));
					$ftype = empty($file_types[$ftype[0]]) ? 'binary' : $ftype[0];
					$unlink_files[] = _AF_ATTACH_DATA_.$ftype.'/'.$md_id.'/'.$file['mf_target'].'/'.$file['mf_upload_name'];
				}
			}
		}

		// 첨부 파일 수 계산을 위해 미리 가져 오기
		$file_count = DB::count(_AF_FILE_TABLE_, ['md_id'=>$md_id,'mf_target'=>1]);

		if ($upload_count>0) {
			for ($i=0; $i < $upload_count; $i++) {
				// 빈 파일 넘김
				if(empty($files['tmp_name'][$i])) continue;

				$file = [
					'tmp_name' => $files['tmp_name'][$i],
					'name' => $files['name'][$i],
					'type' => $files['type'][$i],
					'size' => $files['size'][$i],
					'error' => $files['error'][$i]
				];

				// 실행 가능한 파일 못하게 처리
				$file['name'] = preg_replace('/\.(php|phtm|phar|html?|cgi|pl|exe|[aj]sp|inc)/i', '$0-x', $file['name']);

				$fname = md5($i.$file['name'].time());
				$ftype = explode('/', strtolower($file['type']));
				$ftype = empty($file_types[$ftype[0]]) ? 'binary' : $ftype[0];

				$to_files[$i] = _AF_ATTACH_DATA_ . $ftype . '/' . $md_id . '/1/' . $fname;

				$ret = moveUploadedFile($file, $to_files[$i], 0);
				if(!empty($ret['error'])) throw new Exception($ret['message'], $ret['error']);

				DB::insert(_AF_FILE_TABLE_, [
					'md_id'=>$md_id,
					'mf_target'=>1,
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
			$data['pg_content'] = preg_replace_callback(
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
				$data['pg_content']
			);
		}

		DB::update(_AF_MODULE_TABLE_,
			[
				'md_key'=>'page',
				'md_title'=>$data['md_title'],
				'md_about'=>$data['md_about'],
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
				'pg_update(=)'=>'NOW()'
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

		// Engine == MyISAM 트랜잭션을 지원 안한다.
		if (DB::engine(_AF_PAGE_TABLE_) == 'myisam') {
			if ($new_insert) {
				@DB::delete(_AF_PAGE_TABLE_, ['md_id'=>$md_id]);
				@DB::delete(_AF_MODULE_TABLE_, ['md_id'=>$md_id]);
				@DB::delete(_AF_FILE_TABLE_, ['md_id'=>$md_id,'mf_target'=>1]);
			} else if (count($new_files)>0) {
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

/* End of file updatepage.php */
/* Location: ./module/page/proc/updatepage.php */
