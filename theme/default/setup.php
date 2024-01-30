<?php
if(!defined('__AFOX__')) exit();
// 설정값을 만들때는 모든 값의 합이 65000바이트를 넘지않게 설계한다 (최대 저장 가능한 크기가 약 65000바이트)
?>

<div class="form-check mb-4">
	<input class="form-check-input" type="checkbox" name="use_loader" id="id_use_loader" value="1" <?php echo !empty($_THEME['use_loader'])?'checked':''?>>
	<label class="form-check-label" for="id_use_loader">
	페이지 이동시 로딩 이미지 보여주기
	</label>
</div>

<div class="mb-4">
	<label class="form-label" for="carousel_item">헤드라인</label>
	<div class="input-group mb-2">
	<textarea maxbyte="15000" maxlength="15000" class="form-control" rows="5" name="carousel_item_1" id="carousel_item"><?php echo escapeHTML(empty($_THEME['carousel_item_1'])?'':$_THEME['carousel_item_1'])?></textarea>
	</div>
	<div class="input-group mb-2">
	<textarea maxbyte="15000" maxlength="15000" class="form-control" rows="5" name="carousel_item_2" id="carousel_item"><?php echo escapeHTML(empty($_THEME['carousel_item_2'])?'':$_THEME['carousel_item_2'])?></textarea>
	</div>
	<div class="input-group">
	<textarea maxbyte="15000" maxlength="15000" class="form-control" rows="5" name="carousel_item_3" id="carousel_item"><?php echo escapeHTML(empty($_THEME['carousel_item_3'])?'':$_THEME['carousel_item_3'])?></textarea>
	</div>
	<div class="form-text">사이트 메인의 헤드라인에 표시될 정보를 입력 합니다.</div>
</div>

<div class="mb-4">
	<label class="form-label" for="id_filter">하단 정보</label>
	<div class="input-group">
	<textarea maxbyte="15000" maxlength="15000" class="form-control" rows="5" name="footer_html" id="id_filter"><?php echo escapeHTML(empty($_THEME['footer_html'])?'':$_THEME['footer_html'])?></textarea>
	</div>
	<div class="form-text">사이트 하단에 표시될 정보를 입력 합니다. HTML 가능. (예: 카피라이트나 회사정보 등...)</div>
</div>
