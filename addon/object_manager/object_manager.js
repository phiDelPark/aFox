/*!
 * aFox (http://afox.kr)
 * Copyright 2016 afox, Inc.
 * Licensed under the MIT license
 */

+function ($) {
  'use strict';

	$(window).on('load', function() {
		var js = $('script[src^="'+request_uri+'addon/object_manager/object_manager.js?"]:eq(0)'),
			module = '',
			link_blank = false,
			autosize_image = false,
			autosize_video = false;

		if(js.length>0) {
			js = js.attr('src').getQuery();
			module = js['m'] || '';
			if((js['l'] || '0') == '1') link_blank = true;
			if((js['i'] || '0') == '1') autosize_image = true;
			if((js['v'] || '0') == '1') autosize_video = true;
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
					if(w1<w2) {
						$th.attr('width', '100%');
						$th.removeAttr('height');
					}
					$th.on('load', img_resize);
				}
				img_resize();
			});
		}
		if(module == 'board' && link_blank === true) {
			$('.current_content a[href]').each(function(){
				$(this).attr('target', '_blank');
			});
		}
	});

}(jQuery);
