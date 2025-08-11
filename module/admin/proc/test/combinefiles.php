<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	if(empty($data['mf_srls']) || empty($data['mf_standard'])) {
		return set_error(getLang('error_request'),4303);
	}

	// 권한 체크 // 관리자만
	if(!isAdmin()) return set_error(getLang('error_permitted'), 4501);

	$standard = DB::get(_AF_FILE_TABLE_, 'mf_srl', ['mf_srl'=>$data['mf_standard']]);
	if(empty($standard)) return set_error(getLang('error_founded'),4201);

	DB::transaction();

	$st_srl = $standard['mf_srl'];
	$mf_srls = is_array($data['mf_srls']) ? $data['mf_srls'] : explode($data['mf_srls'], ',');

	try {

		foreach ($mf_srls as $srl) {
			if($srl == $st_srl) continue;
			$file = DB::get(_AF_FILE_TABLE_, ['mf_srl'=>$srl]);
			if(empty($file)) continue;
				$_file_types = array('binary'=>0, 'image' => 1, 'video' => 2, 'audio' => 3);
				$filetype = explode('/', $file['mf_type']);
				$filetype = strtolower(array_shift($filetype));
				$filetype = empty($_file_types[$filetype]) ? 'binary' : $filetype;
				$unfilename = _AF_ATTACH_DATA_ . $filetype . '/' . $file['md_id'] . '/' . $file['mf_target'] . '/' . $file['mf_upload_name'];
				if(!file_exists($unfilename) || unlinkFile($unfilename)) {
					DB::update(_AF_FILE_TABLE_, ['mf_link'=>1,'mf_upload_name'=>$st_srl], ['mf_srl'=>$srl]);
				}
		}

	} catch (Exception $ex) {
		DB::rollback();
		return set_error($ex->getMessage(),$ex->getCode());
	}

	DB::commit();

	return ['error'=>0, 'message'=>getLang('success_finished')];
}

/* End of file deletefiles.php */
/* Location: ./module/admin/proc/deletefiles.php */
