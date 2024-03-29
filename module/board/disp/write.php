<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	$doc = empty($data['srl']) ? [] : getDocument($data['srl']);

	global $_MEMBER;
	if(empty($doc['md_id'])) $doc['md_id'] = _MID_;
	if(empty($doc['wr_srl'])) $doc['wr_srl'] = 0;
	if(empty($doc['wr_extra'])) $doc['wr_extra'] = null;

	$is_manager = isManager($doc['md_id']);
	$PERMIT_KEY = md5($doc['md_id'] .'_'. $doc['wr_srl'] . '_' . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);

	if(!isGrant('write', $doc['md_id'])) {
		return set_error(getLang('error_permitted'),4501);
	}

	// 권한 체크
	if(!empty($doc) && !$is_manager && !get_session('_AF_SECRET_DOCUMENT_'.$PERMIT_KEY)) {

		if(!isGrant('view', $doc['md_id'])) {
			return set_error(getLang('error_permitted'),4501);
		}

		if(empty($_MEMBER) || $_MEMBER['mb_srl'] != $doc['mb_srl']) {

			$input_password = '<form class="input-password" method="post" autocomplete="off">'
						.'<input type="hidden" name="id" value="%s"><input type="hidden" name="srl" value="%s">'
						.'%s<div class="input-group" style="margin-top:10px"><input class="form-control" name="mb_password" type="password" placeholder="'. getLang('password').'" required>'
						.'<span class="input-group-btn"><button class="btn btn-default" type="submit">'. getLang('ok').'</button></span></div></form>';

			if(!empty($doc['mb_srl']) || empty($doc['mb_password'])) {
				return set_error(getLang('error_permitted'),4501);
			} else if(empty($data['mb_password'])) {
				return set_error(sprintf($input_password, $doc['md_id'], $doc['wr_srl'], getLang('request_input', ['password'])), 1);
			} else if (!checkPassword($data['mb_password'], $doc['mb_password'])) {
				return set_error(sprintf($input_password, $doc['md_id'], $doc['wr_srl'], getLang('error_password')), 4801);
			}

			set_session('_AF_SECRET_DOCUMENT_'.$PERMIT_KEY, true);
		}
	}

	// 확장 변수가 있으면 unserialize
	if(!empty($doc['wr_extra']) && !is_array($doc['wr_extra'])) {
		$doc['wr_extra'] = unserialize($doc['wr_extra']);
	}

	$result = $doc;
	$result['tpl'] = 'write';
	$result['list'] = [];
	$result['replys'] = [];

	return $result;
}

/* End of file write.php */
/* Location: ./module/board/disp/write.php */
