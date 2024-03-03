<?php
/*!
 * aFox (https://github.com/phiDelPark/aFox)
 * Copyright 2016 afox, Inc.
 */

class HtmlToMkdw
{
	protected $markdownable = [
		'widget'	=> ['type'=>'admin'],
		'script'	=> ['type'=>'admin'],
		'style'		=> ['type'=>'admin'],
		'br'		=> ['type'=>'break'],
		'hr'		=> ['type'=>'break'],
		'div'		=> ['type'=>'break/block'],
		'p'			=> ['type'=>'break/block'],
		'h1'		=> ['type'=>'block'],
		'h2'		=> ['type'=>'block'],
		'h3'		=> ['type'=>'block'],
		'h4'		=> ['type'=>'block'],
		'h5'		=> ['type'=>'block'],
		'h6'		=> ['type'=>'block'],
		'blockquote'=> ['type'=>'block'],
		'pre'		=> ['type'=>'block'],
		'ol'		=> ['type'=>'block'],
		'ul'		=> ['type'=>'block'],
		'dl'		=> ['type'=>'block'],
		'table'		=> ['type'=>'block'],
		'i'			=> ['type'=>'inline/text'],
		'em'		=> ['type'=>'inline/text'],
		'b'			=> ['type'=>'inline/text'],
		'strong'	=> ['type'=>'inline/text'],
		's'			=> ['type'=>'inline/text'],
		'strike'	=> ['type'=>'inline/text'],
		'del'		=> ['type'=>'inline/text'],
		'u'			=> ['type'=>'inline/text'],
		'code'		=> ['type'=>'inline/text'],
		'input'		=> ['type'=>'inline'],
		'img'		=> ['type'=>'inline'],
		'a'			=> ['type'=>'inline/block'],
	];

	protected $htmlParts = [];

	protected function rnl2br($str, $br = '<br>')
	{
		return preg_replace('/[\r\n]+/s', $br, $str);
	}

	protected function array_ltrim($index, $array)
	{
		for ($i=$index; $i > -1; $i--) {
			if(trim($array[$i])) break;
			$array[$i] = '';
		}
		return $array;
	}

	protected function array_rtrim($index, $array)
	{
		for ($i=$index, $n=count($array); $i < $n; $i++) {
			if(trim($array[$i])) break;
			$array[$i] = '';
		}
		return $array;
	}

	protected function inlineImage()
	{
		if(list($tag, $close, $attr) = current($this->htmlParts))
		{
			$a = ['src'=>'','title'=>'','label'=>'','alt'=>'!IMAGE'];
			if (preg_match_all(
				'/(\b(?:'.implode('|', array_keys($a)).'))\s*=(?:\s*["\'])?(?(2)([^"\']*?)\2|([^"\']+))/is',
				$attr,
				$m2
			)) {
				foreach ($m2[1] as $m2k => $m2v) $a[strtolower($m2v)] = $m2[3][$m2k];
				if ($srl = $a['src']) {
					return sprintf('![%s](%s%s)', $a['alt'] ? $a['alt'] : '!IMAGE', $srl, $a['title']?' "'.$a['title'].'"':'');
				}
			}
		}
		return '';
	}

	protected function inlineLink()
	{
		$array = [];
		$inlinelink = 0;
		$a = ['href'=>'','title'=>'','label'=>'','alt'=>'!LINK'];

		while (list($tag, $close, $attr) = current($this->htmlParts))
		{
			if (!$tag) $array[] = $attr; // text
			else {
				$md = $this->markdownable[$tag];
				$type = explode('/', $md['type']);

				switch ($close.$tag) {
					case '/a':
						if(--$inlinelink) break 1;
						if($srl = $a['href']) {
							if(@end($array) == '['){
								$array[] = ($a['alt'] ? $a['alt'] : '!LINK');
							}
							$array[] = sprintf('](%s%s)', $srl, $a['title']?' "'.$a['title'].'"':'');
						}
						break 2; // exit loop
					case 'a':
						if(!$inlinelink++){ // 처음 한번만
							if(preg_match_all(
								'/(\b(?:'.implode('|', array_keys($a)).'))\s*=(?:\s*["\'])?(?(2)([^"\']*?)\2|([^"\']+))/is',
								$attr, $m2)
							){
								foreach ($m2[1] as $m2k => $m2v) $a[strtolower($m2v)] = $m2[3][$m2k];
							}
							$array[] = '[';
						}
						break;
					default:
						if(method_exists($this, $type[0].'Element')){
							$array[] = $this->{$type[0].'Element'}($tag);
						}
						break;
				}
			}

			next($this->htmlParts);
		}

		return implode('', $array);
	}

