<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	if(!isset($data['wr_srl'])) return set_error(getLang('error_request'),4303);

	global $_MEMBER;
	$doc = getDocument($data['wr_srl'], 'md_id,wr_srl,wr_secret,mb_srl,mb_password');

	if(!empty($doc['error'])) {
		return set_error($doc['message'],$doc['error']);
	} else if(empty($doc['wr_srl'])) {
		return set_error(getLang('error_founded'),4201);
	} else if(!isGrant('view', $doc['md_id'])) {
		return set_error(getLang('error_permitted'),4501);
	}

	// 비밀글이면
	if($doc['wr_secret'] == '1' && !isManager($doc['md_id'])) {
		// 권한 체크
		if(empty($_MEMBER) || empty($doc['mb_srl'])) {
			if(empty($data['mb_password'])) {
				return set_error(getLang('request_input', ['password']));
			}
			if (empty($doc['mb_password']) || !checkPassword($data['mb_password'], $doc['mb_password'])) {
				return set_error(getLang('error_password'),4801);
			}
		} else if($_MEMBER['mb_srl'] != $doc['mb_srl']) {
			return set_error(getLang('error_permitted'),4501);
		}
	}

	$files = getDBList(_AF_FILE_TABLE_, ['md_id'=>$doc['md_id'],'mf_target'=>$doc['wr_srl']], 'mf_type');

	// 요청값이 있으면 요청값만 보냄
	$response_tags = $data['response_tags'];
	if(!empty($response_tags) && count($response_tags) > 0) {
		$response_vals = ['md_id'=>$doc['md_id'],'wr_srl'=>$doc['wr_srl']];
		foreach ($response_tags as $value) {
			$response_vals[$value] = $files[$value];
		}
		$files = $response_vals;
	}

	// JSON 사용시 모듈설정이 필요할때를 위해 만든옵션
	return $files;
}

/* End of file getdocumentfilelist.php */
/* Location: ./module/board/proc/getdocumentfilelist.php */
