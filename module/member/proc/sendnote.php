<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	global $_MEMBER;
	if(empty($data['mb_srl']) || empty($_MEMBER['mb_srl'])) return set_error(getLang('error_request'),4303);
	if(empty($data['nt_content'])) return set_error(getLang('request_input', ['content']),1);

	$srl = $data['mb_srl'];
	$nick = $_MEMBER['mb_nick'];
	$sender = $_MEMBER['mb_srl'];

	if($srl === $sender) return set_error(getLang('error_permitted'),4501);

	$msg = strip_tags($data['nt_content']); // xssClean()

	DB::insert(_AF_NOTE_TABLE_, [
		'mb_srl'=>$srl,
		'nt_sender'=>$sender,
		'nt_sender_nick'=>$nick,
		'nt_content'=>$msg,
		'^nt_send_date'=>'NOW()'
	]);

	return ['error'=>0, 'message'=>getLang('success_sended')];
}

/* End of file sendnote.php */
/* Location: ./module/member/proc/sendnote.php */
