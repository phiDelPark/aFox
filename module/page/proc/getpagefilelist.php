<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	if(!isset($data['md_id'])) return set_error(getLang('error_request'),4303);

	$page = getDBItem(_AF_PAGE_TABLE_, ['md_id'=>$data['md_id']], 'md_id');
	if(!empty($page['error'])) {
		return set_error($page['message'],$page['error']);
	} else if(empty($page['md_id'])) {
		return set_error(getLang('error_founded'),4201);
	} else if(!isGrant('view', $page['md_id'])) {
		return set_error(getLang('error_permitted'),4501);
	}

	$files = getDBList(_AF_FILE_TABLE_, ['md_id'=>$page['md_id'],'mf_target'=>1], 'mf_type');

	// 요청값이 있으면 요청값만 보냄
	$response_tags = $data['response_tags'];
	if(!empty($response_tags) && count($response_tags) > 0) {
		$response_vals = ['md_id'=>$page['md_id'],'pg_srl'=>$page['pg_srl']];
		foreach ($response_tags as $value) {
			$response_vals[$value] = $files[$value];
		}
		$files = $response_vals;
	}

	// JSON 사용시 모듈설정이 필요할때를 위해 만든옵션
	return $files;
}

/* End of file getpagefilelist.php */
/* Location: ./module/board/proc/getpagefilelist.php */
