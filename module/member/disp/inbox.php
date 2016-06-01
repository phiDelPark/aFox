<?php
if(!defined('__AFOX__')) exit();

function proc($data) {
	global $_MEMBER;
	if(empty($_MEMBER)) return set_error(getLang('msg_invalid_request'),303);
	return $result = ['tpl'=>'inbox'];
}

/* End of file inbox.php */
/* Location: ./module/member/inbox.php */