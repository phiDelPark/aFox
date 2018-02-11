<?php
if(!defined('__AFOX__')) exit();

if(__REQ_METHOD__ == 'GET'|| __REQ_METHOD__ == 'POST') {
	if($called_position == 'after_disp' && $called_trigger == 'default' && !empty($_DATA['wr_content'])) {

		$opt = '';
		$opt .= (empty($_ADDON['link_blank']) ? 'l=0&' : 'l=1&');
		$opt .= (empty($_ADDON['autosize_image']) ? 'i=0&' : 'i=1&');
		$opt .= (empty($_ADDON['autosize_video']) ? 'v=0' : 'v=1');

		addJS(_AF_URL_.'addon/object_manager/object_manager.js'.(empty($opt)?'':'?'.$opt));
	}
}

/* End of file index.php */
/* Location: ./addon/object_manager/index.php */

