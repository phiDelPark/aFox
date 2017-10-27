/*!
 * aFox (http://afox.kr)
 * Copyright 2016 afox, Inc.
 * Licensed under the MIT license
 */

+function ($) {
  'use strict';

	$(window).on('load', function() {
		$('[web-code-run="area"]').each(function() {
			var $this = $(this);
			$this.find('[web-code-run="run"]').on('click', function() {
				var pw = pop_win('about:blank', null, null, 'af_editor_components'),
					code = '<!doctype html><html lang="ko"><head><meta charset="utf-8"><link href="%s" rel="stylesheet"><script src="%s"></script><script src="%s"></script><style>%s</style></head><body><div style="margin:5px">%s</div> <script>%s</script></body></html>',
					_css = $this.find('[web-code-run="css"]').text() || '',
					_html = $this.find('[web-code-run="html"]').text() || '',
					_script = $this.find('[web-code-run="script"]').text() || '';
				code = code.sprintf('../../../common/css/bootstrap.min.css', '../../../common/js/jquery.min.js', '../../../common/js/bootstrap.min.js', _css, _html, _script);
				pw.document.open('text/html', 'replace');
				pw.document.write(code);
				pw.document.close();

				// 작동함, 별로 필요없어보여 안하기로...
				//<body onload="document.getElementById('preview-container').style.display='block'"><div id="preview-container" style="display:none">
				// 작동 안함
				//pw.document.getElementById("preview-container").style.display = 'block';
				//code.find('#preview-container').show();
			});
		});
	});

}(jQuery);
