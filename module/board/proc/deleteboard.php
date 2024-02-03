<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	if(empty($data['md_id'])) return set_error(getLang('error_request'),4303);

	// 권한 체크 // 관리자만
	if(!isAdmin()) return set_error(getLang('error_permitted'), 4501);

	DB::transaction();

	try {
		$module = DB::get(_AF_MODULE_TABLE_, ['md_key'=>'board','md_id'=>$data['md_id']]);
		if(empty($module['md_id'])) throw new Exception(getLang('error_request'),4303);

		$md_id = $module['md_id'];

		// 폴더 삭제
		$types = ['binary','image','video','audio','thumbnail'];
		foreach ($types as $val) {
			unlinkAll(_AF_ATTACH_DATA_ . $val . '/' . $md_id . '/');
		}

		// 파일 , 댓글 , 문서 삭제
		DB::query('DELETE d, c, f FROM '._AF_DOCUMENT_TABLE_.' AS d LEFT JOIN '._AF_COMMENT_TABLE_.' AS c ON c.wr_srl = d.wr_srl LEFT JOIN '._AF_FILE_TABLE_.' AS f ON f.md_id = d.md_id WHERE d.md_id = \'' . $md_id . '\' OR (d.md_id = \'_AFOXtRASH_\' AND d.wr_updater = \'' . $md_id . '\')');
		// 모듈 삭제
		DB::delete(_AF_MODULE_TABLE_,['md_key'=>'board','md_id'=>$md_id]);

	} catch (Exception $ex) {
		DB::rollback();
		return set_error($ex->getMessage(),$ex->getCode());
	}

	DB::commit();

	return ['error'=>0, 'message'=>getLang('success_deleted')];
}

/* End of file deleteboard.php */
/* Location: ./module/admin/proc/deleteboard.php */
