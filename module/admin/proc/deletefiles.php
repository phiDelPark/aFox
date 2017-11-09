<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	if(empty($data['mf_srls'])) return set_error(getLang('error_request'),4303);

	// 권한 체크 // 관리자만
	if(!isAdmin()) return set_error(getLang('error_permitted'), 4501);

	DB::transaction();

	$mf_srls = is_array($data['mf_srls']) ? $data['mf_srls'] : [$data['mf_srls']];

	try {

		$callback = function($r) {
			while ($row = mysqli_fetch_assoc($r)) {
				$_file_types = array('binary'=>0, 'image' => 1, 'video' => 2, 'audio' => 3);
				$filetype = strtolower(array_shift(explode('/', $row['mf_type'])));
				$filetype = empty($_file_types[$filetype]) ? 'binary' : $filetype;
				$unfilename = _AF_ATTACH_DATA_ . $filetype . '/' . $row['md_id'] . '/' . $row['mf_target'] . '/' . $row['mf_upload_name'];

				if(!file_exists($unfilename) || unlinkFile($unfilename)) {
					DB::delete(_AF_FILE_TABLE_,['mf_srl'=>$row['mf_srl']]);
				}
			}
			return [];
		};

		DB::getList('SELECT * FROM '._AF_FILE_TABLE_.' WHERE mf_srl IN ('.implode(',', $mf_srls).')', [], $callback);

	} catch (Exception $ex) {
		DB::rollback();
		return set_error($ex->getMessage(),$ex->getCode());
	}

	DB::commit();

	return ['error'=>0, 'message'=>getLang('success_deleted')];
}

/* End of file deletefiles.php */
/* Location: ./module/admin/proc/deletefiles.php */
