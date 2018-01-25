<?php
if(!defined('__AFOX__')) exit();
@include_once dirname(__FILE__) . '/lang/' . _AF_LANG_ . '.php';
@include_once dirname(__FILE__) . '/funcs.php';

// 트리거 확인및 설치
if(DB::count(_AF_TRIGGER_TABLE_,['tg_key'=>'M','tg_id'=>'board','use_pc'=>1,'use_mobile'=>1])<1){
	DB::delete(_AF_TRIGGER_TABLE_,['tg_id'=>'board']);
	DB::insert(_AF_TRIGGER_TABLE_,['tg_key'=>'M','tg_id'=>'board','use_pc'=>1,'use_mobile'=>1]);
}

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
	$act = strtolower($data['disp']);

	if (empty($act)) {
		$act = empty($data['srl']) ? 'documentlist' : 'viewdocument';
		if (empty($data['id']) && empty($data['srl'])) $act = '...error';
	}

	$dir = _AF_MODULES_PATH_ . 'board/disp/';
	$inc_file = $dir . $act . '.php';

	if (($is=file_exists($inc_file)) && checkProtect('disp.'.$act)) {
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
