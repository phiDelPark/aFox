<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	if(!isset($data['wr_srl'])) return set_error(getLang('error_request'),4303);

	global $_MEMBER;
	$doc = getDocument($data['wr_srl']);

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

	// 요청값이 있으면 요청값만 보냄
	$response_tags = $data['response_tags'];
	if(!empty($response_tags) && count($response_tags) > 0) {
		$response_vals = ['md_id'=>$doc['md_id'],'wr_srl'=>$doc['wr_srl']];
		foreach ($response_tags as $value) {
			$response_vals[$value] = $doc[$value];
		}
		$doc = $response_vals;
	}

	// 비밀번호는 암호화 되있지만 그래도 노출 안되게 제거
	unset($doc['mb_password']);
	//if($hide_ipaddress) unset($doc['mb_ipaddress']);

	// 확장 변수가 있으면 unserialize
	if(!empty($doc['wr_extra']) && !is_array($doc['wr_extra'])) {
		$doc['wr_extra'] = unserialize($doc['wr_extra']);
	}

	// 관리자 모드에서 사용하기 위해 필요한 정보 같이 보내기... (관리자만)
	if(!empty($data['with_module_config']) && isManager($doc['md_id'])) {
		// 파일 목록
		$fd = 'mf_srl,mf_name,mf_type,mf_download,mf_description,mf_size,mb_srl,mb_ipaddress';
		$sql = 'SELECT '.$fd.' FROM '._AF_FILE_TABLE_.' WHERE md_id=:1 AND mf_target=:2 ORDER BY mf_type';
		$doc['files'] = DB::getList($sql, [$doc['md_id'],$doc['wr_srl']]);
		// 모듈 정보
		$doc = array_merge($doc, getModule($doc['md_id']));
	}

	// JSON 사용시 모듈설정이 필요할때를 위해 만든옵션
	return $doc;
}

/* End of file getdocument.php */
/* Location: ./module/board/proc/getdocument.php */