	protected function inlineCode()
	{
		$array = [];
		$inlinecode = 0;

		while (list($tag, $close, $attr) = current($this->htmlParts))
		{
			if (!$tag) $array[] = $attr; // text
			else {
				$md = $this->markdownable[$tag];
				$type = explode('/', $md['type']);

				switch ($close.$tag) {
					case '/code':
						if(--$inlinecode) break 1;
						$array[] = "`";
						break 2; // exit loop
					case 'code':
						if(!$inlinecode++) $array[] = "`";
						break;
					default:
						if(method_exists($this, $type[0].'Element')){
							$array[] = $this->{$type[0].'Element'}($tag);
						}
						break;
				}
			}

			next($this->htmlParts);
		}

		return implode('', $array);
	}

	protected function blockPre()
	{
		$array = [];
		$blockpre = 0;

		while (list($tag, $close, $attr) = current($this->htmlParts))
		{
			if (!$tag) {
				if($attr) $array[] = $attr; // text
			} else {
				$md = $this->markdownable[$tag];
				$type = explode('/', $md['type']);

				switch ($close.$tag) {
					case '/pre':
						if(--$blockpre) break 1;
						$array[] = "\n```\n";
						break 2; // exit loop
					case 'pre':
						if(!$blockpre++) $array[] = "\n```\n";
						break;
					case 'code':
						if(@end($array) == "\n```\n"){
							// 코드 문법 강조를 위해 language 처리
							if(preg_match_all('/(\b(?:class))\s*=(?:\s*["\'])?(?(2)([^"\']*?)\2|([^"\']+))/is', $attr, $m2)) {
								if(($class = $m2[3][0]) && substr($class, 0, 9) == 'language-'){
									$array[key($array)] = "\n```".substr($class, 9)."\n";
								}
							}
						}
					case 'img':
						$array[] = $this->inlineImage();
						break;
					case 'a':
						$array[] = $this->inlineLink();
						break;
					//default: break;
				}
			}

			next($this->htmlParts);
		}

		return implode('', $array);
	}

	protected function blockQuote()
	{
		if(!@$__Prefix) {
			$__Prefix = function ($str) {
				if(!$str) return '';
				return implode("\n> ", explode("\n", $str));
			};
		}

		$array = [];
		$blockouote = 0;

		while (list($tag, $close, $attr) = current($this->htmlParts))
		{
			if (!$tag) $array[] = trim($attr, ' '); // text
			else {
				$md = $this->markdownable[$tag];
				$type = explode('/', $md['type']);

				switch ($close.$tag) {
					case '/blockquote':
						if(--$blockouote) break 1;
						break 2; // exit loop
					case 'blockquote':
						$blockouote++;
						break;
					default:
						if(method_exists($this, $type[0].'Element')){
							$array[] = trim($this->{$type[0].'Element'}($tag), ' ');
						}
						break;
				}
			}

			next($this->htmlParts);
		}

		return "\n". $__Prefix("\n". trim(implode('', $array))) ."\n>\n\n";
	}

