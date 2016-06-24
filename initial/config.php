<?php
if(!defined('__AFOX__')) exit();

define('__DEBUG__', 0);
define('_AF_VERSION_', '0.5.5');
define('_AF_SERVER_TIME_', time());

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

$tmp = str_replace('\\', '/', dirname(__FILE__));
define('_AF_URL_', substr(('http' . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 's' : '') . '://' . $_SERVER['SERVER_NAME'] . (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != '80' ? ':'.$_SERVER['SERVER_PORT'] : '') . str_replace(str_replace(preg_replace('/\/\/+/', '/',$_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_FILENAME']), '', $tmp)), 0, -8) . '/');
define('_AF_PATH_', substr($tmp, 0, -8) . '/');
define('_AF_ADMIN_PATH_', _AF_PATH_ . 'module/admin/');
define('_AF_INIT_PATH_', _AF_PATH_ . 'initial/');
define('_AF_LIBS_PATH_', _AF_PATH_ . 'lib/');
define('_AF_MODULES_PATH_', _AF_PATH_ . 'module/');
define('_AF_ADDONS_PATH_', _AF_PATH_ . 'addon/');
define('_AF_WIDGETS_PATH_', _AF_PATH_ . 'widget/');
define('_AF_TPLS_PATH_', _AF_PATH_ . 'tpl/');
define('_AF_THEMES_PATH_', _AF_PATH_ . 'theme/');
define('_AF_LANGS_PATH_', _AF_PATH_ . 'common/lang/');

define('_AF_DATA_PATH_', _AF_PATH_ . 'data/');
define('_AF_CONFIG_DATA_', _AF_DATA_PATH_.'config/');
define('_AF_MEMBER_DATA_', _AF_DATA_PATH_.'member/');
define('_AF_ATTACH_DATA_', _AF_DATA_PATH_.'attach/');
define('_AF_CACHE_DATA_', _AF_DATA_PATH_.'cache/');

define('_AF_DIR_PERMIT_', 0755);
define('_AF_FILE_PERMIT_', 0644);

(@include_once(_AF_CONFIG_DATA_ . '_db_config.php')) OR die("Please install afox");

if(file_exists(_AF_PATH_ . 'install/update.php')) {
	require_once _AF_PATH_ . 'install/update.php';
	exit();
}

define('_AF_USE_SSL_', $_DBINFO['use_ssl']);
define('_AF_HTTP_PORT_', empty($_DBINFO['http_port'])?80:(int)$_DBINFO['http_port']);
define('_AF_HTTPS_PORT_', empty($_DBINFO['https_port'])?443:(int)$_DBINFO['https_port']);

define('_AF_TIME_ZONE_', $_DBINFO['time_zone']);
define('_AF_COOKIE_DOMAIN_', $_DBINFO['cookie_domain']);

date_default_timezone_set(_AF_TIME_ZONE_);
session_set_cookie_params(0, '/', _AF_COOKIE_DOMAIN_);

if(session_status() == PHP_SESSION_NONE) {
	session_start();
}

/** DB및 기본설정과 일부 설정에 필요한 함수는 먼저 읽기 **/

// SQL Injection 대비를 뤼해 DB 사용시 보통은 escape 되지만 직접 query를 사용할땐 escape를 직접하거나 params 사용.
require_once _AF_PATH_ . 'lib/db/mysql.php';
DB::init($_DBINFO);
unset($_DBINFO); // 쓰고나면 정보 제거

// 기본 정보 가져오기
$_CFG = DB::get('SELECT * FROM '._AF_CONFIG_TABLE_.' WHERE 1');
if($tmp = DB::error()) exit($tmp->getMessage());

define('_AF_LANG_', empty($_CFG['lang'])?'kr':$_CFG['lang']);
define('_AF_THEME_', empty($_CFG['theme'])?'default':$_CFG['theme']);
define('_AF_THEME_URL_', _AF_URL_ . 'theme/' . _AF_THEME_ . '/');
define('_AF_THEME_PATH_', _AF_THEMES_PATH_ . _AF_THEME_ . '/');

$_LANG = [];
$_ADDONS = [];
$_ADDELEMENTS = ['JS'=>[],'CSS'=>[]];

// set_cookie 만료시간이 0이면 브라우저 종료전까지 유지, -값이면 만료된 쿠키로 만듬 (제거)
function set_cookie($_name, $value, $expire) { $expire = $expire > 0 ? _AF_SERVER_TIME_ + $expire : (empty($expire) ? 0 : _AF_SERVER_TIME_); setcookie(md5($_name), base64_encode($value), $expire, '/', _AF_COOKIE_DOMAIN_); }
function get_cookie($_name) { $cookie = md5($_name); if (array_key_exists($cookie, $_COOKIE)) return base64_decode($_COOKIE[$cookie]); else return ""; }
function set_session($_name, $value) { $_SESSION[$_name] = $value; }
function get_session($_name) { return isset($_SESSION[$_name]) ? $_SESSION[$_name] : ''; }
function set_error($message, $error = 3) { return $_SESSION['AF_VALIDATOR_ERROR'] = ['error'=>$error, 'message'=>$message]; }
function get_error() { return isset($_SESSION['AF_VALIDATOR_ERROR']) ? $_SESSION['AF_VALIDATOR_ERROR'] : ''; }
function debugPrint($_out = null) { if(!(__DEBUG__ & 1)) return; $print = [date('== Y-m-d H:i:s ==')]; $type = gettype($_out); if(in_array($type, ['array', 'object', 'resource'])) { $print[] = print_r($_out, true); } else { $print[] = $type . '(' . var_export($_out, true) . ')'.PHP_EOL; } file_put_contents(_AF_PATH_ . '_debug.php', implode(PHP_EOL, $print).PHP_EOL, FILE_APPEND|LOCK_EX); }

// 로그인 중이면 맴버 정보 가져오기
unset($_MEMBER);
if($tmp = (isset($_SESSION['ss_mb_id']) ? $_SESSION['ss_mb_id'] : get_cookie('ck_mb_id'))) {
	if(preg_match('/^[a-zA-Z]+\w{2,}$/', $tmp)) {
		$_MEMBER = DB::get("SELECT * FROM "._AF_MEMBER_TABLE_." WHERE mb_id = '{$tmp}'");
		if(DB::error() || empty($_MEMBER['mb_srl'])){
			unset($_MEMBER);
		} else {
			$tmp = $_MEMBER['mb_srl'].'/profile_image.png';
			if(file_exists(_AF_MEMBER_DATA_.$tmp)) $_MEMBER['mb_icon'] = _AF_URL_.'data/member/'.$tmp;
			// 쿠키이면... 키검사... 최고 관리자는 쿠키사용안함
			if(!isset($_SESSION['ss_mb_id'])) {
				$tmp = get_cookie('ck_auto');
				if(empty($tmp) || $_MEMBER['mb_rank'] == 's' || ($tmp !== md5($_SERVER['SERVER_ADDR'] . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . $_MEMBER['mb_password']))) {
					unset($_MEMBER);
				} else {
					set_session('ss_mb_id', $mb['mb_id']);
				}
			}
		}
	}
	// 아니면 삭제
	if(empty($_MEMBER)) {
		set_cookie('ck_mb_id', '', -1);
		set_cookie('ck_auto', '', -1);
		unset($_SESSION['ss_mb_id']);
	}
}

unset($tmp);

/* End of file config.php */
/* Location: ./initial/config.php */