<?php

if(!defined('__AFOX__')) exit();

function proc($data)
{
	$idx = (int)(isset($data['ev_srl']) ? $data['ev_srl'] : 0);

	$reserve = preg_replace('/([^0-9]+)/s', '', $data['ev_reserve'], -1);
	$finish = preg_replace('/([^0-9]+)/s', '', $data['ev_finish'], -1);

	$reserve = empty($reserve) ? new DateTime(date('Ymd', time())) : date_create_from_format('Ymd', $reserve);
	$finish = empty($finish) ? new DateTime(date('Ymd', time())) : date_create_from_format('Ymd', $finish);
	if($reserve > $finish ) $finish = $reserve; // 완료 날짜가 작으면 예약 날짜로 맞춤
	$reserve = $reserve->format("Y-m-d H:i:s");
	$finish = $finish->format("Y-m-d H:i:s");

	$receipt =  0;
	if(empty($data['ev_receipt'])) $data['ev_receipt'] = [];
	foreach($data['ev_receipt'] as $val){
		$receipt = $receipt | 1 << $val;
	}

	$amount = 0;
	$items = '[' . (empty($data['ev_items']) ? '' : urldecode($data['ev_items'])) . ']';
	$json_items = empty($items) ? 0 : json_decode($items, true);
	foreach($json_items as $v) $amount += ($v['price'] + $v['tax']) * $v['count'];

	try{

	  if($idx > 0){
		$ret = DB::get(_AF_LEDGER_DATA_TABLE_, 'ev_srl', ['ev_status{<}' => 9, 'ev_srl' => $idx]);
		if(DB::numRows() == 0) return set_error(getLang('error_request'),4303);
	  }

	  DB::transaction();

	  if((int)$data['ev_payment'] > (int)$data['ev_amount']) $data['ev_payment'] = $data['ev_amount'];

	  $escape_title = DB::escape(xssClean(trim($data['ev_title'])));
	  $escape_memo = DB::escape(xssClean(trim($data['ev_memo'])));

	  if($idx > 0){
		DB::update(_AF_LEDGER_DATA_TABLE_,
		  [
			'ev_category' => (int)$data['ev_category'],
			'ev_status' => (int)$data['ev_status'],
			'ev_amount' => (int)$amount,
			'ev_payment' => (int)$data['ev_payment'],
			'ev_paytype' => (int)$data['ev_paytype'],
			'ev_receipt' => (int)$receipt,
			'ev_title' => $escape_title,
			'ev_memo' => $escape_memo,
			'ev_items' => $items,
			'ev_reserve' => $reserve,
			'ev_finish' => $finish
		  ],
		  ['ev_srl'=>$idx]
		);
		$insert_id = $idx;
	  } else {
		DB::insert(_AF_LEDGER_DATA_TABLE_,
		  [
			'ev_category' => (int)$data['ev_category'],
			'ev_status' => (int)$data['ev_status'],
			'ev_amount' => (int)$amount,
			'ev_payment' => (int)$data['ev_payment'],
			'ev_paytype' => (int)$data['ev_paytype'],
			'ev_receipt' => (int)$receipt,
			'ev_title' => $escape_title,
			'ev_memo' => $escape_memo,
			'ev_items' => $items,
			'ev_reserve' => $reserve,
			'ev_finish' => $finish
		  ]
		);
		$insert_id = DB::insertId();
	  }

	  //CONTENT = 0;  //CATEGORY = 1;  //INSERT = 1;  //MODIFY = 2;  //DELETE = 3;
	  // CONTENT + (id > 0 ? MODIFY : INSERT)
	  DB::insert(_AF_LEDGER_HISTORY_TABLE_, ['hs_target'=>$insert_id, 'hs_work' => (int)($idx > 0 ? 2 : 1), '^hs_changed' => 'NOW()']);

	  DB::commit();

	}catch(Exception $ex){
		return set_error($ex->getMessage(),$ex->getCode());
	}

	return ['error'=>0, 'message'=>getLang('success_saved'), 'ev_srl'=>$insert_id];
}

/* End of file update.php */
/* Location: ./module/ledger/proc/update.php */
