<?php
if(!defined('__AFOX__')) exit();

function proc($data) {
	global $_MEMBER;
	if(empty($_MEMBER)) return set_error(getLang('error_request'),4303);
	return $result = ['tpl'=>'trash'];
}

/* End of file trash.php */
/* Location: ./module/member/disp/trash.php */