<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	if(!isset($data['mb_id']) && !isset($data['mb_srl'])) {
		return set_error(getLang('msg_invalid_request'),303);
	}

	if(!isset($data['mb_srl'])) {
		if(!preg_match('/^[a-zA-Z]+[a-zA-Z0-9_]{2,}/', $data['mb_id'])) {
			return set_error(getLang(getLang('invalid_value'), getLang('id')),701);
		}
	}

	$out = getMember(isset($data['mb_srl']) ? $data['mb_srl'] : $data['mb_id']);
	if(!empty($out['error'])) {
		return set_error($out['message'],$out['error']);
	}

	$out['mb_icon'] = '';
	$_icon = $out['mb_srl'].'/profile_image.png';
	if(file_exists(_AF_MEMBER_DATA_.$_icon)) {
		$out['mb_icon'] = _AF_URL_ . 'data/member/' . $_icon;
	}

	if($out['mb_id']) {
		return $out;
	} else {
		return set_error(getLang('msg_not_founded'),801);
	}
}

/* End of file getmember.php */
/* Location: ./module/member/proc/getmember.php */