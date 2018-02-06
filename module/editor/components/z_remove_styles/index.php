<?php
if(!defined('__AFOX__')) exit();
?>
<div style="margin:10px">
	<h4><?php echo $_COMPONENT_INFO['title'] ?></h4>
	<hr style="margin:10px 0">
	<div class="options">
	<label style="display:block"><input class="option1group" type="checkbox" checked="checked" value="id"> 아이디 삭제</label>
	<label style="display:block"><input class="option1group" type="checkbox" checked="checked" value="class"> 클래스 삭제</label>
	<label style="display:block"><input class="option1group" type="checkbox" checked="checked" value="data-[a-zA-Z]+"> 데이타 삭제</label>
	<label style="display:block"><input class="option3group" type="checkbox" checked="checked" value="script"> 스크립트 삭제</label>
	<hr style="margin:10px 0">
	<label style="display:block"><input class="option1group" type="checkbox" value="style"> 모든 스타일 삭제</label>
	<label style="display:block">- <input class="option2group" type="checkbox" checked="checked" value="background"> 배경 삭제</label>
	<label style="display:block">- <input class="option2group" type="checkbox" checked="checked" value="color"> 글자 색 삭제</label>
	<label style="display:block">- <input class="option2group" type="checkbox" checked="checked" value="(width|height)"> 가로/세로 크기 삭제</label>
	</div>
</div>
<hr style="margin:10px 0">
<div style="margin:10px;text-align:right">
	<button class="btn btn-default" onclick="remove_styles()">확인</button>
	<button class="btn btn-default" onclick="window.close()">취소</button>
</div>
<script>
	function remove_styles() {
		var $editor = opener.AF_EDITOR_<?php echo _AF_EDITOR_NAME_ ?>,
			$txtara = $editor.$textarea,
			$iframe = $editor.$element.find('iframe'),
			html = $iframe.length > 0 ? $iframe.contents().find('body').html() : $txtara.val();

		var options1 = [],
			options2 = [],
			str1 = '',
			str2 = '';

		$('.option3group:checked').each(function (i) {
			options1[i] = $(this).val();
		});

		if(options1.length > 0) {
			for (var i in options1) {
				str1 = str1 + options1[i] + '|';
			}
			str1 = '(' + str1.slice(0, -1) + ')';
			var pattern = new RegExp('<' + str1 + '[^>]*>([^]*?)<\/' + str1 + '>', 'gmi');
			html = html.replace(pattern, '');
		}

		options1 = [];
		str1 = '';

		$('.option1group:checked').each(function (i) {
			options1[i] = $(this).val();
		});

		if($('[value="style"]:checked').length === 0) {
			$('.option2group:checked').each(function (i) {
				options2[i] = $(this).val();
			});
		}

		if(options1.length > 0 || options2.length > 0) {
			var pt1, pt2;

			if(options1.length > 0){
				for (var i in options1) {
					str1 = str1 + options1[i] + '|';
				}
				pt1 = new RegExp('(' + str1.slice(0, -1) + ')' + '\\s*=\\s*\"[^\"]*\"', 'ig');
			}
			if(options2.length > 0){
				for (var i in options2) {
					str2 = str2 + options2[i] + '|';
				}
				pt2 = new RegExp('([^\-]*)' + '(' + str2.slice(0, -1) + ')' + '[a-z\-]*\\s*:\\s*[^;]*;', 'ig');
			}

			var pattern = RegExp('<([a-zA-Z1-5]+)(\\s[^>]*)>', 'ig');
			html = html.replace(pattern, function replacer(match, p1, p2, offset, string) {
				if(pt1) p2 = p2.replace(pt1, '');
				if(pt2) p2 = p2.replace(pt2, '\\1');
				return '<' + p1 + p2 +  '>';
			});
		}

		if($iframe.length > 0) {
			$iframe.contents().find('body').html(html);
		} else {
			$txtara.val(html);
		}

		alert('태그 속성 삭제 완료');
		window.close();
	}
</script>
<?php
/* End of file index.php */
/* Location: ./module/editor/components/z_remove_styles/index.php */
