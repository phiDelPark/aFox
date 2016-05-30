<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	if(empty($data['th_id'])) return set_error(getLang('msg_invalid_request'),303);

	DB::transaction();

	try {
		DB::delete(_AF_THEME_TABLE_,
			[
				'th_id'=>$data['th_id']
			]
		);
	} catch (Exception $ex) {
		DB::rollback();
		return set_error($ex->getMessage(),$ex->getCode());
	}

	DB::commit();

	return ['error'=>0, 'message'=>getLang('success_deleted')];
}

/* End of file deletethemeconfig.php */
/* Location: ./module/admin/proc/deletethemeconfig.php */