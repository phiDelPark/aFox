<?php
if(!defined('__AFOX__')) exit();

function proc($data) {
	global $_CFG;
	global $_MEMBER;

	if(empty($_MEMBER)&&empty($_CFG['use_signup'])) {
		return set_error(getLang('not_use_signup'),3);
	}
	return $result = ['tpl'=>'signup'];
}

/* End of file signup.php */
/* Location: ./module/member/signup.php */