<?php
if(!defined('__AFOX__')) exit();
	// TODO 나중에 필요하면 캐시 처리 하자

	function getCategorys() {
		$categorys = DB::gets(_AF_LEDGER_CATEGORY_TABLE_, [], ['ca_srl'=>'ASC']);
		return empty($categorys) ? [] : $categorys;
	}

	function getDocument($srl) {
		$result = DB::get(_AF_LEDGER_DATA_TABLE_, '*', ['ev_srl'=>$srl]);
		if($ex=DB::error()) return set_error($ex->getMessage(), $ex->getCode());

		if(empty($result)){
			$result['ev_srl'] = 0;
			$result['ev_category'] = 0;
			$result['ev_title'] = '';
			$result['ev_status'] = 0;
			$result['ev_amount'] = 0;
			$result['ev_payment'] = 0;
			$result['ev_paytype'] = 0;
			$result['ev_receipt'] = 0;
			$result['ev_memo'] = '';
			$result['ev_items'] = '';
			$result['ev_reserve'] = date('Y-m-d H:i:s', time());
			$result['ev_finish'] = date('Y-m-d H:i:s', time());
		}

		$result['ev_reserve'] = new DateTime(date('Y-m-d H:i:s', strtotime($result['ev_reserve'])));
		$result['ev_reserve'] = $result['ev_reserve']->format("Y년 m월 d일");
		$result['ev_finish'] = new DateTime(date('Y-m-d H:i:s', strtotime($result['ev_finish'])));
		$result['ev_finish'] = $result['ev_finish']->format("Y년 m월 d일");

		return $result;
	}

	function getDocumentList($page, $t = '', $c = '', $s = '', $order = 'ev_reserve', $callback = null) {

		$wheres = ['ev_status{<}'=>9,'(_AND_)' =>[],'(_OR_)'=>[]];
		if(!empty($c)) $wheres['(_AND_)']['ev_category'] = $c;
		if(!is_null($t) && is_numeric($t)){
			$wheres['(_AND_)']['^'] = '`ev_amount`<=`ev_payment`';
			$wheres['(_AND_)']['ev_paytype'] = $t;
		}else{
			$wheres['(_AND_)']['^'] = '`ev_amount`>`ev_payment`';
		}

		$count = 20;

		if (empty($callback)) {
			$callback = function($r) {
				$rset = [];
				while ($row = DB::fetch($r)) {
					// 확장 변수가 있으면 unserialize
					//if(!empty($row['wr_extra']) && !is_array($row['wr_extra'])) {
					//	$row['wr_extra'] = unserialize($row['wr_extra']);
					//}
					$rset[] = $row;
				}
				return $rset;
			};
		}
		$list = DB::gets(
			_AF_LEDGER_DATA_TABLE_, 'SQL_CALC_FOUND_ROWS *',
			$wheres, $order,
			(((empty($page)?1:$page)-1)*$count).','.$count,
			$callback
		);

		return setDataListInfo($list, $page, $count, DB::foundRows());
	}

/* End of file funcs.php */
/* Location: ./module/board/funcs.php */
