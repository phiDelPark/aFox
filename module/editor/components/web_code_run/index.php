<?php
if(!defined('__AFOX__')) exit();
?>
<div class="web_code_selector" style="display:block">
	<div style="margin:10px">
		<h4><?php echo $_COMPONENT_INFO['title'] ?></h4>
		<hr style="margin:10px 0 20px">
		<label style="display:block"><input name="webCode" type="radio" value="-1" checked="checked"> <span>새로 만들기</span></label>
	</div>
	<hr style="margin:20px 0 10px">
	<div style="margin:10px;text-align:right">
		<button class="btn btn-default" onclick="web_code_select()">확인</button>
		<button class="btn btn-default" onclick="window.close()">취소</button>
	</div>
</div>
<div class="web_code_editor" style="display:none">
	<div style="margin:10px">
		<h4><?php echo $_COMPONENT_INFO['title'] ?></h4>
		<hr style="margin:10px 0 20px">
		<h5>TITLE ▽</h5>
		<input type="text" name="title" value="" style="width:100%">
		<h5>CSS ▽</h5>
		<textarea style="width:100%;height:150px"></textarea>
		<h5>SCRIPT ▽</h5>
		<textarea style="width:100%;height:150px"></textarea>
		<h5>HTML ▽</h5>
		<textarea style="width:100%;height:150px"></textarea>
	</div>
	<hr style="margin:20px 0 10px">
	<div style="margin:10px;text-align:right">
		<button class="btn btn-default" onclick="web_code_run()">확인</button>
		<button class="btn btn-default" onclick="window.close()">취소</button>
	</div>
</div>
<script>
	var $editor = opener.AF_EDITOR_<?php echo _AF_EDITOR_NAME_ ?>,
		$txtara = $editor.$textarea,
		$iframe = $editor.$element.find('iframe'),
		$orihtml = $('<div>'+($iframe.length > 0 ? $iframe.contents().find('body').html() : $txtara.val())+'</div>');

	var $wecode = $orihtml.find('[web-code-run="area"]');

	function web_code_select() {
		var i = $('[name="webCode"]:checked').val();
		if(i > -1) {
			var $wc = $wecode.eq(i),
			$tareas = $('textarea');
			$('[name="title"]').val($wc.find('cite').eq(0).text().trim() || '');
			$tareas.eq(0).val($wc.find('[web-code-run="css"]').text().trim());
			$tareas.eq(1).val($wc.find('[web-code-run="script"]').text().trim());
			$tareas.eq(2).val($wc.find('[web-code-run="html"]').text().trim());
		}
		$('.web_code_selector').hide();
		$('.web_code_editor').attr('data-index', i).show();
	}

	function web_code_run() {
		var $tareas = $('textarea'),
			title = $('[name="title"]').val(),
			index = $('.web_code_editor').attr('data-index'),
			html;

		if(index > -1) {
			var $wc = $orihtml.find('[web-code-run="area"]:eq('+index+')');
			$wc.find('cite').eq(0).text(title ? title : 'Code Run');
			$wc.find('[web-code-run="css"]').text($tareas.eq(0).val());
			$wc.find('[web-code-run="script"]').text($tareas.eq(1).val());
			$wc.find('[web-code-run="html"]').text($tareas.eq(2).val());
			if($iframe.length > 0) {
				$iframe.contents().find('body').html($orihtml.html());
			}else {
				$txtara.val($orihtml.text());
			}
		} else {
			html = '<blockquote web-code-run="area">' + "\n";
			html = html + '<cite>%s</cite><hr>' + "\n";
			html = html + '<pre>CSS:<code web-code-run="css"> %s' + "\n";
			html = html + '</code></pre>' + "\n";
			html = html + '<pre>SCRIPT:<code web-code-run="script"> %s' + "\n";
			html = html + '</code></pre>' + "\n";
			html = html + '<pre>HTML:<code web-code-run="html"> %s' + "\n";
			html = html + '</code></pre>' + "\n";
			html = html + '<button web-code-run="run">코드 실행</button>' + "\n";
			html = html + '</blockquote>' + "\n";
			$editor.paste(
				html.sprintf(
					(title ? title : 'Code Run'),
					($tareas.eq(0).val().escapeHtml() || ('/* 이 아래로 코드 입력 */' + "\n" + "\n")),
					($tareas.eq(1).val().escapeHtml() || ('/* 이 아래로 코드 입력 */' + "\n" + "\n")),
					($tareas.eq(2).val().escapeHtml() || ('&lt;!--/ 이 아래로 코드 입력 /--&gt;' + "\n" + "\n"))
				),
				false
			);
		}

		alert('웹 실행 코드 입력 완료');
		window.close();
	}

	if($wecode && $wecode.length > 0){
		var $lbarea = $('.web_code_selector').find('>div').eq(0),
			$label = $lbarea.find('label').eq(0);
		for (var i = 0; i < $wecode.length; i++) {
			$label.clone().find('span').text($wecode.eq(i).find('cite').eq(0).text()).end().find('input').val(i).removeAttr('checked').end().appendTo($lbarea);
		}
	} else {
		web_code_select();
	}

</script>
<?php
/* End of file index.php */
/* Location: ./module/editor/components/web_code_run/index.php */
