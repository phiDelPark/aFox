/*!
 * aFox (http://afox.kr)
 * Copyright 2016 afox, Inc.
 * Licensed under the MIT license
 */

+function ($) {
  'use strict';

	$(window).on('load', function() {
		var scripts = $('script[src^="'+request_uri+'addon/media_manager/media_manager.js?"]'),
			autosize_image = true,
			autosize_video = true;

		if(scripts.length>0) {
			scripts = scripts.attr('src').getQuery();
			if((scripts['i'] || '1') == '0') autosize_image = false;
			if((scripts['v'] || '1') == '0') autosize_video = false;
		}

		if(autosize_image === true) {
			$('.current_content img').each(function(){
				var w1 = $(this).closest('.current_content').width(),
					w2 = $(this).width();
				if(w1<w2) $(this).attr('width', '100%');
			});
		}
		if(autosize_video === true) {
			$('.current_content video,.current_content iframe[src*="youtube.com"]').each(function(){
				var w1 = $(this).closest('.current_content').width(),
					w2 = $(this).width();
				$(this).attr('width', w1).attr('height', (w1*0.5625));
			});
		}
	});

}(jQuery);