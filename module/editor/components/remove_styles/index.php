<?php
if(!defined('__AFOX__')) exit();
?>
<div style="margin:10px">
	<h4><?php echo $_COMPONENT_INFO['title'] ?></h4>
	<hr style="margin:10px 0">
	<div class="options">
	<label style="display:block"><input class="option1group" type="checkbox" checked="checked" value="id"> 아이디 삭제</label>
	<label style="display:block"><input class="option1group" type="checkbox" checked="checked" value="class"> 클래스 삭제</label>
	<label style="display:block"><input class="option3group" type="checkbox" checked="checked" value="script"> 스크립트 삭제</label>
	<hr style="margin:10px 0">
	<label style="display:block"><input class="option2group" type="checkbox" checked="checked" value="background"> 배경 삭제</label>
	<label style="display:block"><input class="option2group" type="checkbox" checked="checked" value="color"> 글자 색 삭제</label>
	<label style="display:block"><input class="option1group" type="checkbox" value="style"> 모든 스타일 삭제</label>
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

		var options = [],
			str = '';

		$('.option1group:checked').each(function (i) {
			options[i] = $(this).val();
		});

		if(options.length > 0) {
			for (var i in options) {
				str = str + options[i] + '|';
			}
			str = str.slice(0, -1);
			if(options.length > 1){
				str = '(' + str + ')';
			}

			var pattern = new RegExp(str + '\s?=\s?\"([^\"\>]*)\"', 'ig');
			html = html.replace(pattern, '');
		}

		if($('[value="style"]:checked').length === 0) {
			options = [];
			str = '';

			$('.option2group:checked').each(function (i) {
				options[i] = $(this).val();
			});

			if(options.length > 0) {
				for (var i in options) {
					str = str + options[i] + '|';
				}
				str = str.slice(0, -1);
				if(options.length > 1){
					str = '(' + str + ')';
				}

				pattern = RegExp('(style\s?=\s?\"[^\"\>]*' + str + '[a-z\-]+\s?\:\s?[^\"\>]*\")', 'ig');
				html = html.replace(pattern, function replacer(match, p1, offset, string) {
					pattern = RegExp(str+'[a-z\-]*\s?:\s?[^;]*;','ig');
					return p1.replace(pattern, '');
				});
			}
		}

		options = [];
		str = '';

		$('.option3group:checked').each(function (i) {
			options[i] = $(this).val();
		});

		if(options.length > 0) {
			for (var i in options) {
				str = str + options[i] + '|';
			}
			str = str.slice(0, -1);
			if(options.length > 1){
				str = '(' + str + ')';
			}

			var pattern = new RegExp('<' + str + '[^>]*>([^]*?)<\/' + str + '>', 'gmi');
			html = html.replace(pattern, '');
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
/* Location: ./module/editor/components/remove_styles/index.php */
