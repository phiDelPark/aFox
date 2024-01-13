<?php

if(!defined('__AFOX__')) exit();

function proc($data)
{
	$idx = (int)(isset($data['ev_srl']) ? $data['ev_srl'] : 0);

	try{
		$ret = DB::get(_AF_LEDGER_DATA_TABLE_, 'ev_status', ['ev_srl'=>$idx]);
		$num_rows = DB::numRows();
		if($num_rows == 0) return set_error(getLang('error_request'),4303);

		DB::transaction();

		DB::update(_AF_LEDGER_DATA_TABLE_, ['ev_status' => (int)($ret['ev_status'] == 9 ? '0' : '9')], ['ev_srl'=>$idx]);

		//CONTENT = 0;  //CATEGORY = 1;  //INSERT = 1;  //MODIFY = 2;  //DELETE = 3;
		DB::insert(_AF_LEDGER_HISTORY_TABLE_, ['hs_target'=>$idx, 'hs_work' => 3 /*CONTENT+DELETE*/, '^hs_changed' => 'NOW()']);

		DB::commit();

	}catch(Exception $ex){
		return set_error($ex->getMessage(),$ex->getCode());
	}

	return ['error'=>0, 'message'=>getLang('success_deleted'), 'ev_srl'=>$idx];
}

/* End of file deletedocument.php */
/* Location: ./module/ledger/proc/deletedocument.php */
