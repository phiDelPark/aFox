<?php
	if(!defined('__AFOX__')) exit();
?>

<form id="loginForm" method="post" autocomplete="off" aria-label="Input form to sign in">
<input type="hidden" name="success_return_url" value="<?php echo getUrl('member','','act','')?>">
<input type="hidden" name="error_return_url" value="<?php echo getUrl()?>">
<input type="hidden" name="module" value="member">
<input type="hidden" name="act" value="checklogin">
	<h3 class="pb-3 mb-1 fst-italic border-bottom"><?php echo getLang('login')?></h3>
	<div class="w-100 my-5 text-center">
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
			<button type="submit" class="btn btn-primary w-100 my-4"><?php echo getLang('login')?></button>
		</div>
	</div>
</form>
