<?php
if(!defined('__AFOX__')) exit();

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

// 로그인 중이면 맴버 정보 가져오기
if($tmp=(isset($_SESSION['AF_LOGIN_ID']) ? $_SESSION['AF_LOGIN_ID'] : get_cookie('AF_LOGIN_ID'))){
	if(preg_match('/^[a-zA-Z]+\w{2,}$/', $tmp) && ($_MEMBER=DB::get(_AF_MEMBER_TABLE_, ['mb_id'=>$tmp])) && !DB::error()){
		$tmp = $_MEMBER['mb_srl'].'/profile_image.png';
		$_MEMBER['mb_icon'] = file_exists(_AF_MEMBER_DATA_.$tmp) ? _AF_URL_.'data/member/'.$tmp : '';
		$_MEMBER['mb_grade'] = array('m'=>'manager','s'=>'admin');
		if($_MEMBER['mb_grade']=$_MEMBER['mb_grade'][$_MEMBER['mb_rank']]) $_MEMBER['mb_grade'] = 'member';
		unset($_MEMBER['mb_password']);
		// 쿠키이면... 키검사... 최고 관리자는 쿠키 사용안함
		if(!isset($_SESSION['AF_LOGIN_ID'])){
			$tmp = md5($_SERVER['SERVER_ADDR'].$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'].$_MEMBER['mb_password']);
			if($tmp == ($_MEMBER['mb_rank']=='s'?'':get_cookie('AF_AUTO_LOGIN'))){
				set_session('AF_LOGIN_ID', $_MEMBER['mb_id']);
			}else unset($_MEMBER);
		}
	}
}
// 로그인 중이 아니면 로그인 정보 삭제
if(empty($_MEMBER)){
	set_cookie('AF_LOGIN_ID', '', -1);
	set_cookie('AF_AUTO_LOGIN', '', -1);
	unset($_SESSION['AF_LOGIN_ID']);
}

if(__REQ_METHOD__ == 'JSON') $_POST = json_decode(file_get_contents('php://input'), TRUE);

$_DATA = $_GET;
///*첫번째 키가 모듈인지 체크 (.htaccess 대신 사용할때)
if(!empty($_GET) && !isset($_GET['module'])){
	if(file_exists(_AF_MODULES_PATH_ . ($tmp=key($_GET)) . '/setup.php')){
		$_DATA['module'] = $tmp;
		$_DATA['disp'] = empty($_GET[$tmp]) ? 'default' : $_GET[$tmp];
		unset($_DATA[$tmp]);
	}
}//*/

// 넘어온 값을 하나로 합침
if(!empty($_POST)) $_DATA = array_merge($_POST, $_DATA);
//unset($_GET); unset($_POST);

// 문서번호만 오면 id 가져옴
if(count($_DATA)===1 && (!empty($_DATA['srl']) || !empty($_DATA['rp']))){
	if(empty($_DATA['srl'])){
		$tmp = DB::get(_AF_COMMENT_TABLE_, 'wr_srl', ['rp_srl'=>(int)$_DATA['rp']]);
		if(!empty($tmp['wr_srl'])) $_DATA['srl'] = $tmp['wr_srl'];
	}
	if(!empty($_DATA['srl'])){
		$tmp = DB::get(_AF_DOCUMENT_TABLE_, 'md_id', ['wr_srl'=>(int)$_DATA['srl']]);
		if(!empty($tmp['md_id'])) $_DATA['id'] = $tmp['md_id'];
	}
	setQuery('','id',empty($_DATA['id'])?'':$_DATA['id'],'srl',empty($_DATA['srl'])?'':$_DATA['srl'],'rp',empty($_DATA['rp'])?'':$_DATA['rp']);
}

// 유효성 검사
foreach (['module','id','act','disp'] as $tmp){
	if(!isset($_DATA[$tmp]) || !preg_match('/^[a-zA-Z]+\w{2,}$/', $_DATA[$tmp])) $_DATA[$tmp] = '';
}

// module, id 가 없으면 시작 페이지
if(empty($_DATA['module']) && empty($_DATA['id'])) $_DATA['id'] = $_CFG['start'];
if(!empty($_DATA['id']) && ($tmp=getModule($_DATA['id']))){
	$_DATA['module'] = $tmp['md_key'];
	// 모듈 정보에 확장 변수가 있으면 unserialize
	if(($_CFG=array_merge($_CFG,$tmp)) && is_string($_CFG['md_extra'])){
		$_CFG['md_extra'] = unserialize($_CFG['md_extra']);
	}
}

define('__MID__', $_DATA['id']);
define('__MODULE__', $_DATA['module']);
define('__MODAL__', !empty($_DATA['modal']) && $_DATA['modal'] === '1');
define('__POPUP__', __MODAL__ || (!empty($_DATA['popup']) && $_DATA['popup'] === '1'));
// 전체 로그인 사용시 로그인 유저가 아니면 전체 로그인 화면 표시
define('__FULL_LOGIN__', $_CFG['use_full_login'] == 1 && empty($_MEMBER));


if(__MODULE__){
	$tmp = _AF_MODULES_PATH_ . __MODULE__;
	if(!file_exists($tmp . '/index.php')) goUrl(_AF_URL_, '');
	@include_once _AF_MODULES_PATH_ . __MODULE__ . '/lang/' . _AF_LANG_ . '.php';
	require_once $tmp . '/protect.php';
	require_once $tmp . '/index.php';
}

$_CFG['logo'] = file_exists(_AF_CONFIG_DATA_.'logo.png') ? _AF_URL_.'data/config/logo.png' : FALSE;
$_CFG['favicon'] = file_exists(_AF_CONFIG_DATA_.'favicon.ico') ? _AF_URL_.'data/config/favicon.ico' : FALSE;

// CDN 에러면 브라우저 종료전까지 사용안함
if(!empty($_DATA['cdnerr'])){
	setQuery('cdnerr', '');
	set_cookie('_CDN_ERROR_', $_DATA['cdnerr'], 0);
}

$tmp = _AF_CONFIG_DATA_.'base_cdn_list.php';
define('_AF_USE_BASE_CDN_', !get_cookie('_CDN_ERROR_')&&file_exists($tmp) ? $tmp : FALSE);

$tmp = __REQ_METHOD__=='JSON'||__REQ_METHOD__=='JSCALLBACK'?'application/json':'text/html';
header('Content-Type: '.$tmp.'; charset=UTF-8');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
unset($tmp);

/* End of file common.php */
/* Location: ./init/common.php */
