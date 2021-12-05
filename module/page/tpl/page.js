/*!
 * aFox (https://github.com/phiDelPark/aFox)
 * Copyright 2016 afox, Inc.
 */

(function($) {
	'use strict';

	$(document).on('change.af.editor.toolbar', '.af-editor-toolbar', function(e, tar, old, val) {
		var $e = $(this).closest('.af-editor-group');
		if (tar == 'pg_type' && $e.length == 1) $e.data('af.editor').switch(val === '2');
	});

	$(window)
		.on('load', function() {
			$(this).trigger('scroll');
		})
		.on('scroll', function() {
			$('img[data-scroll-src]').each(function() {
				var $th = $(this);
				if ($th.offset().top < ($(window).scrollTop() + $(window).height() + 100)) {
					$th.attr('src', $th.attr('data-scroll-src') || '#');
					$th.removeAttr('data-scroll-src');
				}
			});
		});

})(jQuery);
