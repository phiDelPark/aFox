<?php

if(!defined('__AFOX__')) exit();

function proc($data) {

	if(isset($data['new_md_id'])) $data['md_id'] = $data['new_md_id'];
	if(empty($data['md_id'])) return set_error(getLang('msg_invalid_request'),303);

	if(!preg_match('/^[a-zA-Z]+[a-zA-Z0-9_]{2,}/', $data['md_id'])) {
		return set_error(getLang(getLang('invalid_value'), getLang('id')),701);
	}

	// 분류값 정리
	if(!empty($data['md_category'])) {
		if(preg_match('/[\x{21}-\x{2b}\x{2d}-\x{2f}\x{3a}-\x{40}\x{5b}-\x{60}]+/', $data['md_category'])) {
			return set_error(getLang(getLang('invalid_value'), getLang('category')),701);
		}

		$tmpa = explode(',', $data['md_category']);
		$data['md_category'] = '';
		foreach ($tmpa as $value) {
			$value = trim($value);
			if(!empty($value)) $data['md_category'] .= cut_str($value,20,'') . ',';
		}
		if(!empty($data['md_category'])) $data['md_category'] = substr($data['md_category'], 0, -1);
	}

	// 확장자는 사용하기 쉽게 | 로 구분함
	$file_extension = str_replace(',', '|', $data['md_file_ext']);

	DB::transaction();

	try {

		$module = getModule($data['md_id']);

		if (empty($module['md_id'])) {

			if(!isset($data['new_md_id'])) {
				throw new Exception(getLang('msg_invalid_request'), 303);
			}

			DB::insert(_AF_MODULE_TABLE_,
				[
					'md_id'=>$data['md_id'],
					'md_key'=>'board',
					'md_category'=>$data['md_category'],
					'md_title'=>$data['md_title'],
					'md_description'=>$data['md_description'],
					'md_manager'=>(int)$data['md_manager'],
					'md_file_max'=>(int)$data['md_file_max'],
					'md_file_size'=>(int)$data['md_file_size'],
					'md_file_ext'=>$file_extension,
					'use_style'=>$data['use_style'],
					'use_type'=>$data['use_type'],
					'use_secret'=>$data['use_secret'],
					'point_view'=>empty($data['point_view'])?'0':$data['point_view'],
					'point_write'=>empty($data['point_write'])?'0':$data['point_write'],
					'point_reply'=>empty($data['point_reply'])?'0':$data['point_reply'],
					'point_download'=>empty($data['point_download'])?'0':$data['point_download'],
					'grant_list'=>empty($data['grant_list'])?'0':$data['grant_list'],
					'grant_view'=>empty($data['grant_view'])?'0':$data['grant_view'],
					'grant_write'=>empty($data['grant_write'])?'0':$data['grant_write'],
					'grant_reply'=>empty($data['grant_reply'])?'0':$data['grant_reply'],
					'grant_upload'=>empty($data['grant_upload'])?'0':$data['grant_upload'],
					'grant_download'=>empty($data['grant_download'])?'0':$data['grant_download']
				]
			);
		} else {
			if(isset($data['new_md_id'])) {
				throw new Exception(getLang('msg_target_exists'), 802);
			}

			DB::update(_AF_MODULE_TABLE_,
				[
					'md_key'=>'board',
					'md_category'=>$data['md_category'],
					'md_title'=>$data['md_title'],
					'md_description'=>$data['md_description'],
					'md_manager'=>(int)$data['md_manager'],
					'md_file_max'=>(int)$data['md_file_max'],
					'md_file_size'=>(int)$data['md_file_size'],
					'md_file_ext'=>$file_extension,
					'use_style'=>$data['use_style'],
					'use_type'=>$data['use_type'],
					'use_secret'=>$data['use_secret'],
					'point_view'=>empty($data['point_view'])?'0':$data['point_view'],
					'point_write'=>empty($data['point_write'])?'0':$data['point_write'],
					'point_reply'=>empty($data['point_reply'])?'0':$data['point_reply'],
					'point_download'=>empty($data['point_download'])?'0':$data['point_download'],
					'grant_list'=>empty($data['grant_list'])?'0':$data['grant_list'],
					'grant_view'=>empty($data['grant_view'])?'0':$data['grant_view'],
					'grant_write'=>empty($data['grant_write'])?'0':$data['grant_write'],
					'grant_reply'=>empty($data['grant_reply'])?'0':$data['grant_reply'],
					'grant_upload'=>empty($data['grant_upload'])?'0':$data['grant_upload'],
					'grant_download'=>empty($data['grant_download'])?'0':$data['grant_download']
				], [
					'md_id'=>$data['md_id']
				]
			);
		}

	} catch (Exception $ex) {
		DB::rollback();
		return set_error($ex->getMessage(),$ex->getCode());
	}

	DB::commit();

	return ['error'=>0, 'message'=>getLang('success_saved')];
}

/* End of file updateboard.php */
/* Location: ./module/admin/proc/updateboard.php */