<?php if(!defined('__AFOX__')) exit();

if($_CALLED['position'] == 'after_disp'
	&& (
		$_CALLED['trigger'] == 'default'
		|| $_CALLED['trigger'] == 'inbox'
		|| $_CALLED['trigger'] == 'trash'
	)
){
	if((empty($_DATA['wr_content']) && empty($_DATA['pg_content']) && empty($_DATA['nt_content']))) return;

	$opt = 'm=' . _MODULE_ . '&';
	$opt .= (empty($_ADDON['link_blank']) ? 'l=0&' : 'l=1&');
	$opt .= (empty($_ADDON['autosize_image']) ? 'i=0&' : 'i=1&');
	$opt .= (empty($_ADDON['autosize_video']) ? 'v=0&' : 'v=1&');
	$opt .= (empty($_ADDON['autosize_table']) ? 't=0' : 't=1');

	addJS(_AF_URL_.'addon/object_manager/object_manager'.(__DEBUG__ ? '.js' : '.min.js').(empty($opt)?'':'?'.$opt));
}

/* End of file index.php */
/* Location: ./addon/object_manager/index.php */
