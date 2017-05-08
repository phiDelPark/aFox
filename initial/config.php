<?php
if(!defined('__AFOX__')) exit();

define('_AF_VERSION_', '0.7.0');
define('__DEBUG__', 0);

/*** SSL 설정 ***/
define('_AF_USE_SSL_', 0); // 1 = always, 2 = optional
define('_AF_HTTP_PORT_', 80);
define('_AF_HTTPS_PORT_', 443);
/**************/

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

if(!defined('_AF_PATH_')){
	define('_AF_PATH_', substr(str_replace('\\', '/', dirname(__FILE__)), 0, -8) . '/');
}

define('_AF_ADMIN_PATH_', _AF_PATH_ . 'module/admin/');
define('_AF_INIT_PATH_', _AF_PATH_ . 'initial/');
define('_AF_LIBS_PATH_', _AF_PATH_ . 'lib/');
define('_AF_MODULES_PATH_', _AF_PATH_ . 'module/');
define('_AF_ADDONS_PATH_', _AF_PATH_ . 'addon/');
define('_AF_WIDGETS_PATH_', _AF_PATH_ . 'widget/');
define('_AF_TPLS_PATH_', _AF_PATH_ . 'tpl/');
define('_AF_THEMES_PATH_', _AF_PATH_ . 'theme/');
define('_AF_LANGS_PATH_', _AF_PATH_ . 'common/lang/');

define('_AF_CONFIG_DATA_', _AF_PATH_ . 'data/config/');
define('_AF_MEMBER_DATA_', _AF_PATH_ . 'data/member/');
define('_AF_ATTACH_DATA_', _AF_PATH_ . 'data/attach/');
define('_AF_CACHE_DATA_', _AF_PATH_ . 'data/cache/');

define('_AF_DIR_PERMIT_', 0755);
define('_AF_FILE_PERMIT_', 0644);

(@include_once(_AF_CONFIG_DATA_ . '_db_config.php')) OR die("Please install afox.");

define('_AF_DOMAIN_', $_DBINFO['domain']);
define('_AF_COOKIE_DOMAIN_', $_DBINFO['cookie_domain']);
define('_AF_TIME_ZONE_', $_DBINFO['time_zone']);

date_default_timezone_set(_AF_TIME_ZONE_);
session_set_cookie_params(0, '/', _AF_COOKIE_DOMAIN_);

if(session_status() == PHP_SESSION_NONE) {
	session_start();
}

$_LANG = [];
$_ADDONS = [];
$_ADDELEMENTS = ['JS'=>[],'CSS'=>[]];
unset($_MEMBER);

// DB 라이브러리 미리 로드
// SQL Injection 대비를 뤼해 DB 사용시 보통은 escape 되지만 직접 query를 사용할땐 escape를 직접하거나 parameter 사용.
require_once _AF_PATH_ . 'lib/db/mysql.php';
DB::init($_DBINFO);
unset($_DBINFO); // 쓰고나면 정보 제거

// 업데이트가 있으면 실행
if(file_exists(_AF_PATH_ . 'install/update.php')) {
	require_once _AF_PATH_ . 'install/update.php';
	exit();
}

// 기본 사이트 정보 가져오기
$_CFG = DB::get('SELECT * FROM '._AF_CONFIG_TABLE_.' WHERE 1');
if(DB::error()) exit("Please reinstall afox.");

define('_AF_LANG_', empty($_CFG['lang'])?'kr':$_CFG['lang']);
define('_AF_THEME_', empty($_CFG['theme'])?'default':$_CFG['theme']);
define('_AF_THEME_PATH_', _AF_THEMES_PATH_ . _AF_THEME_ . '/');

/* End of file config.php */
/* Location: ./initial/config.php */