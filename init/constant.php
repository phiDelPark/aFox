<?php if(!defined('__AFOX__')) exit();
define('_AF_VERSION_', '0.400');

define('__DEBUG__', 1);
define('_AF_SERVER_TIME_', time());

/*** SSL ***/
define('_AF_USE_SSL_', 0); // 1 = always, 2 = optional
define('_AF_HTTP_PORT_', 80);
define('_AF_HTTPS_PORT_', 443);
/***********/

define('_AF_DIR_PERMIT_', 0755);
define('_AF_FILE_PERMIT_', 0644);
define('_AF_ATTACH_PERMIT_', 0600);

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

define('_AF_PATH_', str_replace('\\', '/', substr(__DIR__, 0, -4)));

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

define('_AF_PASSWORD_ALGORITHM_', function_exists('password_hash')?'BCRYPT':'MYSQL');

if(!isset($_SERVER['SERVER_ADDR'])) $_SERVER['SERVER_ADDR'] = isset($_SERVER['LOCAL_ADDR']) ? $_SERVER['LOCAL_ADDR'] : '';
/*
if (!function_exists('array_key_first')) { // PHP 7 >= 7.3.0, PHP 8
	function array_key_first(array $arr) {
		foreach($arr as $key=>$key) return $key;
		return NULL;
	}
}
if (! function_exists("array_key_last")) { // PHP 7 >= 7.3.0, PHP 8
	function array_key_last($array) {
		if (!is_array($array) || empty($array)) return NULL;
		return array_keys($array)[count($array)-1];
	}
}
*/
/* End of file constant.php */
/* Location: ./init/constant.php */