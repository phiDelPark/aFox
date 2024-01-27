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

	$count = empty($_CFG['md_list_count']) ? 20 : $_CFG['md_list_count'];

	$result = [];
	$result['tpl'] = 'list';
	$result['_DOCUMENT_LIST_'] = getDocumentList($data['id'], $count, $page, $search, $category);
	$result['_COMMENT_LIST_'] = [];

	return $result;
}

/* End of file documentlist.php */
/* Location: ./module/board/disp/documentlist.php */
