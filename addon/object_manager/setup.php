<?php
if(!defined('__AFOX__')) exit();
?>

<div class="form-check">
	<input class="form-check-input" type="checkbox" name="autosize_image" id="id_autosize_image" value="1" <?php echo !empty($_ADDON['autosize_image'])?'checked':''?>>
	<label class="form-check-label" for="id_autosize_image">
	입력된 이미지 크기를 자동 조절합니다.
	</label>
</div>

<div class="form-check">
	<input class="form-check-input" type="checkbox" name="autosize_video" id="id_autosize_video" value="1" <?php echo !empty($_ADDON['autosize_video'])?'checked':''?>>
	<label class="form-check-label" for="id_autosize_video">
	입력된 영상의 크기를 자동 조절합니다. (유튜브 포함)
	</label>
</div>

<div class="form-check">
	<input class="form-check-input" type="checkbox" name="link_blank" id="id_link_blank" value="1" <?php echo !empty($_ADDON['link_blank'])?'checked':''?>>
	<label class="form-check-label" for="id_link_blank">
	입력된 링크를 새창으로 보여줍니다. (target="_modal" 지원)
	</label>
</div>