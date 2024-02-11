<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	$doc = getDocument($data['srl']);
	if(!empty($doc['error'])) return set_error(getLang($doc['message']),$doc['error']);
	if(empty($doc['wr_srl'])) return set_error(getLang('error_founded'),4201);

	global $_MEMBER;
	$is_manager = isManager($doc['md_id']);
	$is_secret = $doc['wr_secret'] == '1';
	$PERMIT_KEY = md5($doc['md_id'] .'_'. $doc['wr_srl'] . '_' . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);

	// 권한 체크
	if(!$is_manager && !get_session('_AF_SECRET_DOCUMENT_'.$PERMIT_KEY)) {

		if(!isGrant('view', $doc['md_id'])) {
			return set_error(getLang('error_permitted'),4501);
		}
		if(empty($_MEMBER) || $_MEMBER['mb_srl'] != $doc['mb_srl']) {

			$input_password = '<form class="input-password" method="post" autocomplete="off">'
						.'%s<div class="input-group" style="margin-top:10px"><input class="form-control" name="mb_password" type="password" placeholder="'. getLang('password').'" required>'
						.'<span class="input-group-btn"><button class="btn btn-default" type="submit">'. getLang('ok').'</button></span></div></form>';

			if(!empty($doc['mb_srl']) || empty($doc['mb_password'])) {
				return set_error(getLang('error_permitted'),4501);
			} else if(empty($data['mb_password'])) {
				return set_error(sprintf($input_password, getLang('request_input', ['password'])), 1);
			} else if (!checkPassword($data['mb_password'], $doc['mb_password'])) {
				return set_error(sprintf($input_password, getLang('error_password')), 4801);
			}

			set_session('_AF_SECRET_DOCUMENT_'.$PERMIT_KEY, true);
		}
	}

	$result = $doc;
	$result['tpl'] = 'delete';
	$result['list'] = [];
	$result['replys'] = [];

	return $result;
}

/* End of file delete.php */
/* Location: ./module/board/disp/delete.php */
