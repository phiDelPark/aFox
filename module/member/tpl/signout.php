<?php
	if(!defined('__AFOX__')) exit();

	session_unset();
	session_destroy();
	session_start();

	set_cookie('AF_LOGIN_ID', '', -1);
	set_cookie('AF_AUTO_LOGIN', '', -1);

	goUrl(getUrl(''));
?>
