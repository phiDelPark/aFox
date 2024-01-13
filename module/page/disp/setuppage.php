<?php

if(!defined('__AFOX__')) exit();

function proc($data){
	global $_MEMBER;
	// 권한 체크
	if(!isAdmin()){
		return set_error(getLang('error_permitted'),4501);
	}

	$result = DB::get(_AF_PAGE_TABLE_, ['md_id'=>$data['id']]);
	$result['tpl'] = 'setup';
	return $result;
}

/* End of file setuppage.php */
/* Location: ./module/page/disp/setuppage.php */
