<?php

if(!defined('__AFOX__')) exit();

function proc($data) {

	$d = [];
	$d['ids'] = empty($data['md_ids'])?'':serialize($data['md_ids']);
	$d['count'] = empty($data['md_list_count']) ? 20 : abs($data['md_list_count']);

	return setCustomMoudleConfig(_CUSTOM_MOUDLE_GUID_, $d);
}

/* End of file setupmodule.php */
/* Location: ./module/searchex/proc/setupmodule.php */
