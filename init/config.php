<?php
if(!defined('__AFOX__')) exit();

define('_AF_VERSION_', '0.374');
define('__DEBUG__', 0);

/*** SSL ***/
define('_AF_USE_SSL_', 0); // 1 = always, 2 = optional
define('_AF_HTTP_PORT_', 80);
define('_AF_HTTPS_PORT_', 443);
/***********/

define('_AF_CONFIG_TABLE_', 'afox_config');
define('_AF_MEMBER_TABLE_', 'afox_members');
define('_AF_MODULE_TABLE_', 'afox_modules');
define('_AF_THEME_TABLE_', 'afox_themes');
define('_AF_MENU_TABLE_', 'afox_menus');
define('_AF_ADDON_TABLE_', 'afox_addons');
define('_AF_PAGE_TABLE_', 'afox_pages');
define('_AF_DOCUMENT_TABLE_', 'afox_documents');
define('_AF_COMMENT_TABLE_', 'afox_comments');
define('_AF_HISTORY_TABLE_', 'afox_histories');
define('_AF_VISITOR_TABLE_', 'afox_visitors');
define('_AF_NOTE_TABLE_', 'afox_notes');
define('_AF_FILE_TABLE_', 'afox_files');
define('_AF_TRIGGER_TABLE_', 'afox_triggers');
define('_AF_REPORT_TABLE_', 'afox_reports');

define('_AF_PATH_', substr(str_replace('\\', '/', dirname(__FILE__)), 0, -4));

define('_AF_INIT_PATH_', _AF_PATH_ . 'init/');
define('_AF_LIBS_PATH_', _AF_PATH_ . 'lib/');
define('_AF_MODULES_PATH_', _AF_PATH_ . 'module/');
define('_AF_ADDONS_PATH_', _AF_PATH_ . 'addon/');
define('_AF_WIDGETS_PATH_', _AF_PATH_ . 'widget/');
define('_AF_THEMES_PATH_', _AF_PATH_ . 'theme/');
define('_AF_ADMIN_PATH_', _AF_PATH_ . 'module/admin/');
define('_AF_TPLS_PATH_', _AF_PATH_ . 'common/tpl/');

define('_AF_CONFIG_DATA_', _AF_PATH_ . 'data/config/');
define('_AF_MEMBER_DATA_', _AF_PATH_ . 'data/member/');
define('_AF_MODULE_DATA_', _AF_PATH_ . 'data/module/');
define('_AF_ATTACH_DATA_', _AF_PATH_ . 'data/attach/');
define('_AF_CACHE_DATA_', _AF_PATH_ . 'data/cache/');

define('_AF_DIR_PERMIT_', 0755);
define('_AF_FILE_PERMIT_', 0644);
define('_AF_PASSWORD_ALGORITHM_', function_exists('password_hash')?'BCRYPT':'MYSQL');

(@include_once(_AF_CONFIG_DATA_ . '_db_config.php')) OR die("Please <a href=\"./install/\">install</a> afox.");
define('_AF_DOMAIN_', $_DBINFO['domain']);
define('_AF_COOKIE_DOMAIN_', $_DBINFO['cookie_domain']);
define('_AF_CdOOKIE_DOMAIN_', 'cookie_domain');
define('_AF_TIME_ZONE_', $_DBINFO['time_zone']);
define('_AF_SERVER_TIME_', time());

set_cookie('_AF_COOKIE_DOMAIN_', _AF_COOKIE_DOMAIN_, 18000);
date_default_timezone_set(_AF_TIME_ZONE_);
session_set_cookie_params(0, '/', _AF_COOKIE_DOMAIN_);
if(session_status() == PHP_SESSION_NONE) session_start();

$_LANG = [];
$_PROTECT = [];
$_ADDELEMENTS = ['LANG'=>[],'CSS'=>[],'JS'=>[]];

//load DB // When using a query, you must perform the escape yourself, or use parameters
require_once _AF_PATH_ . 'lib/db/mysql'.(function_exists('mysqli_connect')?'i':'').'.php';
DB::init($_DBINFO);
unset($_DBINFO);

//if update
if(file_exists(_AF_PATH_ . 'install/update.php')){
	require_once _AF_PATH_ . 'install/update.php';
	exit();
}

//site default info
$_CFG = DB::get(_AF_CONFIG_TABLE_);
if(DB::error()) exit("Please reinstall afox.");

define('_AF_LANG_', $_CFG['lang']?$_CFG['lang']:'ko');
define('_AF_THEME_', $_CFG['theme']?$_CFG['theme']:'default');
define('_AF_THEME_PATH_', _AF_THEMES_PATH_ . _AF_THEME_ . '/');

// Essential function common to data management //
function encode64($v){ //Because of js without encryption library
	return str_replace(array('=','/'),array('%3d','%2f'),base64_encode(rawurlencode($v)));
}
function decode64($v){
	return rawurldecode(base64_decode(str_replace(array('%3d','%2f'),array('=','/'),$v)));
}
function set_error($msg, $err = 3){
	return $_SESSION['AF_VALIDATOR_ERROR']=['error'=>$err,'message'=>$msg];
}
function get_error(){
	return isset($_SESSION[($key='AF_VALIDATOR_ERROR')])?$_SESSION[$key]:'';
}
function set_session($key, $val){
	if($val===null){unset($_SESSION[$key]);}else{$_SESSION[$key]=$val;}
}
function get_session($key, $remove = false){
	$session=isset($_SESSION[$key])?$_SESSION[$key]:'';
	if($remove&&$session) unset($_SESSION[$key]); return $session;
}
function set_cookie($key, $val, $exp = 0){ //if 0, 1 year //if -, remove
	setcookie(encode64($key),encode64($val),_AF_SERVER_TIME_+($exp?$exp:31536000),'/',_AF_COOKIE_DOMAIN_);
}
function get_cookie($key, $remove = false){
	$cookie = array_key_exists($cki=encode64($key),$_COOKIE)?decode64($_COOKIE[$cki]):'';
	if($remove&&$cookie) unset($_COOKIE[$cki]); return $cookie;
}
function set_cache($key, $val, $exp = 0){
	if(!is_dir(_AF_CACHE_DATA_)&&!mkdir(_AF_CACHE_DATA_,_AF_DIR_PERMIT_,true)) return;
	$s = '<?php if(!defined(\'__AFOX__\'))exit();$_EXPIRE=%s;$_CACHE=%s; ?>';
	$s = sprintf($s,_AF_SERVER_TIME_+($exp?$exp:31536000),var_export($val,true));
	file_put_contents(_AF_CACHE_DATA_.encode64($key).'.php', $s, LOCK_EX);
}
function get_cache($key, $remove = false){ static $__af_caches = null;
	if(!empty($__af_caches[$key])) return $__af_caches[$key];
	if(!file_exists($f=(_AF_CACHE_DATA_.encode64($key).'.php'))) return;
	if((@include $f)!==1||(!empty($_EXPIRE)&&$_EXPIRE<_AF_SERVER_TIME_)||$remove){
		@chmod($f, 0707); @unlink($f); if(!$remove) return;
	} return $__af_caches[$key] = $_CACHE;
}
function debugPrint($o = null){ if(!(__DEBUG__ & 1)) return;
	file_put_contents(_AF_PATH_.'_debug.php',implode(PHP_EOL,[date('== Y-m-d H:i:s =='),
	in_array(($type=gettype($o)),['array','object','resource'])?print_r($o,true):$type.'('.var_export($o,true).')'
	]).PHP_EOL.PHP_EOL,FILE_APPEND|LOCK_EX);
}

/* End of file config.php */
/* Location: ./init/config.php */
