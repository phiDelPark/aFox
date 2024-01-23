<?php
if(!defined('__AFOX__')) exit();

if($called_position == 'before_proc' && ($called_trigger == 'updatecomment' || $called_trigger == 'updatedocument'))
{
	$key = $called_trigger == 'updatecomment' ? 'rp_content' : 'wr_content' ;

	$exs = str_replace(",", "|", trim($_ADDON['exclusion_attr']));
	$_DATA[$key] = preg_replace_callback('/(<[\w\-]+)(\s[^>]*)>/is',
		function($m)use($exs) {
			return $m[1].preg_replace(
				'/\s\b(?!'.$exs.')[\w_-]+\b=["]?(?:.(?!["]?\s+(?:\S+)=|\s*\/?[>"]))*.["]?/mi'
				, '', $m[2]
			).'>';
		},
	$_DATA[$key]);

	$regexs = explode("\r", trim($_ADDON['regex']));
	$length = count($regexs);

	for ($i=0; $i < $length; $i++) {
		if(preg_match('/^(\/.*[^\/]\/[a-zA-Z]*),(.*)$/s', trim($regexs[$i]), $m)) {
			if(count($m) == 3) {
				$_DATA[$key] = preg_replace($m[1], $m[2], $_DATA[$key]);
			}
		}
	}

	$filter = str_replace([',',' '], ['|','(^|$|\s|\&nbsp\;)'], trim($_ADDON['filter']));
	$_change = explode(',', trim($_ADDON['change_text']));
	$length = count($_change);

	if(!empty($filter)) {
		$_DATA[$key] = preg_replace_callback('/('.$filter.')/si',
			function($m)use($_change, $length) {
				$s = $_change[rand(0, $length - 1)];
				if(strlen($s) === 1) $s = str_repeat($s, mb_strlen(trim($m[1]), 'utf-8'));
				return preg_replace('/^(\s*).+(\s*)$/', '\1'.$s.'\2', $m[1]);
			},
		$_DATA[$key]);

		if($called_trigger == 'updatedocument') {
			$_DATA['wr_title'] = preg_replace_callback('/('.$filter.')/si',
				function($m)use($_change, $length) {
					$s = $_change[rand(0, $length - 1)];
					if(strlen($s) === 1) $s = str_repeat($s, mb_strlen(trim($m[1]), 'utf-8'));
					return preg_replace('/^(\s*).+(\s*)$/', '\1'.$s.'\2', $m[1]);
				},
			$_DATA['wr_title']);
		}
	}
}

/* End of file index.php */
/* Location: ./addon/content_filter/index.php */
