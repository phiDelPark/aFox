<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	if(empty($data['rp_srls'])) return set_error(getLang('error_request'),4303);

	global $_MEMBER;
	$is_admin = !empty($_MEMBER) && $_MEMBER['mb_rank'] == 's';

	// 권한 체크 // 관리자만
	if(!$is_admin) return set_error(getLang('error_permitted'), 4501);

	DB::transaction();

	$rp_srls = is_array($data['rp_srls']) ? $data['rp_srls'] : [$data['rp_srls']];

	try {

		DB::update(_AF_COMMENT_TABLE_,
			[
				'rp_status'=>4,
				'rp_content'=>getLang('success_deleted'),
				'mb_srl'=>'0',
				'mb_nick'=>getLang('system'),
				'mb_password'=>md5(time())
			], [
				'rp_srl{IN}'=>implode(',', $rp_srls)
			]
		);

	} catch (Exception $ex) {
		DB::rollback();
		return set_error($ex->getMessage(),$ex->getCode());
	}

	DB::commit();

	return ['error'=>0, 'message'=>getLang('success_deleted')];
}

/* End of file deletecomments.php */
/* Location: ./module/admin/proc/deletecomments.php */
