<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	if(empty($data['md_id']) || empty($data['mf_target'])) return set_error(getLang('error_request'),4303);

	$files = DB::gets(_AF_FILE_TABLE_, ['md_id'=>$data['md_id'],'mf_target'=>$data['mf_target']], 'mf_type');

	return $files;
}

/* End of file getfilelist.php */
/* Location: ./module/admin/proc/getfilelist.php */
