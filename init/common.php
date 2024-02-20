<?php if(!defined('__AFOX__')) exit();
(@include_once(_AF_CONFIG_DATA_.'_db_config.php')) OR exit('Please <a href="./install/">install</a> afox.');
//load DB // When using a query, you must perform the escape yourself, or use parameters
require_once _AF_PATH_ . 'lib/db/mysql'.(function_exists('mysqli_connect')?'i':'').'.php';
define('_AF_TIME_ZONE_', $_DBINFO['time_zone']);
define('_AF_DOMAIN_', $_DBINFO['domain']);
define('_AF_COOKIE_DOMAIN_', $_DBINFO['cookie_domain']);
DB::connect($_DBINFO); unset($_DBINFO);

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

define('_MOBILE_', isMobilePhone());
define('_REQ_METHOD_', getRequestMethod());
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

 //JSON accepts POST only
if(_REQ_METHOD_ == 'JSON') $_POST = json_decode(file_get_contents('php://input'), TRUE);
if(_REQ_METHOD_ != 'GET') $_GET['module'] = @$_POST['module'].'';

//*/첫번째 키가 모듈인지 체크 (.htaccess 대신 사용할때)
if(!isset($_GET['module'])&&file_exists(_AF_MODULES_PATH_.($tmp=key($_GET)).'/setup.php')){
	$_GET['module'] = $tmp; $_GET['disp'] = @$_GET[$tmp];
}//*/
// 보안을 위해 _GET & _POST 키 첫글자는 소문자만 받음
foreach ($_GET as $tmp=>$tmp) if(preg_match('/^[^a-z]/', $tmp)) unset($_GET[$tmp]);
foreach ($_POST as $tmp=>$tmp) if(preg_match('/^[^a-z]/', $tmp)) unset($_POST[$tmp]);

//if only srl, get id
if(!isset($_GET['module'])&&(!empty($_GET['srl']) || !empty($_GET['rp']))){
	if(@$_GET['rp']&&$tmp=DB::get(_AF_COMMENT_TABLE_,'wr_srl',['rp_srl'=>$_GET['rp']]))
		$_GET['srl'] = $tmp['wr_srl'];
	if(@$_GET['srl']&&$tmp=DB::get(_AF_DOCUMENT_TABLE_,'md_id',['wr_srl'=>(int)$_GET['srl']]))
		$_GET['id'] = $tmp['md_id'];
	setQuery('id',$_GET['id'],'srl',$_GET['srl'],'rp',@$_GET['rp']?$_GET['rp']:'');
}

//if no module value exists, use the start page
if(!isset($_GET['module']) && !@$_GET['id']) $_GET['id'] = $_CFG['start'];
if(@$_GET['id'] && ($tmp=getModule($_GET['id']))){
	$_CFG = array_merge($_CFG, $tmp); // join
	$_GET['module'] = $tmp['md_key'];
}

$_CFG['logo'] = file_exists(_AF_CONFIG_DATA_.'logo.png') ? _AF_URL_.'data/config/logo.png' : FALSE;
$_CFG['favicon'] = file_exists(_AF_CONFIG_DATA_.'favicon.ico') ? _AF_URL_.'data/config/favicon.ico' : FALSE;

define('_MID_', _REQ_METHOD_ == 'GET' ? @$_GET['id'] : @$_POST['md_id']);
define('_MODULE_', _REQ_METHOD_ == 'GET' ? @$_GET['module'] : @$_POST['module']);
define('_MODAL_', @$_GET['modal'] === '1');
define('_POPUP_', _MODAL_ || @$_GET['popup'] === '1');

@include_once _AF_THEME_PATH_ . 'lang/' . _AF_LANG_ . '.php';

if(_MODULE_){
	if(file_exists(($tmp = _AF_MODULES_PATH_ . _MODULE_) . '/index.php')){
		@include_once $tmp . '/lang/' . _AF_LANG_ . '.php';
		require_once $tmp . '/protect.php'; require_once $tmp . '/index.php';
	} else goUrl(_AF_URL_, '');
}

$tmp = _AF_CONFIG_DATA_.'base_cdn_list.php';
if(!file_exists($tmp) || !empty($_GET['cdnerr'])){
	setQuery('cdnerr', ''); //If a CDN error, disable until the browser shuts down
	set_cookie('_CDN_ERROR_', TRUE, 0);
}
define('_AF_USE_BASE_CDN_', get_cookie('_CDN_ERROR_') ? FALSE : $tmp);

header('Content-Type: '.(_REQ_METHOD_=='JSON'?'application/json':'text/html').'; charset=UTF-8');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Expires: 0");

/* End of file common.php */
/* Location: ./init/common.php */