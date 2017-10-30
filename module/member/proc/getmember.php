<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	if (!isset($data['mb_id']) && !isset($data['mb_srl'])) {
		return set_error(getLang('error_request'),4303);
	}

	// mb_id가 넘어오면 정상적인지 체크
	if (!isset($data['mb_id'])) {
		if (!preg_match('/^[a-zA-Z]+\w{2,}$/', $data['mb_id'])) {
			return set_error(getLang('error_request'),4303);
		}
	}

	$out = getMember(isset($data['mb_srl']) ? (int)$data['mb_srl'] : $data['mb_id']);
	if (!empty($out['error'])) {
		return set_error($out['message'],$out['error']);
	}

	// 비밀번호는 암호화 되있지만 그래도 노출 안되게 제거
	unset($out['mb_password']);

	if ($out['mb_id']) {
		$out['mb_icon'] = '';
		$_icon = $out['mb_srl'].'/profile_image.png';
		if (file_exists(_AF_MEMBER_DATA_.$_icon)) {
			$out['mb_icon'] = _AF_URL_ . 'data/member/' . $_icon;
		}
		return $out;
	} else {
		return set_error(getLang('error_founded'),4201);
	}
}

/* End of file getmember.php */
/* Location: ./module/member/proc/getmember.php */
