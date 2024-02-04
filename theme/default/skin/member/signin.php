<?php if(!defined('__AFOX__')) exit();
if($_CFG['use_captcha'] == '1') {
	$try_count = get_session('afox_login_try_' . $_SERVER['REMOTE_ADDR']);
	if($try_count > 2) {
		include(_AF_LIBS_PATH_.'simplecaptcha/simple-php-captcha.php');
		$captcha = simple_php_captcha();
		set_session('afox_captcha_' . $_SERVER['REMOTE_ADDR'], $captcha);
	}
}
?>

<form id="loginForm" action="<?php echo getUrl('')?>" method="post" autocomplete="off" aria-label="Input form to sign in">
<input type="hidden" name="success_url" value="<?php echo getUrl('member','','act','')?>">
<input type="hidden" name="error_url" value="<?php echo getUrl()?>">
<input type="hidden" name="module" value="member">
<input type="hidden" name="act" value="checklogin">
	<h3 class="pb-3 mb-1 border-bottom"><?php echo getLang('login')?></h3>
	<div class="w-100 my-5 py-5 text-center">
		<div class="d-inline-block" style="min-width:300px;width:35%">
			<input type="text" class="form-control" name="mb_id" minlength="2" maxlength="20" placeholder="<?php echo getLang('id')?>" required>
			<input type="password" class="form-control mt-2" name="mb_password" placeholder="<?php echo getLang('password')?>" required>
			<div class="d-flex w-100 justify-content-between mt-2" >
				<div class="form-check">
					<input class="form-check-input" type="checkbox" name="auto_login" value="" id="flexCheckAutoLogin">
					<label class="form-check-label" for="flexCheckAutoLogin">
					<?php echo getLang('auto_login')?>
					</label>
				</div>
				<span>
					<a href="<?php echo _AF_URL_ ?>?module=member&disp=signUp"><strong><?php echo getLang('member_signup')?></strong></a> /
					<a href="<?php echo _AF_URL_ ?>?module=member&disp=findAccount"><?php echo getLang('member_find')?></a>
				</span>
			</div>
				<?php if(!empty($captcha['image_src'])) { ?>
				<div class="d-flex w-100 justify-content-between mt-3">
					<img src="<?php echo './lib/'.$captcha['image_src'] ?>" alt="CAPTCHA code">
					<input type="text" class="form-control ms-2" placeholder="<?php echo getLang('captcha_code')?>" name="captcha_code" required>
				</div>
				<div class="clearfix"></div>
				<?php } ?>
			<button type="submit" class="btn btn-primary w-100 my-4"><?php echo getLang('login')?></button>
		</div>
	</div>
</form>
