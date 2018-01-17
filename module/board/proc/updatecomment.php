<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	if(empty($data['wr_srl']) && empty($data['rp_parent']) && empty($data['rp_srl'])) return set_error(getLang('error_request'),4303);
	if(empty($data['rp_content'])) return set_error(getLang('request_input', ['content']),1);

	global $_MEMBER;

	DB::transaction();

	$sendsrl = 0;
	$rp_root = 0;
	$rp_depth = '';
	$rp_parent = (int) abs(empty($data['rp_parent']) ? 0 : $data['rp_parent']);
	$rp_srl = (int) (empty($rp_parent) ? abs(empty($data['rp_srl']) ? 0 : $data['rp_srl']) : $rp_parent);
	$wr_srl = (int) abs(empty($data['wr_srl']) ? 0 : $data['wr_srl']);
	$parent_secret = false;

	try {

		if(!empty($rp_srl)) {
			$cmt = DB::get(_AF_COMMENT_TABLE_, 'wr_srl, rp_parent, rp_secret, rp_depth, mb_srl, mb_password', ['rp_srl'=>$rp_srl]);
			if(empty($cmt['wr_srl'])) throw new Exception(getLang('error_founded'), 4201);
			$wr_srl = (int) abs(empty($cmt['wr_srl']) ? 0 : $cmt['wr_srl']);

			$sendsrl = $cmt['mb_srl'];
			$rp_root = $cmt['rp_parent'];

			// true이면 하위 답변도 비밀글
			$parent_secret = !empty($rp_parent) && $cmt['rp_secret'] == '1';

			if(!empty($rp_parent)) {
				$_len = strlen($cmt['rp_depth']) + 1;
				if ($_len > 5) throw new Exception(getLang('msg_not_write_reply'), 571);

				// 트리 구조 최대한 수를 늘여 사용하기로, 그누보드 참고 = 최대 수가 정해져 있지만 불러올때 쿼리가 짧아서 사용하기로
				$_begin_char = chr(0x21);
				$_end_char = chr(0x7E);
				$rp_depth = $cmt['rp_depth'];

				$_out1 = DB::get(_AF_COMMENT_TABLE_,
					'MAX(SUBSTRING(rp_depth,'.$_len.',1)) as reply',
					[
						'wr_srl'=>$wr_srl,
						'rp_parent'=>$rp_root,
						'rp_depth{LIKE}'=>empty($cmt['rp_depth'])?null:$cmt['rp_depth'].'%',
						'^'=>'SUBSTRING(`rp_depth`,'.$_len.',1)<>\'\''
					]
				);

				if (empty($_out1['reply'])) {
					$rp_depth .= $_begin_char;
				} else if (ord($_out1['reply']) == ord($_end_char))
					throw new Exception(getLang('msg_not_write_reply'), 571);
				else $rp_depth .= chr(ord($_out1['reply']) + 1);
			}
		} else {
			$_out1 = DB::get(_AF_COMMENT_TABLE_, 'max(rp_parent) as max', ['wr_srl'=>$wr_srl]);
			$rp_root = $_out1['max'] + 1;
		}

		$doc = DB::get(_AF_DOCUMENT_TABLE_, 'wr_srl,md_id,mb_srl', ['wr_srl'=>$wr_srl]);
		if(empty($doc['wr_srl'])) throw new Exception(getLang('error_founded'), 4201);

		$module = getModule($doc['md_id']);
		if(!empty($module['error'])) throw new Exception($module['message'], $module['error']);
		// 모듈이 없으면 에러
		if($doc['md_id'] != $module['md_id']) throw new Exception(getLang('error_request'),4303);

		// use_type 값이 1~6 사이이면 모듈에 설정된 값으로 강제 설정
		if(!empty($module['use_type']) && $module['use_type'] < 7) $data['rp_type'] = ((int)$module['use_type'])-1;
		if(!empty($module['use_secret'])) $data['rp_secret'] = ((int)$module['use_secret'])-1;
		if($parent_secret) $data['rp_secret'] = 1;

		$data['mb_ipaddress'] = $_SERVER['REMOTE_ADDR'];

		if(empty($_MEMBER)) {
			$data['mb_nick'] = trim(empty($data['mb_nick'])?'':strip_tags($data['mb_nick']));
			if(empty($data['mb_nick']) || empty($data['mb_password'])) {
				throw new Exception(getLang('request_input', [getLang('%s, %s', ['id', 'password'])]), 1);
			}
			$data['mb_srl'] = 0;
			$data['mb_rank'] = 0;
			$encrypt_password = createHash($data['mb_password']);
		} else {
			$data['mb_srl'] = $_MEMBER['mb_srl'];
			$data['mb_rank'] = $_MEMBER['mb_rank'];
			$data['mb_nick'] = $_MEMBER['mb_nick'];
			$encrypt_password = null;
		}

		$data['rp_content'] = xssClean($data['rp_content']);

		$ret_rp_srl = 0;
		$new_insert = !empty($rp_parent) || empty($rp_srl);

		if ($new_insert) {

			// 권한 체크
			if(!isGrant('reply', $module['md_id'])) {
				throw new Exception(getLang('error_permitted'),4501);
			}

			DB::insert(_AF_COMMENT_TABLE_,
				[
					'wr_srl'=>$wr_srl,
					'rp_parent'=>$rp_root,
					'rp_depth'=>$rp_depth,
					'rp_secret'=>$data['rp_secret'],
					'rp_type'=>$data['rp_type'],
					'rp_content'=>$data['rp_content'],
					'mb_srl'=>$data['mb_srl'],
					'mb_rank'=>$data['mb_rank'],
					'mb_nick'=>$data['mb_nick'],
					'mb_password'=>$encrypt_password,
					'mb_ipaddress'=>$data['mb_ipaddress'],
					'^rp_regdate'=>'NOW()',
					'^rp_update'=>'NOW()'
				]
			);

			$ret_rp_srl = DB::insert_id();

			// 포인트 사용중이면
			$_r = setPoint((int)$module['point_reply']);
			if(!empty($_r['error'])) throw new Exception($_r['message'], $_r['error']);

			setHistoryAction('wr_reply', $wr_srl, false, function($v)use($wr_srl){
				DB::update(_AF_DOCUMENT_TABLE_, ['^wr_reply'=>'wr_reply+1'], ['wr_srl'=>$wr_srl]);
			});

			sendNote(empty($sendsrl) ? $doc['mb_srl'] : $sendsrl,
					cutstr(strip_tags($data['rp_content']),200)
						.sprintf('<br><a href="./?id=%s&srl=%s&rp=%s">%s...</a>', $doc['md_id'], $wr_srl, $ret_rp_srl, getLang('detail')),
					$data['mb_nick']
				);

		} else {
			// 권한 체크
			if(empty($_MEMBER)) {
				if(empty($cmt['mb_password']) || !checkPassword($data['mb_password'], $cmt['mb_password'])) {
					throw new Exception(getLang('error_permitted'),4501);
				}
			} else if(!isManager($module['md_id'])) {
				if($_MEMBER['mb_srl'] != $cmt['mb_srl']) {
					throw new Exception(getLang('error_permitted'),4501);
				}
			}

			DB::update(_AF_COMMENT_TABLE_,
				[
					'rp_secret'=>$data['rp_secret'],
					'rp_type'=>$data['rp_type'],
					'rp_content'=>$data['rp_content'],
					'^rp_update'=>'NOW()'
				], [
					'rp_srl'=>$rp_srl
				]
			);

			$ret_rp_srl = $rp_srl;
		}

		// 비회원이면 비밀번호 다시 안묻기위해 임시권한주기
		if(empty($_MEMBER)) {
			$PERMIT_KEY = md5($module['md_id'].'_'.$ret_rp_srl . '_' . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
			set_session('_AF_SECRET_COMMENT_'.$PERMIT_KEY, true);
		}

	} catch (Exception $ex) {
		DB::rollback();

		// MySQL 5.5 이전 버전은 트랜잭션을 지원 안한다.
		// Engine == MyISAM 트랜잭션을 지원 안한다.
		if (DB::version('5.5.0', '<')) {
			if($new_insert && !empty($ret_rp_srl)) {
				@DB::delete(_AF_COMMENT_TABLE_, ['rp_srl'=>$ret_rp_srl]);
			}
		}

		return set_error($ex->getMessage(),$ex->getCode());
	}

	DB::commit();

	return ['error'=>0, 'message'=>getLang('success_saved'), 'wr_srl'=>$wr_srl, 'rp_srl'=>$ret_rp_srl];
}

/* End of file updatedocument.php */
/* Location: ./module/board/proc/updatedocument.php */
