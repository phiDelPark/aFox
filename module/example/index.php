<?php

if(!defined('__AFOX__')) exit();

@include_once dirname(__FILE__) . '/lang/' . _AF_LANG_ . '.php';

function returnUrlMerge($data, $result) {
	if(!isset($result)) $result = [];
	$result['act'] = $data['act'];
	$result['disp'] = $data['disp'];
	$url_key = empty($result['error'])?'success_return_url':'error_return_url';
	$result['redirect_url'] = isset($data[$url_key])?urldecode($data[$url_key]):'';
	return $result;
}

function procExampleDefault($data) {
	$include_file = dirname(__FILE__) . '/proc/'.strtolower($data['act']).'.php';

	if(file_exists($include_file)) {
		require_once $include_file;
		return returnUrlMerge($data, proc($data));
	} else {
		return returnUrlMerge($data, set_error(getLang('error_request'),4303));
	}
}

function dispExampleDefault($data) {

	if(!empty($data['id'])) {
		require_once dirname(__FILE__) . '/disp/viewpage.php';
		return proc($data);
	} else {
		set_error(getLang('error_request'),4303);
	}
}

/* End of file index.php */
/* Location: ./module/example/index.php */