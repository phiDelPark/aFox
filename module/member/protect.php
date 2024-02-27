<?php
if(!defined('__AFOX__')) exit();

$_PROTECT['proc.banlogin']			= ['grant' => 'm'];
$_PROTECT['proc.checklogin']		= ['grant' => '0'];

$_PROTECT['disp.inbox']				= ['grant' => '0'];
$_PROTECT['disp.trash']				= ['grant' => '0'];
$_PROTECT['disp.sendnotebox']		= ['grant' => '0'];

$_PROTECT['disp.signup']			= ['grant' => '0'];
$_PROTECT['disp.signin']			= ['grant' => '0'];
$_PROTECT['disp.signout']			= ['grant' => '0'];

$_PROTECT['proc.deletenotes']		= ['grant' => '0'];
$_PROTECT['proc.deletetrashes']		= ['grant' => '0'];
$_PROTECT['proc.readallnotes']		= ['grant' => '0'];
$_PROTECT['proc.sendnote']			= ['grant' => '0'];
$_PROTECT['proc.sendauthenticode']	= ['grant' => '0'];

$_PROTECT['proc.getcaptcha']		= ['grant' => '0'];
$_PROTECT['proc.updatemember']		= ['grant' => '0'];

$_PROTECT['proc.getmember']			= [
	'grant' => '0',
	'guest' => 'mb_srl,mb_rank,mb_nick,mb_about',
	'member' => 'mb_srl,mb_rank,mb_nick,mb_email,mb_homepage,mb_about,mb_regdate,mb_login',
	'manager' => '*'
];

/* End of file protect.php */
/* Location: ./module/member/protect.php */
