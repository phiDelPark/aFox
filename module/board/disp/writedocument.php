<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	// 자신 글이면 권한 체크 패스
	if(empty($_MEMBER['mb_srl']) || $_MEMBER['mb_srl'] != $item['mb_srl']) {
		// 권한 체크
		if(!isGrant($data['id'], 'write') && !isManager($data['id'])) {
			return set_error(getLang('msg_not_permitted'),901);
		}
	}

	$result = empty($data['srl']) ? [] : getDocument($data['srl'], false);
	$result['tpl'] = 'write';

	return $result;
}

/* End of file writedocument.php */
/* Location: ./module/board/disp/writedocument.php */