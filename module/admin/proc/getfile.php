<?php

if(!defined('__AFOX__')) exit();

function proc($data) {

	if(!isset($data['mf_srl'])) return set_error(getLang('msg_invalid_request'),303);

	$out = getDBItem(_AF_FILE_TABLE_, ['mf_srl'=>$data['mf_srl']]);
	if(!empty($out['error'])) {
		return set_error($out['message'],$out['error']);
	}

	if($out['mf_srl']) {
		return $out;
	} else {
		return set_error(getLang('msg_not_founded'),801);
	}
}

/* End of file getfile.php */
/* Location: ./module/admin/proc/getfile.php */