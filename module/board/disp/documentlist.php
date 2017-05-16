<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	// 권한 체크
	if(!isGrant($data['id'], 'list') && !isManager($data['id'])) {
		return set_error(getLang('error_permitted'),88088);
	}

	$category = empty($data['category']) ? '' : $data['category'];
	$search = empty($data['search']) ? '' : $data['search'];
	$page = empty($data['page']) ? '' : $data['page'];
	return getDocumentList($data['id'], $page, $search, $category);
}

/* End of file documentlist.php */
/* Location: ./module/board/disp/documentlist.php */