<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	$doc = empty($data['srl']) ? [] : getDocument($data['srl'], false);

	global $_MEMBER;
	$is_manager = isManager($doc['md_id']);
	$is_secret = $doc['wr_secret'] == '1';

	// 권한 체크
	if(!empty($doc) && !$is_manager) {

		if(!isGrant('view', $doc['md_id'])) {
			return set_error(getLang('error_permitted'),88088);
		}

		if(empty($_MEMBER) || $_MEMBER['mb_srl'] != $doc['mb_srl']) {

			// 비밀글이면
			if($is_secret) {
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

				$GLOBALS['_PERMIT_VIEW_'][md5($doc['md_id'].'_'.$data['srl'])] = true;
			}
		}
	}

	$doc['tpl'] = 'write';

	// 확장 변수가 있으면 unserialize
	if(!empty($doc['wr_extra']) && !is_array($doc['wr_extra'])) {
		$doc['wr_extra'] = unserialize($doc['wr_extra']);
	}

	return $doc;
}

/* End of file writedocument.php */
/* Location: ./module/board/disp/writedocument.php */
