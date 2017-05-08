<?php

if(!defined('__AFOX__')) exit();

@include_once dirname(__FILE__) . '/lang/' . _AF_LANG_ . '.php';
@include_once dirname(__FILE__) . '/funcs.php';

function returnUrlMerge($data, $result) {
	if(!isset($result)) $result = [];
	$result['act'] = $data['act'];
	$result['disp'] = $data['disp'];
	$url_key = empty($result['error'])?'success_return_url':'error_return_url';
	$result['redirect_url'] = isset($data[$url_key])?urldecode($data[$url_key]):'';
	return $result;
}

function procBoardDefault($data) {
	$include_file = _AF_MODULES_PATH_ . 'board/proc/'.strtolower($data['act']).'.php';

	if(file_exists($include_file)) {
		require_once $include_file;
		return returnUrlMerge($data, proc($data));
	} else {
		return returnUrlMerge($data, set_error(getLang('error_request'),4303));
	}
}

function dispBoardDefault($data) {
	$dir = _AF_MODULES_PATH_ . 'board/disp/';
	$result = [];

	if($data['disp']) {
		$include_file = $dir . strtolower($data['disp']).'.php';
		if(file_exists($include_file)) {
			require_once $include_file;
			$result = proc($data);
		} else {
			return set_error(getLang('error_request'),4303);
		}
	} else {
		if(!empty($data['srl'])) {
			require_once $dir . 'viewdocument.php';
			$result = proc($data);
			$result['tpl'] = 'view';
		} else if(!empty($data['id'])) {
			require_once $dir . 'documentlist.php';
			$result = proc($data);
			$result['tpl'] = 'list';
		} else {
			return set_error(getLang('error_request'),4303);
		}
	}

	addCSS(_AF_URL_ . 'module/board/tpl/board'. (__DEBUG__ ? '.css?' . _AF_SERVER_TIME_ : '.min.css'));
	addJS(_AF_URL_ . 'module/board/tpl/board'. (__DEBUG__ ? '.js?' . _AF_SERVER_TIME_ : '.min.js'));
	return $result;
}

/* End of file index.php */
/* Location: ./module/board/index.php */