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
	}
}

window.addEventListener('load', e => addonObjectManager());