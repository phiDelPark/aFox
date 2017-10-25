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
					code = '<!doctype html><html lang="ko"><head><meta charset="utf-8"><script src="%s"></script><script src="%s"></script><style>%s</style></head><body>%s <script>%s</script></body></html>',
					_css = $this.find('[web-code-run="css"]').text() || '',
					_html = $this.find('[web-code-run="html"]').text() || '',
					_script = $this.find('[web-code-run="script"]').text() || '';
				code = code.sprintf('../../../common/js/jquery.min.js', '../../../common/js/bootstrap.min.js', _css, _html, _script);
				pw.document.write(code);
			});
		});
	});

}(jQuery);
