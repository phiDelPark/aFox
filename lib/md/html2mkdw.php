<?php
/*!
 * aFox (https://github.com/phiDelPark/aFox)
 * Copyright 2016 afox, Inc.
 */

class HtmlToMkdw
{
	protected $markdownable = [
		'widget'	=> ['type'=>'admin','head'=>'','tail'=>''],
		'script'	=> ['type'=>'admin','head'=>'','tail'=>''],
		'style'		=> ['type'=>'admin','head'=>'','tail'=>''],
		'br'		=> ['type'=>'break','head'=>"<br>",'tail'=>''],
		'hr'		=> ['type'=>'break','head'=>"<hr>",'tail'=>''],
		'h1'		=> ['type'=>'block','head'=>"\n# ",'tail'=>"\n"],
		'h2'		=> ['type'=>'block','head'=>"\n## ",'tail'=>"\n"],
		'h3'		=> ['type'=>'block','head'=>"\n### ",'tail'=>"\n"],
		'h4'		=> ['type'=>'block','head'=>"\n#### ",'tail'=>"\n"],
		'h5'		=> ['type'=>'block','head'=>"\n##### ",'tail'=>"\n"],
		'h6'		=> ['type'=>'block','head'=>"\n###### ",'tail'=>"\n"],
		'p'			=> ['type'=>'block','head'=>"\n",'tail'=>"\n"],
		'div'		=> ['type'=>'block','head'=>'','tail'=>"\n"],
		'blockquote'=> ['type'=>'block','head'=>"> ",'tail'=>"\n\n"],
		'pre'		=> ['type'=>'block','head'=>"\n```\n",'tail'=>"\n```\n"],
		'ol'		=> ['type'=>'block','head'=>"\n",'tail'=>"\n\n"],
		'ul'		=> ['type'=>'block','head'=>"\n",'tail'=>"\n\n"],
		'dl'		=> ['type'=>'block','head'=>"\n",'tail'=>"\n\n"],
		'li'		=> ['type'=>'block','head'=>'- ','tail'=>"\n"],
		'dt'		=> ['type'=>'block','head'=>'- ','tail'=>"\n"],
		'dd'		=> ['type'=>'block','head'=>'- ','tail'=>"\n"],
		'table'		=> ['type'=>'block','head'=>"\n",'tail'=>"\n"],
		'caption'	=> ['type'=>'block','head'=>"",'tail'=>"\n\n"],
		'tr'		=> ['type'=>'block','head'=>"",'tail'=>"|\n"],
		'th'		=> ['type'=>'inline/block','head'=>'| ','tail'=>' '],
		'td'		=> ['type'=>'inline/block','head'=>'| ','tail'=>' '],
		'i'			=> ['type'=>'inline/text','head'=>'*','tail'=>'*'],
		'em'		=> ['type'=>'inline/text','head'=>'*','tail'=>'*'],
		'b'			=> ['type'=>'inline/text','head'=>'**','tail'=>'**'],
		'strong'	=> ['type'=>'inline/text','head'=>'**','tail'=>'**'],
		's'			=> ['type'=>'inline/text','head'=>'~~','tail'=>'~~'],
		'strike'	=> ['type'=>'inline/text','head'=>'~~','tail'=>'~~'],
		'del'		=> ['type'=>'inline/text','head'=>'~~','tail'=>'~~'],
		'u'			=> ['type'=>'inline/text','head'=>'<u>','tail'=>'</u>'],
		'code'		=> ['type'=>'inline/text','head'=>'`','tail'=>'`'],
		'input'		=> ['type'=>'inline','head'=>'`input:`','tail'=>''],
		'img'		=> ['type'=>'inline','head'=>'![','tail'=>']','attrs'=>['src'=>'','title'=>'','label'=>'','alt'=>'!IMAGE']],
		'a'			=> ['type'=>'inline/block','head'=>'[','tail'=>']','attrs'=>['href'=>'','title'=>'','label'=>'','alt'=>'!LINK']],
	];

	protected $htmlParts = [];

