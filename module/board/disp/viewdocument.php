<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	$doc = getDocument($data['srl'], true);
	if(!empty($doc['error'])) return set_error(getLang($doc['message']),$doc['error']);
	if(empty($doc['wr_srl'])) return set_error(getLang('error_founded'),4201);

	global $_MEMBER;
	$is_manager = isManager($doc['md_id']);
	$is_secret = $doc['wr_secret'] == '1';
	$PERMIT_KEY = md5($doc['md_id'].'_'.$doc['wr_srl']);

	// 권한 체크
	if(!$is_manager) {

		if(!isGrant($doc['md_id'], 'view')) {
			set_cookie('_AF_PERMIT_VIEW_'.$PERMIT_KEY, false, -1);
			return set_error(getLang('error_permit'),88088);
		}

		if(empty($_MEMBER) || $_MEMBER['mb_srl'] != $doc['mb_srl']) {

			$GLOBALS['_PERMIT_VIEW_'][$PERMIT_KEY] = get_cookie('_AF_PERMIT_VIEW_'.$PERMIT_KEY);
			// 비밀글이면
			if($is_secret && !$GLOBALS['_PERMIT_VIEW_'][$PERMIT_KEY]) {
				$input_password = '<form class="input-password" method="post" autocomplete="off">'
							.'%s<div class="input-group" style="margin-top:10px"><input class="form-control" name="mb_password" type="password" placeholder="'. getLang('password').'" required>'
							.'<span class="input-group-btn"><button class="btn btn-default" type="submit">'. getLang('ok').'</button></span></div></form>';

				if(!empty($doc['mb_srl']) || empty($doc['mb_password'])) {
					return set_error(getLang('error_permit'),4501);
				} else if(empty($data['mb_password'])) {
					return set_error(sprintf($input_password, getLang('request_input', ['password'])), 1);
				} else if (!verifyEncrypt($data['mb_password'], $doc['mb_password'])) {
					return set_error(sprintf($input_password, getLang('error_password')), 4801);
				}

				$GLOBALS['_PERMIT_VIEW_'][$PERMIT_KEY] = true;
				if(empty($doc['mb_srl'])) {
					set_cookie('_AF_PERMIT_VIEW_'.$PERMIT_KEY, true, 0);
				}
			}
		}
	}

	// 확장 변수가 있으면 unserialize
	if(!empty($doc['wr_extra']) && !is_array($doc['wr_extra'])) {
		$doc['wr_extra'] = unserialize($doc['wr_extra']);
	}

	// 댓글 목록에 권한설정
	$mdid = $doc['md_id'];
	$rsrl = $data['rp'];
	$loginsrl = empty($_MEMBER)?0:$_MEMBER['mb_srl'];
	$pass = empty($rsrl) ? '' : $data['mb_password'];
	$permit = $is_manager || !empty($GLOBALS['_PERMIT_VIEW_'][$PERMIT_KEY]);
	$call = function($r)use($mdid,$rsrl,$pass,$permit,$loginsrl){
		$rset = [];
		$_prlen = 0;
		$rp_permit = 0;
		while ($row = DB::assoc($r)) {
			$_len = strlen($row['rp_depth']);

			// 권한해제 (더이상 하위 댓글이 없으면...)
			if($rp_permit > $_len) $rp_permit = 0;
			// 로그인 유저 권한체크
			if(!empty($loginsrl) && $loginsrl == $row['mb_srl']) {
				$rp_permit = $_len + 1;
			// 비밀번호가 넘어오면 권한체크
			} else if(!empty($pass) && $rsrl === $row['rp_srl']) {
				if(verifyEncrypt($pass, $row['mb_password'])) $rp_permit = $_len + 1;
			}

			// 비밀글 해제 (더이상 하위 댓글이 없으면...)
			if($_prlen > $_len) $_prlen = 0;
			// 하위 댓글에 비밀글 설정
			if($_prlen === 0 && $row['rp_secret'] == '1') $_prlen = $_len + 1;

			$row['rp_secret'] = $_prlen > 0;
			$row['_PERMIT_VIEW_'] = $permit || $rp_permit || $_prlen === 0;
			$rset[] = $row;
		}
		return $rset;
	};

	$cpage = empty($data['cpage']) ? '' : $data['cpage'];
	$doc['CURRENT_COMMENT_LIST'] = getCommentList($data['srl'], $cpage, [], 'rp_parent,rp_depth', $call);

	$category = empty($data['category']) ? '' : $data['category'];
	$search = empty($data['search']) ? '' : $data['search'];
	$page = empty($data['page']) ? '' : $data['page'];
	$list = getDocumentList($doc['md_id'], $page, $search, $category);
	$doc['CURRENT_DOCUMENT_LIST'] = $list;

	return $doc;
}

/* End of file viewdocument.php */
/* Location: ./module/board/disp/viewdocument.php */