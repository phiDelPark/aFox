<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	if(!isset($data['wr_srl'])) return set_error(getLang('msg_invalid_request'),303);

	global $_MEMBER;
	$doc = getDocument((int)$data['wr_srl']);

	if(!empty($doc['error'])) {
		return set_error($doc['message'],$doc['error']);
	} else if(empty($doc['wr_srl'])) {
		return set_error(getLang('msg_not_founded'),801);
	} else if(!isGrant($doc['md_id'], 'view')) {
		return set_error(getLang('msg_not_permitted'),901);
	}

	// 비밀글이면
	if($doc['wr_secret'] == '1' && !isManager($doc['md_id'])) {
		// 권한 체크
		if(empty($_MEMBER) || empty($doc['mb_srl'])) {
			if(empty($data['mb_password'])) {
				return set_error(getLang('warn_input', ['password']), 3);
			}
			if (empty($doc['mb_password']) || !verifyEncrypt($data['mb_password'], $doc['mb_password'])) {
				return set_error(getLang('msg_not_permitted'), 901);
			}
		} else if($_MEMBER['mb_srl'] != $doc['mb_srl']) {
			return set_error(getLang('msg_not_permitted'), 901);
		}
	}

	// 비밀번호는 암호화 되있지만 그래도 노출 안되게 제거
	unset($doc['mb_password']);

	if(!empty($data['with_file_list'])) {
		$fd = 'mf_srl,mf_name,mf_type,mf_download,mf_description,mf_size,mb_srl,mf_ipaddress';
		$doc['files'] = DB::getList('SELECT '.$fd.' FROM '._AF_FILE_TABLE_.' WHERE md_id=\''.$doc['md_id'].'\' AND mf_target='.$doc['wr_srl'].' ORDER BY mf_type');
	}

	return empty($data['with_module_config']) ?  $doc : array_merge($doc, getModule($doc['md_id']));
}

/* End of file getdocument.php */
/* Location: ./module/board/proc/getdocument.php */