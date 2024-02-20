<?php
/*!
 * aFox (https://github.com/phiDelPark/aFox)
 * Copyright 2016 afox, Inc.
 */

class HtmlToMkdw
{
	protected $markdownable = [
		//'br'		=> ['type'=>'break','head'=>"\n"],
		'hr'		=> ['type'=>'break','head'=>"\n___\n"],
		'widget'	=> ['type'=>'admin','head'=>'','tail'=>''],
		'script'	=> ['type'=>'admin','head'=>'','tail'=>''],
		'style'		=> ['type'=>'admin','head'=>'','tail'=>''],
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
		'li'		=> ['type'=>'block','head'=>'- ','tail'=>"\n"],
		'table'		=> ['type'=>'block','head'=>"\n",'tail'=>"\n"],
		'caption'	=> ['type'=>'block','head'=>"",'tail'=>"\n\n"],
		'tr'		=> ['type'=>'block','head'=>"",'tail'=>"|\n"],
		'th'		=> ['type'=>'inline','head'=>'| ','tail'=>' '],
		'td'		=> ['type'=>'inline','head'=>'| ','tail'=>' '],
		'i'			=> ['type'=>'inline','head'=>'*','tail'=>'*'],
		'em'		=> ['type'=>'inline','head'=>'*','tail'=>'*'],
		'b'			=> ['type'=>'inline','head'=>'**','tail'=>'**'],
		'strong'	=> ['type'=>'inline','head'=>'**','tail'=>'**'],
		's'			=> ['type'=>'inline','head'=>'~~','tail'=>'~~'],
		'strike'	=> ['type'=>'inline','head'=>'~~','tail'=>'~~'],
		'del'		=> ['type'=>'inline','head'=>'~~','tail'=>'~~'],
		'u'			=> ['type'=>'inline','head'=>'__','tail'=>'__'],
		'u'			=> ['type'=>'inline','head'=>'__','tail'=>'__'],
		'code'		=> ['type'=>'inline','head'=>'`','tail'=>'`'],
		'input'		=> ['type'=>'inline','head'=>'`input:`'],
		'img'		=> ['type'=>'inline','head'=>'![','tail'=>']','attrs'=>['src'=>'','title'=>'','label'=>'','alt'=>'!IMAGE']],
		'a'			=> ['type'=>'inlineblock','head'=>'[','tail'=>']','attrs'=>['href'=>'','title'=>'','label'=>'','alt'=>'!LINK']],
	];

	protected function rnl2br($str, $br = '<br />')
	{
		return preg_replace('/[\r\n]+/', $br, $str);
	}

