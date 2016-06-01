<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	if(!isset($data['md_id'])) return set_error(getLang('msg_invalid_request'),303);

	$out = getDBItem(_AF_PAGE_TABLE_, ['md_id'=>$data['md_id']]);
	if(!empty($out['error'])) {
		return set_error($out['message'],$out['error']);
	} else if(empty($out['md_id'])) {
		return set_error(getLang('msg_not_founded'),801);
	} else if(!isGrant($out['md_id'], 'view')) {
		return set_error(getLang('msg_not_permitted'),901);
	}

	if(!empty($data['with_file_list'])) {
		$fd = 'mf_srl,mf_name,mf_type,mf_download,mf_description,mf_size,mb_srl,mb_ipaddress';
		$out['files'] = DB::getList('SELECT '.$fd.' FROM '._AF_FILE_TABLE_.' WHERE md_id=\''.$data['md_id'].'\' ORDER BY mf_type');
	}

	return empty($data['with_module_config']) ?  $out : array_merge($out, getModule($out['md_id']));
}

/* End of file getpage.php */
/* Location: ./module/page/proc/getpage.php */