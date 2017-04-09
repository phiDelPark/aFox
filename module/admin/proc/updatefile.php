<?php

if(!defined('__AFOX__')) exit();

function proc($data) {

	if(empty($data['mf_srl'])) return set_error(getLang('error_request'),4303);

	global $_MEMBER;
	$is_admin = !empty($_MEMBER) && $_MEMBER['mb_rank'] == 's';
	// 권한 체크 // 관리자만
	if(!$is_admin) return set_error(getLang('error_permit'), 4501);

	$out = getDBItem(_AF_FILE_TABLE_, ['mf_srl'=>$data['mf_srl']]);
	if(!empty($out['error'])) return set_error($out['message'],$out['error']);
	if(empty($out['mf_srl'])) return set_error(getLang('error_founded'),4201);

	$name = explode('.', $data['mf_name']);
	$ext = count($name)===1 ? 'none' : $name[count($name)-1];
	$name = $name[0];
	$ext = preg_replace('/\.(php|phtm|phar|html?|cgi|pl|exe|jsp|asp|inc)/i', '$0-x', ('.'.$ext));

	DB::transaction();

	try {

		DB::update(_AF_FILE_TABLE_,
			[
				'mf_name'=>$name . $ext,
				'mf_description'=>$data['mf_description']
			], [
				'mf_srl'=>$data['mf_srl']
			]
		);

	} catch (Exception $ex) {
		DB::rollback();
		return set_error($ex->getMessage(),$ex->getCode());
	}

	DB::commit();

	return ['error'=>0, 'message'=>getLang('success_saved')];
}

/* End of file updatefile.php */
/* Location: ./module/admin/proc/updatefile.php */