<?php
if(!defined('__AFOX__')) exit();
	// TODO 나중에 필요하면 캐시 처리 하자

	function getDocument($srl, $field = '*', $inc_hit = FALSE) {

		$field = $field.','.implode(',', ['md_id','mb_srl']);
		$result = DB::get(_AF_DOCUMENT_TABLE_, $field, ['wr_srl'=>$srl]);
		if($ex=DB::error()) return set_error($ex->getMessage(), $ex->getCode());
		if(empty($result['md_id'])||$result['md_id']=='_AFOXtRASH_') {
			return set_error(getLang('error_request'),4303);
		}

		if($inc_hit && !empty($result)) {
			$md_id = $result['md_id'];
			$wr_mb = $result['mb_srl'];
			$out = setHistoryAction('wr_hit', $srl, false, function($v)use($srl,$md_id,$wr_mb){
				// 처음에만 카운터 올림, 포인트 사용
				if(!empty($v['data'])) return;

				// 자신은 포인트 사용 안함
				if(empty($wr_mb) || ($wr_mb !== $v['mb_srl'])) {
					$_r = setPoint((int) getModule($md_id, 'point_view'));
					if(!empty($_r['error'])) return set_error($_r['message'], $_r['error']);
				}

				// 자신은 카운터 안올림
				$ukey = ($v['mb_srl'] > 0 ? 'mb_srl':'mb_ipaddress').'{<>}';
				$uval = ($v['mb_srl'] > 0 ? $v['mb_srl']:$v['ipaddress']);
				DB::update(_AF_DOCUMENT_TABLE_,
					[
						'^wr_hit'=>'wr_hit+1'
					], [
						'wr_srl'=>$srl,
						$ukey => $uval
					]
				);
			});
			if(!empty($out['error'])) return set_error($out['message'], $out['error']);
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
				while ($row = DB::assoc($r)) {
					// 확장 변수가 있으면 unserialize
					if(!empty($row['wr_extra']) && !is_array($row['wr_extra'])) {
						$row['wr_extra'] = unserialize($row['wr_extra']);
					}
					$rset[] = $row;
				}
				return $rset;
			};
		}
		$_list = DB::gets(_AF_DOCUMENT_TABLE_, 'SQL_CALC_FOUND_ROWS *', $_wheres, $order, (((empty($page)?1:$page)-1)*$list_count).','.$list_count);
		return setDataListInfo($_list, DB::found(), $page, $list_count);
	}

	function getComment($srl, $field = '*') {
		return DB::get(_AF_COMMENT_TABLE_, $field, ['rp_srl'=>$srl]);
	}

	function getCommentList($srl, $callback = null) {
		return DB::gets(_AF_COMMENT_TABLE_, ['wr_srl'=>$srl], ['rp_parent'=>'asc','rp_depth'=>'asc'], $callback);
	}

	function getHashtags($content) {
		$tags = array('a', 'pre', 'xml', 'textarea', 'input', 'select', 'option', 'code', 'script', 'style', 'iframe', 'button', 'img', 'embed', 'object', 'ins');
		$pattern = '/<(' . implode('|', $tags) . ')[^>]*>.*?<\/\1>/si';
		$content= preg_replace($pattern, '',$content);
		$content= htmlspecialchars_decode(strip_tags($content), ENT_QUOTES);
		$tags = [];
		$pattern = '/(?:^:|^|\s|>|&nbsp;)(#([\w|ㄱ-ㅎ|ㅏ-ㅣ|가-힣\-]+)){1,}/';
		preg_replace_callback($pattern, function($mc)use(&$tags) {
			if(@preg_match_all('/#([\w|ㄱ-ㅎ|ㅏ-ㅣ|가-힣\-]+)/', $mc[0], $matches)) {
				$tags[] = $matches[1][0];
			}
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
