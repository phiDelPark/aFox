<?php

if(!defined('__AFOX__')) exit();

@include_once _AF_LANGS_PATH_ . 'admin_' . _AF_LANG_ . '.php';

// 관리자만 접근 가능
if(empty($_MEMBER) || $_MEMBER['mb_rank'] != 's') {
	goUrl(_AF_URL_, getLang('error_admin'));
	exit('not admin');
}

function returnUrlMerge($data, $result) {
	if(!isset($result)) $result = [];
	$result['act'] = $data['act'];
	$result['disp'] = $data['disp'];
	$url_key = empty($result['error'])?'success_return_url':'error_return_url';
	$result['redirect_url'] = isset($data[$url_key])?urldecode($data[$url_key]):'';
	return $result;
}

function procAdminDefault($data) {
	$include_file = dirname(__FILE__) . '/proc/'.strtolower($data['act']).'.php';

	if(file_exists($include_file)) {
		require_once $include_file;
		return returnUrlMerge($data, proc($data));
	} else {
		return returnUrlMerge($data, set_error(getLang('error_request'),4303));
	}
}

function dispAdminDefault($data) {

}

/* End of file index.php */
/* Location: ./module/admin/index.php */