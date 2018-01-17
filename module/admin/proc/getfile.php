<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	if(!isset($data['mf_srl'])) return set_error(getLang('error_request'),4303);

	$out = DB::get(_AF_FILE_TABLE_, ['mf_srl'=>$data['mf_srl']]);
	if(empty($out)) return set_error(getLang('error_founded'),4201);

	return $out;
}

/* End of file getfile.php */
/* Location: ./module/admin/proc/getfile.php */
