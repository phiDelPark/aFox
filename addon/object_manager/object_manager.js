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

		if(module == 'board' || module == 'page') {
			var is_modal = false;
			if(link_blank === true) {
				$('.current_content a[href]').each(function(){
					var target = $(this).attr('target') || '';
					if(target == '' && module == 'board')
						$(this).attr('target', '_blank');
					else if(target == '_modal')
					{
						is_modal = true;
						$(this).on('click', function(e){
							e.preventDefault();
							$('#addon_object_manager_modal').modal('show').find('.modal-body').load($(this).attr('href'));
						});
					}
				});
			}
			if(is_modal && $('#addon_object_manager_modal').length === 0)
			{
				var html = '<div id="addon_object_manager_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="addonObjectManagerModalLabel" aria-hidden="true">'
						+ '<div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-body" style="padding:5px;overflow:hidden!important"></div></div></div></div>';
				$(html).appendTo('body')
				.on('shown.bs.modal', function(e) {
					$(this).find('.modal-body:eq(0)')
					.find('div:eq(0)').attr('style', 'width:100%!important;overflow:hidden!important')
					.find('img[data-scroll-src]').each(function(){
						var src = $(this).attr('data-scroll-src') || '';
						$(this).removeAttr('data-scroll-src').attr('src', src);
					});
				}).on('hidden.bs.modal', function(e) {
					$(this).find('.modal-body:eq(0)').empty();
				});
			}
		}
	});

}(jQuery);