	protected function rnl2br($str, $br = '<br>')
	{
		return preg_replace('/[\r\n]+/', $br, $str);
	}

	protected function parts_rtrim($index)
	{
		for ($i=$index, $n=count($this->htmlParts); $i < $n; $i++) {
			if($this->htmlParts[$i][0]) break;
			$this->htmlParts[$i][2] = '';
		}
	}

	protected function inlineImage($part)
	{
		$a = $this->markdownable['img']['attrs'];
		if (preg_match_all('/(\b(?:'.implode('|', array_keys($a)).'))\s*=(?:\s*["\'])?(?(2)([^"\']*?)\2|([^"\']+))/is', $part[2], $m2)) {
			foreach ($m2[1] as $m2k => $m2v) $a[strtolower($m2v)] = $m2[3][$m2k];
		}
		$r = '';
		if ($srl = $a['src']) {
			$r = sprintf('![%s](%s%s)', $a['alt'] ? $a['alt'] : '!IMAGE', $srl, $a['title']?' "'.$a['title'].'"':'');
		}
		return $r;
	}

	protected function inlineLink($i, &$return)
	{
		$inlinelink = 0;
		$a = $this->markdownable['a']['attrs'];

		for ($i=$i, $n=count($this->htmlParts); $i < $n; $i++) {
			$part = $this->htmlParts[$i];
			$close = @$part[1] == '/' ? '/' : '';
			$tag = $part[0];

			if (!$tag) {
				 // text // 오류 방지를 위해 '[',']' 이스케이프
				$r = $this->rnl2br(str_replace(array('[',']'), array('&#91;','&#93;'), $part[2]));
			}
			else {
				$md = $this->markdownable[$tag];
				$r = $close ? $md['tail'] : $r = $md['head'];

				switch ($close.$tag) {
					case '/a':
						if(--$inlinelink) {
							$r = '';
							break 1;
						}
						if ($srl = $a['href']) {
							if(@end($return) == '['){
								$r = ($a['alt'] ? $a['alt'] : '!LINK') . $r;
							}
							$r .= sprintf('(%s%s)', $srl, $a['title']?' "'.$a['title'].'"':'');
						}
						$return[] = $r;
						break 2; // exit loop
					case 'a':
						if(!$inlinelink){ // 처음 한번만
							if (preg_match_all('/(\b(?:'.implode('|', array_keys($a)).'))\s*=(?:\s*["\'])?(?(2)([^"\']*?)\2|([^"\']+))/is', $part[2], $m2)) {
								foreach ($m2[1] as $m2k => $m2v) $a[strtolower($m2v)] = $m2[3][$m2k];
							}
						} else {
							$r = '';
						}
						$inlinelink++;
						break;
					case 'img':
						$r = $this->inlineImage($part);
						break;
					default:
						$r = $this->rnl2br($r);
						break;
				}
			}

			if($r) $return[] = $r;
		}

		return $i;
	}

	protected function inlineCode($i, &$return)
	{
		$inlinecode = 0;

		for ($i=$i, $n=count($this->htmlParts); $i < $n; $i++) {
			$part = $this->htmlParts[$i];
			$close = @$part[1] == '/' ? '/' : '';
			$tag = $part[0];

			if (!$tag) {
				$r = $part[2]; // text
			}
			else {
				$md = $this->markdownable[$tag];
				$isblock = strpos($md['type'], 'block') !== false;
				$r = $close ? $md['tail'] : $r = $md['head'];
				if($isblock) $r = $close ? preg_replace('/[^\n]/', '', $r) : '';

				switch ($close.$tag) {
					case '/code':
						if(--$inlinecode) {
							$r = '';
							break 1;
						}
						if(@end($return) == "`"){ // 빈값이면 제거
							$r = $return[key($return)] = '';
						} else if(@end($return) == "\n"){
							$return[key($return)] = '';
						}
						$return[] = $r;
						break 2; // exit loop
					case 'code':
						if($inlinecode++) $r = '';
						break;
					case 'a':
						if(@end($return) == "`"){ // 빈값이면 제거
							$return[key($return)] = '';
						} else {
							$return[] = '`';
						}
						$i = $this->inlineLink($i, $return);
						$return[] = '`';
						continue 2; // inlineLink 에서 처리 하니 넘김
						break;
					case 'img':
						$r = '`'.$this->inlineImage($part).'`';
						break;
					//default: break;
				}
			}

			if($r) $return[] = $r;
		}

		return $i;
	}

