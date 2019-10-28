<?php
if(!defined('__AFOX__')) exit();
?>

<div class="form-group">
	<label class="checkbox" tabindex="0">
		<input type="checkbox" name="autosize_image" value="1" <?php echo !empty($_ADDON['autosize_image'])?'checked':''?>>
		<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
		입력된 이미지 크기를 자동 조절합니다.
	</label>
	<label class="checkbox" tabindex="0">
		<input type="checkbox" name="autosize_video" value="1" <?php echo !empty($_ADDON['autosize_video'])?'checked':''?>>
		<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
		입력된 영상의 크기를 자동 조절합니다. (유튜브 포함)
	</label>
	<label class="checkbox" tabindex="0">
		<input type="checkbox" name="link_blank" value="1" <?php echo !empty($_ADDON['link_blank'])?'checked':''?>>
		<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
		입력된 링크를 새창으로 보여줍니다. (target="_modal" 지원)
	</label>
</div>
