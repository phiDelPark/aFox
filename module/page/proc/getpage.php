<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	if(!isset($data['md_id'])) return set_error(getLang('error_request'),4303);

	$page = getDBItem(_AF_PAGE_TABLE_, ['md_id'=>$data['md_id']]);
	if(!empty($page['error'])) {
		return set_error($page['message'],$page['error']);
	} else if(empty($page['md_id'])) {
		return set_error(getLang('error_founded'),4201);
	} else if(!isGrant('view', $page['md_id'])) {
		return set_error(getLang('error_permitted'),4501);
	}

	// 페이지는 모듈 정보도 같이 보냄
	$page = array_merge($page, getModule($page['md_id']));

	// JSON 사용시 모듈설정이 필요할때를 위해 만든옵션
	return  $page;
}

/* End of file getpage.php */
/* Location: ./module/page/proc/getpage.php */
