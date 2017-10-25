<?php

if(!defined('__AFOX__')) exit();

function proc($data) {

	if(isset($data['new_md_id'])) $data['md_id'] = $data['new_md_id'];
	if(empty($data['md_id'])) return set_error(getLang('error_request'),4303);

	if(!preg_match('/^[a-zA-Z]+\w{2,}$/', $data['md_id'])) {
		return set_error(getLang('invalid_value', ['id']),701);
	}

	$md_extra = [];
	$data['md_title'] = trim($data['md_title']);
	$data['md_list_count'] = empty($data['md_list_count']) ? 20 : abs($data['md_list_count']);

	// 분류값 정리
	if(!empty($data['md_category'])) {
		if(preg_match('/[\x{21}-\x{2b}\x{2d}-\x{2f}\x{3a}-\x{40}\x{5b}-\x{60}]+/', $data['md_category'])) {
			return set_error(getLang('invalid_value', ['category']),701);
		}

		$tmpa = explode(',', $data['md_category']);
		$data['md_category'] = '';
		foreach ($tmpa as $value) {
			$value = trim($value);
			if(!empty($value)) $data['md_category'] .= cutstr($value,20,'') . ',';
		}
		if(!empty($data['md_category'])) $data['md_category'] = substr($data['md_category'], 0, -1);
	}

	// 확장 변수 키값
	if(!empty($data['md_extra_keys'])) {
		if(preg_match('/[\x{21}-\x{29}\x{2b}\x{2d}-\x{2f}\x{3a}-\x{40}\x{5b}-\x{60}]+/', $data['md_extra_keys'])) {
			return set_error(getLang('invalid_value', ['extra_keys']),701);
		}

		$tmpa = explode(',', $data['md_extra_keys']);
		$md_extra_keys = [];
		foreach ($tmpa as $ex_caption) {
			$ex_caption = trim($ex_caption);
			if(!empty($ex_caption)) {
				$is_required = substr($ex_caption,-1,1) === '*';
				if($is_required) $ex_caption = trim(substr($ex_caption,0,-1));
				if(!empty($ex_caption)){
					$ex_caption = cutstr($ex_caption,20,'');
					$md_extra_keys[md5($ex_caption)] = $ex_caption . ($is_required?'*':'');
				}
			}
		}
		if(!empty($md_extra_keys)) {
			//확장 변수 갯수 제한 99개
			if(count($md_extra_keys) > 99) {
				return set_error(getLang('msg_count_overflow', ['extra_keys','99']));
			}
			$md_extra['keys'] = $md_extra_keys;
		}
	}


	// 확장자는 사용하기 쉽게 | 로 구분함
	$file_extension = str_replace(',', '|', $data['md_file_ext']);

	// 관리자 이이디가 넘어오면 srl로 변경
	$md_manager = $data['md_manager'];
	if(!empty($md_manager)) {
		$mb = getMember($md_manager);
		if(empty($mb['mb_srl'])) return set_error(getLang('invalid_value', ['admin']),701);
		$md_manager = (int) $mb['mb_srl'];
	}

	DB::transaction();

	try {

		$module = getModule($data['md_id']);

		if (empty($module['md_id'])) {

			if(!isset($data['new_md_id'])) {
				throw new Exception(getLang('error_request'),4303);
			}

			DB::insert(_AF_MODULE_TABLE_,
				[
					'md_id'=>$data['md_id'],
					'md_key'=>'board',
					'md_category'=>$data['md_category'],
					'md_title'=>$data['md_title'],
					'md_description'=>$data['md_description'],
					'md_manager'=>$md_manager,
					'md_file_max'=>(int)$data['md_file_max'],
					'md_file_size'=>(int)$data['md_file_size']*1024,
					'md_file_ext'=>$file_extension,
					'md_list_count'=>(int)$data['md_list_count'],
					'use_style'=>$data['use_style'],
					'use_type'=>((int)$data['use_type'])?$data['use_type']:$data['use_default_type'],
					'use_secret'=>$data['use_secret'],
					'thumb_width'=>abs($data['thumb_width']),
					'thumb_height'=>abs($data['thumb_height']),
					'thumb_option'=>$data['thumb_option'],
					'point_view'=>empty($data['point_view'])?'0':$data['point_view'],
					'point_write'=>empty($data['point_write'])?'0':$data['point_write'],
					'point_reply'=>empty($data['point_reply'])?'0':$data['point_reply'],
					'point_download'=>empty($data['point_download'])?'0':$data['point_download'],
					'grant_list'=>empty($data['grant_list'])?'0':$data['grant_list'],
					'grant_view'=>empty($data['grant_view'])?'0':$data['grant_view'],
					'grant_write'=>empty($data['grant_write'])?'0':$data['grant_write'],
					'grant_reply'=>empty($data['grant_reply'])?'0':$data['grant_reply'],
					'grant_upload'=>empty($data['grant_upload'])?'0':$data['grant_upload'],
					'grant_download'=>empty($data['grant_download'])?'0':$data['grant_download'],
					'md_extra'=>empty($md_extra)?'':serialize($md_extra),
					'(md_regdate)'=>'NOW()'
				]
			);
		} else {
			if(isset($data['new_md_id'])) {
				throw new Exception(getLang('error_exists'), 4251);
			}

			DB::update(_AF_MODULE_TABLE_,
				[
					'md_key'=>'board',
					'md_category'=>$data['md_category'],
					'md_title'=>$data['md_title'],
					'md_description'=>$data['md_description'],
					'md_manager'=>$md_manager,
					'md_file_max'=>(int)$data['md_file_max'],
					'md_file_size'=>(int)$data['md_file_size']*1024,
					'md_file_ext'=>$file_extension,
					'md_list_count'=>(int)$data['md_list_count'],
					'use_style'=>$data['use_style'],
					'use_type'=>((int)$data['use_type'])?$data['use_type']:$data['use_default_type'],
					'use_secret'=>$data['use_secret'],
					'thumb_width'=>abs($data['thumb_width']),
					'thumb_height'=>abs($data['thumb_height']),
					'thumb_option'=>$data['thumb_option'],
					'point_view'=>empty($data['point_view'])?'0':$data['point_view'],
					'point_write'=>empty($data['point_write'])?'0':$data['point_write'],
					'point_reply'=>empty($data['point_reply'])?'0':$data['point_reply'],
					'point_download'=>empty($data['point_download'])?'0':$data['point_download'],
					'grant_list'=>empty($data['grant_list'])?'0':$data['grant_list'],
					'grant_view'=>empty($data['grant_view'])?'0':$data['grant_view'],
					'grant_write'=>empty($data['grant_write'])?'0':$data['grant_write'],
					'grant_reply'=>empty($data['grant_reply'])?'0':$data['grant_reply'],
					'grant_upload'=>empty($data['grant_upload'])?'0':$data['grant_upload'],
					'grant_download'=>empty($data['grant_download'])?'0':$data['grant_download'],
					'md_extra'=>empty($md_extra)?'':serialize($md_extra)
				], [
					'md_id'=>$data['md_id']
				]
			);
		}

		// 썸네일 제거
		unlinkAll(_AF_ATTACH_DATA_.'thumbnail/'.$data['md_id'].'/');

	} catch (Exception $ex) {
		DB::rollback();
		return set_error($ex->getMessage(),$ex->getCode());
	}

	DB::commit();

	return ['error'=>0, 'message'=>getLang('success_saved')];
}

/* End of file updateboard.php */
/* Location: ./module/admin/proc/updateboard.php */
