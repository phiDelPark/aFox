<?php
	if(!defined('__AFOX__')) exit();

	$signUp =  __MODULE__ == 'member' && $_DATA['disp'] == 'signUp';
	$try_count = get_session('af_login_try_' . $_SERVER['REMOTE_ADDR']);
	if($_CFG['use_captcha'] == '1' || $try_count > 2) {
		include(_AF_LIBS_PATH_.'simplecaptcha/simple-php-captcha.php');
		$captcha = simple_php_captcha();
		set_session('af_captcha_' . $_SERVER['REMOTE_ADDR'], $captcha);
	}
?>

<div id="fullscreen_bg" class="fullscreen_bg"/>
<div role="dialog" aria-labelledby="<?php echo $signUp?'afSignUpFormTitle':'afMsgBox'?>" style="z-index:99999;display:block">
	<div class="modal-dialog<?php echo $signUp?' modal-lg':''?>">
		<?php if($signUp) { ?>
			<div class="modal-content">
			<div class="modal-body">
			<?php displayModule(); ?>
			</div>
			</div>
		<?php } else { ?>
		<form action="/" method="post" autocomplete="off" data-exec-ajax="member.loginCheck" class="modal-content">
			<input type="hidden" name="error_return_url" value="<?php echo getUrl('')?>" />
			<input type="hidden" name="success_return_url" value="<?php echo getUrl('')?>" />
			<div class="modal-header" id="<?php echo $signUp?'afSignUpFormTitle':'afMsgBox'?>">
				<img src="theme/default/img/logo.png" width="100%" style="max-width:310px">
				<span class="sr-only"><?php echo escapeHtml($_CFG['title']).' '.getLang('login')?></span>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<input type="text" class="form-control" name="mb_id" minlength="2" maxlength="20" placeholder="<?php echo getLang('id')?>" required>
				</div>
				<div class="form-group">
					<input type="password" class="form-control" name="mb_password" placeholder="<?php echo getLang('password')?>" required>
				</div>
				<?php if(!empty($captcha)) { ?>
				<div class="captcha-group clearfix">
					<div class="form-group pull-left">
						<img src="<?php echo './lib/'.$captcha['image_src'] ?>" alt="CAPTCHA code">
					</div>
					<div class="form-group pull-right">
						<input type="text" class="form-control" placeholder="<?php echo getLang('captcha_code')?>" name="captcha_code" required>
					</div>
				</div>
				<?php } ?>
				<label class="checkbox" tabindex="0">
					<input type="checkbox" name="auto_login" value="1">
					<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
					<?php echo getLang('auto_login')?>
				</label>
				<?php if($error = get_error()) { ?><p style="color:red"><?php echo $error['message']?></p><?php } ?>
			</div>
			<div class="modal-footer">
				<div class="pull-left">
					<?php if(!empty($_CFG['use_signup'])) { ?><a href="<?php echo _AF_URL_ ?>?module=member&amp;disp=signUp"><strong><?php echo getLang('member_signup')?></strong></a> /<?php } ?>
					<a href="<?php echo _AF_URL_ ?>?module=member&amp;disp=findAccount"><?php echo getLang('member_find')?></a>
				</div>
				<button type="submit" class="btn btn-default btn-primary" data-key="ok"><?php echo getLang('login')?></button>
			</div>
		</form>
		<?php } ?>
	</div>
</div>
</div>
<script>
	jQuery(window).on('load', function() {
		jQuery('input[type="text"]:eq(0)').focus();
	});
</script>
