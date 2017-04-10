<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	if(empty($data['rp_srl'])) return set_error(getLang('error_request'),4303);

	global $_MEMBER;

	DB::transaction();

	$rp_srl = (int) abs(empty($data['rp_srl']) ? 0 : $data['rp_srl']);

	try {
		$cmt = getDBItem(_AF_COMMENT_TABLE_, ['rp_srl'=>$rp_srl], 'wr_srl, rp_status, rp_parent, rp_depth, mb_srl, mb_password');
		if(!empty($cmt['error'])) throw new Exception($cmt['message'], $cmt['error']);
		if(empty($cmt['wr_srl'])) throw new Exception(getLang('error_founded'), 4201);

		$doc = getDBItem(_AF_DOCUMENT_TABLE_, ['wr_srl'=>$cmt['wr_srl']], 'md_id, wr_srl');
		if(!empty($doc['error'])) throw new Exception($doc['message'], $doc['error']);
		if(empty($doc['wr_srl']) || ($doc['wr_srl'] != $cmt['wr_srl'])) throw new Exception(getLang('error_request'),4303);

		$wr_srl = $doc['wr_srl'];
		$is_manager = isManager($doc['md_id']);

		// 권한 체크
		if(!$is_manager) {
			if(empty($_MEMBER) || empty($cmt['mb_srl'])) {
				if(empty($data['mb_password'])) {
					throw new Exception(getLang('request_input', ['password']), 3);
				}
				if (empty($cmt['mb_password']) || !verifyEncrypt($data['mb_password'], $cmt['mb_password'])) {
					throw new Exception(getLang('error_permit'),4501);
				}
			} else if($_MEMBER['mb_srl'] != $cmt['mb_srl']) {
				throw new Exception(getLang('error_permit'),4501);
			}
		}

		$_cnt = DB::count(_AF_COMMENT_TABLE_, [
			'wr_srl'=>$cmt['wr_srl'],
			'rp_srl{<>}'=>$rp_srl,
			'rp_parent'=>$cmt['rp_parent'],
			'rp_depth{LIKE}'=>empty($cmt['rp_depth'])?null:$cmt['rp_depth'].'%'
		]);
		if ($_cnt > 0 && (!$is_manager||$cmt['rp_status'] == 4)) throw new Exception(getLang('msg_reply_exists'), 4501);

		if($_cnt > 0 && $is_manager) {
			DB::update(_AF_COMMENT_TABLE_,
				[
					'rp_status'=>4,
					'rp_content'=>getLang('msg_is_deleted'),
					'mb_srl'=>'0',
					'mb_nick'=>getLang('system'),
					'mb_password'=>md5(time())
				], [
					'rp_srl'=>$rp_srl
				]
			);
		} else {
			DB::delete(_AF_COMMENT_TABLE_,
				[
					'rp_srl'=>$rp_srl
				]
			);

			setHistoryAction('wr_reply', $wr_srl, false, function($v)use($wr_srl){
				DB::update(
					_AF_DOCUMENT_TABLE_,
					['(wr_reply)'=>'wr_reply-1'],
					['wr_srl'=>$wr_srl]
				);
			});
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