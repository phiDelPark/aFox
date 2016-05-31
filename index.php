<?php

header('P3P: CP="ALL CURa ADMa DEVa TAIa OUR BUS IND PHY ONL UNI PUR FIN COM NAV INT DEM CNT STA POL HEA PRE LOC OTC"');
@set_time_limit(0);

define('__AFOX__', TRUE);

if(!empty($_GET['file'])) {
	require_once dirname(__FILE__) . '/config/file.php';
	exit();
}

require_once dirname(__FILE__) . '/config/initialize.php';

if(__MODULE__ && !empty($_DATA['act'])) {

	$triggercall = 'proc'.__MODULE__.$_DATA['act'];
	$callproc = 'proc'.ucwords(__MODULE__).'Default';

	if(function_exists($callproc)) {
		$_result = triggerCall($triggercall, 'before', $_DATA);
		if(!$_result) {
			$_result = call_user_func($callproc, $_DATA);
			triggerCall($triggercall, 'after', $_result);
		}
	} else {
		$_result = set_error(getLang('msg_invalid_request'),303);
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