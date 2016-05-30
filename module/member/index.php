<?php

if(!defined('__AFOX__')) exit();

@include_once _AF_LANGS_PATH_ . 'module_' . _AF_LANG_ . '.php';

function returnUrlMerge($data, $result) {
	if(!isset($result)) $result = [];
	$result['act'] = $data['act'];
	$result['disp'] = $data['disp'];

	if(empty($result['error'])) {
		$result['redirect_url'] = isset($data['success_return_url']) ? urldecode($data['success_return_url']) : '';
	} else {
		$result['redirect_url'] = isset($data['error_return_url']) ? urldecode($data['error_return_url']) : '';
	}
	return $result;
}

function procMemberDefault($data) {
	$inclued_file = dirname(__FILE__) . '/proc/'.strtolower($data['act']).'.php';

	if(file_exists($inclued_file)) {
		require_once $inclued_file;
		return returnUrlMerge($data, proc($data));
	} else {
		return returnUrlMerge($data, set_error(getLang('msg_invalid_request'),303));
	}
}

function dispMemberDefault($data) {
	$dir = dirname(__FILE__) . '/disp/';

	if($data['disp']) {
		$inclued_file = $dir . strtolower($data['disp']).'.php';
		if(file_exists($inclued_file)) {
			require_once $inclued_file;
			$result = proc($data);
		} else {
			return set_error(getLang('msg_invalid_request'),303);
		}
	} else {
		return set_error(getLang('msg_invalid_request'),303);
	}

	return $result;
}

/* End of file index.php */
/* Location: ./module/member/index.php */