<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	if(empty($data['md_id'])) return set_error(getLang('error_request'),4303);

	// 권한 체크 // 관리자만
	if(!isAdmin()) return set_error(getLang('error_permitted'), 4501);

	$srls = [];
	$mf_srls = explode(',', $data['mf_srls']);
	foreach ($mf_srls as $value) {
		if($value = trim($value)) $srls[] = $value;
	}
	if(!count($srls)) return set_error(getLang('warn_selected', ['image']),4303);

	$module = getModule($data['md_id']);
	if(empty($module)) return set_error(getLang('error_founded'), 4201);

	DB::transaction();

	try {
		$callback = function($r) {
			$file_types = array('binary'=>0, 'image' => 1, 'video' => 2, 'audio' => 3);
			while ($row = DB::fetch($r)) {
				$ftype = explode('/', strtolower($row['mf_type']));
				$ftype = empty($file_types[$ftype[0]]) ? 'binary' : $ftype[0];
				$unfilename = _AF_ATTACH_DATA_.$ftype.'/'.$row['md_id'].'/'.$row['mf_target'].'/'.$row['mf_upload_name'];
				if(!file_exists($unfilename) || unlinkFile($unfilename)) {
					DB::delete(_AF_FILE_TABLE_,['mf_srl'=>$row['mf_srl']]);
					@unlinkAll(_AF_ATTACH_DATA_.'thumbnail/'.$row['md_id'].'/'.$row['mf_target'].'/'.$row['mf_srl'].'/');
				}
			}
			return [];
		};

		DB::query('SELECT * FROM '._AF_FILE_TABLE_.' WHERE md_id=\''.$data['md_id'].'\' AND mf_srl IN ('.implode(',', $srls).')', $callback);

	} catch (Exception $ex) {
		DB::rollback();
		return set_error($ex->getMessage(),$ex->getCode());
	}

	DB::commit();

	return ['error'=>0, 'message'=>getLang('success_deleted')];
}

/* End of file deletefiles.php */
/* Location: ./module/gallery/proc/deletefiles.php */
