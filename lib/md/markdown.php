<?php
/*!
 * aFox (https://github.com/phiDelPark/aFox)
 * Copyright 2016 afox, Inc.
 */
require_once _AF_LIBS_PATH_ . 'md/parsedown/Parsedown.php';
require_once _AF_LIBS_PATH_ . 'md/html2mkdw.php';

class MD {

	public static function toMKDW($html, $admin)
	{
		if (!@$__markdown) $__markdown = new HtmlToMkdw;
		return $__markdown->html($html, $admin);
	}

	public static function toHTML($text)
	{
		if (!@$__Parsedown) $__Parsedown = new Parsedown;
		$__Parsedown->setUrlsLinked(false);
		$text = preg_replace(
			'/((<[\w]+[^>]+)javascript\s*\:|(&)amp;)/i', '\2\3',
			  $__Parsedown->text($text)
		);
		/*// DEPRECATED (Parsedown 에서 자동으로 하게 고침)
		$text = preg_replace_callback('@(<pre[^>]*>)(.+?)(<\/pre>)@s',
			function ($m) use($__Parsedown) {
				return $m[1].$__Parsedown->line($m[2]).$m[3];
			}
		, $text);
		//*/
		return $text;
	}
}

/* End of file markdown.php */
/* Location: ./md/markdown.php */
