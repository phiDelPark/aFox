<?php
if(!defined('__AFOX__')) exit();

addJS(_AF_URL_.'addon/media_manager/media_manager.js');

if(!empty($_DATA['wr_content'])) {

	$patterns = '/(\[youtube\]\((https?:\/\/[a-z.]*youtube.com\/[^\"\)]+)[^\)]*\))/is';
	$replacement = '<iframe src="\\2" frameborder="0" allowfullscreen></iframe>';
	$_DATA['wr_content'] = preg_replace($patterns, $replacement, $_DATA['wr_content']);
}

/* End of file dispboarddefault.php */
/* Location: ./addon/media_manager/after/dispboarddefault.php */