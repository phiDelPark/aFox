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

	$result = getDocumentList($data['id'], $page, $search, $category);
	$result['tpl'] = 'list';

	return $result;
}

/* End of file documentlist.php */
/* Location: ./module/board/disp/documentlist.php */
