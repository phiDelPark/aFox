<?php

if(!defined('__AFOX__')) exit();

function proc($data) {

	if(!empty($data['create_database'])){
		DB::query("SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\"");
		DB::query("SET time_zone = \"+00:00\"");
		try{
			$create_sql = '
				CREATE TABLE IF NOT EXISTS '._AF_LEDGER_DATA_TABLE_.' (
				ev_srl          INT(11)      UNSIGNED NOT NULL AUTO_INCREMENT,
				ev_category     INT(11)      NOT NULL DEFAULT 0,
				ev_title        VARCHAR(255) NOT NULL,
				ev_status       CHAR(1)      NOT NULL DEFAULT \'0\',
				ev_amount       BIGINT(20)   UNSIGNED DEFAULT 0,
				ev_payment      BIGINT(20)   UNSIGNED DEFAULT 0,
				ev_paytype      CHAR(1)      NOT NULL DEFAULT \'0\',
				ev_receipt      CHAR(1)      NOT NULL DEFAULT \'0\',
				ev_memo         TEXT,
				ev_items        TEXT,
				ev_reserve      DATETIME     NOT NULL DEFAULT \'0000-00-00 00:00:00\',
				ev_finish       DATETIME     NOT NULL DEFAULT \'0000-00-00 00:00:00\',
				CONSTRAINT SRL_PK PRIMARY KEY (ev_srl),
				INDEX STATUS_INDEX (ev_status, ev_reserve, ev_finish),
				INDEX CATEGORY_INDEX (ev_category, ev_status, ev_reserve, ev_finish)) ENGINE=INNODB DEFAULT CHARSET=utf8;';
			DB::query($create_sql);
			$create_sql = '
				CREATE TABLE IF NOT EXISTS '._AF_LEDGER_CATEGORY_TABLE_.' (
				ca_srl       INT(11)      UNSIGNED NOT NULL AUTO_INCREMENT,
				ca_name      CHAR(20)     NOT NULL,
				CONSTRAINT ID_PK PRIMARY KEY (ca_srl)) ENGINE=INNODB DEFAULT CHARSET=utf8;';
			DB::query($create_sql);
			$create_sql = '
				CREATE TABLE IF NOT EXISTS '._AF_LEDGER_HISTORY_TABLE_.' (
				hs_target         INT(11),
				hs_work           INT(11),
				hs_changed        DATETIME     NOT NULL DEFAULT \'0000-00-00 00:00:00\') ENGINE=INNODB DEFAULT CHARSET=utf8;';
			DB::query($create_sql);
		}catch(Exception $e){
			return set_error($e->getMessage(), $e->getCode());
		}
		return set_error(getLang('success_created_db', 0), 0);

	} else {

		if(!empty($data['md_category']) && preg_match('/[\x{21}-\x{2b}\x{2d}\x{2f}\x{3a}-\x{40}\x{5b}-\x{60}]+/', $data['md_category'])) {
			return set_error(getLang('invalid_value', ['category']),2001);
		}

		$value = cutstr(trim($data['md_category']),20,'');
		if(empty($value)){
			return set_error(getLang('invalid_value', ['category']),2001);
		}

		$ca_srl = empty($data['md_srl']) ? 0 : $data['md_srl'];

		if($ca_srl > 0) {
		  $ret = DB::get(_AF_LEDGER_CATEGORY_TABLE_, 'ca_srl', ['ca_srl'=>$ca_srl]);
		  $num_rows = DB::numRows();
		  if($num_rows == 0) return set_error(getLang('error_request'),4303);
		}

		DB::transaction();

		try {

			if (empty($ca_srl)) {
				DB::insert(_AF_LEDGER_CATEGORY_TABLE_,
					[
						'ca_name'=>$value
					]
				);
				$insert_id = DB::insertId();
			} else {
				DB::update(_AF_LEDGER_CATEGORY_TABLE_,
					[
						'ca_name'=>$value
					], [
						'ca_srl'=>$ca_srl
					]
				);
				$insert_id = $ca_srl;
			}

		//CONTENT = 0;  //CATEGORY = 1;  //INSERT = 1;  //MODIFY = 2;  //DELETE = 3;
		// CATEGORY * 10000 + (id > 0 ? MODIFY : INSERT)
		DB::insert(_AF_LEDGER_HISTORY_TABLE_, ['hs_target'=>$insert_id, 'hs_work' => (int)($ca_srl > 0 ? 10002 : 10001), '^hs_changed' => 'NOW()']);

		} catch (Exception $ex) {
			DB::rollback();
			return set_error($ex->getMessage(),$ex->getCode());
		}

		DB::commit();
	}

	return ['error'=>0, 'message'=>getLang('success_saved')];
}

/* End of file setupmodule.php */
/* Location: ./module/ledger/proc/setupmodule.php */
