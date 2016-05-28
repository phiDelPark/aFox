<?php

if(!defined('__AFOX__')) exit();

@include_once _AF_LANGS_PATH_ . 'module_' . _AF_LANG_ . '.php';
@include_once dirname(__FILE__) . '/funcs.php';

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

function procBoardDefault($data) {
	$inclued_file = dirname(__FILE__) . '/proc/'.strtolower($data['act']).'.php';

	if(file_exists($inclued_file)) {
		require_once $inclued_file;
		return returnUrlMerge($data, proc($data));
	} else {
		return returnUrlMerge($data, set_error(getLang('msg_invalid_request'),303));
	}
}

function dispBoardDefault($data) {
	$dir = dirname(__FILE__) . '/disp/';
	$result = [];

	if($data['disp']) {
		$inclued_file = $dir . strtolower($data['disp']).'.php';
		if(file_exists($inclued_file)) {
			require_once $inclued_file;
			$result = proc($data);
		} else {
			return set_error(getLang('msg_invalid_request'),303);
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
			return set_error(getLang('msg_invalid_request'),303);
		}
	}

	addCSS(_AF_URL_ . 'module/board/tpl/board.css');
	addJS(_AF_URL_ . 'module/board/tpl/board.js');
	return $result;
}

/* End of file index.php */
/* Location: ./module/board/index.php */