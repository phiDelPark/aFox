<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	$mb_id          = trim($data['mb_id']);
	$mb_password    = trim($data['mb_password']);

	if(!$mb_id || !$mb_password) {
		return set_error(getLang('request_input',[$mb_id?'password':'id']),1);
	}

	if(!preg_match('/^[a-zA-Z]+\w{2,}$/', $mb_id)) {
		return set_error(getLang('invalid_value',['id']),2001);
	}

	$auto_login = isset($data['auto_login']) && $data['auto_login'] == 1;

	global $_CFG;

	$sql = 'SELECT * FROM '._AF_MEMBER_TABLE_.' WHERE mb_id= :1';
	$mb = DB::get($sql, [$mb_id]);
	if($ex = DB::error()) return set_error($ex->getMessage(),$ex->getCode());

	if(empty($mb['mb_srl']) || !checkPassword($mb_password, $mb['mb_password'])) {
		return set_error(getLang('msg_wrong_password'),4601);
	}

	// 정상적인 접근이면 암호화된 비밀번호로 교체
	$mb_password    = $mb['mb_password'];

	// TODO 차단 탈퇴 인증 체크,

	set_session('AF_LOGIN_ID', $mb['mb_id']);
	// FLASH XSS 공격에 대응하기 위하여 회원의 고유키를 생성해 놓는다. 관리자에서 검사함 - 110106
	set_session('AF_LOGIN_KEY', md5($mb['mb_regdate'] . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']));

	// 최고 관리자는 자동 로그인 안함
	if($mb['mb_rank'] !== 's' && $auto_login) {
		$key = md5($_SERVER['SERVER_ADDR'] . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . $mb_password);
		set_cookie('AF_LOGIN_ID', $mb_id, 86400 * 31);
		set_cookie('AF_AUTO_LOGIN', $key, 86400 * 31);
	} else {
		set_cookie('AF_LOGIN_ID', '', -1);
		set_cookie('AF_AUTO_LOGIN', '', -1);
	}

	$setvalues = ['(mb_login)'=>'NOW()'];

	if(substr($mb['mb_login'], 0, 10) != date('Y-m-d')) {
		// 포인트
		if(!empty($_CFG['point_login'])) {
			$point = (int) $_CFG['point_login'];
			$setvalues['(mb_point)'] = 'mb_point'.($point>0?'+':'').$point;
		}
		setHistoryAction('mb_login', $mb['mb_srl'], true);
	}

	DB::update(_AF_MEMBER_TABLE_, $setvalues, ['mb_srl'=>$mb['mb_srl']]);

	return ['error'=>'0', 'message'=>getLang('success_login')];
}

/* End of file logincheck.php */
/* Location: ./module/member/proc/logincheck.php */
