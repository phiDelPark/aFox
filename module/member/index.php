<?php
if(!defined('__AFOX__')) exit();
@include_once dirname(__FILE__) . '/lang/' . _AF_LANG_ . '.php';

function _checkGrant($key) {
	global $_PROTECT;
	$grant = $_PROTECT[$key]['grant'];
	if (is_null($grant)) {
		return set_error(getLang('error_request'),4303);
	} else if (!isGrant($grant)) {
		return set_error(getLang('error_permitted'), 4501);
	}
}

function _checkProtect($key, $data) {
	global $_PROTECT;
	global $_MEMBER;
	$result = [];
	$grade = empty($_MEMBER['mb_grade']) ? 'guest' : $_MEMBER['mb_grade'];
	//자기 자신 제외
	if (!empty($_MEMBER['mb_srl']) && $_MEMBER['mb_srl'] = $data['mb_srl']) {
		$_PROTECT[$key][$grade] = '*';
	}
	if (is_null($_PROTECT[$key][$grade]) || $_PROTECT[$key][$grade] === '*') {
		$result = $data;
	} else {
		$a = explode(',', str_replace(' ', '', $_PROTECT[$key][$grade]));
		foreach ($a as $val) $result[$val] = $data[$val];
	}
	return $result;
}

function _returnUrlMerge($data, $result) {
	if(!isset($result)) $result = [];
	$result['act'] = $data['act'];
	$result['disp'] = $data['disp'];
	$url_key = empty($result['error'])?'success_return_url':'error_return_url';
	$result['redirect_url'] = isset($data[$url_key])?urldecode($data[$url_key]):'';
	return $result;
}

function procMemberDefault($data) {
	$act = strtolower($data['act']);
	$dir = _AF_MODULES_PATH_ . 'member/proc/';
	$inc_file = $dir . $act . '.php';

	if (file_exists($inc_file)) {
		$result = _checkGrant('proc.'.$act);
	} else {
		$result = set_error(getLang('error_request'),4303);
	}

	if (empty($result['error'])) {
		require_once $inc_file;
		return _returnUrlMerge($data, _checkProtect('proc.'.$act, proc($data)));
	} else {
		return _returnUrlMerge($data, $result);
	}
}

function dispMemberDefault($data) {
	$act = strtolower($data['disp']);
	$dir = _AF_MODULES_PATH_ . 'member/disp/';
	$inc_file = $dir . $act . '.php';

	if (file_exists($inc_file)) {
		$result = _checkGrant('disp.' . $act);
	} else {
		$result = set_error(getLang('error_request'),4303);
	}

	if (empty($result['error'])) {
		require_once $inc_file;
		return proc($data);
	} else {
		return $result;
	}
}

/* End of file index.php */
/* Location: ./module/member/index.php */
