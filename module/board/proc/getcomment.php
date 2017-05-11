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

	$doc = getDocument($cmt['wr_srl'], 'md_id,wr_srl,wr_title');
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
			if (empty($cmt['mb_password']) || !checkPassword($data['mb_password'], $cmt['mb_password'])) {
				return set_error(getLang('error_permit'),4501);
			}
		} else if($_MEMBER['mb_srl'] != $cmt['mb_srl']) {
			return set_error(getLang('error_permit'),4501);
		}
	}

	// 요청값이 있으면 요청값만 보냄
	$response_tags = $data['response_tags'];
	if(!empty($response_tags) && count($response_tags) > 0) {
		$response_vals = ['md_id'=>$doc['md_id'],'wr_srl'=>$doc['wr_srl'],'rp_srl'=>$cmt['rp_srl']];
		foreach ($response_tags as $value) {
			$response_vals[$value] = $cmt[$value];
		}
		$cmt = $response_vals;
	}

	// 비밀번호는 암호화 되있지만 그래도 노출 안되게 제거
	unset($cmt['mb_password']);
	//if($hide_ipaddress) unset($cmt['mb_ipaddress']);

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