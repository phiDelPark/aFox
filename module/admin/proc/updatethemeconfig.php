<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	if(empty($data['th_id'])) return set_error(getLang('error_request'),4303);

	DB::transaction();

	try {

		$th_id = $data['th_id'];

		$out = DB::get(_AF_THEME_TABLE_, 'th_id', ['th_id'=>$th_id]);
		if($ex=DB::error()) return set_error($ex->getMessage(), $ex->getCode());

		$theme_exists = !empty($out['th_id']);

		$remove_array = ['th_id', 'module', 'id', 'act', 'disp', 'success_return_url', 'error_return_url','response_tags'];
		foreach ($remove_array as $value) {
			if(isset($data[$value])) unset($data[$value]);
		}

		// 오류 방지를 위해서 확장 필드 최대 사이즈 체크
		$extra = serialize($data);
		if(strlen($extra) > 65535) {
			throw new Exception(getLang('msg_max_overflow', [65535]), 1401);
		}

		if($theme_exists) {
			DB::update(_AF_THEME_TABLE_,['th_extra'=>$extra], ['th_id'=>$th_id]);
		} else {
			DB::insert(_AF_THEME_TABLE_,['th_id'=>$th_id,'th_extra'=>$extra]);
		}

		// 캐시 재생성
		set_cache('_AF_THEME_'.$th_id, $data);

	} catch (Exception $ex) {
		DB::rollback();
		return set_error($ex->getMessage(),$ex->getCode());
	}

	DB::commit();

	return ['error'=>0, 'message'=>getLang('success_saved')];
}

/* End of file updatethemeconfig.php */
/* Location: ./module/admin/proc/updatethemeconfig.php */