	protected function blockList()
	{
		$array = [];
		$blocklist = 0;
		$listtype = '-';

		while (list($tag, $close, $attr) = current($this->htmlParts))
		{
			if (!$tag) $array[] = $this->rnl2br(trim($attr)); // text
			else {
				$md = $this->markdownable[$tag];
				$type = explode('/', $md['type']);

				switch ($close.$tag) {
					case '/ol': case '/ul': case '/dl':
						if(--$blocklist) break 1;
						break 2; // exit loop
					case '/li': case '/dt': case '/dd':
						break;
					case 'ol': case 'ul': case 'dl':
						if(!$blocklist++) $listtype = $tag == 'ol' ? 1 : '-';
						break;
					case 'li': case 'dt': case 'dd':
						$tmp = $listtype == '-' ? '- ' : ($listtype++) . '. ';
						$array[] = "\n".str_pad('', $blocklist - 1 + ($tag == 'dd' ? 1 : 0), ' ') . $tmp;
						break;
					default:
						if(method_exists($this, $type[0].'Element')){
							$array[] = $this->rnl2br(trim($this->{$type[0].'Element'}($tag)));
						}
						break;
				}
			}

			next($this->htmlParts);
		}

		return "\n\n" . implode('', $array) . "\n\n";
	}

	protected function blockTable()
	{
		if(!@$__Escape) {
			$__Escape = function ($str) {
				return str_replace('|', '&#124;', $str);
			};
		}

		$array = $span = [];
		$blocktable = $tr = 0; // 테이블 안에 테이블 제거

		while (list($tag, $close, $attr) = current($this->htmlParts))
		{
			if (!$tag) $array[] = $__Escape($attr); // text
			else {
				$md = $this->markdownable[$tag];
				$type = explode('/', $md['type']);

				switch ($close.$tag) {
					case '/caption':
						$array[] = "\n\n";
						break;
					case '/table':
						if(--$blocktable) break 1;
						break 2; // exit loop
					case '/tr':
						$tmp = '';
						if($tr == 1) {
							$tmp = "|\n";
							for($c=$span['count']; $c>0; $c--) $tmp .='| --- ';
						}
						$array[] = $tmp . "|\n";
						break;
					case '/th': case '/td':
						$array[] = ' ';
						break;
					case 'caption':
						break;
					case 'table':
						$blocktable++;
						$span['rowspan'] = [];
						break;
					case 'tr':
						$tr++;
						$span['count'] = 0; // 해더 입력을 위해
						$span['colspan'] = [];
						break;
					case 'th': case 'td':
						if((int)@$span['colspan'] > 0){ // colspan 처리
							$array[] = '|  ';
							$span['colspan']--;
						}
						if((int)@$span['rowspan'][$span['count']] > 0){ // rowspan 처리
							$array[] = '|  ';
							$span['rowspan'][$span['count']]--;
						}
						if (preg_match_all('/(\b(?:rowspan|colspan))\s*=(?:\s*["\'])?(?(2)([^"\']*?)\2|([^"\']+))/is', $attr, $m2)) {
							// rowspan,colspan 처리를 위해 값입력 -1
							foreach ($m2[1] as $m2k => $m2v) {
								$span[strtolower($m2v)][$span['count']] = (int)$m2[3][$m2k] - 1;
							}
							$span['colspan'] = (int)@$span['colspan'][$span['count']];
						}
						$span['count']++;
						$array[] = '| ';
						break;
					default:
						if(method_exists($this, $type[0].'Element')){
							$array[] = $__Escape($this->{$type[0].'Element'}($tag));
						}
						break;
				}
			}
			next($this->htmlParts);
		}

		// 줄바꿈 처리
		$array = preg_replace_callback('/(\| ([^\|]+)|\|[\r\n]+)/s',
			function ($m) {
				return @$m[2] ? '| '. $this->rnl2br(trim($m[2])) .' ' : "|\n";
			}
			, implode('', $array)
		);
		return "\n\n" . $array . "\n\n";
	}

