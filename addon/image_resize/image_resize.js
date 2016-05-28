/*!
 * aFox (http://afox.kr)
 * Copyright 2016 afox, Inc.
 * Licensed under the MIT license
 */

+function ($) {
  'use strict';

	$('.current_content img,.current_content video').each(function(){
		if(!this.hasAttribute('width')) {
			$(this).attr('width', '100%');
		}
	});

}(jQuery);