<?php
if(!defined('__AFOX__')) exit();
@include_once dirname(__FILE__) . '/config.php';

function procLedgerDefault($data) {
	$act = strtolower($data['act']);
	$dir = _AF_MODULES_PATH_ . 'ledger/proc/';
	$inc_file = $dir . $act . '.php';

	if (($is=file_exists($inc_file)) && checkProtect('proc.'.$act)) {
		require_once $inc_file;
		$data = array_merge(getModule('@ledger'), $data);
		return checkProtectData('proc.'.$act, proc($data));
	} else {
		return set_error(
			getLang($is ? 'error_permitted' : 'error_request'),
			$is ? 4501 : 4303
		);
	}
}

function dispLedgerDefault($data) {
	$disp = empty($data['disp']) ? 'default' : strtolower($data['disp']);
	$dir = _AF_MODULES_PATH_ . 'ledger/disp/';
	$inc_file = $dir . $disp . '.php';

	if (($is=file_exists($inc_file)) && checkProtect('disp.'.$disp)) {
		require_once $inc_file;
		$data = array_merge(getModule('@ledger'), $data);
		return proc($data);
	} else {
		return set_error(
			getLang($is ? 'error_permitted' : 'error_request'),
			$is ? 4501 : 4303
		);
	}
}

/* End of file index.php */
/* Location: ./module/ledger/index.php */
