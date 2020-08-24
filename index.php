<?php
define('__AFOX__', TRUE);

header('P3P: CP="ALL CURa ADMa DEVa TAIa OUR BUS IND PHY ONL UNI PUR FIN COM NAV INT DEM CNT STA POL HEA PRE LOC OTC"');
@set_time_limit(0);
ob_start();
//phpinfo();
// IP 허용/차단
$is_check_ip = __DIR__ . '/data/config/access_ip.php';
if(file_exists($is_check_ip)) {
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

// 파일번호가 넘어오면 파일읽기
if(!empty($_GET['file'])) {
	require_once __DIR__ . '/lib/file/file.php';
	exit();
}

require_once __DIR__ . '/init/common.php';

if(__MODULE__ && !empty($_DATA['act'])) {
	// 전체 로그인시엔 일부 함수만 실행가능
	if(__FULL_LOGIN__ && __MODULE__ != 'member') {
		exit();
	}

	$callproc = 'proc'.ucwords(__MODULE__).'Default';
	if(function_exists($callproc)) {
		if(triggerCall('before_proc', $_DATA['act'], $_DATA)) {
			$_result = call_user_func($callproc, $_DATA);
			triggerCall('after_proc', $_DATA['act'], $_result);
		} else {
			$_result = get_error();
		}

		$redirect_url = empty($_result['error'])?'success_return_url':'error_return_url';
		if (!empty($_DATA[$redirect_url])) {
			$_result['redirect_url'] = urldecode($_DATA[$redirect_url]);
		}
	} else {
		$_result = set_error(getLang('error_request'),4303);
	}

	if(__REQ_METHOD__ == 'JSON' || __REQ_METHOD__ == 'JSCALLBACK') {
		echo json_encode($_result);
		unset($_SESSION['AF_VALIDATOR_ERROR']);
	} else {
		goUrl(empty($_result['redirect_url']) ? _AF_URL_ : $_result['redirect_url']);
	}
} else {
	require_once _AF_TPLS_PATH_ . (__MODULE__ == 'admin' ? 'admin' : 'default') . '.php';
	unset($_SESSION['AF_VALIDATOR_ERROR']);
}

/* End of file index.php */
/* Location: ./index.php */
