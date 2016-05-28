<?php

if(!defined('__AFOX__')) exit();

function proc($data) {

	session_unset();
	session_destroy();
	session_start();

	set_cookie('ck_mb_id', '', -1);
	set_cookie('ck_auto', '', -1);

	return ['error'=>'0', 'message'=>getLang('success_logout')];
}

/* End of file logout.php */
/* Location: ./module/member/proc/logout.php */