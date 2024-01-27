<?php if(!defined('__AFOX__')) exit();

@include_once _AF_PATH_ . 'common/lang/' . _AF_LANG_ . '.php';
require_once _AF_INIT_PATH_ . 'function.php';

// 방문 기록 사용시
if($_CFG['use_visit'] == '1' && get_cookie('ck_visit_ip') != $_SERVER['REMOTE_ADDR']){
	set_cookie('ck_visit_ip', $_SERVER['REMOTE_ADDR'], 86400); // 하루동안 저장
	if(!isCrawler()) insertVisitorHistory();
}

define('__MOBILE__', isMobilePhone());
define('__REQ_METHOD__', getRequestMethod());
define('_AF_URL_', getRequestUri());
define('_AF_THEME_URL_', _AF_URL_ . 'theme/' . _AF_THEME_ . '/');

// 로그인 중인 회원 정보 가져오기
$_MEMBER = isset($_SESSION['AF_LOGIN_ID']) ? $_SESSION['AF_LOGIN_ID'] : get_cookie('AF_LOGIN_ID');
if($_MEMBER && preg_match('/^[a-zA-Z]+\w{2,}$/', $_MEMBER) && $_MEMBER = getMember($_MEMBER)){
		if($_MEMBER['mb_grade'] != 'admin' && !isset($_SESSION['AF_LOGIN_ID'])){ // 쿠키검사, 관리자는 안함
$tmp=md5($_SERVER['SERVER_ADDR'].$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'].$_MEMBER['mb_password']);
			if($tmp && get_cookie('AF_AUTO_LOGIN') === $tmp){
				set_session('AF_LOGIN_ID', $_MEMBER['mb_id']);
			} else $_MEMBER = [];
		}
} else $_MEMBER = [];
if(empty($_MEMBER)){ // 로그인이 아니면 삭제
	set_cookie('AF_LOGIN_ID', '', -1);
	set_cookie('AF_AUTO_LOGIN', '', -1);
	unset($_SESSION['AF_LOGIN_ID']);
} else unset($_MEMBER['mb_password']); // 검사 후 비번 삭제

$_POST = __REQ_METHOD__ == 'JSON' // JSON 은 POST 만 받음
	? json_decode(file_get_contents('php://input'), TRUE)
	: array_merge($_GET, $_POST); // 넘어온 값을 하나로 합침
//unset($_GET);

///*첫번째 키가 모듈인지 체크 (.htaccess 대신 사용할때)
if(!isset($_POST['module'])){
	if(file_exists(_AF_MODULES_PATH_ . ($tmp=key($_POST)) . '/setup.php')){
		$_POST['module'] = $tmp;
		if(empty($_POST['act'])) $_POST['disp'] = empty($_POST[$tmp]) ? 'default' : $_GET[$tmp];
	}
}//*/

// 유효성 검사
foreach (['module','id','act','disp'] as $tmp){
	if(!isset($_POST[$tmp])||!preg_match('/^[a-zA-Z]+\w{2,}$/', $_POST[$tmp]))
		$_POST[$tmp] = '';
}

// 문서번호만 오면 id 가져옴
if(empty($_POST['act']) && (!empty($_POST['srl']) || !empty($_POST['rp']))){
	if(!empty($_POST['rp'])&&$tmp=DB::get(_AF_COMMENT_TABLE_,'wr_srl',['rp_srl'=>(int)$_POST['rp']]))
		$_POST['srl'] = $tmp['wr_srl'];
	if(!empty($_POST['srl'])&&$tmp=DB::get(_AF_DOCUMENT_TABLE_,'md_id',['wr_srl'=>(int)$_POST['srl']]))
		$_POST['id'] = $tmp['md_id'];
	setQuery('','id',$_POST['id'],'srl',$_POST['srl'],'rp',empty($_POST['rp'])?'':$_POST['rp']);
}

// module, id 가 없으면 시작 페이지
// 위 유효성 검사에서 module, id 변수가 없으면 빈값을 넣기에 empty 안씀
if(!$_POST['module'] && !$_POST['id']) $_POST['id'] = $_CFG['start'];
if($_POST['id'] && ($tmp=getModule($_POST['id']))){
	$_CFG = array_merge($_CFG, $tmp); // 설정 하나로 합침
	$_POST['module'] = $tmp['md_key'];
}

define('__MID__', $_POST['id']);
define('__MODULE__', $_POST['module']);
define('__MODAL__', !empty($_POST['modal']) && $_POST['modal'] === '1');
define('__POPUP__', __MODAL__ || (!empty($_POST['popup']) && $_POST['popup'] === '1'));

$_CFG['logo'] = file_exists(_AF_CONFIG_DATA_.'logo.png') ? _AF_URL_.'data/config/logo.png' : FALSE;
$_CFG['favicon'] = file_exists(_AF_CONFIG_DATA_.'favicon.ico') ? _AF_URL_.'data/config/favicon.ico' : FALSE;

@include_once _AF_THEME_PATH_ . 'lang/' . _AF_LANG_ . '.php';

if(__MODULE__ && ($tmp = _AF_MODULES_PATH_ . __MODULE__)){
	if(!file_exists($tmp . '/index.php')) goUrl(_AF_URL_, '');
	@include_once $tmp . '/lang/' . _AF_LANG_ . '.php';
	require_once $tmp . '/protect.php';
	require_once $tmp . '/index.php';
}

if(!empty($_POST['cdnerr'])){ // CDN 에러시 브라우저 종료전까지 사용안함
	setQuery('cdnerr', ''); set_cookie('_CDN_ERROR_', $_POST['cdnerr'], 0);
}

$tmp = _AF_CONFIG_DATA_.'base_cdn_list.php';
define('_AF_USE_BASE_CDN_', !get_cookie('_CDN_ERROR_')&&file_exists($tmp) ? $tmp : FALSE);

$tmp = __REQ_METHOD__=='JSON'?'application/json':'text/html';
header('Content-Type: '.$tmp.'; charset=UTF-8');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache"); unset($tmp);

/* End of file common.php */
/* Location: ./init/common.php */