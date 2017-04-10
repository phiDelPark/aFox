<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	if(!isset($data['rp_srl'])) return set_error(getLang('error_request'),4303);
	global $_MEMBER;

	$cmt = getComment($data['rp_srl']);
	if(!empty($cmt['error'])) {
		return set_error($cmt['message'],$cmt['error']);
	} else if(empty($cmt['rp_srl'])) {
		return set_error(getLang('error_founded'),4201);
	}

	$doc = getDocument($cmt['wr_srl']);
	if(!empty($doc['error'])) {
		return set_error($doc['message'],$doc['error']);
	} else if(!isGrant($doc['md_id'], 'view')) {
		return set_error(getLang('error_permit'),4501);
	}

	// 비밀글이면
	if($cmt['rp_secret'] == '1' && !isManager($doc['md_id'])) {
		// 권한 체크
		if(empty($_MEMBER) || empty($cmt['mb_srl'])) {
			if(empty($data['mb_password'])) {
				return set_error(getLang('request_input', ['password']));
			}
			if (empty($cmt['mb_password']) || !verifyEncrypt($data['mb_password'], $cmt['mb_password'])) {
				return set_error(getLang('error_permit'),4501);
			}
		} else if($_MEMBER['mb_srl'] != $cmt['mb_srl']) {
			return set_error(getLang('error_permit'),4501);
		}
	}

	$responses = $data['response_tags'];
	if(!empty($responses) && count($responses) > 0) {
		// 요청값이 mb_password이면 권한만 체크
		if(count($responses) === 1 && $responses[0] === 'mb_password') {
			return ['rp_srl', $data['rp_srl']];
		}
	}

	// 비밀번호는 암호화 되있지만 그래도 노출 안되게 제거
	unset($cmt['mb_password']);

	// 관리자 모드에서 사용하기 위해 필요한 정보 같이 보내기... (관리자만)
	if(!empty($data['with_module_config']) && isManager($doc['md_id'])) {
		$cmt['wr_title'] = $doc['wr_title'];
		$cmt = array_merge($cmt, getModule($doc['md_id']));
	}

	// JSON 사용시 모듈설정이 필요할때를 위해 만든옵션
	return $cmt;
}

/* End of file getcomment.php */
/* Location: ./module/board/proc/getcomment.php */