<?php define('__AFOX__', TRUE);
@set_time_limit(0); ob_start(); //phpinfo();
require_once __DIR__ . '/init/constant.php';;
if(file_exists(($chkip=_AF_CONFIG_DATA_.'access_ip.php'))){include $chkip;$chkip=false;//checkIP
	foreach($_ACCESS_IPS as $tmp){if($chkip=preg_match("/^{$tmp}$/",$_SERVER['REMOTE_ADDR']))break;};
	if((!$chkip && $_ACCESS_IP_MODE=='possible')||($chkip && $_ACCESS_IP_MODE=='intercept'))
		exit("Your IP is not allowed to access this page!");
}
if(@$_GET['file']){ require_once _AF_LIBS_PATH_.'file/file.php'; exit(); }
require_once __DIR__ . '/init/common.php';
if(_MODULE_ && @$_POST['act']){
	$callproc = 'proc'.ucwords(_MODULE_).'Default';
	if(function_exists($callproc)){
		if(triggerCall('before_proc', $_POST['act'], $_POST)) {
			$_result = call_user_func($callproc, $_POST);
			triggerCall('after_proc', $_POST['act'], $_result);
		} else $_result = get_error();
		$tmp = empty($_result['error'])?'success_url':'error_url';
		if(@$_result[$tmp]||@$_POST[$tmp]){
			$_result['redirect_url']=urldecode(@$_result[$tmp]?$_result[$tmp]:$_POST[$tmp]);
		}
	}else $_result = set_error(getLang('error_request'),4303);
	if(_REQ_METHOD_ == 'JSON'){
		echo json_encode($_result);
		unset($_SESSION['AF_VALIDATOR_ERROR']);
	}else goUrl(empty($_result['redirect_url']) ? _AF_URL_ : $_result['redirect_url']);
}else{
	require_once _AF_TPLS_PATH_ . (_MODULE_ == 'admin' ? 'admin' : 'default') . '.php';
	unset($_SESSION['AF_VALIDATOR_ERROR']);
}
/* End of file index.php*/
/* Location: ./index.php */