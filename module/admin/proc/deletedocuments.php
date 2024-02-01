<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	if(empty($data['wr_srls'])) return set_error(getLang('error_request'),4303);

	// 권한 체크 // 관리자만
	if(!isAdmin()) return set_error(getLang('error_permitted'), 4501);

	DB::transaction();

	$wr_srls = is_array($data['wr_srls']) ? $data['wr_srls'] : [$data['wr_srls']];

	try {

		// 휴지통으로 보냄
		DB::update(_AF_DOCUMENT_TABLE_,
			[
				'^wr_updater'=>_AF_DOCUMENT_TABLE_.'.md_id',
				'^wr_update'=>'NOW()',
				'md_id'=>'_AFOXtRASH_'
			], [
				'wr_srl{IN}'=>implode(',', $wr_srls)
			]
		);

	} catch (Exception $ex) {
		DB::rollback();
		return set_error($ex->getMessage(),$ex->getCode());
	}

	DB::commit();

	return ['error'=>0, 'message'=>getLang('success_deleted')];
}

/* End of file deletedocuments.php */
/* Location: ./module/admin/proc/deletedocuments.php */
