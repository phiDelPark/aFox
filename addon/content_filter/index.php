<?php
if(!defined('__AFOX__')) exit();

if($called_position == 'before_proc' && ($called_trigger == 'updatecomment' || $called_trigger == 'updatedocument'))
{
	$key = $called_trigger == 'updatecomment' ? 'rp_content' : 'wr_content' ;

	$filter = str_replace([',',' '], ['|','(^|$|\s|\&nbsp\;)'], trim($_ADDON['filter']));
	$_change = explode(',', trim($_ADDON['change_text']));
	$length = count($_change);

	if(!empty($filter))
	{
		$_DATA[$key] = preg_replace_callback('/('.$filter.')/si',
			function($m)use($_change, $length)
			{
				$s = $_change[rand(0, $length - 1)];
				if($s == '♡') $s = str_repeat($s, mb_strlen(trim($m[1]), 'utf-8'));
				return preg_replace('/^(\s*).+(\s*)$/', '\1'.$s.'\2', $m[1]);
			},
		$_DATA[$key]);

		if($called_trigger == 'updatedocument')
		{
			$_DATA['wr_title'] = preg_replace_callback('/('.$filter.')/si',
				function($m)use($_change, $length) {
					$s = $_change[rand(0, $length - 1)];
					if($s == '♡') $s = str_repeat($s, mb_strlen(trim($m[1]), 'utf-8'));
					return preg_replace('/^(\s*).+(\s*)$/', '\1'.$s.'\2', $m[1]);
				},
			$_DATA['wr_title']);
		}
	}

	$regexs = explode("\r", trim($_ADDON['regex']));
	$length = count($regexs);

	for ($i=0; $i < $length; $i++)
	{
		if(preg_match('/^(\/.*[^\/]\/[a-zA-Z]*)(,|@)(.*)$/s', trim($regexs[$i]), $m))
		{
			if(count($m) == 4)
			{
				if($m[2] == '@')
				{
					$eval = trim($m[3]);
					$_DATA[$key] = preg_replace_callback($m[1],
						function($matches)use($eval) {
							$return = "";
							@eval($eval);
							return $return;
						},
					$_DATA[$key]);
				}
				else
					$_DATA[$key] = preg_replace($m[1], $m[3], $_DATA[$key]);
			}
		}
	}
}

/* End of file index.php */
/* Location: ./addon/content_filter/index.php */
