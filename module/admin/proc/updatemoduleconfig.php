<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	if(empty($data['md_id'])) return set_error(getLang('error_request'),4303);
	$md_id = $data['md_id'];
	if(!preg_match('/^[a-zA-Z]+\w{2,}$/', $md_id)) {
		return set_error(getLang('invalid_value', ['id']),2001);
	}

	$md_title = empty($data['md_title']) ? '' : trim($data['md_title']);
	$md_description = empty($data['md_description']) ? '' : trim($data['md_description']);
	$md_list_count = empty($data['md_list_count']) ? 20 : abs($data['md_list_count']);
	$md_category = empty($data['md_category']) ? '' : $data['md_category'];
	$md_manager = empty($data['md_manager']) ? 0 : $data['md_manager'];
	$use_style = empty($data['use_style']) ? '' : $data['use_style'];
	$use_type = empty($data['use_type']) ? '' : $data['use_type'];
	$use_secret = empty($data['use_secret']) ? '' : $data['use_secret'];

	// 확장자는 사용하기 쉽게 | 로 구분함
	$file_extension = str_replace(',', '|', empty($data['md_file_ext']) ? '' : $data['md_file_ext']);
	$md_file_max = empty($data['md_file_max']) ? 0 : abs($data['md_file_max']);
	$md_file_size = empty($data['md_file_size']) ? 0 : abs($data['md_file_size']);

	// 분류값 정리
	if($md_category) {
		if(preg_match('/[\x{21}-\x{2b}\x{2d}\x{2f}\x{3a}-\x{40}\x{5b}-\x{60}]+/', $md_category)) {
			return set_error(getLang('invalid_value', ['category']),2001);
		}
		$tmpa = explode(',', $md_category);
		$md_category = '';
		foreach ($tmpa as $value) {
			$value = trim($value);
			if(!empty($value)) $md_category .= cutstr($value,20,'') . ',';
		}
		if(!empty($md_category)) $md_category = substr($md_category, 0, -1);
	}

	$md_extra_keys = [];
	foreach($data as $key=>$val){
		if(strpos($key, 'key_') === 0){
			$md_extra_keys[substr($key, 4)] = $val;
		}
	}
	$ex_keys = ['keys'=>$md_extra_keys];


	// 관리자 이이디가 넘어오면 srl로 변경
	if(!empty($md_manager)) {
		$mb = getMember($md_manager);
		if(empty($mb['mb_srl'])) return set_error(getLang('invalid_value', ['admin']),2001);
		$md_manager = (int) $mb['mb_srl'];
	}

	DB::transaction();

	try {

		$module = getModule('@'.$md_id);

		if (empty($module['md_id'])) {

			// 오류 방지를 위해서 확장 필드 최대 사이즈 체크
			$_extras = serialize($ex_keys);
			if(strlen($_extras) > 65535) throw new Exception(getLang('msg_max_overflow', [65535]), 1401);

			DB::insert(_AF_MODULE_TABLE_,
				[
					'md_id'=>'@'.$md_id,
					'md_key'=>$md_id,
					'md_category'=>$md_category,
					'md_title'=>$md_title,
					'md_description'=>$md_description,
					'md_manager'=>$md_manager,
					'md_file_max'=>$md_file_max,
					'md_file_size'=>$md_file_size*1024,
					'md_file_ext'=>$file_extension,
					'md_list_count'=>$md_list_count,
					'use_style'=>$use_style,
					'use_type'=>$use_type,
					'use_secret'=>$use_secret,
					'thumb_width'=>empty($data['thumb_width'])?0:abs($data['thumb_width']),
					'thumb_height'=>empty($data['thumb_height'])?0:abs($data['thumb_height']),
					'thumb_option'=>empty($data['thumb_option'])?'0':$data['thumb_option'],
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
					'md_extra'=>$_extras,
					'^md_regdate'=>'NOW()'
				]
			);
		} else {

			if ($md_category != $module['md_category']) {
				$diff = array_diff(explode(',',$module['md_category']),explode(',',$md_category));
				if (count($diff)>0 && !empty($diff[0])) {
					$diff = implode(',', $diff);
					$out = DB::get(_AF_DOCUMENT_TABLE_, 'wr_category', ['md_id'=>'@'.$md_id, 'wr_category{IN}'=>$diff]);
					if(!empty($out)) throw new Exception(getLang('msg_not_change_category', [$out['wr_category']]), 3);
				}
			}

			// 확장 변수가 있으면 unserialize
			$_extras = empty($module['md_extra']) || !is_string($module['md_extra']) ? [] : unserialize($module['md_extra']);
			// 확장변수 키가 있으면 합침
			if(empty($ex_keys)) unset($_extras['keys']);
			else $_extras['keys'] = $ex_keys['keys'];
			// 오류 방지를 위해서 확장 필드 최대 사이즈 체크
			$_extras = serialize($_extras);
			if(strlen($_extras) > 65535) throw new Exception(getLang('msg_max_overflow', [65535]), 1401);

			DB::update(_AF_MODULE_TABLE_,
				[
					'md_category'=>$md_category,
					'md_title'=>$md_title,
					'md_description'=>$md_description,
					'md_manager'=>$md_manager,
					'md_file_max'=>$md_file_max,
					'md_file_size'=>$md_file_size*1024,
					'md_file_ext'=>$file_extension,
					'md_list_count'=>$md_list_count,
					'use_style'=>$use_style,
					'use_type'=>$use_type,
					'use_secret'=>$use_secret,
					'thumb_width'=>empty($data['thumb_width'])?0:abs($data['thumb_width']),
					'thumb_height'=>empty($data['thumb_height'])?0:abs($data['thumb_height']),
					'thumb_option'=>empty($data['thumb_option'])?'0':$data['thumb_option'],
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
					'md_extra'=>$_extras
				], [
					'md_id'=>'@'.$md_id
				]
			);
		}

		// 썸네일 제거
		unlinkAll(_AF_ATTACH_DATA_.'thumbnail/'.'@'.$md_id.'/');

	} catch (Exception $ex) {
		DB::rollback();
		return set_error($ex->getMessage(),$ex->getCode());
	}

	DB::commit();

	return ['error'=>0, 'message'=>getLang('success_saved')];
}

/* End of file updatemoduleconfig.php */
/* Location: ./module/admin/proc/updatemoduleconfig.php */
