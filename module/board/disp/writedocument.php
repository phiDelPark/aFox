<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	$doc = empty($data['srl']) ? [] : getDocument($data['srl'], false);

	global $_MEMBER;
	$is_manager = isManager($doc['md_id']);
	$is_secret = $doc['wr_secret'] == '1';

	// 권한 체크
	if(!empty($doc) && !$is_manager) {

		if(!isGrant($doc['md_id'], 'view')) {
			return set_error(getLang('error_permit'),88088);
		}

		if(empty($_MEMBER) || $_MEMBER['mb_srl'] != $doc['mb_srl']) {

			// 비밀글이면
			if($is_secret) {
				$input_password = '<form class="input-password" method="post" autocomplete="off">'
							.'%s<div class="input-group" style="margin-top:10px"><input class="form-control" name="mb_password" type="password" placeholder="'. getLang('password').'" required>'
							.'<span class="input-group-btn"><button class="btn btn-default" type="submit">'. getLang('ok').'</button></span></div></form>';

				if(!empty($doc['mb_srl']) || empty($doc['mb_password'])) {
					return set_error(getLang('error_permit'),4501);
				} else if(empty($data['mb_password'])) {
					return set_error(sprintf($input_password, getLang('request_input', ['password'])), 1);
				} else if (!verifyEncrypt($data['mb_password'], $doc['mb_password'])) {
					return set_error(sprintf($input_password, getLang('error_password')), 4801);
				}

				$GLOBALS['_PERMIT_VIEW_'][md5($doc['md_id'].'_'.$data['srl'])] = true;
			}
		}
	}

	$doc['tpl'] = 'write';

	return $doc;
}

/* End of file writedocument.php */
/* Location: ./module/board/disp/writedocument.php */