	protected function inlineElement($tag, $i, &$return)
	{
		switch ($tag) {
			case 'code':
				$i = $this->inlineCode($i, $return);
				break;
			case 'a':
				$i = $this->inlineLink($i, $return);
				break;
			case 'img':
				$return[] = $this->inlineImage($this->htmlParts[$i]);
				break;
		}
		return $i;
	}

	protected function blockPre($i, &$return)
	{
		$blockpre = 0;

		for ($i=$i, $n=count($this->htmlParts); $i < $n; $i++) {
			$part = $this->htmlParts[$i];
			$close = @$part[1] == '/' ? '/' : '';
			$tag = $part[0];

			if (!$tag) $r = $part[2]; // text
			else {
				$md = $this->markdownable[$tag];
				$r = $close ? $md['tail'] : $r = $md['head'];

				switch ($close.$tag) {
					case '/pre':
						if(--$blockpre) {
							$r = '';
							break 1;
						}
						$return[] = $r;
						break 2; // exit loop
					case 'pre':
						if($blockpre++) $r = '';
						break;
					case 'code':
						if(@end($return) == "\n```\n"){
							// 코드 문법 강조를 위해 language 처리
							if(preg_match_all('/(\b(?:class))\s*=(?:\s*["\'])?(?(2)([^"\']*?)\2|([^"\']+))/is', $part[2], $m2)) {
								if(($class = $m2[3][0]) && substr($class, 0, 9) == 'language-'){
									$return[key($return)] = "\n```".substr($class, 9)."\n";
								}
							}
						}
						$array = [];
						$i = $this->inlineCode($i, $array);
						if(@$array[0] == '`') @$array[0] = '';
						if(@end($array) == '`') $array[key($array)] = '';
						$return[] = implode('', $array);
						continue 2; // inlineCode 에서 처리 하니 넘김
						break;
					case 'a':
					case 'img':
						$i = $this->inlineElement($tag, $i, $return);
						continue 2; // inlineElement 에서 처리 하니 넘김
						break;
					//default: break;
				}
			}

			if($r) $return[] = $r;
		}

		return $i;
	}

	protected function blockQuote($i, &$return)
	{
		if(!@$__Prefix) {
			$__Prefix = function ($str) {
				if(!$str) return '';
				$head = "\n> ";
				return implode($head, explode("\n", $str));
			};
		}

		$blockouote = 0;

		for ($i=$i, $n=count($this->htmlParts); $i < $n; $i++) {
			$part = $this->htmlParts[$i];
			$close = @$part[1] == '/' ? '/' : '';
			$tag = $part[0];

			if (!$tag) $r = $part[2]; // text
			else {
				$md = $this->markdownable[$tag];
				$r = $close ? $md['tail'] : $r = $md['head'];

				switch ($close.$tag) {
					case '/blockquote':
						if(--$blockouote) {
							$r = '';
							break 1;
						}
						$return[] = $r;
						break 2; // exit loop
					case 'blockquote':
						if($blockouote++) $r = '';
						break;
					case 'pre':
						$array = [];
						$i = $this->blockPre($i, $array);
						$return[] = $__Prefix(implode('', $array));
						continue 2; // blockPre 에서 처리 하니 넘김
						break;
					case 'a':
					case 'img':
					case 'code':
						$i = $this->inlineElement($tag, $i, $return);
						continue 2; // inlineElement 에서 처리 하니 넘김
						break;
					//default: break;
				}
			}

			if($r) $return[] = $__Prefix($r);
		}

		return $i;
	}

