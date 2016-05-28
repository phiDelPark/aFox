<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	// 권한 체크
	if(!isGrant($data['id'], 'view')) {
		return set_error(getLang('msg_not_permitted'),901);
	}
	return getDBItem(_AF_PAGE_TABLE_, ['md_id'=>$data['id']]);
}

/* End of file viewpage.php */
/* Location: ./module/page/disp/viewpage.php */