<?php if(!defined('__AFOX__')) exit();

function proc($data) {
	if(empty($data['md_id']) || empty($data['mf_srl'])) return set_error(getLang('error_request'),4303);
	return DB::get(_AF_FILE_TABLE_, ['md_id'=>$data['md_id'],'mf_srl'=>$data['mf_srl']]);
}

/* End of file getfile.php */
/* Location: ./module/gallery/proc/getfile.php */
