<?php
if(!defined('__AFOX__')) exit();

function proc($data) {
	global $_MEMBER;
	//if(!empty($_MEMBER['mb_srl'])) return  ['error'=>4303, 'message'=>getLang('error_request')];
	return ['tpl'=>'signin'];
}

/* End of file signin.php */
/* Location: ./module/member/disp/signin.php */