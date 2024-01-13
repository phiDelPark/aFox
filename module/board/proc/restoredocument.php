<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	if(empty($data['wr_srl'])) return set_error(getLang('error_request'),4303);
	$wr_srl = (int) abs(empty($data['wr_srl']) ? 0 : $data['wr_srl']);

	global $_MEMBER;

	$doc = DB::get(_AF_DOCUMENT_TABLE_, 'wr_srl, wr_updater, md_id, mb_srl, mb_password', ['wr_srl'=>$wr_srl]);
	if(empty($doc['wr_srl'])) return set_error(getLang('error_request'),4303);

	$module = getModule($doc['wr_updater']);
	// 모듈이 없으면 에러
	if(empty($module)) return set_error(getLang('error_founded'), 4201);
	if($module['md_id'] != $doc['wr_updater']) return set_error(getLang('invalid_value',['module']), 2001);

	// 권한 체크
	if(empty($_MEMBER)) {
		if(empty($data['mb_password'])) {
			return set_error(getLang('request_input', ['password']), 1);
		}
		if (empty($doc['mb_password']) || !checkPassword($data['mb_password'], $doc['mb_password'])) {
			return set_error(getLang('error_permitted'),4501);
		}
	} else if(!isManager($doc['md_id'])) {
		if($_MEMBER['mb_srl'] != $doc['mb_srl']) {
			return set_error(getLang('error_permitted'),4501);
		}
	}

	DB::transaction();

	try {

		// 복구
		DB::update(_AF_DOCUMENT_TABLE_,
			[
				'md_id'=>$doc['wr_updater'],
				'wr_updater'=>'',
				'^wr_update'=>'NOW()'
			], [
				'wr_srl'=>$wr_srl
			]
		);
	} catch (Exception $ex) {
		DB::rollback();
		return set_error($ex->getMessage(),$ex->getCode());
	}

	DB::commit();

	return ['error'=>0, 'message'=>getLang('success_deleted'), 'wr_srl'=>$wr_srl];
}

/* End of file restoredocument.php */
/* Location: ./module/board/proc/restoredocument.php */
