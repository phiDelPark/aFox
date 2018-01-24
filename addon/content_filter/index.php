<?php
if(!defined('__AFOX__')) exit();

$_change_filter = '좋아,사랑해,이쁘니,귀요미,♡';

if($called_position == 'before_proc' && ($called_trigger == 'updatecomment' || $called_trigger == 'updatedocument')) {

	$key = $called_trigger == 'updatecomment' ? 'rp_content' : 'wr_content' ;

	$filter = str_replace([',',' '], ['|','(^|$|\s|\&nbsp\;)'], trim($_ADDON['filter']));
	$_change = explode(',', $_change_filter);
	$length = count($_change);

	$_DATA[$key] = preg_replace_callback('/('.$filter.')/si',
		function($m)use($_change, $length) {
			$s = $_change[rand(0, $length - 1)];
			if($s == '♡') $s = str_repeat($s, mb_strlen(trim($m[1]), 'utf-8'));
			return preg_replace('/^(\s*).+(\s*)$/', '\1'.$s.'\2', $m[1]);
		},
	$_DATA[$key]);

	if($called_trigger == 'updatedocument') {
		$_DATA['wr_title'] = preg_replace_callback('/('.$filter.')/si',
			function($m)use($_change, $length) {
				$s = $_change[rand(0, $length - 1)];
				if($s == '♡') $s = str_repeat($s, mb_strlen(trim($m[1]), 'utf-8'));
				return preg_replace('/^(\s*).+(\s*)$/', '\1'.$s.'\2', $m[1]);
			},
		$_DATA['wr_title']);
	}
}

/* End of file index.php */
/* Location: ./addon/content_filter/index.php */