	protected function inlineImage($part)
	{
		$inlineImage = 0;
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

	protected function inlineLink($i, $parts, &$return)
	{
		$inlineLink = 0;
		$a = $this->markdownable['a']['attrs'];

		for ($i=$i, $n=count($parts); $i < $n; $i++) {
			$part = $parts[$i];
			$close = @$part[1] == '/';
			$tag = $part[0];

			if (!$tag) {
				 // text // 오류 방지를 위해 '[',']' 이스케이프
				$r = $this->rnl2br(str_replace(array('[',']'), array('&#91;','&#93;'), $part[2]));
			}
			else {
				$md = $this->markdownable[$tag];

				if($close){
					$r = $md['tail'];

					switch ($tag) {
						case 'a':
							if(--$inlineLink) {
								$r = '';
								break 1;
							}
							if ($srl = $a['href']) {
								if(($t = count($return) - 1) > -1 && $return[$t] == '['){
									$r = ($a['alt'] ? $a['alt'] : '!LINK') . $r;
								}
								$r .= sprintf('(%s%s)', $srl, $a['title']?' "'.$a['title'].'"':'');
							}
							$return[] = $r;
							break 2; // exit loop
						default:
							$r = $this->rnl2br($r);
							break;
					}

				} else {
					$r = $md['head'];

					switch ($tag) {
						case 'a':
							if(!$inlineLink){ // 처음 한번만
								if (preg_match_all('/(\b(?:'.implode('|', array_keys($a)).'))\s*=(?:\s*["\'])?(?(2)([^"\']*?)\2|([^"\']+))/is', $part[2], $m2)) {
									foreach ($m2[1] as $m2k => $m2v) $a[strtolower($m2v)] = $m2[3][$m2k];
								}
							} else {
								$r = '';
							}
							$inlineLink++;
							break;
						case 'img':
							$r = $this->inlineImage($part);
							break;
						default:
							$r = $this->rnl2br($r);
							break;
					}
				}
			}

			$return[] = $r;
		}

		return $i;
	}

	protected function blockPre($i, $parts, &$return)
	{
		$blockpre = $inlinecode = 0;

		for ($i=$i, $n=count($parts); $i < $n; $i++) {
			$part = $parts[$i];
			$close = @$part[1] == '/';
			$tag = $part[0];

			if (!$tag) $r = $part[2]; // text
			else {
				$md = $this->markdownable[$tag];

				if($close){
					$r = $md['tail'];

					switch ($tag) {
						case 'pre':
							if(--$blockpre) {
								$r = '';
								break 1;
							}
							$return[] = $r;
							break 2; // exit loop
						case 'code':
							if(($inlinecode -= 2) > -1) break;
							$r = '';
							break;
						//default: break;
					}

				} else {
					$r = $md['head'];

					switch ($tag) {
						case 'pre':
							if($blockpre) $r = '';
							$blockpre++;
							break;
						case 'code':
							if(($t = count($return) - 1) > -1 && $return[$t] == "\n```\n"){
								$r = '';
								$inlinecode++;
								// 코드 문법 강조를 위해 language 처리
								if(preg_match_all('/(\b(?:class))\s*=(?:\s*["\'])?(?(2)([^"\']*?)\2|([^"\']+))/is', $part[2], $m2)) {
									if(($class = $m2[3][0]) && substr($class, 0, 9) == 'language-'){
										$return[$t] = str_replace('```', '```'.substr($class, 9), $return[$t]);
									}
								}
							} else {
								$inlinecode += 2;
							}
							break;
						case 'a':
							$i = $this->inlineLink($i, $parts, $return);
							continue 2; // inlineLink 에서 처리 하니 넘김
							break;
						case 'img':
							$r = $this->inlineImage($part);
							break;
						//default: break;
					}
				}
			}

			$return[] = $r;
		}

		return $i;
	}

	function blockQuotePrefix($str){
		if(!$str) return '';
		$head = "\n> ";
		return implode($head, explode("\n", $str));
	}

	protected function blockQuote($i, $parts, &$return)
	{

		$blockouote = 0;

		for ($i=$i, $n=count($parts); $i < $n; $i++) {
			$part = $parts[$i];
			$close = @$part[1] == '/';
			$tag = $part[0];

			if (!$tag) $r = $part[2]; // text
			else {
				$md = $this->markdownable[$tag];

				if($close){
					$r = $md['tail'];

					switch ($tag) {
						case 'blockquote':
							if(--$blockouote) {
								$r = '';
								break 1;
							}
							$return[] = $r;
							break 2; // exit loop
						//default: break;
					}

				} else {
					$r = $md['head'];

					switch ($tag) {
						case 'blockquote':
							if($blockouote) $r = '';
							$blockouote++;
							break;
						case 'pre':
							$array = [];
							$i = $this->blockPre($i, $parts, $array);
							$return[] = $this->blockQuotePrefix(implode('', $array));
							continue 2; // blockPre 에서 처리 하니 넘김
							break;
						case 'a':
							$i = $this->inlineLink($i, $parts, $return);
							continue 2; // inlineLink 에서 처리 하니 넘김
							break;
						case 'img':
							$r = $this->inlineImage($part);
							break;
						//default: break;
					}
				}
			}

			$return[] = $this->blockQuotePrefix($r);
		}

		return $i;
	}

	protected function blockList($i, $parts, &$return)
	{
		$blocklist = 0;
		$listtype = $parts[$i][0] == 'ol' ? 1 : '-';

		for ($i=$i, $n=count($parts); $i < $n; $i++) {
			$part = $parts[$i];
			$close = @$part[1] == '/';
			$tag = $part[0];

			if (!$tag) $r = $this->rnl2br($part[2]); // text
			else {
				$md = $this->markdownable[$tag];

				if($close){
					$r = $md['tail'];

					switch ($tag) {
						case 'ol':
						case 'ul':
							if(--$blocklist) {
								$r = '';
								break 1;
							}
							$return[] = $r;
							break 2; // exit loop
						default:
							$r = $this->rnl2br($r);
							break;
					}

				} else {
					$r = $md['head'];

					switch ($tag) {
						case 'ol':
						case 'ul':
							if($blocklist) $r = '';
							$blocklist++;
							break;
						case 'li':
							$r = $listtype . ' ';
							if($listtype != '-') $listtype++;
							break;
						case 'a':
							$i = $this->inlineLink($i, $parts, $return);
							continue 2; // inlineLink 에서 처리 하니 넘김
							break;
						case 'img':
							$r = $this->inlineImage($part);
							break;
						default:
							$r = $this->rnl2br($r);
							break;
					}
				}
			}

			$return[] = $r;
		}

		return $i;
	}

	protected function blockTable($i, $parts, &$return)
	{
		$blocktable = $tr = 0; // 테이블 안에 테이블 제거
		$span = [];

		for ($i=$i, $n=count($parts); $i < $n; $i++) {
			$part = $parts[$i];
			$close = @$part[1] == '/';
			$tag = $part[0];

			if (!$tag) $r = $this->rnl2br(trim($part[2])); // text
			else {
				$md = $this->markdownable[$tag];

				if($close){
					$r = $md['tail'];

					switch ($tag) {
						case 'table':
							if(--$blocktable) {
								$r = '';
								break 1;
							}
							$return[] = $r;
							break 2; // exit loop
						case 'tr':
							if($tr == 1){
								for ($c=$span['count']; $c > 0; $c--) {
									$r .= '| --- ';
								}
								$r .= "|\n";
							}
							break;
						default:
							$r = $this->rnl2br($r);
							break;
					}

				} else {
					$r = $md['head'];

					switch ($tag) {
						case 'table':
							if($blocktable) $r = '';
							$blocktable++;
							$span['rowspan'] = [];
							break;
						case 'tr':
							$tr++;
							$span['count'] = 0; // 해더 입력을 위해
							$span['colspan'] = [];
							break;
						case 'th':
						case 'td':
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
							$i = $this->inlineLink($i, $parts, $return);
							continue 2; // inlineLink 에서 처리 하니 넘김
							break;
						case 'img':
							$r = $this->inlineImage($part);
							break;
						default:
							$r = $this->rnl2br($r);
							break;
					}
				}
			}

			$return[] = $r;
		}

		return $i;
	}

	public function html($html, $admin)
	{
		$html = preg_replace('#(<!--.*?-->|\r)#s', '', $html);

		$parts = [];
		$mdable = $this->markdownable;

		$ttttmp = $html;
		$html = preg_replace('/<br[^>]*>[\r\n]*/i', "\n", $html);

		$html = preg_replace_callback('@(.*?)<(/?)([a-z]+[0-9]?)((?>"[^"]*"|\'[^\']*\'|[^>])*?)(/?)>@is',
			function ($m) use ($mdable, &$parts) {
				if($t = $m[1]) $parts[] = ['', '', $t];
				if (($tag = strtolower($m[3])) && @$mdable[$tag]) {
					$parts[] = [$tag, $m[2], $m[4]];
				}
				return '';
			}
			, $html
		);

		$return = [];
		for ($i=0, $n=count($parts); $i < $n; $i++) {
			$part = $parts[$i];
			$tag = $part[0];
			$close = @$part[1] == '/';

			if (!$tag) $r = $part[2]; // text
			else if ($md = @$mdable[$tag]) {
				if ($md['type'] == 'admin' && $admin) {
					$return[] = '<'.$part[1].$tag.$part[2].'>';
					continue;
				}
				//$isblock = strpos($md['type'], 'block') !== false;
				//$isinline = strpos($md['type'], 'inline') !== false;

				if($close){
					$r = $md['tail'];

				} else {
					$r = $md['head'];

					switch ($tag) {
						case 'table':
							$i = $this->blockTable($i, $parts, $return);
							continue 2; // blockTable 에서 처리 하니 넘김
							break;
						case 'blockquote':
							$i = $this->blockQuote($i, $parts, $return);
							continue 2; // blockQuote 에서 처리 하니 넘김
							break;
						case 'pre':
							$i = $this->blockPre($i, $parts, $return);
							continue 2; // blockPre 에서 처리 하니 넘김
							break;
						case 'a':
							$i = $this->inlineLink($i, $parts, $return);
							continue 2; // inlineLink 에서 처리 하니 넘김
							break;
						case 'img':
							$r = $this->inlineImage($part);
							break;
						//default: break;
					}
				}
			}

			$return[] = $r;
		}

		$html = implode('', $return);
		//$html = preg_replace('/[\n]{3,}/', "\n\n", $html);
		debugPrint($html);
		return preg_replace('/^(((> )[\r\n]+){2,}>\s$|(\n\n)[\n]+)/m', "\\3\\4", $html);
	}
}

/* End of file html2mkdw.php */
/* Location: ./md/html2mkdw.php */