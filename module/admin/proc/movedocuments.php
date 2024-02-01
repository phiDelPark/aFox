<?php if(!defined('__AFOX__')) exit();

function proc($data) {
	if(empty($data['md_id']) || empty($data['wr_srls'])) return set_error(getLang('error_request'),4303);

	// 권한 체크 // 관리자만
	if(!isAdmin()) return set_error(getLang('error_permitted'), 4501);

	DB::transaction();

	$tmp = explode(':', $data['md_id']);
	$md_id = trim($tmp[0]);
	$md_cate = count($tmp) > 1 ? trim($tmp[1]) : '';
	$wr_srls = is_array($data['wr_srls']) ? $data['wr_srls'] : [$data['wr_srls']];

	try {
		$module = DB::get(_AF_MODULE_TABLE_, ['md_key'=>'board','md_id'=>$md_id]);
		if (empty($module['md_id'])) throw new Exception(getLang('error_founded'),4201);

		if (!empty($module['md_category'])) {
			if (empty($md_cate)) throw new Exception(getLang('request_input', ['category']), 1);
			$md_categorys = explode(',', $module['md_category']);
			if (!in_array($md_cate, $md_categorys)) {
				throw new Exception(getLang('warning_not_exists', [$md_cate]), 3105);
			}
		}

		$param = ['md_id'=>$md_id];
		if(!empty($md_cate)) $param['wr_category'] = $md_cate;

		$source_md_id = '';
		$source_wr_srl = '';

		foreach ($wr_srls as $wr_srl)
		{
			$result = DB::get(_AF_DOCUMENT_TABLE_, 'md_id,mb_srl', ['wr_srl'=>$wr_srl]);
			if (empty($result['md_id'])) throw new Exception(getLang('error_founded'),4201);
			$source_md_id = $result['md_id'];

			// 파일 이동
			$variable = ['binary','image','video','audio','thumbnail'];
			foreach ($variable as $val) {
				$s = _AF_ATTACH_DATA_ . $val . '/' . $source_md_id . '/' . $wr_srl . '/';
				$t = _AF_ATTACH_DATA_ . $val . '/' . $md_id . '/' . $wr_srl . '/';

				if(!is_dir(dirname($s))) continue;

				// 이동할 폴더가 이미 있으면 에러
				if(is_dir(dirname($t))) {
					throw new Exception('UPLOAD_ERR_CODE(7)',10407);
				}

				// 에러시 다시 돌리기 위해 여기서 입력
				$source_wr_srl = $wr_srl;

				if(mkdir($t, _AF_DIR_PERMIT_, true))
				{
					$dir = opendir($s);
					while (false !== ($filename = readdir($dir))) {
						if($filename == '.' || $filename == '..')
							continue;
						copy($s.$filename, $t.$filename);
					}
					unlinkAll(_AF_ATTACH_DATA_ . $val . '/' . $source_md_id . '/');
				}
			}

			DB::update(_AF_FILE_TABLE_,
				$param, [
					'mf_target'=>$wr_srl
				]
			);

			DB::update(_AF_DOCUMENT_TABLE_,
				$param, [
					//'wr_srl{IN}'=>implode(',', $wr_srls)
					'wr_srl'=>$wr_srl
				]
			);

			$source_md_id = '';
			$source_wr_srl = '';
		}

	} catch (Exception $ex) {
		//DB::rollback(); //롤백 안하고 이미 이동된거 그냥둠
		// 에러난 문서의 이동된 파일 다시 돌리기
		if (!empty($md_id) && !empty($source_wr_srl))
		{
			$variable = ['binary','image','video','audio','thumbnail'];
			foreach ($variable as $val) {
				$s = _AF_ATTACH_DATA_ . $val . '/' . $source_md_id . '/' . $source_wr_srl . '/';
				$t = _AF_ATTACH_DATA_ . $val . '/' . $md_id . '/' . $source_wr_srl . '/';

				if(!is_dir(dirname($t))) continue;

				if(mkdir($s, _AF_DIR_PERMIT_, true))
				{
					$dir = opendir($t);
					while (false !== ($filename = readdir($dir))) {
						if($filename == '.' || $filename == '..')
							continue;
						copy($t.$filename, $s.$filename);
					}
					unlinkAll(_AF_ATTACH_DATA_ . $val . '/' . $md_id . '/');
				}
			}
		}
		return set_error($ex->getMessage(),$ex->getCode());
	}

	DB::commit();

	return ['error'=>0, 'message'=>getLang('success_deleted'), 'md_id'=>$md_id];
}

/* End of file movedocuments.php */
/* Location: ./module/admin/proc/movedocuments.php */
