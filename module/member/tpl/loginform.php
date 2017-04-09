<?php
	if(!defined('__AFOX__')) exit();
?>

<form class="login-content" method="post" autocomplete="off" style="text-align:right" data-exec-ajax="member.loginCheck">
<input type="hidden" name="success_return_url" value="<?php echo getUrl()?>" />
	<div class="panel panel-default" style="margin:auto;width:350px">
		<div class="panel-heading">
			<h4 class="panel-title"><?php echo getLang('login')?></h4>
		</div>
		<div class="panel-body" style="padding:25px 20px 20px">
			<div class="form-group">
				<input type="text" class="form-control" name="mb_id" maxlength="20" placeholder="<?php echo getLang('id')?>" required /> <span class="sr-only"><?php echo getLang('id')?></span>
			</div>
			<div class="form-group">
				<input type="password" class="form-control" name="mb_password" placeholder="<?php echo getLang('password')?>" required /> <span class="sr-only"><?php echo getLang('password')?></span>
			</div>
			<div class="checkbox">
				<label><input type="checkbox" name="auto_login" /> <?php echo getLang('auto_login')?></label>
			</div>
			<div class="pull-left">
				<a href="<?php echo _AF_URL_ ?>?module=member&disp=signUp"><strong><?php echo getLang('member_signup')?></strong></a> /
				<a href="<?php echo _AF_URL_ ?>?module=member&disp=findAccount"><?php echo getLang('member_find')?></a>
			</div>
			<button type="submit" class="btn btn-primary"><?php echo getLang('login')?></button>
		</div>
	</div>
</form>