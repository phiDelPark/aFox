<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	if(empty($data['rp_srl'])) return set_error(getLang('error_request'),4303);
	$rp_srl = (int) abs(empty($data['rp_srl']) ? 0 : $data['rp_srl']);

	global $_MEMBER;

	$cmt = DB::get(_AF_COMMENT_TABLE_, 'wr_srl, rp_status, rp_parent, rp_depth, mb_srl, mb_password', ['rp_srl'=>$rp_srl]);
	if(empty($cmt['wr_srl'])) return set_error(getLang('error_founded'), 4201);

	$doc = DB::get(_AF_DOCUMENT_TABLE_, 'md_id, wr_srl', ['wr_srl'=>$cmt['wr_srl']]);
	if(empty($doc['wr_srl']) || ($doc['wr_srl'] != $cmt['wr_srl'])) return set_error(getLang('error_request'),4303);

	$wr_srl = $doc['wr_srl'];
	$is_manager = isManager($doc['md_id']);

	// 권한 체크
	if(!$is_manager) {
		if(empty($_MEMBER) || empty($cmt['mb_srl'])) {
			if(empty($cmt['mb_srl']) && empty($data['mb_password'])) {
				return set_error(getLang('request_input', ['password']), 1);
			}
			if (empty($cmt['mb_password']) || !checkPassword($data['mb_password'], $cmt['mb_password'])) {
				return set_error(getLang('error_permitted'),4501);
			}
		} else if($_MEMBER['mb_srl'] != $cmt['mb_srl']) {
			return set_error(getLang('error_permitted'),4501);
		}
	}

	$cmt['rp_depth'] = empty($cmt['rp_depth'])?null:$cmt['rp_depth'];
	$_cnt = DB::count(_AF_COMMENT_TABLE_, [
		'wr_srl'=>$cmt['wr_srl'],
		'rp_srl{<>}'=>$rp_srl,
		'rp_parent'=>$cmt['rp_parent'],
		empty($cmt['rp_depth'])?'':'rp_depth{LIKE}'=>$cmt['rp_depth'].'%'
	]);
	if (!$is_manager && $_cnt > 0) return set_error(getLang('msg_reply_exists'), 4501);

	DB::transaction();

	try {
		// 하위 댓글이 있으면 삭제 표시만... (관리자전용)
		if($_cnt > 0) {
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

			//TODO History를 검색해서 포인트 있으면 회수 코드 작성하기

			DB::update(_AF_DOCUMENT_TABLE_, ['^wr_reply'=>'wr_reply-1'], ['wr_srl'=>$wr_srl]);
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