	protected function blockHeader()
	{
		$array = [];
		$blockheader = 0;

		while (list($tag, $close, $attr) = current($this->htmlParts))
		{
			if (!$tag) $array[] = $attr; // text
			else {
				$md = $this->markdownable[$tag];
				$type = explode('/', $md['type']);

				switch ($close.$tag) {
					case '/h1': case '/h2': case '/h3': case '/h4': case '/h5': case '/h6':
						if(--$blockheader) break 1;
						break 2; // exit loop
					case 'h1': case 'h2': case 'h3': case 'h4': case 'h5': case 'h6':
						if(!$blockheader++) $array[] = str_pad('', substr($tag, 1), '#');
						break;
					default:
						if(method_exists($this, $type[0].'Element')){
							$array[] = $this->{$type[0].'Element'}($tag);
						}
						break;
				}
			}

			next($this->htmlParts);
		}

		return "\n".$this->rnl2br(implode('', $array))."\n";
	}

	protected function inlineElement($tag)
	{
		switch ($tag) {
			case 'input':
				return '`input:`';
			case 'img':
				return $this->inlineImage();
			case 'a':
				return $this->inlineLink();
			case 'code':
				return $this->inlineCode();
			case 'u':
				return '~';
			case 'i': case 'em':
				return '*';
			case 'b': case 'strong':
				return '**';
			case 's': case 'strike': case 'del':
				return '~~';
		}
		return '';
	}

	protected function blockElement($tag)
	{
		switch ($tag) {
			case 'pre':
				return $this->blockPre();
			case 'table':
				return $this->blockTable();
			case 'blockquote':
				return $this->blockQuote();
			case 'ol': case 'ul': case 'dl':
				return $this->blockList();
			case 'h1': case 'h2': case 'h3': case 'h4': case 'h5': case 'h6':
				return $this->blockHeader();
		}
		return '';
	}

	protected function breakElement($tag)
	{
		switch ($tag) {
			case 'hr':
			case 'br':
				return '<'.$tag.'>';
			case 'p':
			case 'div':
				return "\n";
		}
		return '';
	}
/*
	protected function getIndexByName($tag)
	{
		for ($i=count($this->htmlParts)-1; $i > -1; $i--) {
			if ($this->htmlParts[$i][0] == $tag) return $i;
		}
		return -1;
	}
*/

	protected function escapeMKDW($str){
		return preg_replace('/([\\\`\*\_\{\}\[\]\(\)\>\#\+\-\.\!])/m', '\\\\$1', $str);
	}

	public function html($html, $admin)
	{
		$pre = 0;
		$this->htmlParts = [];

		$html = preg_replace('#(<!--.*?-->|\r| )#s', '', $html);
		$html = preg_replace_callback('@(.*?)<(/?)([a-z]+[0-9]?)((?>"[^>"]*"|\'[^\']*\'|[^>])*?)(/?)>@is',
			function ($m) use(&$pre) {
				if (($tag = strtolower($m[3])) == 'pre') $m[2] == '/' ? $pre-- : $pre++;
				// pre 가 아니면 공백은 1개이상 불필요
				$m[1] = $pre ? $m[1] : preg_replace('/( )[ ]+/m', "$1", $m[1]);
				$this->htmlParts[] = ['', '', $this->escapeMKDW($m[1])];
				$this->htmlParts[] = [$tag, $m[2], $m[4]];
				return '';
			}
			, $html
		);

		$result = [];
		reset($this->htmlParts);

		while (list($tag, $close, $attr) = current($this->htmlParts))
		{
			if(!$tag) $result[] = $attr; // text
			else if($md = @$this->markdownable[$tag])
			{
				$type = explode('/', $md['type']);
				if(method_exists($this, $type[0].'Element')) {
					$result[] = $this->{$type[0].'Element'}($tag);
				} else if($type[0] == 'admin' && $admin) {
					$result[] = '<'.$close.$tag.$attr.'>';
				}
			}
			next($this->htmlParts);
		}

		$html = implode('', $result) . preg_replace('/[\r\n\t]+/', ' ', $html);
		debugPrint($html);
		return preg_replace('/(((> )[\r\n]+){2,}>\s$|(\n\n\n)[\n]+)/m', "$3$4", $html);
	}
}

/* End of file html2mkdw.php */
/* Location: ./md/html2mkdw.php */