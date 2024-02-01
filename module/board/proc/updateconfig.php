<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	if(__MODULE__!='board'||empty($data['md_id'])) return set_error(getLang('error_request'),4303);
	if(isManager($data['md_id']) === false) return set_error(getLang('error_permitted'),4501);

	$module = getModule($data['md_id']);
	if(empty($module['md_id'])) return set_error(getLang('error_founded'),4201);

	// 확장 변수가 있으면 unserialize
	if(!empty($module['md_extra']) && !is_array($module['md_extra'])) {
		$md_extra = unserialize($module['md_extra']);
	} else {
		$md_extra = [];
	}

	$remove_array = ['md_id', 'module', 'id', 'act', 'disp', 'success_url', 'error_url','response_tags'];
	foreach ($remove_array as $value) {
		if(isset($data[$value])) unset($data[$value]);
	}

	// 오류 방지를 위해서 확장 필드 최대 사이즈 체크
	$md_extra['configs'] = $data;
	$md_extra = serialize($md_extra);
	if(strlen($md_extra) > 65535) {
		return set_error(getLang('overflow_max', ['extra_keys',65535]), 1401);
	}

	DB::update(_AF_MODULE_TABLE_, ['md_extra'=>$md_extra], ['md_id'=>$module['md_id']]);

	// 썸네일 제거
	unlinkAll(_AF_ATTACH_DATA_.'thumbnail/'.$module['md_id'].'/');

	return ['error'=>0, 'message'=>getLang('success_saved')];
}

/* End of file updateconfig.php */
/* Location: ./module/board/proc/updateconfig.php */
