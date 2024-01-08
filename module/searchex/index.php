<?php
if(!defined('__AFOX__')) exit();

function procSearchexDefault($data) {
	$act = strtolower($data['act']);
	$dir = _AF_MODULES_PATH_ . 'searchex/proc/';
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

function dispSearchexDefault($data) {
	$disp = 'default';
	$dir = _AF_MODULES_PATH_ . 'searchex/disp/';
	$inc_file = $dir . $disp . '.php';

	if (($is=file_exists($inc_file)) && checkProtect('disp.'.$disp)) {
		require_once $inc_file;
		$data = array_merge(getModule('@searchex'), $data);
		return proc($data);
	} else {
		return set_error(
			getLang($is ? 'error_permitted' : 'error_request'),
			$is ? 4501 : 4303
		);
	}
}

/* End of file index.php */
/* Location: ./module/searchex/index.php */
