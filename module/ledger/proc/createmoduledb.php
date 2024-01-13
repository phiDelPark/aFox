<?php

if(!defined('__AFOX__')) exit();

function proc($data) {

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
}

/* End of file createmoduledb.php */
/* Location: ./module/ledger/proc/createmoduledb.php */
