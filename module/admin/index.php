<?php if(!defined('__AFOX__')) exit();
include_once _AF_INIT_PATH_ . 'patterns.php';

if(empty($_MEMBER) || ($_MEMBER['mb_rank'] != 's' && $_MEMBER['mb_rank'] != 'm')) {
	goUrl(_AF_URL_, getLang('error_permitted'));
}

//destroy session if you are not an administrator
$admin_key = md5($_MEMBER['mb_regdate'].$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']);
if (get_session('AF_LOGIN_KEY') !== $admin_key) {
	session_destroy();
	// TODO 관리자에게 쪽지 보낸다.
	goUrl(_AF_URL_, getLang('error_permitted'));
}
if($_POST['disp'] == 'member'){
	@include_once _AF_MODULES_PATH_ . 'member/lang/' . _AF_LANG_ . '.php';
} else if(!empty($_POST['md_id'])){
	@include_once _AF_MODULES_PATH_ . $_POST['md_id'] . '/lang/' . _AF_LANG_ . '.php';
}else{
	@include_once _AF_MODULES_PATH_ . 'admin/lang/' . _AF_LANG_ . '.php';
}
addJSLang(['menu','addon','theme','board','page','document','comment','file','trash_bin','confirm_empty','confirm_delete','warn_selected','prompt_move_board']);

function setDataListInfo($data, $page, $count, $total) {
	$r = [];
	$r['data'] = $data;
	$r['total_count'] = $total;
	$r['total_page'] = $r['end_page'] = ceil($r['total_count'] / $count);
	$r['start_page'] = ($page - 1 - (($page - 1) % 10)) + 1;
	if ($r['end_page'] > ($r['start_page'] + 9)) $r['end_page'] = $r['start_page'] + 9;
	$r['current_page'] = $page;
	return $r;
}

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
