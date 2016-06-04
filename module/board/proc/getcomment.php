<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	if(!isset($data['rp_srl'])) return set_error(getLang('msg_invalid_request'),303);
	global $_MEMBER;

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

	// 비밀글이면
	if($cmt['rp_secret'] == '1' && !isManager($doc['md_id'])) {
		// 권한 체크
		if(empty($_MEMBER) || empty($cmt['mb_srl'])) {
			if(empty($data['mb_password'])) {
				return set_error(getLang('warn_input', ['password']), 3);
			}
			if (empty($cmt['mb_password']) || !verifyEncrypt($data['mb_password'], $cmt['mb_password'])) {
				return set_error(getLang('msg_not_permitted'), 901);
			}
		} else if($_MEMBER['mb_srl'] != $cmt['mb_srl']) {
			return set_error(getLang('msg_not_permitted'), 901);
		}
	}

	// 비밀번호는 암호화 되있지만 그래도 노출 안되게 제거
	unset($cmt['mb_password']);

	// JSON 사용시 모듈설정이 필요할때를 위해 만든옵션
	return empty($data['with_module_config']) ? $cmt : array_merge($cmt, getModule($doc['md_id']));
}

/* End of file getcomment.php */
/* Location: ./module/board/proc/getcomment.php */