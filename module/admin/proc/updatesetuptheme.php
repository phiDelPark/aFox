<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	if(empty($data['th_id'])) return set_error(getLang('msg_invalid_request'),303);

	if(!is_dir(_AF_THEMES_PATH_.$data['th_id'])) {
		return set_error(getLang('msg_not_founded'),801);
	}

	DB::transaction();

	try {

		$th_id = $data['th_id'];
		DB::update(_AF_CONFIG_TABLE_,
			['theme'=>$data['th_id']],
			['theme{IS}'=>'not null']
		);

	} catch (Exception $ex) {
		DB::rollback();
		return set_error($ex->getMessage(),$ex->getCode());
	}

	DB::commit();

	return ['error'=>0, 'message'=>getLang('success_saved')];
}

/* End of file updatethemeconfig.php */
/* Location: ./module/admin/proc/updatethemeconfig.php */