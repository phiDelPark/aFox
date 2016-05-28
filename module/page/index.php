<?php

if(!defined('__AFOX__')) exit();

@include_once _AF_LANGS_PATH_ . 'module_' . _AF_LANG_ . '.php';

function returnUrlMerge($data, $result) {
	if(!isset($result)) $result = [];
	$result['act'] = $data['act'];
	$result['disp'] = $data['disp'];

	if(empty($result['error'])) {
		$result['redirect_url'] = isset($data['success_return_url']) ? $data['success_return_url'] : '';
	} else {
		$result['redirect_url'] = isset($data['error_return_url']) ? $data['error_return_url'] : '';
	}
	return $result;
}

function procPageDefault($data) {
	$inclued_file = dirname(__FILE__) . '/proc/'.strtolower($data['act']).'.php';

	if(file_exists($inclued_file)) {
		require_once $inclued_file;
		return returnUrlMerge($data, proc($data));
	} else {
		return returnUrlMerge($data, set_error(getLang('msg_invalid_request'),303));
	}
}

function dispPageDefault($data) {

	if(!empty($data['id'])) {
		require_once dirname(__FILE__) . '/disp/viewpage.php';
		return proc($data);
	} else {
		set_error(getLang('msg_invalid_request'),303);
	}
}

/* End of file index.php */
/* Location: ./module/page/index.php */