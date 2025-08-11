<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	if(empty($data['md_id'])) return set_error(getLang('error_request'),4303);
	if($data['md_id'] === 'welcome') return set_error(getLang('warn_not_allowable',['Welcome']),303);

	DB::transaction();

	try {
		$page = DB::get(_AF_PAGE_TABLE_, ['md_id'=>$data['md_id']]);
		if(empty($page['md_id'])) throw new Exception(getLang('error_request'),4303);

		$md_id = $page['md_id'];

		// 파일 삭제
		$types = ['binary','image','video','audio','thumbnail'];
		foreach ($types as $val) {
			unlinkAll(_AF_ATTACH_DATA_ . $val . '/' . $md_id . '/1/');
		}

		// 파일 , 페이지, 모듈 삭제
		DB::query('DELETE p, m, f FROM '._AF_PAGE_TABLE_.' AS p LEFT JOIN '._AF_MODULE_TABLE_.' AS m ON m.md_key = \'page\' AND m.md_id = p.md_id LEFT JOIN '._AF_FILE_TABLE_.' AS f ON f.md_id = p.md_id WHERE p.md_id = \'' . $md_id . '\'');

	} catch (Exception $ex) {
		DB::rollback();
		return set_error($ex->getMessage(),$ex->getCode());
	}

	DB::commit();

	return ['error'=>0, 'message'=>getLang('success_deleted')];
}

/* End of file deletepage.php */
/* Location: ./module/page/proc/deletepage.php */
