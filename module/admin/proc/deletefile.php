<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	if(empty($data['mf_srl'])) return set_error(getLang('error_request'),4303);

	// 권한 체크 // 관리자만
	if(!isAdmin()) return set_error(getLang('error_permitted'), 4501);

	$file = getDBItem(_AF_FILE_TABLE_, ['mf_srl'=>$data['mf_srl']]);
	if(!empty($file['error'])) return set_error($file['message'],$file['error']);
	if(empty($file['mf_srl'])) return set_error(getLang('error_founded'),4201);

	DB::transaction();

	try {
		$_file_types = array('binary'=>0, 'image' => 1, 'video' => 2, 'audio' => 3);
		$filetype = strtolower(array_shift(explode('/', $file['mf_type'])));
		$filetype = empty($_file_types[$filetype]) ? 'binary' : $filetype;
		$unfilename = _AF_ATTACH_DATA_ . $filetype . '/' . $file['md_id'] . '/' . $file['mf_target'] . '/' . $file['mf_upload_name'];

		if(!file_exists($unfilename) || unlinkFile($unfilename)) {
			DB::delete(_AF_FILE_TABLE_,['mf_srl'=>$file['mf_srl']]);
		}

	} catch (Exception $ex) {
		DB::rollback();
		return set_error($ex->getMessage(),$ex->getCode());
	}

	DB::commit();

	return ['error'=>0, 'message'=>getLang('success_deleted')];
}

/* End of file deletefile.php */
/* Location: ./module/admin/proc/deletefile.php */
