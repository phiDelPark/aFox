<?php if(!defined('__AFOX__')) exit();

function proc($data) {
	if(empty($data['md_id']) || empty($data['mf_about']) || empty($data['mf_srls']))
	return set_error(getLang('error_request'),4303);

	$module = getModule($data['md_id']);
	if(empty($module)) return set_error(getLang('error_founded'), 4201);

	$data['wr_category'] = $data['wr_tags'] = '';
	$wr_tags = explode(',', $data['mf_about']);
	if(!empty($module['md_category'])) {
		$md_categorys = explode(',', $module['md_category']);
		foreach ($wr_tags as $value) {
			if(!in_array($value, $md_categorys)) {
				return set_error(getLang('invalid_value', ['category']), 2001);
			}
		}
		$data['wr_tags'] = implode(',',$wr_tags);
	}
	if(!$data['wr_tags']) return set_error(getLang('request_input',['category']), 1);

	$srls = [];
	$mf_srls = explode(',', $data['mf_srls']);
	foreach ($mf_srls as $value) {
		if($value = trim($value)) $srls[] = $value;
	}
	if(!count($srls)) return set_error(getLang('warn_selected', ['image']),4303);

	DB::transaction();

	try {
		DB::update(_AF_FILE_TABLE_, ['mf_about'=>$data['wr_tags']], ['md_id'=>$data['md_id'],'mf_srl{IN}'=>implode(',', $srls)]);
	} catch (Exception $ex) {
		DB::rollback();
		return set_error($ex->getMessage(),$ex->getCode());
	}

	DB::commit();

	return ['error'=>0, 'message'=>getLang('success_deleted')];
}

/* End of file getfiles.php */
/* Location: ./module/gallery/proc/getfiles.php */
