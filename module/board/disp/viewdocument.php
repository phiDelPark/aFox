<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	global $_MEMBER;

	$item = getDocument($data['srl'], true);
	if(empty($item['error'])) {
		// 자신 글이면 권한 체크 패스
		if(empty($_MEMBER['mb_srl']) || $_MEMBER['mb_srl'] != $item['mb_srl']) {
			// 권한 체크
			if(!isGrant($data['id'], 'view') && !isManager($data['id'])) {
				return set_error(getLang('msg_not_permitted'),901);
			}
		}
		$cpage = empty($data['cpage']) ? '' : $data['cpage'];
		$item['CURRENT_COMMENT_LIST'] = getCommentList($data['srl'], $cpage);

		$category = empty($data['category']) ? '' : $data['category'];
		$search = empty($data['search']) ? '' : $data['search'];
		$page = empty($data['page']) ? '' : $data['page'];
		$list = getDocumentList($data['id'], $page, $search, $category);
		$item['CURRENT_DOCUMENT_LIST'] = $list;
	}

	return $item;
}

/* End of file viewdocument.php */
/* Location: ./module/board/disp/viewdocument.php */