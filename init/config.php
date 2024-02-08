<?php if(!defined('__AFOX__')) exit();

require_once __DIR__ . '/constant.php';
(@include_once(_AF_CONFIG_DATA_.'_db_config.php')) OR exit('Please <a href="./install/">install</a> afox.');

define('_AF_TIME_ZONE_', $_DBINFO['time_zone']);
define('_AF_DOMAIN_', $_DBINFO['domain']);
define('_AF_COOKIE_DOMAIN_', $_DBINFO['cookie_domain']);

// javascript 에서 먼저 읽을 수 있게 set_cookie_params 전에 입력
set_cookie('_AF_COOKIE_DOMAIN_', _AF_COOKIE_DOMAIN_, 0);
date_default_timezone_set(_AF_TIME_ZONE_);
session_set_cookie_params(0, '/', _AF_COOKIE_DOMAIN_);
if(session_status() == PHP_SESSION_NONE) session_start();

$_LANG = [];
$_PROTECT = [];
$_ADDELEMENTS = ['LANG'=>[],'CSS'=>[],'JS'=>[]];

//load DB // When using a query, you must perform the escape yourself, or use parameters
require_once _AF_PATH_ . 'lib/db/mysql'.(function_exists('mysqli_connect')?'i':'').'.php';
DB::init($_DBINFO); unset($_DBINFO);

//site default info
($_CFG = @DB::get(_AF_CONFIG_TABLE_)) OR exit("Please reinstall afox.");
(_AF_VERSION_ == @$_CFG['version']) OR exit('Please <a href="./install/update.php">update</a> afox.');

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
function set_session($key, $val){$_SESSION[$key]=$val;}
function get_session($key){return isset($_SESSION[$key])?$_SESSION[$key]:'';}
function set_cookie($key, $val, $exp = 0){ //If 0, remove it when exit the browser //if -, remove
	setcookie(encode64($key),encode64($val),($exp>0?_AF_SERVER_TIME_+$exp:$exp),'/',_AF_COOKIE_DOMAIN_);
}
function get_cookie($key){
	return array_key_exists($cki=encode64($key),$_COOKIE)?decode64($_COOKIE[$cki]):'';
}
function set_cache($key, $val, $exp = 0){ //If 0, keep //if -, remove
	if(!is_dir(_AF_CACHE_DATA_)&&!mkdir(_AF_CACHE_DATA_,_AF_DIR_PERMIT_,true)) return;
	$s = '<?php if(!defined(\'__AFOX__\'))exit();$_EXPIRE=%s;$_CACHE=%s;?>';
	$s = sprintf($s,($exp?_AF_SERVER_TIME_+$exp:$exp),var_export($val,true));
	file_put_contents(_AF_CACHE_DATA_.encode64($key).'.php', $s, LOCK_EX);
}
function get_cache($key){ static $__af_caches = null;
	if(!empty($__af_caches[$key])) return $__af_caches[$key];
	if(!file_exists($f=(_AF_CACHE_DATA_.encode64($key).'.php'))) return;
	if((@include $f)!==1||(!empty($_EXPIRE)&&$_EXPIRE<_AF_SERVER_TIME_))
	{@chmod($f,0707);@unlink($f);return;} return ($__af_caches[$key] = $_CACHE);
}
function debugPrint($o = null){ if(!(__DEBUG__ & 1)) return;
	file_put_contents(_AF_PATH_.'_debug.php',implode(PHP_EOL,[date('== Y-m-d H:i:s =='),
	in_array(($type=gettype($o)),['array','object','resource'])?print_r($o,true):$type.'('.var_export($o,true).')'
	]).PHP_EOL.PHP_EOL,FILE_APPEND|LOCK_EX);
}

/* End of file config.php */
/* Location: ./init/config.php */
