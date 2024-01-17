/*!
 * aFox (https://github.com/phiDelPark/aFox)
 * Copyright 2016 afox, Inc.
 * Licensed under the MIT license
 */

// 모달 열리때 실행하기 위해 함수 등록
window.addonObjectManager = (content_id = '.current_content') => {
	let
		js = document.querySelector('script[src^="'+request_uri+'addon/object_manager/object_manager."]'),
		module = '',
		link_blank = false,
		autosize_image = false,
		autosize_video = false;

	if(js) {
		js = js.getAttribute('src').getQuery();
		module = js['m'] || '';
		if((js['l'] || '0') == '1') link_blank = true;
		if((js['i'] || '0') == '1') autosize_image = true;
		if((js['v'] || '0') == '1') autosize_video = true;
	}

	const reSizeElement = (el, width) => {
		if(el.offsetWidth > width){
			el.style.width = '100%';
			el.style.height = 'auto';
			el.removeAttribute('height');
			//el.style.height = (width*0.5625) + 'px';
		}
	};

	if(autosize_video === true) {
		const
			videos = document.querySelectorAll(content_id + ' video,' + content_id + ' iframe[src*="youtube.com"]'),
			width = (videos[0]?.closest(content_id).clientWidth) || '200';
		videos.forEach(el => {
			el.addEventListener('load', e => reSizeElement(el, width));
			reSizeElement(el, width);
		});
	}

	if(autosize_image === true) {
		const
			images = document.querySelectorAll(content_id + ' img,' + content_id + ' input[type=image]'),
			width = (images[0]?.closest(content_id).clientWidth) || '200';

		images.forEach(el => {
			el.addEventListener('load', e => reSizeElement(el, width));
			reSizeElement(el, width);
		});
	}

	if(link_blank === true && (module == 'board' || module == 'page')) {
		let is_modal = false;
		const
			alinks = document.querySelectorAll(content_id + ' a[href]');

		alinks.forEach(el => {
			const target = el.getAttribute('target') || false;
			if(!target) el.setAttribute('target', '_blank');
		});

		/*
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
			var html = '<div id="addon_object_manager_modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">'
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
		*/
	}
}

window.addEventListener('load', e => addonObjectManager());