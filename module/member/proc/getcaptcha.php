<?php

if(!defined('__AFOX__')) exit();

function proc($data) {

	include(_AF_LIBS_PATH_.'/simplecaptcha/simple-php-captcha.php');
	$captcha = simple_php_captcha();
	set_session('af_captcha_' . $_SERVER['REMOTE_ADDR'], $captcha);

	return ['error'=>0, 'message'=>'success', 'src'=>$captcha['image_src']];
}

/* End of file getcaptcha.php */
/* Location: ./module/member/proc/getcaptcha.php */
