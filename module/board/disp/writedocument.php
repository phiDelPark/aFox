<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	$doc = empty($data['srl']) ? [] : getDocument($data['srl'], false);

	// 권한 체크
	if(!empty($doc) && !isManager($doc['md_id'])) {
	global $_MEMBER;
		if(!isGrant($doc['md_id'], 'view')) {
			return set_error(getLang('error_permit'),4501);
		}
		// 비밀글이면
		if($doc['wr_secret'] == '1') {
			$input_password = '<form class="input-password" method="post" autocomplete="off">'
							.'%s<div class="input-group" style="margin-top:10px"><input class="form-control" name="mb_password" type="password" placeholder="'. getLang('password').'" required>'
							.'<span class="input-group-btn"><button class="btn btn-default" type="submit">'. getLang('ok').'</button></span></div></form>';

			// 권한 체크
			if(empty($_MEMBER) || empty($doc['mb_srl'])) {
				if (empty($doc['mb_password'])) {
					return set_error(getLang('error_permit'),4501);
				} else if(empty($data['mb_password'])) {
					return set_error(sprintf($input_password, getLang('request_input', ['password'])));
				} else if (!verifyEncrypt($data['mb_password'], $doc['mb_password'])) {
					return set_error(sprintf($input_password, getLang('error_password')), 4801);
				}
			} else if($_MEMBER['mb_srl'] != $doc['mb_srl']) {
				return set_error(getLang('error_permit'),4501);
			}

			$GLOBALS['_PERMIT_VIEW_'][md5($doc['md_id'].'_'.$data['srl'])] = true;
		}
	}

	$doc['tpl'] = 'write';

	return $doc;
}

/* End of file writedocument.php */
/* Location: ./module/board/disp/writedocument.php */