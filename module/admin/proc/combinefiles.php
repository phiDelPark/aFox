<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	// 권한 체크 // 관리자만
	if(!isAdmin()) return set_error(getLang('error_permitted'), 4501);

	$srls = [];

	for ($i=0; $i < 99; $i++) {
		if(isset($data['mf_standard'.$i]) && isset($data['mf_srls'.$i])){
			$standard = DB::get(_AF_FILE_TABLE_, 'mf_srl', ['mf_srl'=>$data['mf_standard'.$i]]);
			if(empty($standard)) return set_error(getLang('error_request'),4201);
			$mf_srls = is_array($data['mf_srls'.$i]) ? $data['mf_srls'.$i] : explode(',', $data['mf_srls'.$i]);
			if(count($mf_srls)>1) $srls[$standard['mf_srl']] = $mf_srls;
		}
	}

	if(count($srls)===0) return set_error(getLang('error_request'),4201);

	DB::transaction();
	$success_srls = [];

	try {

		foreach ($srls as $key => $vals) {
			foreach ($vals as $srl) {
				$file = DB::get(_AF_FILE_TABLE_, ['mf_srl'=>$srl]);
				if(empty($file)) continue;
				if($srl == $key) continue;
				$success_srls[$file['mf_target']] = $file['md_id'];
				$_file_types = array('binary'=>0, 'image' => 1, 'video' => 2, 'audio' => 3);
				$filetype = explode('/', $file['mf_type']);
				$filetype = strtolower(array_shift($filetype));
				$filetype = isset($_file_types[$filetype]) ? $filetype : 'binary';
				$unfilename = _AF_ATTACH_DATA_ . $filetype . '/' . $file['md_id'] . '/' . $file['mf_target'] . '/' . $file['mf_upload_name'];
				if(!file_exists($unfilename) || unlinkFile($unfilename)) {
					DB::update(_AF_FILE_TABLE_, ['mf_link'=>1,'mf_upload_name'=>$key], ['mf_srl'=>$srl]);
				}
			}
		}

		foreach ($success_srls as $mf_target => $md_id){
			unlinkAll(_AF_ATTACH_DATA_.'thumbnail'.'/'.$md_id.'/'.$mf_target.'/');
		}

	} catch (Exception $ex) {
		DB::rollback();
		return set_error($ex->getMessage(),$ex->getCode());
	}

	DB::commit();

	return ['error'=>0, 'message'=>getLang('success_finished')];
}

/* End of file combinefiles.php */
/* Location: ./module/admin/proc/combinefiles.php */