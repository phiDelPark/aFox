<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	// 권한 체크
	if(!isGrant('list', $data['id']) && !isManager($data['id'])) {
		return set_error(getLang('error_permitted'),4501);
	}

	global $_CFG;

	$category = empty($data['category']) ? '' : $data['category'];
	$search = empty($data['search']) ? '' : $data['search'];
	$page = empty($data['page']) ? '' : $data['page'];
	$asc = isset($data['asc']);

	$count = empty($_CFG['md_list_count']) ? 20 : $_CFG['md_list_count'];
	$_list = getDocumentList($data['id'], $count, $page, $search, $category, 'wr_regdate'.($asc?' ASC':''));

	$result = [];
	$result['tpl'] = 'list';
	$result['list'] = $_list['list'];
	$result['total_count'] = $_list['total_count'];
	$result['total_page'] = $_list['total_page'];
	$result['current_page'] = $_list['current_page'];
	$result['replys'] = [];

	return $result;
}

/* End of file list.php */
/* Location: ./module/board/disp/list.php */
