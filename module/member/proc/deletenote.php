<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	global $_MEMBER;
	if(empty($data['nt_srl']) || empty($_MEMBER['mb_srl'])) return set_error(getLang('error_request'),4303);
	$nt_srls = is_array($data['nt_srl']) ? $data['nt_srl'] : [$data['nt_srl']];

	DB::transaction();

	try {

		DB::delete(_AF_NOTE_TABLE_,[
			'nt_srl{IN}'=>implode(',', $nt_srls),
			'mb_srl'=>$_MEMBER['mb_srl']
		]);

	} catch (Exception $ex) {
		DB::rollback();
		return set_error($ex->getMessage(),$ex->getCode());
	}

	DB::commit();

	return ['error'=>0, 'message'=>getLang('success_deleted')];
}

/* End of file deletenote.php */
/* Location: ./module/member/proc/deletenote.php */