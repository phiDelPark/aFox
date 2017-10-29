<?php
if(!defined('__AFOX__')) exit();
// 설정값을 만들때는 모든 값의 합이 65000바이트를 넘지않게 설계한다 (최대 저장 가능한 크기가 약 65000바이트)
?>

<div class="form-group">
	<label for="id_filter">옵션</label>
	<label class="checkbox" tabindex="0">
		<input type="checkbox" name="use_loader" value="1" <?php echo !empty($_THEME['use_loader'])?'checked':''?>>
		<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
		페이지 이동시 로딩 이미지 보여주기
	</label>
	<label for="id_filter">헤드라인</label>
	<div class="form-group">
	<textarea maxbyte="15000" maxlength="15000" class="form-control mh-10 vresize" name="carousel_item_1" id="id_filter"><?php echo escapeHtml(empty($_THEME['carousel_item_1'])?'':$_THEME['carousel_item_1'])?></textarea>
	</div>
	<div class="form-group">
	<textarea maxbyte="15000" maxlength="15000" class="form-control mh-10 vresize" name="carousel_item_2" id="id_filter"><?php echo escapeHtml(empty($_THEME['carousel_item_2'])?'':$_THEME['carousel_item_2'])?></textarea>
	</div>
	<div class="form-group">
	<textarea maxbyte="15000" maxlength="15000" class="form-control mh-10 vresize" name="carousel_item_3" id="id_filter"><?php echo escapeHtml(empty($_THEME['carousel_item_3'])?'':$_THEME['carousel_item_3'])?></textarea>
	<p class="help-block">사이트 메인의 헤드라인에 표시될 정보를 입력 합니다.</p>
	</div>
	<label for="id_filter">하단 정보</label>
	<div class="form-group">
	<textarea maxbyte="15000" maxlength="15000" class="form-control mh-10 vresize" name="footer_html" id="id_filter"><?php echo escapeHtml(empty($_THEME['footer_html'])?'':$_THEME['footer_html'])?></textarea>
	<p class="help-block">사이트 하단에 표시될 정보를 입력 합니다. HTML 가능. (예: 카피라이트나 회사정보 등...)</p>
	</div>
</div>
