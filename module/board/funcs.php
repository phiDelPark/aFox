<?php
if(!defined('__AFOX__')) exit();
	// TODO 나중에 필요하면 캐시 처리 하자

	function getDocument($srl, $field = '*', $inc_hit = FALSE) {
		global $_MEMBER;

		$field = $field.','.implode(',', ['md_id','mb_srl']);
		$result = DB::get(_AF_DOCUMENT_TABLE_, $field, ['wr_srl'=>$srl]);
		if($ex=DB::error()) return set_error($ex->getMessage(), $ex->getCode());
		if(empty($result['md_id'])) {
			return set_error(getLang('error_request'),4303);
		}elseif($result['md_id']=='_AFOXtRASH_'){
			//휴지통은 관리자 혹은 자신만 가능
			if(empty($_MEMBER) || empty($_MEMBER['mb_srl']) || (!isManager($result['wr_updater'])&&$_MEMBER['mb_srl']!=$result['mb_srl']))
			return set_error(getLang('error_permitted'),4501);
		}

		if($inc_hit && !empty($result)) {
			$wr_mb = $result['mb_srl'];
			$point = (int) getModule($result['md_id'], 'point_view');

			if($point !== 0) {
				$_out = setHistoryAction('wr_hit::'.$srl, $point, false, function($v)use($srl,$wr_mb,$point){
					// 처음에만 포인트 사용
					if(!empty($v['data'])) return;
					// 자신은 포인트 사용 안함
					if(empty($wr_mb) || $wr_mb !== $v['mb_srl']) {
						$_r = setPoint($point);
						if(!empty($_r['error'])) return set_error($_r['message'], $_r['error']);
					}
				});
				if(!empty($_out['error'])) return set_error($_out['message'], $_out['error']);
			}

			$uinfo = [
				'mb_srl'=>empty($_MEMBER) ? 0 : $_MEMBER['mb_srl'],
				'ipaddress'=>$_SERVER['REMOTE_ADDR']
			];

			$hit_key = 'afox_wr_hit::'.$srl;
			$hit_chk = get_session($hit_key);
			if(empty($hit_chk)) $hit_chk = get_cookie($hit_key);
			if(empty($hit_chk)) {
				// 자신은 카운터 안올림
				$ukey = ($uinfo['mb_srl'] > 0 ? 'mb_srl':'mb_ipaddress').'{<>}';
				$uval = ($uinfo['mb_srl'] > 0 ? $uinfo['mb_srl']:$uinfo['ipaddress']);
				DB::update(_AF_DOCUMENT_TABLE_,
					['^wr_hit'=>'wr_hit+1'],
					['wr_srl'=>$srl, $ukey=>$uval]
				);
			}
			set_session($hit_key, true);
			set_cookie($hit_key, true, 86400 * 31);
		}
		return $result;
	}

	function getDocumentList($id, $page, $search = '', $category = '', $order = 'wr_regdate', $callback = null) {
		$schs = [];
		if(!empty($search)) {
			$schkeys = ['title'=>'wr_title','content'=>'wr_content','nick'=>'mb_nick','tag'=>'wr_tags','date'=>'wr_regdate'];
			$ss = explode(':', $search);
			if(count($ss)>1 && !empty($schkeys[$ss[0]])) {
				$search = trim(implode(':', array_slice($ss,1)));
				if(!empty($search)) $schs = [$schkeys[$ss[0]].'{LIKE}'=>($ss[0]==='date'?'':'%').$search.'%'];
			} else {
				$schs = ['wr_title{LIKE}'=>'%'.$search.'%', 'wr_content{LIKE}'=>'%'.$search.'%'];
			}
		}

		$_wheres = [
			'md_id'=>$id,
			'(_AND_)' =>empty($category)?[]:['wr_category'=>$category],
			'(_OR_)' =>$schs
		];
		$list_count = getModule($id, 'md_list_count');
		if(empty($list_count)) $list_count = 20;

		if (empty($callback)) {
			$callback = function($r) {
				$rset = [];
				while ($row = DB::fetch($r)) {
					// 확장 변수가 있으면 unserialize
					if(!empty($row['wr_extra']) && !is_array($row['wr_extra'])) {
						$row['wr_extra'] = unserialize($row['wr_extra']);
					}
					$rset[] = $row;
				}
				return $rset;
			};
		}
		$_list = DB::gets(_AF_DOCUMENT_TABLE_, 'SQL_CALC_FOUND_ROWS *', $_wheres, $order, (((empty($page)?1:$page)-1)*$list_count).','.$list_count, $callback);
		return setDataListInfo($_list, $page, $list_count, DB::foundRows());
	}

	function getComment($srl, $field = '*') {
		return DB::get(_AF_COMMENT_TABLE_, $field, ['rp_srl'=>$srl]);
	}

	function getCommentList($srl, $callback = null) {
		return DB::gets(_AF_COMMENT_TABLE_, ['wr_srl'=>$srl], ['rp_parent'=>'asc','rp_depth'=>'asc'], $callback);
	}

	function getHashtags($content) {
		$tags = array('pre', 'code', 'xml', 'textarea', 'input', 'select', 'option', 'script', 'style', 'iframe', 'button', 'img', 'embed', 'object', 'ins');
		$pattern = '/<(' . implode('|', $tags) . ')[^>]*>.*?<\/\1>/si';
		$content= preg_replace($pattern, '',$content);
		$content= htmlspecialchars_decode(strip_tags($content), ENT_QUOTES);
		$tags = [];
		$pattern = '/#([\w|ㄱ-ㅎ|ㅏ-ㅣ|가-힣\-\_]+){1,}/';
		preg_replace_callback($pattern, function($matches)use(&$tags) {
			$tags[md5($matches[1])] = $matches[1];
			return '';
		}, $content);
		return implode(',', $tags);
	}

	function highlightText($key, $html) {
		return preg_replace_callback('#((?:(?!<[/a-z]).)*)([^>]*>|$)#si', function($mc)use($key) {
			return str_ireplace($key, '<mark>'.$key.'</mark>', $mc[1]).$mc[2];
		}, $html);
	}

/* End of file funcs.php */
/* Location: ./module/board/funcs.php */