	protected function blockList($i, &$return)
	{
		$blocklist = 0;
		$listtype = $this->htmlParts[$i][0] == 'ol' ? 1 : '-';

		for ($i=$i, $n=count($this->htmlParts); $i < $n; $i++) {
			$part = $this->htmlParts[$i];
			$close = @$part[1] == '/' ? '/' : '';
			$tag = $part[0];

			if (!$tag) $r = $this->rnl2br($part[2]); // text
			else {
				$md = $this->markdownable[$tag];
				$r = $close ? $md['tail'] : $r = $md['head'];

				switch ($close.$tag) {
					case '/ol': case '/ul': case '/dl':
						if(--$blocklist) {
							$r = '';
							break 1;
						}
						$return[] = $r;
						break 2; // exit loop
					case '/li': case '/dt': case '/dd':
						if($blocklist > 1) $r = '';
						$this->parts_rtrim($i + 1);
						break;
					case 'ol': case 'ul': case 'dl':
						if($blocklist++) $r = '';
						break;
					case 'li': case 'dt': case 'dd':
						if($blocklist > 1){
							$r = '<br> &bull; ';
						} else {
							if($listtype != '-') {
								$r = $listtype++ . '. ';
							} else if($tag == 'dd') {
								$r = '&rsaquo; ';
							} else {
								$r = '- ';
							}
						}
						break;
					case 'a':
					case 'img':
					case 'code':
						$i = $this->inlineElement($tag, $i, $return);
						continue 2; // inlineElement 에서 처리 하니 넘김
						break;
					default:
						// li, dt, dd 바로 다음엔 줄 바꿈 안함
						if (preg_match('/^(- |[0-9]+\. |&rsaquo; |<br> &bull; )$/', end($return), $t)) {
							$r = ltrim($r);
						}
						$r = $this->rnl2br($r);
						break;
				}
			}

			if($r) $return[] = $r;
		}

		return $i;
	}

	protected function blockTable($i, &$return)
	{
		$blocktable = $tr = 0; // 테이블 안에 테이블 제거
		$span = [];

		for ($i=$i, $n=count($this->htmlParts); $i < $n; $i++) {
			$part = $this->htmlParts[$i];
			$close = @$part[1] == '/' ? '/' : '';
			$tag = $part[0];

			if (!$tag) {
				$r = $this->rnl2br($part[2]); // text
			} else {
				$md = $this->markdownable[$tag];
				$r = $close ? $md['tail'] : $r = $md['head'];

				switch ($close.$tag) {
					case '/table':
						if(--$blocktable) {
							$r = '';
							break 1;
						}
						$return[] = $r;
						break 2; // exit loop
					case '/tr':
						if($tr == 1){
							for ($c=$span['count']; $c > 0; $c--) {
								$r .= '| --- ';
							}
							$r .= "|\n";
						}
						//break;
					case '/th': case '/td':
						$this->parts_rtrim($i + 1);
						break;
					case '/caption':
						if($tr) $r = $this->rnl2br($r);
						$this->parts_rtrim($i + 1);
						break;
					case 'table':
						if($blocktable++) $r = '';
						$span['rowspan'] = [];
						$this->parts_rtrim($i + 1);
						break;
					case 'tr':
						$tr++;
						$span['count'] = 0; // 해더 입력을 위해
						$span['colspan'] = [];
						$this->parts_rtrim($i + 1);
						break;
					case 'th': case 'td':
						if((int)@$span['colspan'] > 0){ // colspan 처리
							$r = '|  ' . $r;
							$span['colspan']--;
						}
						if((int)@$span['rowspan'][$span['count']] > 0){ // rowspan 처리
							$r = '|  ' . $r;
							$span['rowspan'][$span['count']]--;
						}
						if ($part[2] && preg_match_all('/(\b(?:rowspan|colspan))\s*=(?:\s*["\'])?(?(2)([^"\']*?)\2|([^"\']+))/is', $part[2], $m2)) {
							// rowspan,colspan 처리를 위해 값입력 -1
							foreach ($m2[1] as $m2k => $m2v) {
								$span[strtolower($m2v)][$span['count']] = (int)$m2[3][$m2k] - 1;
							}
							$span['colspan'] = (int)@$span['colspan'][$span['count']];
						}
						$span['count']++;
						break;
					case 'a':
					case 'img':
					case 'code':
						$i = $this->inlineElement($tag, $i, $return);
						continue 2; // inlineElement 에서 처리 하니 넘김
						break;
					default:
						$r = $this->rnl2br($r);
						break;
				}
			}

			if($r) $return[] = $r;
		}

		return $i;
	}

