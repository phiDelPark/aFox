<?php

if(!defined('__AFOX__')) exit();

function proc($data) {

	// 권한 체크 // 관리자만
	if(!isAdmin()) return set_error(getLang('error_permitted'), 4501);

	DB::transaction();

	try {

		$callback = function($r) {
			while ($row = DB::assoc($r)) {
				$wr_srl = $row['wr_srl'];
				$md_id = $row['wr_updater'];
				// 파일 , 댓글 , 문서 삭제
				DB::delete(_AF_COMMENT_TABLE_,['wr_srl'=>$wr_srl]);
				DB::delete(_AF_FILE_TABLE_,['md_id'=>$md_id,'mf_target'=>$wr_srl]);
				// 파일 삭제
				$variable = ['binary','image','video','audio','thumbnail'];
				foreach ($variable as $val) {
					unlinkAll(_AF_ATTACH_DATA_ . $val . '/' . $md_id . '/' . $wr_srl . '/');
				}
			}
			return [];
		};

		DB::query('SELECT wr_srl,wr_updater FROM '._AF_DOCUMENT_TABLE_.' WHERE md_id=\'_AFOXtRASH_\'', $callback);
		DB::delete(_AF_DOCUMENT_TABLE_,['md_id'=>'_AFOXtRASH_']);

	} catch (Exception $ex) {
		DB::rollback();
		return set_error($ex->getMessage(),$ex->getCode());
	}

	DB::commit();

	return ['error'=>0, 'message'=>getLang('success_finished')];
}

/* End of file emptyrecyclebin.php */
/* Location: ./module/admin/proc/emptyrecyclebin.php */
