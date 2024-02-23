<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	global $_MEMBER;

	// 로그인중이 아니면 에러
	if(empty($_MEMBER['mb_srl'])) return set_error(getLang('error_request'),4303);

	DB::update(_AF_NOTE_TABLE_, ['nt_read_date(=)'=>'NOW()'], ['mb_srl'=>$_MEMBER['mb_srl'],'nt_read_date'=>'0000-00-00 00:00:00']);

}
/* End of file readallnotes.php */
/* Location: ./module/member/proc/readallnotes.php */
