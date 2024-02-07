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

	$auto_login = !empty($data['auto_login']);

	global $_CFG;

	$mb = DB::get(_AF_MEMBER_TABLE_, ['mb_id'=>$mb_id]);
	if($ex = DB::error()) return set_error('error', 3);

	$count_key = 'afox_login_try_' . $_SERVER['REMOTE_ADDR'];
	$captcha_key = 'afox_captcha_' . $_SERVER['REMOTE_ADDR'];
	$try_count = (int)get_session($count_key);

	if($_CFG['use_captcha'] == '1' && $try_count > 2) {
		if(empty($data['captcha_code'])) {
			return set_error(getLang('request_input',['captcha_code']), 4001);
		}
		$captcha = get_session($captcha_key);
		if(empty($captcha) || $data['captcha_code'] != $captcha['code']) {
			return set_error(getLang('invalid_value',['captcha_code']), 4001);
		} else {
			set_session($count_key, '');
			set_session($captcha_key, '');
			$try_count = 0;
		}
	}

	if($try_count > 2 || empty($mb['mb_srl']) || !checkPassword($mb_password, $mb['mb_password'])) {
		set_session($count_key, ++$try_count);
		if($try_count > 2) {
			return set_error(getLang('msg_login_overtry'), 4001);
		} else {
			return set_error(getLang('msg_wrong_password'), 4801);
		}
	}

	set_session($count_key, '');
	set_session($captcha_key, '');

	// 암호화된 비밀번호로 교체
	$mb_password = $mb['mb_password'];

	// TODO 차단 탈퇴 인증 체크,

	set_session('AF_LOGIN_ID', $mb['mb_id']);
	// FLASH XSS 공격에 대응하기 위하여 회원의 고유키를 생성해 놓는다.
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

	$setvalues = ['^mb_login'=>'NOW()'];

	if(substr($mb['mb_login'], 0, 10) != date('Y-m-d')) {
		// 포인트 //TODO 포인트 - 시 모자르면 로그인 못하게 할까?
		$point = (int)(empty($_CFG['point_login'])?0:$_CFG['point_login']);
		if($point !== 0) {
			$setvalues['^mb_point'] = 'mb_point'.($point>0?'+':'').$point;
			// 아직 $_MEMBER 에 정보가 없기에 직접 입력 필요
			// setHistory('mb_login', $point, true);
			DB::insert(_AF_HISTORY_TABLE_,
				[
					'mb_srl'=>$mb['mb_srl'],
					'hs_action'=>'::mb_login::',
					'hs_value'=>$point,
					'^hs_regdate'=>'NOW()'
				]
			);
		}
	}

	DB::update(_AF_MEMBER_TABLE_, $setvalues, ['mb_srl'=>$mb['mb_srl']]);

	return ['error'=>'0', 'message'=>getLang('success_login')];
}

/* End of file checklogin.php */
/* Location: ./module/member/proc/checklogin.php */
