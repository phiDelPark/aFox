/*!
 * aFox (http://afox.kr)
 * Copyright 2016 afox, Inc.
 * Licensed under the MIT license
 */

+function ($) {
  'use strict';

	$(window).on('load', function() {
		var scripts = $('script[src^="'+request_uri+'addon/object_manager/object_manager.js?"]'),
			link_blank = true,
			autosize_image = true,
			autosize_video = true;

		if(scripts.length>0) {
			scripts = scripts.attr('src').getQuery();
			if((scripts['l'] || '1') == '0') link_blank = false;
			if((scripts['i'] || '1') == '0') autosize_image = false;
			if((scripts['v'] || '1') == '0') autosize_video = false;
		}

		if(autosize_video === true) {
			$('.current_content video,.current_content iframe[src*="youtube.com"]').each(function(){
				var $th = $(this),
					w1 = $th.closest('.current_content').width(),
					w2 = $th.width();
				$th.attr('width', w1).attr('height', (w1*0.5625));
			});
		}
		if(autosize_image === true) {
			$('.current_content img').each(function(){
				var $th = $(this);
				function img_resize(){
					var w1 = $th.closest('.current_content').width(),
						w2 = $th.width();
					if(w1<w2) $th.attr('width', '100%');
					$th.on('load', img_resize);
				}
				img_resize();
			});
		}
		if(link_blank === true) {
			$('.current_content a[href]').each(function(){
				$(this).attr('target', '_blank');
			});
		}
	});

}(jQuery);
