<?php

if(!defined('__AFOX__')) exit();

function proc($data) {

	if(!isset($data['md_id'])) return set_error(getLang('error_request'),4303);

	$out = getDBItem(_AF_MODULE_TABLE_, ['md_key'=>'board','md_id'=>$data['md_id']]);
	if(!empty($out['error'])) {
		return set_error($out['message'],$out['error']);
	}

	if($out['md_id']) {
		// KB 로 바꿈
		if(!empty($out['md_file_size'])) $out['md_file_size'] = round((int)$out['md_file_size'] / 1024);
		// 관리자 이이디가 넘어오면 id로 변경
		$md_manager = $out['md_manager'];
		if(!empty($md_manager)) {
			$mb = getMember($md_manager);
			$out['md_manager'] = empty($mb['mb_id'])?'':$mb['mb_id'];
		}
		return $out;
	} else {
		return set_error(getLang('error_founded'),4201);
	}
}

/* End of file getboard.php */
/* Location: ./module/admin/proc/getboard.php */