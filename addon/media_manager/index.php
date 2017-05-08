<?php
if(!defined('__AFOX__')) exit();

if($called_position == 'after_disp' && $called_trigger == 'default' && !empty($_DATA['wr_content'])) {

	addJS(_AF_URL_.'addon/media_manager/media_manager.js');

	if($_ADDON['print_youtube']!=='0') {

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
}

/* End of file index.php */
/* Location: ./addon/media_manager/index.php */