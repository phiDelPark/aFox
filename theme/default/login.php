<?php
	if(!defined('__AFOX__')) exit();
	addJSLang(['error','id','password','login','auto_login','member_signup','member_find']);
	$signUp =  __MODULE__ == 'member' && $_DATA['disp'] == 'signUp';
?>

<div id="fullscreen_bg" class="fullscreen_bg"/>
<div class="modal fade in"<?php echo $signUp?'':' id="afMessageBox"'?> tabindex="-1" role="login" aria-labelledby="loginBox" aria-hidden="true" style="z-index: 99999; display: block; padding-right: 17px;">
	<div class="modal-dialog<?php echo $signUp?' modal-lg':''?>">
		<?php if($signUp) { ?>
			<div class="modal-content">
			<div class="modal-body">
			<?php displayModule(); ?>
			</div>
			</div>
		<?php } else { ?>
		<form action="/" method="post" autocomplete="off" data-exec-ajax="member.loginCheck" class="modal-content">
			<input type="hidden" name="success_return_url" value="<?php echo getUrl()?>" />
			<div class="modal-header">
				<img src="theme/default/img/logo.png">
			</div>
			<div class="modal-body">
				<div class="form-group">
					<input type="text" class="form-control" name="mb_id" maxlength="20" placeholder="<?php echo getLang('id')?>" pattern="^[a-zA-Z]+\w{2,}$" required="">
					<span class="sr-only"><?php echo getLang('id')?></span>
				</div>
				<div class="form-group">
					<input type="password" class="form-control" name="mb_password" placeholder="<?php echo getLang('password')?>" required="">
					<span class="sr-only"><?php echo getLang('password')?></span>
				</div>
				<label class="checkbox" tabindex="0">
					<input type="checkbox" name="auto_login" value="1">
					<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
					<?php echo getLang('auto_login')?>
				</label>
				<?php if($error = get_error()) { ?><p style="color:red"><?php echo $error['message']?></p><?php } ?>
			</div>
			<div class="modal-footer">
				<div class="pull-left">
					<?php if(!empty($_CFG['use_signup'])) { ?><a href="http://192.168.0.5/?module=member&amp;disp=signUp"><strong><?php echo getLang('member_signup')?></strong></a> /<?php } ?>
					<a href="http://192.168.0.5/?module=member&amp;disp=findAccount"><?php echo getLang('member_find')?></a>
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
