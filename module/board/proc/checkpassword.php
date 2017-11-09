<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	$key = empty($data['rp_srl']) ? 'wr_srl' : 'rp_srl';
	if(empty($data[$key])) return set_error(getLang('error_request'),4303);

	global $_MEMBER;

	if($key == 'rp_srl') {
		$ret = getComment($data['rp_srl'],'wr_srl,mb_srl,mb_password');
		$tmp = getDocument((int)$ret['wr_srl'],'md_id');
		$ret['md_id'] = $tmp['md_id'];
	} else {
		$ret = getDocument($data['wr_srl'],'md_id,mb_srl,mb_password');
	}

	if(!empty($ret['error'])) return set_error($ret['message'],$ret['error']);

	if(!isManager($ret['md_id'])) {
		// 권한 체크
		if(empty($_MEMBER) || empty($ret['mb_srl'])) {
			if(empty($data['mb_password'])) {
				return set_error(getLang('request_input', ['password']), 1);
			}
			if (empty($ret['mb_password']) || !checkPassword($data['mb_password'], $ret['mb_password'])) {
				return set_error(getLang('error_password'),4801);
			}
		} else if($_MEMBER['mb_srl'] != $ret['mb_srl']) {
			return set_error(getLang('error_permitted'),4501);
		}
	}

	$r = ['md_id'=>$ret['md_id'],$key=>$data[$key]];
	if($key == 'rp_srl') $r['wr_srl'] = $ret['wr_srl'];

	return $r;
}

/* End of file checkpassword.php */
/* Location: ./module/board/proc/checkpassword.php */
