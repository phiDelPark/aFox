<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	if(empty($data['ao_id'])) return set_error(getLang('error_request'),4303);

	DB::transaction();

	try {

		DB::delete(_AF_TRIGGER_TABLE_,['tg_key'=>'A','tg_id'=>$data['ao_id']]);
		DB::delete(_AF_ADDON_TABLE_,['ao_id'=>$data['ao_id']]);

	} catch (Exception $ex) {
		DB::rollback();
		return set_error($ex->getMessage(),$ex->getCode());
	}

	DB::commit();

	return ['error'=>0, 'message'=>getLang('success_deleted')];
}

/* End of file deleteaddonconfig.php */
/* Location: ./module/admin/proc/deleteaddonconfig.php */
