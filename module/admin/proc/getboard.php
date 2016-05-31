<?php

if(!defined('__AFOX__')) exit();

function proc($data) {

	if(!isset($data['md_id'])) return set_error(getLang('msg_invalid_request'),303);

	$out = getDBItem(_AF_MODULE_TABLE_, ['md_key'=>'board','md_id'=>$data['md_id']]);
	if(!empty($out['error'])) {
		return set_error($out['message'],$out['error']);
	}

	if($out['md_id']) {
		return $out;
	} else {
		return set_error(getLang('msg_not_founded'),801);
	}
}

/* End of file getboard.php */
/* Location: ./module/admin/proc/getboard.php */