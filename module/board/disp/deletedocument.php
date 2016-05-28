<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	// 자신 글이면 권한 체크 패스
	if(empty($_MEMBER['mb_srl']) || $_MEMBER['mb_srl'] != $item['mb_srl']) {
		// 권한 체크
		if(!isGrant($data['id'], 'view') && !isManager($data['id'])) {
			return set_error(getLang('msg_not_permitted'),901);
		}
	}

	$result = getDocument($data['srl'], false);
	$result['tpl'] = 'delete';

	return $result;
}

/* End of file deletedocument.php */
/* Location: ./module/board/disp/deletedocument.php */