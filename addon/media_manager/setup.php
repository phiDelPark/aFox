<?php
if(!defined('__AFOX__')) exit();
?>

<div class="form-group">
    <div class="switch-group">
        <input type="hidden" name="autosize_image" value="<?php echo $_ADDON['autosize_image']==='0'?'0':'1'?>">
        <div class="switch-control">
            <span class="switch switch-handle-on"><?php echo getLang('use')?></span>
            <span class="switch switch-label">이미지조절</span>
            <span class="switch switch-handle-off"><?php echo getLang('notuse')?></span>
        </div>
    </div>
    <p class="help-block">입력된 이미지 크기를 자동 조절합니다.</p>
</div>

<div class="form-group">
	<div class="switch-group">
		<input type="hidden" name="autosize_video" value="<?php echo $_ADDON['autosize_video']==='0'?'0':'1'?>">
		<div class="switch-control">
			<span class="switch switch-handle-on"><?php echo getLang('use')?></span>
			<span class="switch switch-label">영상조절</span>
			<span class="switch switch-handle-off"><?php echo getLang('notuse')?></span>
		</div>
	</div>
	<p class="help-block">입력된 영상의 크기를 자동 조절합니다. (유튜브 포함)</p>
</div>