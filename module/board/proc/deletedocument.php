<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	if(empty($data['wr_srl'])) return set_error(getLang('error_request'),4303);

	global $_MEMBER;

	DB::transaction();

	$wr_srl = (int) abs(empty($data['wr_srl']) ? 0 : $data['wr_srl']);

	try {
		$doc = getDBItem(_AF_DOCUMENT_TABLE_, ['wr_srl'=>$wr_srl], 'wr_srl, wr_updater, md_id, mb_srl, mb_password');

		if(!empty($doc['error'])) throw new Exception($doc['message'], $doc['error']);
		if(empty($doc['wr_srl'])) throw new Exception(getLang('error_request'),4303);

		// 권한 체크
		if(!isManager($doc['md_id'])) {
			if(empty($_MEMBER) || empty($doc['mb_srl'])) {
				if(empty($data['mb_password'])) {
					throw new Exception(getLang('request_input', ['password']), 3);
				}
				if (empty($doc['mb_password']) || !checkPassword($data['mb_password'], $doc['mb_password'])) {
					throw new Exception(getLang('error_permitted'),4501);
				}
			} else if($_MEMBER['mb_srl'] != $doc['mb_srl']) {
				throw new Exception(getLang('error_permitted'),4501);
			}
		}

		$md_id = $doc['md_id'];

		// 관리자나 자신이 아니면 휴지통
		if (!isAdmin() && (empty($_MEMBER) || $_MEMBER['mb_srl'] != $doc['mb_srl'])) {
			unset($data['is_empty']);
		}

		if(empty($data['is_empty'])) {
			// 이미 휴지통이면 에러
			if($md_id == '_AFOXtRASH_') {
				throw new Exception(getLang('error_request'),4303);
			}
			// 휴지통으로 보냄
			DB::update(_AF_DOCUMENT_TABLE_,
				[
					'md_id'=>'_AFOXtRASH_',
					'wr_updater'=>$md_id,
					'(wr_update)'=>'NOW()'
				], [
					'wr_srl'=>$wr_srl
				]
			);
		} else {
			// 완전 삭제
			// 휴지통이면 원래 모듈 id 가져오기
			if($md_id == '_AFOXtRASH_') {
				$module = getModule($doc['wr_updater']);
				if(!empty($module['error'])) throw new Exception($module['message'], $module['error']);
				if(empty($module['md_id']) || $module['md_id'] != $doc['wr_updater']) throw new Exception(getLang('invalid_value',['module']), 303);
				$md_id = $module['md_id'];
			}
			// 파일 , 댓글 , 문서 삭제
			DB::delete(_AF_COMMENT_TABLE_,['wr_srl'=>$wr_srl]);
			DB::delete(_AF_FILE_TABLE_,['md_id'=>$md_id,'mf_target'=>$wr_srl]);
			DB::delete(_AF_DOCUMENT_TABLE_,['wr_srl'=>$wr_srl]);
		}

		// 파일 삭제 // 휴지통 이동이면 썸네일만 제거
		$variable = empty($data['is_empty']) ? ['thumbnail'] : ['binary','image','video','audio','thumbnail'];
		foreach ($variable as $val) {
			unlinkAll(_AF_ATTACH_DATA_ . $val . '/' . $md_id . '/' . $wr_srl . '/');
		}

	} catch (Exception $ex) {
		DB::rollback();
		return set_error($ex->getMessage(),$ex->getCode());
	}

	DB::commit();

	return ['error'=>0, 'message'=>getLang('success_deleted'), 'wr_srl'=>$wr_srl];
}

/* End of file deletedocument.php */
/* Location: ./module/board/proc/deletedocument.php */
