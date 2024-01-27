<?php define('__AFOX__', TRUE);
header('P3P: CP="ALL CURa ADMa DEVa TAIa OUR BUS IND PHY ONL UNI PUR FIN COM NAV INT DEM CNT STA POL HEA PRE LOC OTC"');
@set_time_limit(0);
ob_start(); //phpinfo();
$is_check_ip = __DIR__ . '/data/config/access_ip.php';
if(file_exists($is_check_ip)) { // IP 허용/차단
	include $is_check_ip;
	$is_check_ip = false;
	foreach ($_ACCESS_IPS as $tmp) {
		$is_check_ip = preg_match("/^{$tmp}$/", $_SERVER['REMOTE_ADDR']);
		if ($is_check_ip) break;
	}
	if((!$is_check_ip && $_ACCESS_IP_MODE == 'possible') || ($is_check_ip && $_ACCESS_IP_MODE == 'intercept')) {
		die ("Your IP is not allowed to access this page!");
	}
}
require_once __DIR__ . '/init/config.php';
if(!empty($_GET['file'])) { // 파일번호가 넘어오면 파일읽기
	require_once __DIR__ . '/lib/file/file.php';
	exit();
}
require_once __DIR__ . '/init/common.php';
if(__MODULE__ && !empty($_POST['act'])) {
	$callproc = 'proc'.ucwords(__MODULE__).'Default';
	if(function_exists($callproc)) {
		if(triggerCall('before_proc', $_POST['act'], $_POST)) {
			$_result = call_user_func($callproc, $_POST);
			triggerCall('after_proc', $_POST['act'], $_result);
		} else $_result = get_error();
		$redirect_url = empty($_result['error'])?'success_url':'error_url';
		if (!empty($_POST[$redirect_url])) {
			$_result['redirect_url'] = urldecode($_POST[$redirect_url]);
		}
	} else $_result = set_error(getLang('error_request'),4303);
	if(__REQ_METHOD__ == 'JSON') {
		echo json_encode($_result);
		unset($_SESSION['AF_VALIDATOR_ERROR']);
	} else goUrl(empty($_result['redirect_url']) ? _AF_URL_ : $_result['redirect_url']);
} else {
	require_once _AF_TPLS_PATH_ . (__MODULE__ == 'admin' ? 'admin' : 'default') . '.php';
	unset($_SESSION['AF_VALIDATOR_ERROR']);
}
/* End of file index.php */
/* Location: ./index.php */