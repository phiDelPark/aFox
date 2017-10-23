/*!
 * aFox (http://afox.kr)
 * Copyright 2016 afox, Inc.
 */

(function($) {
	'use strict';

	$(window).load(function() {
		$("#bsPreLoader").fadeOut("slow");

		$('.sticky-message').each(function() {
			var $this = $(this);
			$this.find('a[href="#Close"]').click(function() {
				$this.remove();
				return false;
			});
		});
	});

})(jQuery);
