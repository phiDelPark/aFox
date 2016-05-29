/*!
 * aFox (http://afox.kr)
 * Copyright 2016 afox, Inc.
 * Licensed under the MIT license
 */

+function ($) {
  'use strict';

	$('.current_content img,.current_content video').each(function(){
		if(!this.hasAttribute('width')) {
			var w1 = $(this).closest('.current_content').width(),
				w2 = $(this).width();
			if(w1<w2) $(this).attr('width', '100%');
		}
	});

}(jQuery);