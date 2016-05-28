<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	if(!isset($data['rp_srl'])) return set_error(getLang('msg_invalid_request'),303);

	$cmt = getComment($data['rp_srl']);
	if(!empty($cmt['error'])) {
		return set_error($cmt['message'],$cmt['error']);
	} else if(empty($cmt['rp_srl'])) {
		return set_error(getLang('msg_not_founded'),801);
	}

	$doc = getDocument($cmt['wr_srl']);
	if(!empty($doc['error'])) {
		return set_error($doc['message'],$doc['error']);
	} else if(!isGrant($doc['md_id'], 'view')) {
		return set_error(getLang('msg_not_permitted'),901);
	}

	// JSON은 비밀번호는 암호화 되있지만 그래도 노출 안되게 제거
	unset($cmt['mb_password']);

	return empty($data['with_module_config']) ?  $cmt : array_merge($cmt, getModule($doc['md_id']));
}

/* End of file getcomment.php */
/* Location: ./module/board/proc/getcomment.php */