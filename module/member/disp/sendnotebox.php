<?php
if(!defined('__AFOX__')) exit();

function proc($data) {
	global $_CFG;
	global $_MEMBER;

	// TODO 쪽지 차단 기능 추가하자
	if(empty($_MEMBER)) {
		return set_error(getLang('error_request'),4303);
	}

	$_item = DB::get(_AF_NOTE_TABLE_, ['nt_srl'=>$data['srl']]);
	if(empty($_item['nt_sender'])) return set_error(getLang('error_founded'),4201);

	return $result = ['nt_sender'=>$_item['nt_sender'],'nt_sender_nick'=>$_item['nt_sender_nick'],'tpl'=>'sendnotebox'];
}

/* End of file sendnotebox.php */
/* Location: ./module/member/disp/sendnotebox.php */
