<?php
if(!defined('__AFOX__')) exit();

require_once dirname(__FILE__) . '/config.php';

@include_once _AF_LANGS_PATH_ . 'default_' . _AF_LANG_ . '.php';
require_once _AF_CONFIG_PATH_ . 'function.php';

define('__MOBILE__', checkUserAgent() == 'MOBILE');
define('__REQ_METHOD__', getRequestMethod());

if(__REQ_METHOD__ == 'JSON') {
	$_POST = json_decode(file_get_contents('php://input'), TRUE);
}

// 넘어온 값을 하나로 합침
$_DATA = is_null($_POST) ? $_GET : (is_null($_GET) ? $_POST : array_merge($_POST, $_GET));
unset($_GET);
unset($_POST);

$tmp_arr = ['module','id','act','disp'];
foreach ($tmp_arr as $tmp) {
	if(!isset($_DATA[$tmp]) || !preg_match('/^[a-zA-Z]+[a-zA-Z0-9_]{2,}/', $_DATA[$tmp])) $_DATA[$tmp] = '';
}

if($_DATA['module'] == 'admin' || isset($_DATA['admin'])) {
	define('__MODULE__', 'admin');
} else {
	// module, id 가 없으면 시작 페이지
	if(empty($_DATA['module'])&&empty($_DATA['id'])) $_DATA['id'] = $_CFG['start'];
	if(!empty($_DATA['id'])) {
		$tmp = getModule($_DATA['id']);
		if(empty($tmp['error'])) {
			$_CFG['module'] = $tmp;
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
define('__USE_BASE_CDN__', !get_cookie('_CDN_ERROR_')&&file_exists($tmp) ? $tmp : FALSE);

// 테마 설정 저장 // TODO 캐시 처리 하자
$tmp_arr = DB::query('SELECT extra FROM '._AF_THEME_TABLE_.' WHERE th_id=\''._AF_THEME_.'\'');
if(!DB::error()) {
	$tmp = DB::assoc($tmp_arr);
	$_CFG['theme'] = unserialize($tmp['extra']);
	$_CFG['theme']['th_id'] = _AF_THEME_;
}

// 실행 가능한 에드온 정보 합치기
$tmp = (__MOBILE__?'ao_use_mobile':'ao_use_pc').'=1';
$tmp_arr = DB::query('SELECT ao_id,extra FROM '._AF_ADDON_TABLE_.' WHERE '.$tmp);
if(!DB::error()) {
	while ($tmp = DB::assoc($tmp_arr)) {
		$_ADDONS[$tmp['ao_id']] = $tmp['extra']; // unserialize는 필요할때 하기로... // TODO 캐시 처리 필요할까?
	}
}
$_CFG['logo'] = file_exists(_AF_CONFIG_DATA_.'logo.png') ? _AF_URL_.'data/config/logo.png' : FALSE;
$_CFG['favicon'] = file_exists(_AF_CONFIG_DATA_.'favicon.ico') ? _AF_URL_.'data/config/favicon.ico' : FALSE;

unset($tmp);
unset($tmp_arr);

/* End of file initialize.php */
/* Location: ./config/initialize.php */