<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	$doc = getDocument($data['srl'], true);
	if(!empty($doc['error'])) return set_error(getLang($doc['message']),$doc['error']);
	if(empty($doc['wr_srl'])) return set_error(getLang('error_founded'),4201);

	global $_MEMBER;
	$is_manager = isManager($doc['md_id']);
	$login_srl = empty($_MEMBER)?0:$_MEMBER['mb_srl'];

	// 관리자면 권한부여
	$doc['grant_view'] = $is_manager;
	// 관리자가 아니면 권한 체크
	if(!$is_manager) {

		$PERMIT_KEY = md5($doc['md_id'].'_'.$doc['wr_srl'] . '_' . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);

		if(!isGrant($doc['md_id'], 'view')) {
			set_session('_AF_SECRET_DOCUMENT_'.$PERMIT_KEY, false);
			return set_error(getLang('error_permit'),88088);
		}

		if(empty($login_srl) || $login_srl != $doc['mb_srl']) {

			// 비밀글이 아니면 권한부여
			$doc['grant_view'] = $doc['wr_secret'] != '1' || get_session('_AF_SECRET_DOCUMENT_'.$PERMIT_KEY);
			// 비밀글이면
			if(!$doc['grant_view']) {
				$input_password = '<form class="input-password" method="post" autocomplete="off">'
							.'%s<div class="input-group" style="margin-top:10px"><input class="form-control" name="mb_password" type="password" placeholder="'. getLang('password').'" required>'
							.'<span class="input-group-btn"><button class="btn btn-default" type="submit">'. getLang('ok').'</button></span></div></form>';

				if(!empty($doc['mb_srl']) || empty($doc['mb_password'])) {
					return set_error(getLang('error_permit'),4501);
				} else if(empty($data['mb_password'])) {
					return set_error(sprintf($input_password, getLang('request_input', ['password'])), 1);
				} else if (!checkPassword($data['mb_password'], $doc['mb_password'])) {
					return set_error(sprintf($input_password, getLang('error_password')), 4801);
				}

				$doc['grant_view'] = true;
				if(empty($doc['mb_srl'])) {
					set_session('_AF_SECRET_DOCUMENT_'.$PERMIT_KEY, true);
				}
			}
		} else {
			// 자기글은 권한부여
			$doc['grant_view'] = true;
		}
	}

	$doc['grant_write'] = $is_manager || empty($doc['mb_srl']) || $login_srl == $doc['mb_srl'];

	// 확장 변수가 있으면 unserialize
	if(!empty($doc['wr_extra']) && !is_array($doc['wr_extra'])) {
		$doc['wr_extra'] = unserialize($doc['wr_extra']);
	}

	// 댓글 목록에 권한설정
	$mdid = $doc['md_id'];
	$rsrl = $data['rp'];
	$pass = empty($rsrl) ? '' : $data['mb_password'];

	// 문서 주인일경우 첫번째 댓글에만 권한 부여
	if(empty($login_srl) && empty($doc['mb_srl']) && $doc['wr_secret'] != '1' && !empty($data['mb_password'])) {
		$wr_permit = checkPassword($data['mb_password'], $doc['mb_password']);
	} else {
		$wr_permit = ($login_srl && $login_srl == $doc['mb_srl']) || ($doc['wr_secret'] == '1' && $doc['grant_view']);
	}

	$call = function($r)use($mdid,$rsrl,$pass,$wr_permit,$login_srl,$is_manager){
		$rset = [];
		$_prlen = 0;
		$rp_permit = 0;
		while ($row = DB::assoc($r)) {
			$_len = strlen($row['rp_depth']);

			// 권한해제 (더이상 하위 댓글이 없으면...)
			if($rp_permit > $_len) $rp_permit = 0;
			// 로그인 유저 권한체크
			if(!empty($login_srl) && $login_srl == $row['mb_srl']) {
				$rp_permit = $_len + 1;
			} else {
				$RP_PERMIT_KEY = md5($mdid.'_'.$row['rp_srl'] . '_' . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
				// 비밀글이 아니면 권한부여
				if($row['rp_secret'] != '1' || get_session('_AF_SECRET_COMMENT_'.$RP_PERMIT_KEY)) {
					$rp_permit = $_len + 1;
				} else if(!empty($pass) && $rsrl == $row['rp_srl']) {
					// 비밀번호가 넘어오면 권한체크
					if(checkPassword($pass, $row['mb_password'])) {
						$rp_permit = $_len + 1;
						set_session('_AF_SECRET_COMMENT_'.$RP_PERMIT_KEY, true);
					}
				}
			}

			// 비밀글 해제 (더이상 하위 댓글이 없으면...)
			if($_prlen > $_len) $_prlen = 0;
			// 하위 댓글에 비밀글 설정
			if($_prlen == 0 && $row['rp_secret'] == '1') $_prlen = $_len + 1;

			$row['rp_secret'] = $_prlen > 0;
			$row['grant_view'] = $is_manager || $rp_permit || $_prlen == 0 || ($wr_permit && $_len == 0);
			$row['grant_write'] = $row['rp_status']!='4' && ($is_manager || empty($row['mb_srl']) || $login_srl == $row['mb_srl']);
			//unset($row['mb_password']);

			$rset[] = $row;
		}
		return $rset;
	};

	//unset($doc['mb_password']);

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