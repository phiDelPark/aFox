<?php
if(!defined('__AFOX__')) exit();

if(empty($_MEMBER) || ($_MEMBER['mb_rank'] != 's' && $_MEMBER['mb_rank'] != 'm')) {
	goUrl(_AF_URL_, getLang('error_permitted'));
}

// 관리자의 아이피, 브라우저와 다르다면 세션을 끊고 관리자에게 메일을 보낸다.
$admin_key = md5($_MEMBER['mb_regdate'].$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']);
if (get_session('AF_LOGIN_KEY') !== $admin_key) {
	session_destroy();
	// TODO 관리자에게 쪽지 보낸다.
	goUrl(_AF_URL_, getLang('error_permitted'));
}

if(!empty($_DATA['disp']) && !empty($_DATA['mid'])){
	@include_once _AF_MODULES_PATH_ . $_DATA['mid'] . '/lang/' . _AF_LANG_ . '.php';
}else{
	@include_once _AF_MODULES_PATH_ . $_DATA['disp'] . '/lang/' . _AF_LANG_ . '.php';
}
addJSLang(['menu','addon','theme','board','page','document','comment','file','recycle_bin','confirm_empty','confirm_select_move','confirm_select_empty','confirm_select_delete','warning_selected','confirm_select_trash','confirm_select_combine','prompt_move_board_id','standard_point']);

function procAdminDefault($data) {
	$act = strtolower($data['act']);
	$dir = _AF_MODULES_PATH_ . 'admin/proc/';
	$inc_file = $dir . $act . '.php';

	if (($is=file_exists($inc_file)) && checkProtect('proc.'.$act)) {
		require_once $inc_file;
		return proc($data);
	} else {
		return set_error(
			getLang($is ? 'error_permitted' : 'error_request'),
			$is ? 4501 : 4303
		);
	}
}

function dispAdminDefault($data) {

}

/* End of file index.php */
/* Location: ./module/admin/index.php */
