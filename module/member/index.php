<?php
if(!defined('__AFOX__')) exit();

// 모듈 설정이 없으므로 직접 기본정보 입력
$_CFG['md_title'] = getLang('md_title_' . strtolower($_DATA['member']));
$_CFG['md_description'] = getLang('md_description_' . strtolower($_DATA['member']));

function procMemberDefault($data) {
	$act = strtolower($data['act']);
	$dir = _AF_MODULES_PATH_ . 'member/proc/';
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

function dispMemberDefault($data) {
	$disp = strtolower($data['disp']);
	$dir = _AF_MODULES_PATH_ . 'member/disp/';
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
/* Location: ./module/member/index.php */
