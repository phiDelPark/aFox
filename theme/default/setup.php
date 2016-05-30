<?php
if(!defined('__AFOX__')) exit();
?>

<div class="form-group">
	<label for="id_filter">헤드라인</label>
	<div class="form-group">
	<textarea class="form-control min-height-100 vresize" name="carousel_item_1" id="id_filter"><?php echo escapeHtml(empty($_THEME['carousel_item_1'])?'':$_THEME['carousel_item_1'])?></textarea>
	</div>
	<div class="form-group">
	<textarea class="form-control min-height-100 vresize" name="carousel_item_2" id="id_filter"><?php echo escapeHtml(empty($_THEME['carousel_item_2'])?'':$_THEME['carousel_item_2'])?></textarea>
	</div>
	<div class="form-group">
	<textarea class="form-control min-height-100 vresize" name="carousel_item_3" id="id_filter"><?php echo escapeHtml(empty($_THEME['carousel_item_3'])?'':$_THEME['carousel_item_3'])?></textarea>
	<p class="help-block">사이트 메인의 헤드라인에 표시될 정보를 입력 합니다.</p>
	</div>
	<label for="id_filter">하단 정보</label>
	<div class="form-group">
	<textarea class="form-control min-height-100 vresize" name="footer_html" id="id_filter"><?php echo escapeHtml(empty($_THEME['footer_html'])?'':$_THEME['footer_html'])?></textarea>
	<p class="help-block">사이트 하단에 표시될 정보를 입력 합니다. HTML 가능. (예: 카피라이트나 회사정보 등...)</p>
	</div>
</div>