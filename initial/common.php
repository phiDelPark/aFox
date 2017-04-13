<?php
if(!defined('__AFOX__')) exit();

require_once dirname(__FILE__) . '/config.php';

@include_once _AF_LANGS_PATH_ . 'default_' . _AF_LANG_ . '.php';
require_once _AF_INIT_PATH_ . 'function.php';

if($_CFG['use_visit'] == '1' && get_cookie('ck_visit_ip') != $_SERVER['REMOTE_ADDR']) {
	set_cookie('ck_visit_ip', $_SERVER['REMOTE_ADDR'], 86400); // 하루동안 저장
	if(checkUserAgent() != 'BOT') {
		$tmp = empty($_SERVER['HTTP_REFERER'])?'':strip_tags($_SERVER['HTTP_REFERER']);
		DB::insert(_AF_VISITOR_TABLE_, ['mb_ipaddress'=>strip_tags($_SERVER['REMOTE_ADDR']),'vs_agent'=>strip_tags($_SERVER['HTTP_USER_AGENT']),'vs_referer'=>$tmp,'(vs_regdate)'=>'NOW()']);
	}
}

define('__MOBILE__', checkUserAgent() == 'MOBILE');
define('__REQ_METHOD__', getRequestMethod());

if(__REQ_METHOD__ == 'JSON') {
	$_POST = json_decode(file_get_contents('php://input'), TRUE);
}

// 넘어온 값을 하나로 합침
$_DATA = is_null($_POST) ? $_GET : (is_null($_GET) ? $_POST : array_merge($_POST, $_GET));
unset($_GET);
unset($_POST);

define('__POPUP__', !empty($_DATA['popup']) && $_DATA['popup'] === '1');

// 문서번호만 오면 id 가져옴
if(count($_DATA)===1 && (!empty($_DATA['srl']) || !empty($_DATA['rp']))) {
	if(empty($_DATA['srl'])) {
		$tmp = getDBItem(_AF_COMMENT_TABLE_,['rp_srl'=>(int)$_DATA['rp']], 'wr_srl');
		if(empty($tmp['error'])) $_DATA['srl'] = $tmp['wr_srl'];
	}
	if(!empty($_DATA['srl'])) {
		$tmp = getDBItem(_AF_DOCUMENT_TABLE_,['wr_srl'=>(int)$_DATA['srl']], 'md_id');
		if(empty($tmp['error'])) $_DATA['id'] = $tmp['md_id'];
	}
	setQuery('','id',empty($_DATA['id'])?'':$_DATA['id'],'srl',empty($_DATA['srl'])?'':$_DATA['srl'],'rp',empty($_DATA['rp'])?'':$_DATA['rp']);
}

$tmp_arr = ['module','id','act','disp'];
foreach ($tmp_arr as $tmp) {
	if(!isset($_DATA[$tmp]) || !preg_match('/^[a-zA-Z]+\w{2,}$/', $_DATA[$tmp])) $_DATA[$tmp] = '';
}

if($_DATA['module'] == 'admin' || isset($_DATA['admin'])) {
	define('__MODULE__', 'admin');
} else {
	// module, id 가 없으면 시작 페이지
	if(empty($_DATA['module'])&&empty($_DATA['id'])) $_DATA['id'] = $_CFG['start'];
	if(!empty($_DATA['id'])) {
		$tmp = getModule($_DATA['id']);
		if(empty($tmp['error'])) {
			$_CFG = array_merge($_CFG, $tmp);
			// 모듈 정보에 확장 변수가 있으면 unserialize
			if(!empty($_CFG['md_extra']) && !is_array($_CFG['md_extra'])) $_CFG['md_extra'] = unserialize($_CFG['md_extra']);
			$_DATA['module'] = $tmp['md_key'];
		}
	}
	define('__MODULE__', $_DATA['module']);
}

if(__MODULE__) require_once _AF_MODULES_PATH_ . __MODULE__ . '/index.php';

// CDN 에러면 브라우저 종료전까지 사용안함
if(!empty($_DATA['cdnerr'])) {
	setQuery('cdnerr', '');
	set_cookie('_CDN_ERROR_', $_DATA['cdnerr'], 0);
	unset($_DATA['cdnerr']);
}

$tmp = _AF_CONFIG_DATA_.'base_cdn_list.php';
define('_AF_USE_BASE_CDN_', !get_cookie('_CDN_ERROR_')&&file_exists($tmp) ? $tmp : FALSE);

// 실행 가능한 애드온 정보 합치기
$tmp = (__MOBILE__?'use_mobile':'use_pc').'=1';
$tmp_arr = DB::query('SELECT ao_id,ao_extra FROM '._AF_ADDON_TABLE_.' WHERE '.$tmp);
if(!DB::error()) {
	while ($tmp = DB::assoc($tmp_arr)) {
		$_ADDONS[$tmp['ao_id']] = $tmp['ao_extra']; // unserialize는 필요할때 하기로... // TODO 캐시 처리 필요할까?
	}
}
$_CFG['logo'] = file_exists(_AF_CONFIG_DATA_.'logo.png') ? _AF_URL_.'data/config/logo.png' : FALSE;
$_CFG['favicon'] = file_exists(_AF_CONFIG_DATA_.'favicon.ico') ? _AF_URL_.'data/config/favicon.ico' : FALSE;

unset($tmp);
unset($tmp_arr);
header('Content-Type: text/html; charset=utf-8');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.

/* End of file common.php */
/* Location: ./initial/common.php */