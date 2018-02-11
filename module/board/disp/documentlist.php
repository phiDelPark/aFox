<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	// 권한 체크
	if(!isGrant('list', $data['id']) && !isManager($data['id'])) {
		return set_error(getLang('error_permitted'),4501);
	}

	$category = empty($data['category']) ? '' : $data['category'];
	$search = empty($data['search']) ? '' : $data['search'];
	$page = empty($data['page']) ? '' : $data['page'];

	$result = [];
	$result['tpl'] = 'list';
	$result['_DOCUMENT_LIST_'] = getDocumentList($data['id'], $page, $search, $category);
	$result['_COMMENT_LIST_'] = [];

	return $result;
}

/* End of file documentlist.php */
/* Location: ./module/board/disp/documentlist.php */