	protected function blockElement($tag, $i, &$return)
	{
		switch ($tag) {
			case 'table':
				$i = $this->blockTable($i, $return);
				break;
			case 'blockquote':
				$i = $this->blockQuote($i, $return);
				break;
			case 'ol': case 'ul': case 'dl':
				$i = $this->blockList($i, $return);
				break;
		}
		return $i;
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
	public function html($html, $admin)
	{
		$ltrim = false;
		$this->htmlParts = [];

		$html = preg_replace('#(<!--.*?-->|\r)#s', '', $html);
		$html = preg_replace('/[\r\n]*<(hr|br)[^>]*>[\r\n]*/i', '<\1>', $html);
		$html = preg_replace_callback('@(.*?)<(/?)([a-z]+[0-9]?)((?>"[^"]*"|\'[^\']*\'|[^>])*?)(/?)>@is',
			function ($m) use(&$ltrim) {
				$this->htmlParts[] = ['', '', $ltrim ? ltrim($m[1],"\r\n") : $m[1]];
				$ltrim = false;
				if (($tag = strtolower($m[3])) && ($md = @$this->markdownable[$tag])) {
					$isblock = strpos($md['type'], 'block') !== false;
					$isinline = strpos($md['type'], 'inline') !== false;
					if ($isblock) { // 블록 안쪽 (끝, 시작) 줄 바꿈 지움
						if($m[2]=='/') {
							end($this->htmlParts);
							if (($t = key($this->htmlParts)) > -1) {
								$this->htmlParts[$t][2] = rtrim($this->htmlParts[$t][2], "\r\n");
							}
							//if(!$isinline) $ltrim = true; // 블록 바깥 다음 줄 바꿈 지움 (자동 입력)
						} else $ltrim = true;
					}
					$this->htmlParts[] = [$tag, $m[2], $m[4]];
				}
				return '';
			}
			, $html
		);

		$return = [];
		for ($i=0, $n=count($this->htmlParts); $i < $n; $i++) {
			$part = $this->htmlParts[$i];
			$tag = $part[0];
			$close = @$part[1] == '/' ? '/' : '';

			if (!$tag) $r = $part[2]; // text
			else if ($md = @$this->markdownable[$tag]) {

				if ($md['type'] == 'admin' && $admin) {
					$return[] = '<'.$part[1].$tag.$part[2].'>';
					continue;
				}

				$r = $close ? $md['tail'] : $r = $md['head'];
				//$isblock = strpos($md['type'], 'block') !== false;
				//$isinline = strpos($md['type'], 'inline') !== false;

				switch ($close.$tag) {
					case 'pre':
						$i = $this->blockPre($i, $return);
						continue 2; // blockPre 에서 처리 하니 넘김
						break;
					case 'table':
					case 'blockquote':
					case 'ol': case 'ul': case 'dl':
						$i = $this->blockElement($tag, $i, $return);
						continue 2; // blockElement 에서 처리 하니 넘김
						break;
					case 'a':
					case 'img':
					case 'code':
						$i = $this->inlineElement($tag, $i, $return);
						continue 2; // inlineElement 에서 처리 하니 넘김
						break;
					//default: break;
				}
			}

			if($r) $return[] = $r;
		}

		$html = implode('', $return) . $html;
		//$html = preg_replace('/[\n]{3,}/', "\n\n", $html);
		//debugPrint($html);
		return preg_replace('/^(((> )[\r\n]+){2,}>\s$|(\n\n)[\n]+)/m', "\\3\\4", $html);
	}
}

/* End of file html2mkdw.php */
/* Location: ./md/html2mkdw.php */