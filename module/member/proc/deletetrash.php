<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	global $_MEMBER;
	if(empty($data['wr_srl']) || empty($_MEMBER['mb_srl'])) return set_error(getLang('error_request'),4303);
	$wr_srls = is_array($data['wr_srl']) ? $data['wr_srl'] : [$data['wr_srl']];
	$mb_srl = $_MEMBER['mb_srl'];

	DB::transaction();

	try {

		foreach ($wr_srls as $wr_srl) {

			$doc = DB::get(_AF_DOCUMENT_TABLE_, ['md_id'=>'_AFOXtRASH_','wr_srl'=>$wr_srl,'mb_srl'=>$mb_srl]);
			if(empty($doc['wr_srl'])) throw new Exception(getLang('error_request'),4303);

			$module = getModule($doc['wr_updater']);
			if(empty($module)) throw new Exception(getLang('error_founded'), 4201);
			if($module['md_id'] != $doc['wr_updater']) throw new Exception(getLang('invalid_value',['module']), 2001);
			$md_id = $module['md_id'];

			// 파일 , 댓글 , 문서 삭제
			DB::delete(_AF_COMMENT_TABLE_,['wr_srl'=>$wr_srl]);
			DB::delete(_AF_FILE_TABLE_,['md_id'=>$md_id,'mf_target'=>$wr_srl]);
			DB::delete(_AF_DOCUMENT_TABLE_,['wr_srl'=>$wr_srl]);
			// 파일 삭제
			$types = ['binary','image','video','audio','thumbnail'];
			foreach ($types as $val) {
				unlinkAll(_AF_ATTACH_DATA_ . $val . '/' . $md_id . '/' . $wr_srl . '/');
			}
		}

	} catch (Exception $ex) {
		DB::rollback();
		return set_error($ex->getMessage(),$ex->getCode());
	}

	DB::commit();

	return ['error'=>0, 'message'=>getLang('success_deleted')];
}

/* End of file deletetrash.php */
/* Location: ./module/member/proc/deletetrash.php */
