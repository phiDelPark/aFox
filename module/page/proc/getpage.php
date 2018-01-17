<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	if(!isset($data['md_id'])) return set_error(getLang('error_request'),4303);

	$field = '*';
	$default_field = 'md_id,pg_srl';

	// 요청값이 있으면 요청값만 보냄
	$response_tags = $data['response_tags'];
	if(!empty($response_tags) && count($response_tags) > 0) {
		$field = $default_field.','.implode(',', $response_tags);
	}

	$page = DB::get(_AF_PAGE_TABLE_, $field, ['md_id'=>$data['md_id']]);
	if(empty($page['md_id'])) {
		return set_error(getLang('error_founded'),4201);
	} else if(!isGrant('view', $page['md_id'])) {
		return set_error(getLang('error_permitted'),4501);
	}

	// 모듈 정보도 같이 보냄
	if(empty($response_tags)) $page = array_merge($page, getModule($page['md_id']));

	// JSON 사용시 모듈설정이 필요할때를 위해 만든옵션
	return  $page;
}

/* End of file getpage.php */
/* Location: ./module/page/proc/getpage.php */
