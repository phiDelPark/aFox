<?php
if(!defined('__AFOX__')) exit();

if($called_position == 'after_disp' && $called_trigger == 'default' && !empty($_DATA['wr_content'])) {
	$opt = '';
	if($_ADDON['autosize_image'] == '0' || $_ADDON['autosize_video'] == '0') {
		$opt .= ($_ADDON['autosize_image'] != '0' ? 'i=1&' : 'i=0&');
		$opt .= ($_ADDON['autosize_video'] != '0' ? 'v=1' : 'v=0');
	}
	addJS(_AF_URL_.'addon/media_manager/media_manager.js'.(empty($opt)?'':'?'.$opt));
}

/* End of file index.php */
/* Location: ./addon/media_manager/index.php */