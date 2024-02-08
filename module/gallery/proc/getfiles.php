<?php if(!defined('__AFOX__')) exit();

function proc($data) {
	if(empty($data['md_id']) || empty($data['mf_srls'])) return set_error(getLang('error_request'),4303);

	$srls = [];
	$mf_srls = explode(',', $data['mf_srls']);
	foreach ($mf_srls as $value) {
		if($value = trim($value)) $srls[] = $value;
	}
	if(!count($srls)) return set_error(getLang('error_request'),4303);

	return DB::gets(_AF_FILE_TABLE_, ['md_id'=>$data['md_id'],'mf_srl{IN}'=>implode(',', $srls)]);
}

/* End of file getfiles.php */
/* Location: ./module/gallery/proc/getfiles.php */
