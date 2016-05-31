<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	$doc = getDocument($data['srl'], true);

	if(empty($doc['error'])) {
	global $_MEMBER;
	$is_manager = isManager($doc['md_id']);

		// 권한 체크
		if(!$is_manager) {
			if(!isGrant($doc['md_id'], 'view')) {
				return set_error(getLang('msg_not_permitted'),901);
			}

			// 비밀글이면
			if($doc['wr_secret'] == '1') {
				$input_password = '<form class="input-password" method="post" autocomplete="off">'
								.'%s<div class="input-group" style="margin-top:10px"><input class="form-control" name="mb_password" type="password" placeholder="'. getLang('password').'" required>'
								.'<span class="input-group-btn"><button class="btn btn-default" type="submit">'. getLang('ok').'</button></span></div></form>';

				// 권한 체크
				if(empty($_MEMBER) || empty($doc['mb_srl'])) {
					if (empty($doc['mb_password'])) {
						return set_error(getLang('msg_not_permitted'), 901);
					} else if(empty($data['mb_password'])) {
						return set_error(sprintf($input_password, getLang('warn_input', ['password'])), 90);
					} else if (!verifyEncrypt($data['mb_password'], $doc['mb_password'])) {
						return set_error(sprintf($input_password, getLang('msg_wrong_password')), 906);
					}
				} else if($_MEMBER['mb_srl'] != $doc['mb_srl']) {
					return set_error(getLang('msg_not_permitted'), 901);
				}

				$GLOBALS['_PERMIT_VIEW_'][md5($doc['md_id'].'_'.$data['srl'])] = true;
			}
		}

		$call = null;

		if(!$is_manager && !empty($data['rp']) && !empty($data['mb_password'])) {
			if(empty($GLOBALS['_PERMIT_VIEW_'][md5($doc['md_id'].'_'.$data['srl'])])) {
				$rsrl = $data['rp'];
				$pass = $data['mb_password'];
				$mdid= $doc['md_id'];
				$call = function($r)use($rsrl,$pass,$mdid){
					$rset = [];
					while ($row = DB::assoc($r)) {
						$rset[] = $row;
						if($rsrl===$row['rp_srl']) {
							if(verifyEncrypt($pass, $row['mb_password'])){
								$GLOBALS['_PERMIT_VIEW_'][md5($mdid.'_'.$row['wr_srl'].'_'.$row['rp_srl'])] = true;
							}
						}
					}
					return $rset;
				};
			}
		}

		$cpage = empty($data['cpage']) ? '' : $data['cpage'];
		$doc['CURRENT_COMMENT_LIST'] = getCommentList($data['srl'], $cpage, [], 'rp_parent,rp_depth', $call);

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