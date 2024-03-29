<?php if(!defined('__AFOX__')) exit();
include_once _AF_INIT_PATH_ . 'patterns.php';

// 트리거 확인및 설치
installModuleTrigger('board', 0);
@include_once dirname(__FILE__) . '/funcs.php';

// 모듈 설정 확장변수 unserialize
$_CFG['md_extra'] = empty($_CFG['md_extra']) ? [] : unserialize($_CFG['md_extra']);

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
	if (!($disp = strtolower(@$data['disp']))) {
		$disp = empty($data['srl']) ? 'list' : 'view';
	}

	$dir = _AF_MODULES_PATH_ . 'board/disp/';
	$inc_file = $dir . $disp . '.php';

	if (($is=file_exists($inc_file)) && checkProtect('disp.'.$disp)) {
		require_once $inc_file;
		$result = proc($data);
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
