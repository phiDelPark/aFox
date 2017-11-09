<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	if(empty($data['mf_srl']) || empty($data['mf_name'])) return set_error(getLang('error_request'),4303);

	$out = getDBItem(_AF_FILE_TABLE_, ['mf_srl'=>$data['mf_srl']]);
	if(!empty($out['error'])) return set_error($out['message'],$out['error']);
	if(empty($out['mf_srl'])) return set_error(getLang('error_founded'),4201);

	$name = explode('.', $data['mf_name']);
	$ext = count($name)===1 ? 'none' : $name[count($name)-1];
	$name = count($name)===1 ? $name[0] : substr($data['mf_name'], 0, strlen('.'.$ext) * -1);
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
