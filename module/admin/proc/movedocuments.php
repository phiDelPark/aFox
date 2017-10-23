<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	if(empty($data['md_id']) || empty($data['wr_srls'])) return set_error(getLang('error_request'),4303);

	global $_MEMBER;
	$is_admin = !empty($_MEMBER) && $_MEMBER['mb_rank'] == 's';

	// 권한 체크 // 관리자만
	if(!$is_admin) return set_error(getLang('error_permitted'), 4501);

	DB::transaction();

	$tmp = explode(':', $data['md_id']);
	$md_id = trim($tmp[0]);
	$md_cate = trim($tmp[1]);
	$wr_srls = is_array($data['wr_srls']) ? $data['wr_srls'] : [$data['wr_srls']];

	try {
		$module = getDBItem(_AF_MODULE_TABLE_, ['md_key'=>'board','md_id'=>$md_id]);

		if(!empty($module['error'])) throw new Exception($module['message'], $module['error']);
		if(empty($module['md_id'])) throw new Exception(getLang('error_founded'),4201);

		$param = ['md_id'=>$md_id];
		if(!empty($md_cate)) $param['wr_category'] = $md_cate;

		DB::update(_AF_DOCUMENT_TABLE_,
			$param, [
				'wr_srl{IN}'=>implode(',', $wr_srls)
			]
		);

	} catch (Exception $ex) {
		DB::rollback();
		return set_error($ex->getMessage(),$ex->getCode());
	}

	DB::commit();

	return ['error'=>0, 'message'=>getLang('success_deleted'), 'md_id'=>$md_id];
}

/* End of file movedocuments.php */
/* Location: ./module/admin/proc/movedocuments.php */
