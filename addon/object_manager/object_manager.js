/*!
 * aFox (https://github.com/phiDelPark/aFox)
 * Copyright 2016 afox, Inc.
 * Licensed under the MIT license
 */

// 모달 열리때 실행하기 위해 함수 등록
window.addonObjectManager = (content_id = '.current_content') => {
	let
		js = document.querySelector('script[src^="'+request_uri+'addon/object_manager/"]'),
		module = '',
		link_blank = false,
		autosize_image = false,
		autosize_video = false,
		autosize_table = false

	if(js) {
		js = js.getAttribute('src').getQuery()
		module = js['m'] || ''
		if((js['l'] || '0') == '1') link_blank = true
		if((js['i'] || '0') == '1') autosize_image = true
		if((js['v'] || '0') == '1') autosize_video = true
		if((js['t'] || '0') == '1') autosize_table = true
	}
/*
	const reSizeElement = (el, width) => {
		if(el.offsetWidth > width){
			el.style.width = '100%'
			el.style.height = 'auto'
			el.removeAttribute('height')
			//el.style.height = (width*0.5625) + 'px'
		}
	}
*/
	if(autosize_video === true) {
		const
			$videos = document.querySelectorAll(content_id + ' video,' + content_id + ' a[href*="youtube.com"]'),
			width = ($videos[0]?.closest(content_id).clientWidth) || '200'
		$videos.forEach($e => {
			if($e.tagName == 'A') {
				const $iframe = document.createElement('iframe')
				$iframe.setAttribute('frameborder', '0')
				$iframe.setAttribute('allowfullscreen', '')
				$iframe.setAttribute('title', 'YouTube video player')
				$iframe.setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share')
				$iframe.src = 'https://www.youtube.com/embed/' + $e.href.getQuery('v')
				$iframe.style.width = width + 'px'
				$iframe.style.height = (width*0.5625) + 'px'
				$e.before($iframe)
				$e.style.display = 'none'
			} else {
				$e.style.width = width + 'px'
			}
		})
	}

	if(autosize_table === true) {
		const
			$tables = document.querySelectorAll(content_id + ' table'),
			width = ($tables[0]?.closest(content_id).clientWidth) || '200'
		$tables.forEach($table => {
			const $tr = $table.querySelectorAll('tr')
			$tr.forEach($tr => {
				const $td = $tr.querySelectorAll('th,td'),
					n = $td?.length || 0
				$td.forEach($td => $td.style.maxWidth = ((width / n) - 1) + 'px');
			})
		})
	}


/*
	if(autosize_image === true) {
		const
			$images = document.querySelectorAll(content_id + ' img,' + content_id + ' input[type=image]')
			//width = (images[0]?.closest(content_id).clientWidth) || '200'

		$images.forEach($e => {
			//el.addEventListener('load', e => reSizeElement(el, width))
			//reSizeElement(el, width)
			$e.style.maxWidth = '100%'
		})
	}
*/

	if(link_blank === true && (module == 'board' || module == 'page')) {
		const $alinks = document.querySelectorAll(content_id + ' a[href]')

		$alinks.forEach($e => {
			const target = $e.getAttribute('target') || false
			if(!target) $e.setAttribute('target', '_blank')
		})
	}
}

window.addEventListener('load', e => addonObjectManager())