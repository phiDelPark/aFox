<?php
if(!defined('__AFOX__')) exit();
// 설정값을 만들때는 모든 값의 합이 65000바이트를 넘지않게 설계한다
// (최대 저장 가능한 크기가 약 65000바이트)

$default_filter = '';
$default_change_text = '♡,사랑해';
$default_regex_text = '/<(svg)[^>]*>((?!<\/svg>).)*<\/svg>/is,\[\1\]';
$default_exclusion = "href,src,width,height,loading,target,webcode,class";
?>

<div class="form-check">
	<input class="form-check-input" type="checkbox" name="remove_attr" id="id_remove_attr" value="1" <?php echo !empty($_ADDON['remove_attr'])?'checked':''?>>
	<label class="form-check-label" for="id_remove_attr">
	태그 속성 지우기
	</label>
</div>

<div class="mb-3">
	<div class="input-group">
		<label class="input-group-text w-100p" for="id_exclusion_attr">제외</label>
		<input type="text" name="exclusion_attr" class="form-control" id="id_exclusion_attr" maxlength="255" value="<?php echo escapeHtml(empty($_ADDON['exclusion_attr'])?$default_exclusion:$_ADDON['exclusion_attr'])?>">
	</div>
	<p class="form-text">태그 속성 지우기 사용시 제외 할 속성 (예: id,class)</p>
</div>

<div class="mb-3">
<label for="id_regex">정규식</label>
<textarea maxbyte="30000" maxlength="30000" class="form-control" rows="5" name="regex" id="id_regex"><?php echo escapeHtml(empty($_ADDON['regex'])?$default_regex_text:$_ADDON['regex'])?></textarea>
<p class="form-text">작성 규칙: /(정규식)/옵션,치환자(\1)<br>정규식을 콘텐츠 내용에 적용합니다. (정규식과 정규식 사이는 엔터로 구분합니다)</p>
</div>

<div class="mb-3">
<label for="id_filter">단어 필터</label>
<textarea maxbyte="30000" maxlength="30000" class="form-control" rows="3" name="filter" id="id_filter"><?php echo escapeHtml(empty($_ADDON['filter'])?$default_filter:$_ADDON['filter'])?></textarea>
</div>

<div class="mb-3">
<label for="id_text">바꿀 단어</label>
<input type="text" name="change_text" id="id_text" class="form-control" value="<?php echo escapeHtml(empty($_ADDON['change_text'])?$default_change_text:$_ADDON['change_text'])?>">
<p class="form-text">입력된 단어 필터가 있으면 바꿀 단어로 바꿔줍니다. (단어와 단어 사이는 ,로 구분합니다)</p>
</div>