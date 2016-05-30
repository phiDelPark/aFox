<?php

if(!defined('__AFOX__')) exit();

@include_once _AF_LANGS_PATH_ . 'module_' . _AF_LANG_ . '.php';

// 관리자만 접근 가능
if(empty($_MEMBER) || $_MEMBER['mb_rank'] != 's') {
	goUrl(_AF_URL_, getLang('msg_not_admin'));
	exit('not admin');
}

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

function procAdminDefault($data) {
	$inclued_file = dirname(__FILE__) . '/proc/'.strtolower($data['act']).'.php';

	if(file_exists($inclued_file)) {
		require_once $inclued_file;
		return returnUrlMerge($data, proc($data));
	} else {
		return returnUrlMerge($data, set_error(getLang('msg_invalid_request'),303));
	}
}

function dispAdminDefault($data) {

}

/* End of file index.php */
/* Location: ./module/admin/index.php */