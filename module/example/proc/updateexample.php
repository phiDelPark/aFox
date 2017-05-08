<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	global $_MEMBER;
	// 관리자만 접근 가능
	if(empty($_MEMBER) || $_MEMBER['mb_rank'] != 's') return set_error(getLang('error_founded'),4201);

	if(isset($data['new_md_id'])) $data['md_id'] = $data['new_md_id'];
	if(empty($data['md_id'])) return set_error(getLang('error_request'),4303);

	// md_id 검사 정규식 (영문, 숫자, 언더바(_)만 가능)
	if(!preg_match('/^[a-zA-Z]+\w{2,}$/', $data['md_id'])) {
		return set_error(getLang('invalid_value', ['id']),701);
	}

	$md_extra = [];
	$data['md_title'] = trim($data['md_title']);
	$data['md_list_count'] = empty($data['md_list_count']) ? 20 : abs($data['md_list_count']);

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
					'md_key'=>'example',
					'(md_regdate)'=>'NOW()'
				]
			);
		} else {
			if(isset($data['new_md_id'])) {
				throw new Exception(getLang('error_exists'), 4251);
			}

			DB::update(_AF_MODULE_TABLE_,
				[
					'md_key'=>'example'
				], [
					'md_id'=>$data['md_id']
				]
			);
		}

		// 썸네일 제거
		unlinkAll(_AF_ATTACH_DATA_.'thumbnail/'.$data['md_id'].'/');

	} catch (Exception $ex) {
		DB::rollback();

		// myisam면 rollback 수동으로 해야됨
		if(DB::engine(_AF_MODULE_TABLE_) === 'myisam') {

		}

		return set_error($ex->getMessage(),$ex->getCode());
	}

	DB::commit();

	return ['error'=>0, 'message'=>getLang('success_saved')];
}

/* End of file updateexample.php */
/* Location: ./module/example/proc/updateexample.php */