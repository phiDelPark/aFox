<?php
	if(!defined('__AFOX__')) exit();
?>

<form class="login-content" method="post" autocomplete="off" role="form" data-exec-ajax="member.loginCheck">
<input type="hidden" name="success_return_url" value="<?php echo getUrl()?>">
	<div class="panel panel-default" style="margin:auto;width:380px">
		<div class="panel-heading">
			<strong class="panel-title"><i class="glyphicon glyphicon-user" aria-hidden="true"></i> <?php echo getLang('login')?></strong>
		</div>
		<div class="panel-body" style="padding:25px 20px 20px">
			<div class="form-group">
				<input type="text" class="form-control" name="mb_id" minlength="2" maxlength="20" placeholder="<?php echo getLang('id')?>" required>
			</div>
			<div class="form-group">
				<input type="password" class="form-control" name="mb_password" placeholder="<?php echo getLang('password')?>" required>
			</div>
			<label class="checkbox" tabindex="0">
				<input type="checkbox" name="auto_login" value="1">
				<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
					<?php echo getLang('auto_login')?>
			</label>
		</div>
		<div class="panel-footer" style="text-align:right;background-color:transparent">
			<div class="pull-left" >
				<a href="<?php echo _AF_URL_ ?>?module=member&disp=signUp"><strong><?php echo getLang('member_signup')?></strong></a> /
				<a href="<?php echo _AF_URL_ ?>?module=member&disp=findAccount"><?php echo getLang('member_find')?></a>
			</div>
			<button type="submit" class="btn btn-primary"><?php echo getLang('login')?></button>
		</div>
	</div>
</form>
