<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	$doc = getDocument($data['srl'], true);

	if(empty($doc['error'])) {
	global $_MEMBER;
		// 권한 체크
		if(!isManager($doc['md_id'])) {
			if(!isGrant($doc['md_id'], 'view')) {
				return set_error(getLang('msg_not_permitted'),901);
			}
			// 비밀글이면
			if($doc['wr_secret'] == '1') {
				// 권한 체크
				if(empty($_MEMBER) || empty($doc['mb_srl'])) {
					if(empty($data['mb_password'])) {
						return set_error('<form class="input-password" method="post" autocomplete="off">'
										.sprintf(getLang('warn_input'), getLang('password'))
										.'<div class="input-group" style="margin-top:10px"><input class="form-control" name="mb_password" type="password" placeholder="'. getLang('password').'" required>'
										.'<span class="input-group-btn"><button class="btn btn-default" type="submit">'. getLang('ok').'</button></span></div></form>'
									, 90);
					}
					if (empty($doc['mb_password']) || !verifyEncrypt($data['mb_password'], $doc['mb_password'])) {
						return set_error(getLang('msg_not_permitted'), 901);
					} else {
						$GLOBALS['_PERMIT_VIEW_'][md5($doc['md_id'].'_'.$data['srl'])] = true;
					}
				} else if($_MEMBER['mb_srl'] != $doc['mb_srl']) {
					return set_error(getLang('msg_not_permitted'), 901);
				}
			}
		}

		$cpage = empty($data['cpage']) ? '' : $data['cpage'];
		$doc['CURRENT_COMMENT_LIST'] = getCommentList($data['srl'], $cpage);

		$category = empty($data['category']) ? '' : $data['category'];
		$search = empty($data['search']) ? '' : $data['search'];
		$page = empty($data['page']) ? '' : $data['page'];
		$list = getDocumentList($doc['md_id'], $page, $search, $category);
		$doc['CURRENT_DOCUMENT_LIST'] = $list;
	}

	return $doc;
}

/* End of file viewdocument.php */
/* Location: ./module/board/disp/viewdocument.php */