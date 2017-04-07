<?php

if(!defined('__AFOX__')) exit();

@include_once _AF_LANGS_PATH_ . 'module_' . _AF_LANG_ . '.php';

function returnUrlMerge($data, $result) {
	if(!isset($result)) $result = [];
	$result['act'] = $data['act'];
	$result['disp'] = $data['disp'];
	$url_key = empty($result['error'])?'success_return_url':'error_return_url';
	$result['redirect_url'] = isset($data[$url_key])?urldecode($data[$url_key]):'';
	return $result;
}

function procPageDefault($data) {
	$include_file = dirname(__FILE__) . '/proc/'.strtolower($data['act']).'.php';

	if(file_exists($include_file)) {
		require_once $include_file;
		return returnUrlMerge($data, proc($data));
	} else {
		return returnUrlMerge($data, set_error(getLang('msg_invalid_request'),303));
	}
}

function dispPageDefault($data) {
	$dir = dirname(__FILE__) . '/disp/';
	$result = [];

	if($data['disp']) {
		$include_file = $dir . strtolower($data['disp']) . '.php';
		if(strtolower($data['disp'])=='setuppage' && file_exists($include_file)) {
			require_once $include_file;
			$result = proc($data);
			$result['tpl'] = 'setup';
			addCSS(_AF_URL_ . 'module/page/tpl/page.min.css');
			addJS(_AF_URL_ . 'module/page/tpl/page.min.js');
			return $result;
		} else {
			return set_error(getLang('msg_invalid_request'),303);
		}
	} else {
		if(!empty($data['id'])) {
			require_once dirname(__FILE__) . '/disp/viewpage.php';
			return proc($data);
		} else {
			return set_error(getLang('msg_invalid_request'),303);
		}
	}
}

/* End of file index.php */
/* Location: ./module/page/index.php */