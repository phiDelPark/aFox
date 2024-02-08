<?php if(!defined('__AFOX__')) exit();
(@include_once(_AF_CONFIG_DATA_.'_db_config.php')) OR exit('Please <a href="./install/">install</a> afox.');
//load DB // When using a query, you must perform the escape yourself, or use parameters
require_once _AF_PATH_ . 'lib/db/mysql'.(function_exists('mysqli_connect')?'i':'').'.php';
define('_AF_TIME_ZONE_', $_DBINFO['time_zone']);
define('_AF_DOMAIN_', $_DBINFO['domain']);
define('_AF_COOKIE_DOMAIN_', $_DBINFO['cookie_domain']);
DB::init($_DBINFO); unset($_DBINFO);

date_default_timezone_set(_AF_TIME_ZONE_);
session_set_cookie_params(0, '/', _AF_COOKIE_DOMAIN_);
if(session_status() == PHP_SESSION_NONE) session_start();

$_LANG = [];
$_PROTECT = [];
$_ADDELEMENTS = ['LANG'=>[],'CSS'=>[],'JS'=>[]];

//site default info
($_CFG = @DB::get(_AF_CONFIG_TABLE_)) OR exit("Please reinstall afox.");
(_AF_VERSION_ == @$_CFG['version']) OR exit('Please <a href="./install/update.php">update</a> afox.');

define('_AF_LANG_', $_CFG['lang']?$_CFG['lang']:'ko');
define('_AF_THEME_', $_CFG['theme']?$_CFG['theme']:'default');
define('_AF_THEME_PATH_', _AF_THEMES_PATH_ . _AF_THEME_ . '/');

@include_once _AF_PATH_ . 'common/lang/' . _AF_LANG_ . '.php';
require_once _AF_INIT_PATH_ . 'function.php';

//when using visit history
if($_CFG['use_visit'] == '1' && get_cookie('ck_visit_ip') != $_SERVER['REMOTE_ADDR']){
	set_cookie('ck_visit_ip', $_SERVER['REMOTE_ADDR'], 86400); // 하루동안 저장
	if(!isCrawler()) insertVisitorHistory();
}

define('__MOBILE__', isMobilePhone());
define('__REQ_METHOD__', getRequestMethod());
define('_AF_URL_', getRequestUri());
define('_AF_THEME_URL_', _AF_URL_ . 'theme/' . _AF_THEME_ . '/');

//login member
$_MEMBER = isset($_SESSION['AF_LOGIN_ID']) ? $_SESSION['AF_LOGIN_ID'] : get_cookie('AF_LOGIN_ID');
if($_MEMBER && preg_match('/^[a-zA-Z]+\w{2,}$/', $_MEMBER) && $_MEMBER = getMember($_MEMBER)){
	if($_MEMBER['mb_grade'] != 'admin' && !isset($_SESSION['AF_LOGIN_ID'])){ // 쿠키검사, 관리자는 안함
$tmp=md5($_SERVER['SERVER_ADDR'].$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'].$_MEMBER['mb_password']);
$tmp&&get_cookie('AF_AUTO_LOGIN')===$tmp?set_session('AF_LOGIN_ID',$_MEMBER['mb_id']):($_MEMBER=[]);
	}
} else $_MEMBER = [];
if(empty($_MEMBER)){ //not logged in
	set_cookie('AF_LOGIN_ID', '', -1);
	set_cookie('AF_AUTO_LOGIN', '', -1);
	unset($_SESSION['AF_LOGIN_ID']);
} else unset($_MEMBER['mb_password']); //delete password

$_POST = __REQ_METHOD__ == 'JSON' //JSON accepts POST only
	? json_decode(file_get_contents('php://input'), TRUE)
	: array_merge($_GET, $_POST); //join
//unset($_GET);

///*첫번째 키가 모듈인지 체크 (.htaccess 대신 사용할때)
if(!isset($_POST['module'])){
	if(file_exists(_AF_MODULES_PATH_ . ($tmp=key($_POST)) . '/setup.php')){
		$_POST['module'] = $tmp;
		if(empty($_POST['act'])) $_POST['disp']=empty($_POST[$tmp])?'default':$_GET[$tmp];
	}
}//*/

//validation test
foreach (['module','id','act','disp'] as $tmp){
	if(!isset($_POST[$tmp])||!preg_match('/^[a-zA-Z]+\w{2,}$/',$_POST[$tmp]))$_POST[$tmp]='';
}

//if only srl, get id
if(empty($_POST['act']) && (!empty($_POST['srl']) || !empty($_POST['rp']))){
	if(!empty($_POST['rp'])&&$tmp=DB::get(_AF_COMMENT_TABLE_,'wr_srl',['rp_srl'=>(int)$_POST['rp']]))
		$_POST['srl'] = $tmp['wr_srl'];
	if(!empty($_POST['srl'])&&$tmp=DB::get(_AF_DOCUMENT_TABLE_,'md_id',['wr_srl'=>(int)$_POST['srl']]))
		$_POST['id'] = $tmp['md_id'];
	setQuery('id',$_POST['id'],'srl',$_POST['srl'],'rp',empty($_POST['rp'])?'':$_POST['rp']);
}

//if no module value exists, use the start page
if(!$_POST['module'] && !$_POST['id']) $_POST['id'] = $_CFG['start'];
if($_POST['id'] && ($tmp=getModule($_POST['id']))){
	$_CFG = array_merge($_CFG, $tmp); // join
	$_POST['module'] = $tmp['md_key'];
}
$_CFG['logo'] = file_exists(_AF_CONFIG_DATA_.'logo.png') ? _AF_URL_.'data/config/logo.png' : FALSE;
$_CFG['favicon'] = file_exists(_AF_CONFIG_DATA_.'favicon.ico') ? _AF_URL_.'data/config/favicon.ico' : FALSE;

define('__MID__', $_POST['id']);
define('__MODULE__', $_POST['module']);
define('__MODAL__', !empty($_POST['modal']) && $_POST['modal'] === '1');
define('__POPUP__', __MODAL__ || (!empty($_POST['popup']) && $_POST['popup'] === '1'));

@include_once _AF_THEME_PATH_ . 'lang/' . _AF_LANG_ . '.php';

if(__MODULE__){
	if(file_exists(($tmp = _AF_MODULES_PATH_ . __MODULE__) . '/index.php')){
		@include_once $tmp . '/lang/' . _AF_LANG_ . '.php';
		require_once $tmp . '/protect.php'; require_once $tmp . '/index.php';
	} else goUrl(_AF_URL_, '');
}

$tmp = _AF_CONFIG_DATA_.'base_cdn_list.php';
if(!file_exists($tmp) || !empty($_POST['cdnerr'])){
	setQuery('cdnerr', ''); //If a CDN error, disable until the browser shuts down
	set_cookie('_CDN_ERROR_', TRUE, 0);
}
define('_AF_USE_BASE_CDN_', get_cookie('_CDN_ERROR_') ? FALSE : $tmp);

header('Content-Type: '.(__REQ_METHOD__=='JSON'?'application/json':'text/html').'; charset=UTF-8');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache"); unset($tmp);

/* End of file common.php */
/* Location: ./init/common.php */