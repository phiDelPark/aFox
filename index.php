<?php
define('__AFOX__', TRUE);

header('P3P: CP="ALL CURa ADMa DEVa TAIa OUR BUS IND PHY ONL UNI PUR FIN COM NAV INT DEM CNT STA POL HEA PRE LOC OTC"');
@set_time_limit(0);
ob_start();

if(!empty($_GET['file'])) {
	require_once dirname(__FILE__) . '/lib/file/file.php';
	exit();
}

require_once dirname(__FILE__) . '/initial/common.php';

if(__MODULE__ && !empty($_DATA['act'])) {

	$callproc = 'proc'.ucwords(__MODULE__).'Default';

	if(function_exists($callproc)) {
		$_result = triggerCall('before', 'proc', $_DATA['act'], $_DATA);
		if(empty($_result['error'])) {
			$_result = call_user_func($callproc, $_DATA);
			triggerCall('after', 'proc', $_DATA['act'], $_result);
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