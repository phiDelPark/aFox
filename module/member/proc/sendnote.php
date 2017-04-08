<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	global $_MEMBER;
	if(empty($data['mb_srl']) || empty($_MEMBER['mb_srl'])) return set_error(getLang('msg_invalid_request'),303);
	if(empty($data['nt_content'])) return set_error(getLang('warn_input', ['content']));

	$srl = $data['mb_srl'];
	$sender = empty($_MEMBER) ? 0 : $_MEMBER['mb_srl'];

	if($srl === $sender) return set_error(getLang('msg_not_permitted'),901);

	$nick = empty($_MEMBER) ? getLang('none') : $_MEMBER['mb_nick'];
	$msg = xssClean($data['nt_content']);

	DB::insert(_AF_NOTE_TABLE_, [
		'mb_srl'=>$srl,
		'nt_sender'=>$sender,
		'nt_sender_nick'=>$nick,
		'nt_content'=>$msg,
		'(nt_send_date)'=>'NOW()'
	]);

	return ['error'=>0, 'message'=>getLang('success_sended')];
}

/* End of file deletenote.php */
/* Location: ./module/member/proc/deletenote.php */