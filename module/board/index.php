<?php
if(!defined('__AFOX__')) exit();

@include_once dirname(__FILE__) . '/lang/' . _AF_LANG_ . '.php';
@include_once dirname(__FILE__) . '/funcs.php';

function procBoardDefault($data) {
	$act = strtolower($data['act']);
	$dir = _AF_MODULES_PATH_ . 'board/proc/';
	$inc_file = $dir . $act . '.php';

	if (($is=file_exists($inc_file)) && checkProtect('proc.'.$act)) {
		require_once $inc_file;
		return checkProtectData('proc.'.$act, proc($data));
	} else {
		return set_error(
			getLang($is ? 'error_permitted' : 'error_request'),
			$is ? 4501 : 4303
		);
	}
}

function dispBoardDefault($data) {
	$tpl = '';
	$act = strtolower($data['disp']);

	if (empty($act)) {
		$act = empty($data['srl']) ? 'documentlist' : 'viewdocument';
		$tpl = empty($data['srl']) ? 'list' : 'view';
		if (empty($data['id']) && empty($data['srl'])) $act = '...error';
	}

	$dir = _AF_MODULES_PATH_ . 'board/disp/';
	$inc_file = $dir . $act . '.php';

	if (($is=file_exists($inc_file)) && checkProtect('disp.'.$act)) {
		require_once $inc_file;
		$result = proc($data);
		if (!empty($tpl)) $result['tpl'] = $tpl;
		return $result;
	} else {
		return set_error(
			getLang($is ? 'error_permitted' : 'error_request'),
			$is ? 4501 : 4303
		);
	}
}

/* End of file index.php */
/* Location: ./module/board/index.php */
