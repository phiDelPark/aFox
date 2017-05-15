<?php
define('__AFOX__', TRUE);

header('P3P: CP="ALL CURa ADMa DEVa TAIa OUR BUS IND PHY ONL UNI PUR FIN COM NAV INT DEM CNT STA POL HEA PRE LOC OTC"');
@set_time_limit(0);
ob_start();

// IP 허용/차단
if(file_exists(__DIR__ . '/data/config/access_ip.php')) {
	$is_check_ip = false;
	include __DIR__ . '/data/config/access_ip.php';
	foreach ($_ACCESS_IPS as $tmp) {
		$is_check_ip = preg_match("/^{$tmp}$/", $_SERVER['REMOTE_ADDR']);
		if ($is_check_ip) break;
	}
	if((!$is_check_ip && $_ACCESS_IP_MODE == 'possible') || ($is_check_ip && $_ACCESS_IP_MODE == 'intercept')) {
		die ("Your IP is not allowed to access this page!");
	}
}

// 파일번호가 넘어오면 파일읽기
if(!empty($_GET['file'])) {
	require_once __DIR__ . '/lib/file/file.php';
	exit();
}

require_once __DIR__ . '/initial/common.php';

if(__MODULE__ && !empty($_DATA['act'])) {

	$callproc = 'proc'.ucwords(__MODULE__).'Default';

	if(function_exists($callproc)) {
		$md_id = $_DATA['id'];
		$_result = triggerCall('before_proc', $_DATA['act'], $md_id, $_DATA);
		if(empty($_result['error'])) {
			$_result = call_user_func($callproc, $_DATA);
			triggerCall('after_proc', $_DATA['act'], $md_id, $_result);
		}
	} else {
		$_result = set_error(getLang('error_request'),4303);
	}

	if(__REQ_METHOD__ == 'JSON' || __REQ_METHOD__ == 'XML') {
		unset($_SESSION['AF_VALIDATOR_ERROR']);
		header('Content-Type: application/json');
		echo json_encode($_result);
	} else {
		goUrl(empty($_result['redirect_url']) ? _AF_URL_ : $_result['redirect_url']);
	}
} else {
	require_once _AF_TPLS_PATH_ . (__MODULE__ == 'admin' ? 'admin' : 'default') . '.php';
	unset($_SESSION['AF_VALIDATOR_ERROR']);
}

/* End of file index.php */
/* Location: ./index.php */