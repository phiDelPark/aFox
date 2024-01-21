<?php
if(!defined('__AFOX__')) exit();

define('_AF_VERSION_', '0.374');
define('__DEBUG__', 1);

/*** SSL 설정 ***/
define('_AF_USE_SSL_', 0); // 1 = always, 2 = optional
define('_AF_HTTP_PORT_', 80);
define('_AF_HTTPS_PORT_', 443);
/**************/

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

// 이 아래 부터는 자동으로 입력 혹은 불러와야할 정보들
(@include_once(_AF_CONFIG_DATA_ . '_db_config.php')) OR die("Please <a href=\"./install/\">install</a> afox.");
define('_AF_DOMAIN_', $_DBINFO['domain']);
define('_AF_COOKIE_DOMAIN_', $_DBINFO['cookie_domain']);
define('_AF_TIME_ZONE_', $_DBINFO['time_zone']);
define('_AF_SERVER_TIME_', time());

date_default_timezone_set(_AF_TIME_ZONE_);
session_set_cookie_params(0, '/', _AF_COOKIE_DOMAIN_);
if(session_status() == PHP_SESSION_NONE) session_start();

$_LANG = [];
$_PROTECT = [];
$_ADDELEMENTS = ['LANG'=>[],'CSS'=>[],'JS'=>[]];

// DB 라이브러리 미리 로드
// SQL Injection 대비를 위해 DB 사용시 보통은 escape 되지만 직접 query를 사용할땐 escape를 직접하거나 parameter 사용
require_once _AF_PATH_ . 'lib/db/mysql'.(function_exists('mysqli_connect')?'i':'').'.php';
DB::init($_DBINFO);
unset($_DBINFO); // 쓰고나면 정보 제거

// 업데이트가 있으면 실행
if(file_exists(_AF_PATH_ . 'install/update.php')){
	require_once _AF_PATH_ . 'install/update.php';
	exit();
}

// 기본 사이트 정보 가져오기
$_CFG = DB::get(_AF_CONFIG_TABLE_);
if(DB::error()) exit("Please reinstall afox.");

define('_AF_LANG_', $_CFG['lang']?$_CFG['lang']:'ko');
define('_AF_THEME_', $_CFG['theme']?$_CFG['theme']:'default');
define('_AF_THEME_PATH_', _AF_THEMES_PATH_ . _AF_THEME_ . '/');


// 아래부턴 데이터 관리를 위해 공통으로 사용할 필수 함수들... //
// function.php 에 포함 안하는 이유는? load 안해도 사용하기 위해... //

function set_error($msg, $err = 3){
	return $_SESSION['AF_VALIDATOR_ERROR']=['error'=>$err,'message'=>$msg];
}
function get_error(){
	return isset($_SESSION[($key='AF_VALIDATOR_ERROR')])?$_SESSION[$key]:'';
}

function set_session($key, $val){
	if($val===null){unset($_SESSION[$key]);}else{$_SESSION[$key]=$val;}
}
function get_session($key){
	return isset($_SESSION[$key])?$_SESSION[$key]:'';
}

// 만료시간이 0이면 브라우저 종료전까지 유지, -값이면 만료로 만듬 (제거)
function set_cookie($key, $val, $exp = 0){
	setcookie(md5($key),base64_encode($val),$exp?_AF_SERVER_TIME_+$exp:0,'/',_AF_COOKIE_DOMAIN_);
}
function get_cookie($key){
	return array_key_exists($cki=md5($key),$_COOKIE)?base64_decode($_COOKIE[$cki]):'';
}

// 만료시간이 0이면 직접 지우기까지 계속 유지, - 값이면 읽을때 삭제됨 (재생성 필요)
function set_cache($key, $val, $exp = 0){
	if(!is_dir($dir=_AF_CACHE_DATA_.md5($key))&&!mkdir($dir,_AF_DIR_PERMIT_,true)) return;
	$h = @opendir($dir); while($e=readdir($h)){if($e!='.' && $e!='..'){
		@chmod($dir.'/'.$e, 0707); @unlink($dir.'/'.$e); }} @closedir($h);
	$s = '<?php if(!defined(\'__AFOX__\'))exit();$_EXPIRE=%s;$_CACHE=%s; ?>';
	$s = sprintf($s, $exp?_AF_SERVER_TIME_+$exp:0, var_export($val,true));
	file_put_contents($dir.'/'.md5(_AF_SERVER_TIME_).'.php', $s, LOCK_EX);
}
function get_cache($key){ static $__af_caches = null;
	if(!empty($__af_caches[$key])) return $__af_caches[$key];
	if(!is_dir($dir=_AF_CACHE_DATA_.md5($key))||!is_file($f=$dir.'/'.(@scandir($dir)[2]))) return;
	if((@include $f)!==1||(!empty($_EXPIRE)&&$_EXPIRE<_AF_SERVER_TIME_)){
		@chmod($f, 0707); @unlink($f); @chmod($dir, 0707); @rmdir($dir); return;
	} return $__af_caches[$key] = $_CACHE;
}

function debugPrint($o = null){ if(!(__DEBUG__ & 1)) return;
	file_put_contents(_AF_PATH_.'_debug.php',implode(PHP_EOL,[date('== Y-m-d H:i:s =='),
	in_array(($type=gettype($o)),['array','object','resource'])?print_r($o,true):$type.'('.var_export($o,true).')'
	]).PHP_EOL.PHP_EOL,FILE_APPEND|LOCK_EX);
}

/* End of file config.php */
/* Location: ./init/config.php */
