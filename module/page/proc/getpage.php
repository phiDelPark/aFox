<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	if(!isset($data['md_id'])) return set_error(getLang('msg_invalid_request'),303);

	$out = getDBItem(_AF_PAGE_TABLE_, ['md_id'=>$data['md_id']]);
	if(!empty($out['error'])) {
		return set_error($out['message'],$out['error']);
	} else if(empty($out['md_id'])) {
		return set_error(getLang('msg_not_founded'),801);
	} else if(!isGrant($out['md_id'], 'view')) {
		return set_error(getLang('msg_not_permitted'),901);
	}

	// 관리자 모드에서 사용하기 위해 필요한 정보 같이 보내기... (관리자만)
	if(!empty($data['with_module_config']) && isManager($doc['md_id'])) {
		// 파일 목록
		$fd = 'mf_srl,mf_name,mf_type,mf_download,mf_description,mf_size,mb_srl,mb_ipaddress';
		$sql = 'SELECT '.$fd.' FROM '._AF_FILE_TABLE_.' WHERE md_id=:1 ORDER BY mf_type';
		$out['files'] = DB::getList($sql, [$data['md_id']]);
		// 모듈 정보
		$out = array_merge($out, getModule($out['md_id']));
	}

	// JSON 사용시 모듈설정이 필요할때를 위해 만든옵션
	return  $out;
}

/* End of file getpage.php */
/* Location: ./module/page/proc/getpage.php */