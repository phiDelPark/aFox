<?php if(!defined('__AFOX__')) exit();

function proc($data) {

	if(isset($data['new_md_id'])) $data['md_id'] = $data['new_md_id'];
	if(empty($data['md_id'])) return set_error(getLang('error_request'),4303);
	if(!preg_match('/'._AF_PATTERN_ID_.'/', $data['md_id'])) return set_error(getLang('invalid_value', ['id']),2001);

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

	$file_accept = 'jpg,jpeg,png';
	$file_max = abs($data['md_file_max']);
	$file_size = abs($data['md_file_size']) * 1024;
	$list_count = empty($data['md_list_count']) ? 100 : abs($data['md_list_count']);

	$data['thumb_width'] = abs($data['thumb_width']) < 1 ? 400 : abs($data['thumb_width']);
	$data['thumb_height'] = abs($data['thumb_height']) < 1 ? 180 : abs($data['thumb_height']);

	// 관리자 이이디가 넘어오면 srl로 변경
	$md_manager = $data['md_manager'];
	if(!empty($md_manager)) {
		$mb = getMember($md_manager);
		if(empty($mb['mb_srl'])) return set_error(getLang('invalid_value', ['admin']), 2001);
		$md_manager = (int) $mb['mb_srl'];
	} else {
		$md_manager = 0;
	}

	$module = getModule($data['md_id']);

	DB::transaction();

	try {
		if(empty($module['md_id'])) {

			if(!isset($data['new_md_id'])) {
				throw new Exception(getLang('error_request'),4303);
			}

			DB::insert(_AF_MODULE_TABLE_,
				[
					'md_id'=>$data['md_id'],
					'md_key'=>'gallery',
					'md_category'=>$category,
					'md_title'=>$data['md_title'],
					'md_about'=>$data['md_about'],
					'md_manager'=>$md_manager,
					'md_file_max'=>$file_max,
					'md_file_size'=>$file_size,
					'md_file_accept'=>$file_accept,
					'md_list_count'=>$list_count,
					'thumb_width'=>abs($data['thumb_width']),
					'thumb_height'=>abs($data['thumb_height']),
					'thumb_option'=>empty($data['thumb_option'])?'0':$data['thumb_option'],
					'point_view'=>empty($data['point_view'])?'0':$data['point_view'],
					'point_write'=>empty($data['point_write'])?'0':$data['point_write'],
					'grant_list'=>empty($data['grant_list'])?'0':$data['grant_list'],
					'grant_view'=>empty($data['grant_view'])?'0':$data['grant_view'],
					'grant_upload'=>empty($data['grant_upload'])?'0':$data['grant_upload'],
					'md_regdate(=)'=>'NOW()'
				]
			);
		} else {
			if(isset($data['new_md_id']) || $module['md_key'] != 'gallery') {
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

			// 썸네일 사이즈 변경시 이전 썸네일 삭제 체크
			if(is_dir(_AF_ATTACH_DATA_.'thumbnail/'.$data['md_id'].'/')
				&& (abs($module['thumb_width']) != abs($data['thumb_width'])
					|| abs($module['thumb_height']) != abs($data['thumb_height']))
			){
				throw new Exception(getLang('invalid_value', ['thumbnail']), 4303);
			}

			DB::update(_AF_MODULE_TABLE_,
				[
					'md_key'=>'gallery',
					'md_category'=>$category,
					'md_title'=>$data['md_title'],
					'md_about'=>$data['md_about'],
					'md_manager'=>$md_manager,
					'md_file_max'=>$file_max,
					'md_file_size'=>$file_size,
					'md_file_accept'=>$file_accept,
					'md_list_count'=>$list_count,
					'thumb_width'=>abs($data['thumb_width']),
					'thumb_height'=>abs($data['thumb_height']),
					'thumb_option'=>empty($data['thumb_option'])?'0':$data['thumb_option'],
					'point_view'=>empty($data['point_view'])?'0':$data['point_view'],
					'point_write'=>empty($data['point_write'])?'0':$data['point_write'],
					'grant_list'=>empty($data['grant_list'])?'0':$data['grant_list'],
					'grant_view'=>empty($data['grant_view'])?'0':$data['grant_view'],
					'grant_upload'=>empty($data['grant_upload'])?'0':$data['grant_upload'],
					'md_extra'=>$_extras
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

/* End of file updatesetup.php */
/* Location: ./module/gallery/proc/updatesetup.php */
