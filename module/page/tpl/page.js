/*!
 * aFox (http://afox.kr)
 * Copyright 2016 afox, Inc.
 */

(function($) {
	'use strict';

	$(document).on('change.af.editor.toolbar', '.af-editor-toolbar', function(e, tar, old, val) {
		var $e = $(this).closest('.af-editor-group');
		if (tar == 'pg_type' && $e.length == 1) $e.data('af.editor').switch(val === '2');
	});

})(jQuery);
