<?php
if(!defined('__AFOX__')) exit();
?>

<div class="form-group">
	<div class="switch-group">
		<input type="hidden" name="print_youtube" value="<?php echo $_ADDON['print_youtube']==='0'?'0':'1'?>">
		<div class="switch-control">
			<span class="switch switch-handle-on"><?php echo getLang('use')?></span>
			<span class="switch switch-label">유튜브표시</span>
			<span class="switch switch-handle-off"><?php echo getLang('notuse')?></span>
		</div>
	</div>
	<p class="help-block">에디터로 입력된 Youtube 영상을 표시합니다.</p>
</div>