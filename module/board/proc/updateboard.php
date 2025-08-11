<?php if(!defined('__AFOX__')) exit();

function proc($data)
{
	if(isset($data['new_md_id'])) $data['md_id'] = $data['new_md_id'];
	if(empty($data['md_id'])||!preg_match('/'._AF_PATTERN_ID_.'/', $data['md_id'])){
		return set_error(getLang('invalid_value', ['id']), 2001);
	}

	$category = ''; // 분류 정리
	if(!empty($data['md_category'])) {
		if(!preg_match('/'._AF_PATTERN_CATEGORY_.'/u', $data['md_category'])) {
			return set_error(getLang('invalid_value', ['category']),2001);
		}
		$tmpa = explode(',', $data['md_category']);
		foreach ($tmpa as $value) {
			$value = trim($value);
			if(!empty($value)) $category .= cutstr($value,20,'') . ',';
		}
		if($category) $category = substr($category, 0, -1);
	}

	$ex_keys = []; // 확장 변수 키값
	if(!empty($data['md_extra_keys'])) {
		if(!preg_match('/'._AF_PATTERN_EXTRAKEY_.'/u', $data['md_extra_keys'])) {
			return set_error(getLang('invalid_value', ['extra_keys']),2001);
		}
		$ex_keys['keys'] = [];
		$tmp = explode(',', $data['md_extra_keys']);
		foreach ($tmp as $cap) {
			$_boxs = explode('&', $cap);
			if($is = (count($_boxs)<2)) $_boxs = explode('|', $cap);
			$_boxs[0] = cutstr(trim($_boxs[0]), 20);
			$ex_keys['keys'][md5($_boxs[0])] = implode($is?'|':'&', $_boxs);
		}
		if(count($ex_keys['keys']) > 99) { //확장 변수 갯수 제한 99개
			return set_error(getLang('overflow_count', ['extra_keys',99]));
		}
	}

	$list_count = empty($data['md_list_count']) ? 20 : abs($data['md_list_count']);

	$file_max = abs($data['md_file_max']);
	$file_size = abs($data['md_file_size']) * 1024;
	$file_accept = str_replace(' ','',empty($data['md_file_accept'])?'':$data['md_file_accept']);

	// 관리자 이이디가 넘어오면 srl로 변경
	$md_manager = empty($data['md_manager']) ? 0 : $data['md_manager'];
	if($md_manager && !($md_manager=getMember($md_manager))) {
		return set_error(getLang('invalid_value', ['manager']),2001);
	} else {
		$md_manager = $md_manager ? (int)$md_manager['mb_srl'] : 0;
	}

	$module = getModule($data['md_id']);

	DB::transaction();

	try {
		if(empty($module['md_id'])) {

			if(!isset($data['new_md_id'])) {
				throw new Exception(getLang('error_request'),4303);
			}
			// 오류 방지를 위해서 확장 필드 최대 사이즈 체크
			$_extras = serialize($ex_keys);
			if(strlen($_extras) > 65535) throw new Exception(getLang('overflow_size', ['extra_keys',65535]), 1401);

			DB::insert(_AF_MODULE_TABLE_,
				[
					'md_id'=>$data['md_id'],
					'md_key'=>'board',
					'md_category'=>$category,
					'md_title'=>$data['md_title'],
					'md_about'=>$data['md_about'],
					'md_manager'=>$md_manager,
					'md_file_max'=>$file_max,
					'md_file_size'=>$file_size,
					'md_file_accept'=>$file_accept,
					'md_list_count'=>$list_count,
					'use_style'=>$data['use_style'],
					'use_type'=>empty($data['use_type'])?$data['use_default_type']:$data['use_type'],
					'use_secret'=>$data['use_secret'],
					'thumb_width'=>abs($data['thumb_width']),
					'thumb_height'=>abs($data['thumb_height']),
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
					'md_regdate(=)'=>'NOW()'
				]
			);
		} else {
			if(isset($data['new_md_id']) || $module['md_key'] != 'board') {
				throw new Exception(getLang('warn_exists', ['id']), 3103);
			}

			if ($category != $module['md_category']) {
				$diff = array_diff(explode(',',$module['md_category']), explode(',',$category));
				if (count($diff)>0 && !empty($diff[0])) {
					$diff = implode(',', $diff);
					$out = DB::get(_AF_DOCUMENT_TABLE_, 'wr_category', ['md_id'=>$data['md_id'], 'wr_category{IN}'=>$diff]);
					if(!empty($out)) throw new Exception(getLang('cant_change_category', [$out['wr_category']]), 3);
				}
			}

			// 확장 변수가 있으면 unserialize
			$_extras = empty($module['md_extra']) || !is_string($module['md_extra']) ? [] : unserialize($module['md_extra']);
			// 확장변수 키가 있으면 합침
			if(empty($ex_keys)) unset($_extras['keys']);
			else $_extras['keys'] = $ex_keys['keys'];
			// 오류 방지를 위해서 확장 필드 최대 사이즈 체크
			$_extras = serialize($_extras);
			if(strlen($_extras) > 65535) throw new Exception(getLang('overflow_size', ['extra_keys',65535]), 1401);

			DB::update(_AF_MODULE_TABLE_,
				[
					'md_key'=>'board',
					'md_category'=>$category,
					'md_title'=>$data['md_title'],
					'md_about'=>$data['md_about'],
					'md_manager'=>$md_manager,
					'md_file_max'=>$file_max,
					'md_file_size'=>$file_size,
					'md_file_accept'=>$file_accept,
					'md_list_count'=>$list_count,
					'use_style'=>$data['use_style'],
					'use_type'=>empty($data['use_type'])?$data['use_default_type']:$data['use_type'],
					'use_secret'=>$data['use_secret'],
					'thumb_width'=>abs($data['thumb_width']),
					'thumb_height'=>abs($data['thumb_height']),
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
