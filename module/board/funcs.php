<?php
if(!defined('__AFOX__')) exit();
	// TODO 나중에 필요하면 캐시 처리 하자

	function getDocument($srl, $inc_hit = FALSE, $field = '*') {

		$result = getDBItem(_AF_DOCUMENT_TABLE_, ['wr_srl'=>$srl], $field);
		if(!empty($result['error'])) return set_error($result['message'], $result['error']);

		if($inc_hit && empty($result['error'])) {
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
						'(wr_hit)'=>'wr_hit+1'
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

	function getDocumentList($id, $page, $search = '', $category = '', $wheres = [], $order = 'wr_regdate desc', $callback = null) {
		$schs = [];
		if(!empty($search)) {
			$schkeys = ['tags'=>'wr_tags','nick'=>'mb_nick','date'=>'wr_regdate'];
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
			'AND' =>empty($category)?[]:['wr_category'=>$category],
			'OR' =>$schs
		];
		if(count($wheres)) $_wheres = array_merge($_wheres, $wheres);
		$list_count = getModule($id, 'md_list_count');

		if (empty($callback)) {
			$callback = function($r) {
				$rset = [];
				while ($row = mysqli_fetch_assoc($r)) {
					// 확장 변수가 있으면 unserialize
					if(!empty($row['wr_extra']) && !is_array($row['wr_extra'])) {
						$row['wr_extra'] = unserialize($row['wr_extra']);
					}
					$rset[] = $row;
				}
				return $rset;
			};
		}
		return getDBList(_AF_DOCUMENT_TABLE_, $_wheres, $order, $page, $list_count, $callback);
	}

	function getComment($srl, $field = '*') {
		return getDBItem(_AF_COMMENT_TABLE_, ['rp_srl'=>$srl], $field);
	}

	function getCommentList($srl, $page, $wheres = [], $order = 'rp_parent,rp_depth', $callback = null) {
		$_wheres = ['wr_srl'=>$srl];
		if(count($wheres)) $_wheres = array_merge($_wheres, $wheres);
		return getDBList(_AF_COMMENT_TABLE_, $_wheres, $order, $page, 50, $callback);
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

/* End of file funcs.php */
/* Location: ./module/board/funcs.php */