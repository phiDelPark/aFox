<?php

if(!defined('__AFOX__')) exit();

function proc($data){
	if(empty($data['mb_id'])){
		return set_error(getLang('error_request'),4303);
	}

	$mb_id = $data['mb_id'];

	$member = DB::get(_AF_MEMBER_TABLE_, ['mb_id'=>$mb_id]);
	if(empty($member['mb_srl'])){
		return set_error(getLang('error_request'),4303);
	}

	$is_admin = isAdmin();

	if(!$is_admin || $member['mb_rank'] === 's'){
		return set_error(getLang('error_permitted'),4501);
	}

	DB::transaction();

	try{
		DB::update(_AF_MEMBER_TABLE_,
			['mb_id'=>'@'.$mb_id],
			['mb_id'=>$mb_id]
		);
	}catch(Exception $ex){
		DB::rollback();
		return set_error($ex->getMessage(),$ex->getCode());
	}

	DB::commit();

	return ['error'=>'0', 'message'=>getLang('success_ban_login')];
}

/* End of file logout.php */
/* Location: ./module/member/proc/logout.php */