<?php if(!defined('__AFOX__')) exit();
include_once _AF_INIT_PATH_ . 'patterns.php';

function procGalleryDefault($data) {
	$act = strtolower($data['act']);
	$dir = _AF_MODULES_PATH_ . 'gallery/proc/';
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

function dispGalleryDefault($data) {
	if (!($disp = strtolower(@$data['disp']))) {
		$disp = empty($data['srl']) ? 'list' : 'view';
	}

	$dir = _AF_MODULES_PATH_ . 'gallery/disp/';
	$inc_file = $dir . $disp . '.php';

	if (($is=file_exists($inc_file)) && checkProtect('disp.'.$disp)) {
		require_once $inc_file;
		return proc($data);
	} else {
		return set_error(
			getLang($is ? 'error_permitted' : 'error_request'),
			$is ? 4501 : 4303
		);
	}
}

/* End of file index.php */
/* Location: ./module/gallery/index.php */
