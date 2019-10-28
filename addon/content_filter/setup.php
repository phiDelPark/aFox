<?php
if(!defined('__AFOX__')) exit();
// 설정값을 만들때는 모든 값의 합이 65000바이트를 넘지않게 설계한다 (최대 저장 가능한 크기가 약 65000바이트)

$default_filter = '';
$default_change_text = '♡,사랑해';
$default_regex_text = '/(<[a-z][^>]*)\s(translation|class)=\"((?!(\"|\\\")).*?)\"([^>]*>)/mi,\1\5';
?>

<div class="form-group">
    <label for="id_regex">정규식</label>
    <textarea maxbyte="30000" maxlength="30000" class="form-control mh-10 vresize" name="regex" id="id_regex"><?php echo escapeHtml(empty($_ADDON['regex'])?$default_regex_text:$_ADDON['regex'])?></textarea>
    <p class="help-block">작성 규칙: /정규식/옵션,치환자<br>정규식을 콘텐츠 내용에 적용합니다. (정규식과 정규식 사이는 엔터로 구분합니다)</p>
	<label for="id_filter">단어 필터</label>
	<textarea maxbyte="30000" maxlength="30000" class="form-control mh-10 vresize" name="filter" id="id_filter"><?php echo escapeHtml(empty($_ADDON['filter'])?$default_filter:$_ADDON['filter'])?></textarea>
    <label for="id_text">바꿀 단어</label>
    <input type="text" name="change_text" id="id_text" class="form-control" value="<?php echo escapeHtml(empty($_ADDON['change_text'])?$default_change_text:$_ADDON['change_text'])?>">
	<p class="help-block">입력된 단어 필터가 있으면 바꿀 단어로 바꿔줍니다. (단어와 단어 사이는 ,로 구분합니다)</p>
</div>
