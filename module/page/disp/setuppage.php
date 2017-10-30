<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	// 권한 체크
	if(!isManager($data['id'])) {
		return set_error(getLang($data['id']),901);
	}

	$result = getDBItem(_AF_PAGE_TABLE_, ['md_id'=>$data['id']]);
	$result['tpl'] = 'setup';
	return $result;
}

/* End of file setuppage.php */
/* Location: ./module/page/disp/setuppage.php */
