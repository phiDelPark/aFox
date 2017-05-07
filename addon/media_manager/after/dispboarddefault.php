<?php
if(!defined('__AFOX__')) exit();

addJS(_AF_URL_.'addon/media_manager/media_manager.js');

if($_ADDON['print_youtube']!=='0' && !empty($_DATA['wr_content'])) {
	//$patterns = '/(\[youtube\]\((https?:\/\/[a-z\.]*youtub?e?)\.(com|be)(\/embed\/|\/watch\?v\=|\/)([^\"\)]+)[^\)]*\))/is';
	$patterns = '/<img src=\"https?:\/\/img.youtube.com\/vi\/([^\"\/]+)\/([0-3]).jpg\" data-pos=\"([a-zA-Z0-9]*)\">/is';
	$replacement = '<iframe src="https://www.youtube.com/embed/\\1" frameborder="0" allowfullscreen></iframe>';
	//$_DATA['wr_content'] = preg_replace($patterns, $replacement, $_DATA['wr_content']);

	$_DATA['wr_content'] = preg_replace_callback('/<img[^>]*src=\"https?:\/\/img.youtube.com\/vi\/[^"]+"\s*([^>]*)>/is',  function($m)use($call){
			if(preg_match_all('/([a-z0-9_-]+)="([^"]+)"/is', $m[1], $m2)) {
				$attrs = [];
				foreach ($m2[1] as $key => $val) $attrs[$val] = $m2[2][$key];
				if(!empty($attrs['data-vid'])){
					return '<iframe src="https://www.youtube.com/embed/'.$attrs['data-vid'].($attrs['data-pos']?'?t='.$attrs['data-pos']:'').'" frameborder="0" allowfullscreen></iframe>';
				}
			}
			return '';
		}, $_DATA['wr_content']);
}

/* End of file dispboarddefault.php */
/* Location: ./addon/media_manager/after/dispboarddefault.php */