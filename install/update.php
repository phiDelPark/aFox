<?php define('__AFOX__',   TRUE);
require_once __DIR__ . '/../init/constant.php';
include_once _AF_CONFIG_DATA_.'_db_config.php';
//load DB // When using a query, you must perform the escape yourself, or use parameters
require_once _AF_LIBS_PATH_ . 'db/mysql'.(function_exists('mysqli_connect')?'i':'').'.php';
DB::connect($_DBINFO);
$success = true;
?>
<!doctype html><html lang="ko"><head><meta charset="utf-8"></head><body>
<?php
	ob_start();
	ob_end_clean();

	/**********************************/
	$r = DB::query('SHOW FULL COLUMNS FROM '._AF_CONFIG_TABLE_, [], true);
	$o = [
		'lang CHAR(5) NOT NULL',
		'start CHAR(11) NOT NULL',
		'theme VARCHAR(100) NOT NULL',
		'title VARCHAR(255) NOT NULL',
		'version CHAR(11) NOT NULL',
		'use_signup CHAR(1) NOT NULL DEFAULT 0',
		'use_visit CHAR(1) NOT NULL DEFAULT 0',
		'use_captcha CHAR(1) NOT NULL DEFAULT 0',
		'use_protect CHAR(1) NOT NULL DEFAULT 0',
		'point_login INT(11) NOT NULL DEFAULT 0'
	];
	$t = [];
	foreach($r as $v) {
		$t[] = $v['Field'].' '.strtoupper($v['Type']=='mediumtext'?'text':$v['Type']).($v['Null']=='NO'?' NOT NULL':'')
			.($v['Default']!==NULL?' DEFAULT '.$v['Default']:'').($v['Extra']=='auto_increment'?' AUTO_INCREMENT':'');
	}
	echo '<b style="color:blue">'._AF_CONFIG_TABLE_."</b><br>\n";
	foreach($o as $v) {
		if(!in_array($v, $t)){
			echo '<b style="color:red">- miss: </b>'.$v."<br>\n";
			ob_flush();
			flush();
			$success = false;
		}
	}

	/**********************************/
	$r = DB::query('SHOW FULL COLUMNS FROM '._AF_THEME_TABLE_, [], true);
	$o = [
		'th_id VARCHAR(100) NOT NULL',
		'th_extra TEXT'
	];
	$t = [];
	foreach($r as $v) {
		$t[] = $v['Field'].' '.strtoupper($v['Type']=='mediumtext'?'text':$v['Type']).($v['Null']=='NO'?' NOT NULL':'')
			.($v['Default']!==NULL?' DEFAULT '.$v['Default']:'').($v['Extra']=='auto_increment'?' AUTO_INCREMENT':'');
	}
	echo '<b style="color:blue">'._AF_THEME_TABLE_."</b><br>\n";
	foreach($o as $v) {
		if(!in_array($v, $t)){
			echo '<b style="color:red">- miss: </b>'.$v."<br>\n";
			ob_flush();
			flush();
			$success = false;
		}
	}

	/**********************************/
	$r = DB::query('SHOW FULL COLUMNS FROM '._AF_MENU_TABLE_, [], true);
	$o = [
		'mu_srl INT(11) NOT NULL',
		'mu_parent INT(11) NOT NULL DEFAULT 0',
		'mu_status CHAR(1) NOT NULL DEFAULT 0',
		'mu_type CHAR(1) NOT NULL',
		'mu_title VARCHAR(255) NOT NULL',
		'mu_about VARCHAR(255) NOT NULL DEFAULT ',
		'mu_link VARCHAR(255) NOT NULL DEFAULT ',
		'mu_collapse CHAR(1) NOT NULL DEFAULT 0',
		'mu_new_win CHAR(1) NOT NULL DEFAULT 0'
	];
	$t = [];
	foreach($r as $v) {
		$t[] = $v['Field'].' '.strtoupper($v['Type']=='mediumtext'?'text':$v['Type']).($v['Null']=='NO'?' NOT NULL':'')
			.($v['Default']!==NULL?' DEFAULT '.$v['Default']:'').($v['Extra']=='auto_increment'?' AUTO_INCREMENT':'');
	}
	echo '<b style="color:blue">'._AF_MENU_TABLE_."</b><br>\n";
	foreach($o as $v) {
		if(!in_array($v, $t)){
			echo '<b style="color:red">- miss: </b>'.$v."<br>\n";
			ob_flush();
			flush();
			$success = false;
		}
	}

	/**********************************/
	$r = DB::query('SHOW FULL COLUMNS FROM '._AF_MEMBER_TABLE_, [], true);
	$o = [
		'mb_srl INT(11) NOT NULL AUTO_INCREMENT',
		'mb_id CHAR(11) NOT NULL',
		'mb_password VARCHAR(100) NOT NULL',
		'mb_rank CHAR(1) NOT NULL DEFAULT 0',
		'mb_status CHAR(1) NOT NULL DEFAULT 0',
		'mb_point INT(11) DEFAULT 0',
		'mb_nick VARCHAR(20) NOT NULL',
		'mb_email VARCHAR(255) NOT NULL DEFAULT ',
		'mb_homepage VARCHAR(255) NOT NULL DEFAULT ',
		'mb_about TEXT',
		'mb_extra TEXT',
		'mb_block_id TEXT',
		'mb_login DATETIME NOT NULL',
		'mb_regdate DATETIME NOT NULL'
	];
	$t = [];
	foreach($r as $v) {
		$t[] = $v['Field'].' '.strtoupper($v['Type']=='mediumtext'?'text':$v['Type']).($v['Null']=='NO'?' NOT NULL':'')
			.($v['Default']!==NULL?' DEFAULT '.$v['Default']:'').($v['Extra']=='auto_increment'?' AUTO_INCREMENT':'');
	}
	echo '<b style="color:blue">'._AF_MEMBER_TABLE_."</b><br>\n";
	foreach($o as $v) {
		if(!in_array($v, $t)){
			echo '<b style="color:red">- miss: </b>'.$v."<br>\n";
			ob_flush();
			flush();
			$success = false;
		}
	}

	/**********************************/
	$r = DB::query('SHOW FULL COLUMNS FROM '._AF_ADDON_TABLE_, [], true);
	$o = [
		'ao_id VARCHAR(100) NOT NULL',
		'ao_extra TEXT',
		'use_editor CHAR(1) NOT NULL DEFAULT 0'
	];
	$t = [];
	foreach($r as $v) {
		$t[] = $v['Field'].' '.strtoupper($v['Type']=='mediumtext'?'text':$v['Type']).($v['Null']=='NO'?' NOT NULL':'')
			.($v['Default']!==NULL?' DEFAULT '.$v['Default']:'').($v['Extra']=='auto_increment'?' AUTO_INCREMENT':'');
	}
	echo '<b style="color:blue">'._AF_ADDON_TABLE_."</b><br>\n";
	foreach($o as $v) {
		if(!in_array($v, $t)){
			echo '<b style="color:red">- miss: </b>'.$v."<br>\n";
			ob_flush();
			flush();
			$success = false;
		}
	}

	/**********************************/
	$r = DB::query('SHOW FULL COLUMNS FROM '._AF_MODULE_TABLE_, [], true);
	$o = [
		'md_id CHAR(11) NOT NULL',
		'md_key VARCHAR(100) NOT NULL',
		'md_status CHAR(1) NOT NULL DEFAULT 0',
		'md_category VARCHAR(255) NOT NULL DEFAULT ',
		'md_title VARCHAR(255) NOT NULL',
		'md_about VARCHAR(255) NOT NULL DEFAULT ',
		'md_extra TEXT',
		'md_file_max INT(11) NOT NULL DEFAULT 0',
		'md_file_size INT(11) NOT NULL DEFAULT 0',
		'md_file_accept VARCHAR(255) NOT NULL DEFAULT ',
		'md_list_count INT(11) NOT NULL DEFAULT 20',
		'md_manager INT(11) NOT NULL DEFAULT 0',
		'use_style CHAR(1) NOT NULL DEFAULT 0',
		'use_type CHAR(1) NOT NULL DEFAULT 0',
		'use_secret CHAR(1) NOT NULL DEFAULT 0',
		'thumb_width INT(11) NOT NULL DEFAULT 0',
		'thumb_height INT(11) NOT NULL DEFAULT 0',
		'thumb_option CHAR(1) NOT NULL DEFAULT 0',
		'point_view INT(11) NOT NULL DEFAULT 0',
		'point_write INT(11) NOT NULL DEFAULT 0',
		'point_reply INT(11) NOT NULL DEFAULT 0',
		'point_download INT(11) NOT NULL DEFAULT 0',
		'grant_list CHAR(1) NOT NULL DEFAULT 0',
		'grant_view CHAR(1) NOT NULL DEFAULT 0',
		'grant_write CHAR(1) NOT NULL DEFAULT 0',
		'grant_reply CHAR(1) NOT NULL DEFAULT 0',
		'grant_upload CHAR(1) NOT NULL DEFAULT 0',
		'grant_download CHAR(1) NOT NULL DEFAULT 0',
		'md_regdate DATETIME NOT NULL'
	];
	$t = [];
	foreach($r as $v) {
		$t[] = $v['Field'].' '.strtoupper($v['Type']=='mediumtext'?'text':$v['Type']).($v['Null']=='NO'?' NOT NULL':'')
			.($v['Default']!==NULL?' DEFAULT '.$v['Default']:'').($v['Extra']=='auto_increment'?' AUTO_INCREMENT':'');
	}
	echo '<b style="color:blue">'._AF_MODULE_TABLE_."</b><br>\n";
	foreach($o as $v) {
		if(!in_array($v, $t)){
			echo '<b style="color:red">- miss: </b>'.$v."<br>\n";
			ob_flush();
			flush();
			$success = false;
		}
	}

	/**********************************/
	$r = DB::query('SHOW FULL COLUMNS FROM '._AF_DOCUMENT_TABLE_, [], true);
	$o = [
		'wr_srl INT(11) NOT NULL AUTO_INCREMENT',
		'md_id CHAR(11) NOT NULL',
		'wr_parent INT(11) NOT NULL DEFAULT 0',
		'wr_status CHAR(1) NOT NULL DEFAULT 0',
		'wr_secret CHAR(1) NOT NULL DEFAULT 0',
		'wr_type CHAR(1) NOT NULL DEFAULT 0',
		'wr_category VARCHAR(20) NOT NULL DEFAULT ',
		'wr_title VARCHAR(255) NOT NULL',
		'wr_content LONGTEXT',
		'wr_extra TEXT',
		'wr_tags VARCHAR(255)',
		'wr_reply INT(11) NOT NULL DEFAULT 0',
		'wr_file INT(11) NOT NULL DEFAULT 0',
		'wr_hit INT(11) NOT NULL DEFAULT 0',
		'wr_yes INT(11) NOT NULL DEFAULT 0',
		'wr_no INT(11) NOT NULL DEFAULT 0',
		'mb_srl INT(11) NOT NULL DEFAULT 0',
		'mb_rank CHAR(1) NOT NULL DEFAULT 0',
		'mb_nick VARCHAR(20) NOT NULL',
		'mb_password VARCHAR(100)',
		'mb_ipaddress VARCHAR(128) NOT NULL DEFAULT ',
		'wr_update DATETIME NOT NULL',
		'wr_updater VARCHAR(20)',
		'wr_regdate DATETIME NOT NULL'
	];
	$t = [];
	foreach($r as $v) {
		$t[] = $v['Field'].' '.strtoupper($v['Type']=='mediumtext'?'text':$v['Type']).($v['Null']=='NO'?' NOT NULL':'')
			.($v['Default']!==NULL?' DEFAULT '.$v['Default']:'').($v['Extra']=='auto_increment'?' AUTO_INCREMENT':'');
	}
	echo '<b style="color:blue">'._AF_DOCUMENT_TABLE_."</b><br>\n";
	foreach($o as $v) {
		if(!in_array($v, $t)){
			echo '<b style="color:red">- miss: </b>'.$v."<br>\n";
			ob_flush();
			flush();
			$success = false;
		}
	}

	/**********************************/
	$r = DB::query('SHOW FULL COLUMNS FROM '._AF_COMMENT_TABLE_, [], true);
	$o = [
		'rp_srl INT(11) NOT NULL AUTO_INCREMENT',
		'wr_srl INT(11) NOT NULL',
		'rp_depth CHAR(5) NOT NULL DEFAULT ',
		'rp_parent INT(11) NOT NULL DEFAULT 0',
		'rp_status CHAR(1) NOT NULL DEFAULT 0',
		'rp_secret CHAR(1) NOT NULL DEFAULT 0',
		'rp_type CHAR(1) NOT NULL DEFAULT 0',
		'rp_content TEXT',
		'rp_file INT(11) NOT NULL DEFAULT 0',
		'mb_srl INT(11) NOT NULL DEFAULT 0',
		'mb_rank CHAR(1) NOT NULL DEFAULT 0',
		'mb_nick VARCHAR(20) NOT NULL',
		'mb_password VARCHAR(100)',
		'mb_ipaddress VARCHAR(128) NOT NULL DEFAULT ',
		'rp_update DATETIME NOT NULL',
		'rp_regdate DATETIME NOT NULL'
	];
	$t = [];
	foreach($r as $v) {
		$t[] = $v['Field'].' '.strtoupper($v['Type']=='mediumtext'?'text':$v['Type']).($v['Null']=='NO'?' NOT NULL':'')
			.($v['Default']!==NULL?' DEFAULT '.$v['Default']:'').($v['Extra']=='auto_increment'?' AUTO_INCREMENT':'');
	}
	echo '<b style="color:blue">'._AF_COMMENT_TABLE_."</b><br>\n";
	foreach($o as $v) {
		if(!in_array($v, $t)){
			echo '<b style="color:red">- miss: </b>'.$v."<br>\n";
			ob_flush();
			flush();
			$success = false;
		}
	}

	/**********************************/
	$r = DB::query('SHOW FULL COLUMNS FROM '._AF_PAGE_TABLE_, [], true);
	$o = [
		'md_id CHAR(11) NOT NULL',
		'pg_type CHAR(1) NOT NULL DEFAULT 0',
		'pg_content LONGTEXT',
		'pg_extra TEXT',
		'pg_file INT(11) NOT NULL DEFAULT 0',
		'pg_reply INT(11) NOT NULL DEFAULT 0',
		'pg_update DATETIME NOT NULL',
		'pg_regdate DATETIME NOT NULL'
	];
	$t = [];
	foreach($r as $v) {
		$t[] = $v['Field'].' '.strtoupper($v['Type']=='mediumtext'?'text':$v['Type']).($v['Null']=='NO'?' NOT NULL':'')
			.($v['Default']!==NULL?' DEFAULT '.$v['Default']:'').($v['Extra']=='auto_increment'?' AUTO_INCREMENT':'');
	}
	echo '<b style="color:blue">'._AF_PAGE_TABLE_."</b><br>\n";
	foreach($o as $v) {
		if(!in_array($v, $t)){
			echo '<b style="color:red">- miss: </b>'.$v."<br>\n";
			ob_flush();
			flush();
			$success = false;
		}
	}

	/**********************************/
	$r = DB::query('SHOW FULL COLUMNS FROM '._AF_FILE_TABLE_, [], true);
	$o = [
		'mf_srl INT(11) NOT NULL AUTO_INCREMENT',
		'md_id CHAR(11) NOT NULL',
		'mf_target INT(11) NOT NULL',
		'mf_name VARCHAR(255) NOT NULL',
		'mf_upload_name VARCHAR(32) NOT NULL',
		'mf_type VARCHAR(128) NOT NULL',
		'mf_about VARCHAR(255)',
		'mf_link CHAR(1) NOT NULL DEFAULT 0',
		'mf_size INT(11) NOT NULL',
		'mf_download INT(11) NOT NULL DEFAULT 0',
		'mb_srl INT(11) NOT NULL DEFAULT 0',
		'mb_ipaddress VARCHAR(128) NOT NULL DEFAULT ',
		'mf_regdate DATETIME NOT NULL'
	];
	$t = [];
	foreach($r as $v) {
		$t[] = $v['Field'].' '.strtoupper($v['Type']=='mediumtext'?'text':$v['Type']).($v['Null']=='NO'?' NOT NULL':'')
			.($v['Default']!==NULL?' DEFAULT '.$v['Default']:'').($v['Extra']=='auto_increment'?' AUTO_INCREMENT':'');
	}
	echo '<b style="color:blue">'._AF_FILE_TABLE_."</b><br>\n";
	foreach($o as $v) {
		if(!in_array($v, $t)){
			echo '<b style="color:red">- miss: </b>'.$v."<br>\n";
			ob_flush();
			flush();
			$success = false;
		}
	}

	/**********************************/
	$r = DB::query('SHOW FULL COLUMNS FROM '._AF_HISTORY_TABLE_, [], true);
	$o = [
		'mb_srl INT(11) NOT NULL DEFAULT 0',
		'hs_action VARCHAR(100) NOT NULL',
		'hs_value VARCHAR(255) NOT NULL DEFAULT ',
		'hs_regdate DATETIME NOT NULL'
	];
	$t = [];
	foreach($r as $v) {
		$t[] = $v['Field'].' '.strtoupper($v['Type']=='mediumtext'?'text':$v['Type']).($v['Null']=='NO'?' NOT NULL':'')
			.($v['Default']!==NULL?' DEFAULT '.$v['Default']:'').($v['Extra']=='auto_increment'?' AUTO_INCREMENT':'');
	}
	echo '<b style="color:blue">'._AF_HISTORY_TABLE_."</b><br>\n";
	foreach($o as $v) {
		if(!in_array($v, $t)){
			echo '<b style="color:red">- miss: </b>'.$v."<br>\n";
			ob_flush();
			flush();
			$success = false;
		}
	}

	/**********************************/
	$r = DB::query('SHOW FULL COLUMNS FROM '._AF_NOTE_TABLE_, [], true);
	$o = [
		'nt_srl INT(11) NOT NULL AUTO_INCREMENT',
		'mb_srl INT(11) NOT NULL DEFAULT 0',
		'nt_sender INT(11) NOT NULL DEFAULT 0',
		'nt_sender_nick VARCHAR(20) NOT NULL',
		'nt_send_date DATETIME NOT NULL',
		'nt_read_date DATETIME NOT NULL',
		'nt_content TEXT'
	];
	$t = [];
	foreach($r as $v) {
		$t[] = $v['Field'].' '.strtoupper($v['Type']=='mediumtext'?'text':$v['Type']).($v['Null']=='NO'?' NOT NULL':'')
			.($v['Default']!==NULL?' DEFAULT '.$v['Default']:'').($v['Extra']=='auto_increment'?' AUTO_INCREMENT':'');
	}
	echo '<b style="color:blue">'._AF_NOTE_TABLE_."</b><br>\n";
	foreach($o as $v) {
		if(!in_array($v, $t)){
			echo '<b style="color:red">- miss: </b>'.$v."<br>\n";
			ob_flush();
			flush();
			$success = false;
		}
	}

	/**********************************/
	$r = DB::query('SHOW FULL COLUMNS FROM '._AF_VISITOR_TABLE_, [], true);
	$o = [
		'mb_ipaddress VARCHAR(128) NOT NULL',
		'vs_agent VARCHAR(255) NOT NULL',
		'vs_referer VARCHAR(255) NOT NULL',
		'vs_regdate DATETIME NOT NULL'
	];
	$t = [];
	foreach($r as $v) {
		$t[] = $v['Field'].' '.strtoupper($v['Type']=='mediumtext'?'text':$v['Type']).($v['Null']=='NO'?' NOT NULL':'')
			.($v['Default']!==NULL?' DEFAULT '.$v['Default']:'').($v['Extra']=='auto_increment'?' AUTO_INCREMENT':'');
	}
	echo '<b style="color:blue">'._AF_VISITOR_TABLE_."</b><br>\n";
	foreach($o as $v) {
		if(!in_array($v, $t)){
			echo '<b style="color:red">- miss: </b>'.$v."<br>\n";
			ob_flush();
			flush();
			$success = false;
		}
	}

	/**********************************/
	$r = DB::query('SHOW FULL COLUMNS FROM '._AF_TRIGGER_TABLE_, [], true);
	$o = [
		'tg_key CHAR(1) NOT NULL',
		'tg_id VARCHAR(100) NOT NULL',
		'use_pc CHAR(1) NOT NULL DEFAULT 0',
		'use_mobile CHAR(1) NOT NULL DEFAULT 0',
		'grant_access CHAR(1) NOT NULL DEFAULT 0'
	];
	$t = [];
	foreach($r as $v) {
		$t[] = $v['Field'].' '.strtoupper($v['Type']=='mediumtext'?'text':$v['Type']).($v['Null']=='NO'?' NOT NULL':'')
			.($v['Default']!==NULL?' DEFAULT '.$v['Default']:'').($v['Extra']=='auto_increment'?' AUTO_INCREMENT':'');
	}
	echo '<b style="color:blue">'._AF_TRIGGER_TABLE_."</b><br>\n";
	foreach($o as $v) {
		if(!in_array($v, $t)){
			echo '<b style="color:red">- miss: </b>'.$v."<br>\n";
			ob_flush();
			flush();
			$success = false;
		}
	}

?>
<?php
	if($success) {
		DB::query('UPDATE `'._AF_CONFIG_TABLE_.'` SET `version`="'._AF_VERSION_.'" WHERE 1');
		if($error = DB::error()) echo "<br>\n".('<b style="color:red">'.$error->getMessage().'</b>');
		else echo "<br>\n".('<b style="color:green">업데이트 성공</b>');
	}
?>
</body></html>

<?php
/* End of file update.php */
/* Location: ./install/update.php */
