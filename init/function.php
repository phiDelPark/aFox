<?php
if(!defined('__AFOX__')) exit();

	require_once _AF_LIBS_PATH_ . 'parsedown/Parsedown.php';
	define('ENFORCE_SSL', 1);
	define('RELEASE_SSL', 2);
	define('FOLLOW_REQUEST_SSL', 0);

	function getUrlQuery() {
		$n = func_num_args();
		$a = func_get_args();
		$p = [];
		preg_replace_callback('/[?&]+([^=&]+)=([^&]*)/',
			function($m)use(&$p) {
				$p[$m[1]] = $m[2];
				return '';
			},
		$a[0]);
		return empty($a[1]) ? $p : $p[$a[1]];
	}

	function setUrlQuery() {
		$n = func_num_args();
		$a = func_get_args();
		$url = $a[0];
		$_empty = $a[1] == '';

		$a = array_slice($a, $_empty ? 2 : 1);
		$q = $_empty ? [] : getUrlQuery($url);
		$n = count($a);

		if(is_array($a[0])) {
			foreach ($a[0] as $k => $v) {$q[$k] = $v;}
		} else {
			for($i=0; $i<$n; $i+=2) {$q[$a[$i]] = $a[$i+1];}
		}

		$r = '';
		foreach ($q as $k => $v) {if(isset($v) && $v!='') $r.=$k.'='.$v.'&';}

		$pos = strpos($url, '?');
		$url = ($pos !== false) ? substr($url, 0, $pos) : $url;
		return $url . ($n == 0 ? $r : substr('?'.$r, 0, -1));
	}

	function getQuery($val) {
		return getUrlQuery(getUrl(), $val);
	}

	function setQuery() {
		$a = array_merge([getUrl()], func_get_args());
		$u = call_user_func_array('setUrlQuery', $a);
		$p = strpos($u, '?');
		$q = ($p !== false) ? substr($u, $p+1) : '';
		return $_SERVER["QUERY_STRING"] = $q;
	}

	// XE getRequestUri 참고 https://www.xpressengine.com/
	function getRequestUri($ssl_mode = FOLLOW_REQUEST_SSL) {
		static $url = [];

		if(!isset($_SERVER['SERVER_PROTOCOL'])) return; // Check HTTP Request

		if(_AF_USE_SSL_ === ENFORCE_SSL) $ssl_mode = ENFORCE_SSL; // always

		$domain = _AF_DOMAIN_ ? _AF_DOMAIN_ : $_SERVER['HTTP_HOST'];
		$domain_key = md5($domain);

		if(isset($url[$ssl_mode][$domain_key])) return $url[$ssl_mode][$domain_key];

		$current_use_ssl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on');

		switch($ssl_mode) {
			case FOLLOW_REQUEST_SSL: $use_ssl = $current_use_ssl; break;
			case ENFORCE_SSL: $use_ssl = 1; break;
			case RELEASE_SSL: $use_ssl = 0; break;
		}

		$target_url = 'http://' . $domain . getScriptPath();
		if(substr_compare($target_url, '/', -1) !== 0) $target_url.= '/';

		$url_info = parse_url($target_url);
		if($current_use_ssl != $use_ssl) unset($url_info['port']);

		$cfg_port = $use_ssl ? _AF_HTTPS_PORT_ : _AF_HTTP_PORT_;
		$def_port = $use_ssl ? 443 : 80;

		if($cfg_port != $def_port) {
			$url_info['port'] = $cfg_port;
		} elseif(isset($url_info['port']) && $url_info['port'] == $def_port) {
			unset($url_info['port']);
		}

		$url[$ssl_mode][$domain_key] = sprintf('%s://%s%s%s', $use_ssl ? 'https' : $url_info['scheme'], $url_info['host'], empty($url_info['port']) ? '' : ':' . $url_info['port'], $url_info['path']);

		return $url[$ssl_mode][$domain_key];
	}

	function getRequestMethod() {
		if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
			$type = strpos($_SERVER['HTTP_ACCEPT'], 'json') ? 'JSON' : 'XML';
		} else {
			$type = $_SERVER['REQUEST_METHOD'];
		}
		return $type;
	}

	function getScriptPath() {
		static $url = null;
		if($url == null) $url = preg_replace('/index.php$/i', '', str_replace('\\', '/', $_SERVER['SCRIPT_NAME']));
		return $url;
	}

	function getUrl() {
		if(_AF_USE_SSL_ === ENFORCE_SSL || (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')) { // If using SSL always
			$uri = getRequestUri(ENFORCE_SSL);
		} elseif(_AF_USE_SSL_ === RELEASE_SSL) { // optional SSL use
			$uri = getRequestUri(__MODULE__ == 'admin' || __MODULE__ == 'member' ? ENFORCE_SSL : RELEASE_SSL);
		} else { // no SSL
			$uri = _AF_DOMAIN_ ? getRequestUri(FOLLOW_REQUEST_SSL) : getScriptPath();
		}
		$n = func_num_args();
		$a = func_get_args();
		if($n == 1 && $a[0] == '') return $uri;
		$url = $uri . ($_SERVER["QUERY_STRING"] ? '?' . $_SERVER["QUERY_STRING"] : '');
		return $n > 0 ? call_user_func_array('setUrlQuery', array_merge([$url], $a)) : $url;
	}

	// 사용법: getLang(key), getLang(key,isescape), getLang(key,[sprintf],isescape)
	// 이스케이프시 홑따옴표는 안되니 필요하면 escapeHtml 사용
	// 홑따옴표 이스케이프시 escapeHtml(getLang('msg',false),false,ENT_QUOTES)
	function getLang($key, $args1 = true, $args2 = true) {
		global $_LANG;
		if(empty($key)) return '';
		$lstr = strtolower($key);
		$result = isset($_LANG[$lstr]) ? $_LANG[$lstr] : $key;
		if(is_array($args1)) {
			$escape = $args2;
			$args = [$result];
			foreach ($args1 as $v) {
				$lstr = strtolower($v);
				$args[] = isset($_LANG[$lstr]) ? $_LANG[$lstr] : $v;
			}
			$result = call_user_func_array('sprintf', $args);
		} else {
			$escape = $args1;
		}
		return $escape ? nl2br(escapeHtml($result)) : $result;
	}

	function getSiteMenu($get = '') {
		static $menus = [];
		if(!isset($menus['header']) || !isset($menus['footer'])) {
			for ($i=0; $i < 2; $i++) {
				$out = DB::gets(_AF_MENU_TABLE_, ['mu_type'=>$i], ['mu_srl'=>'ASC'], function($r){
					$rset = [];
					while ($row = DB::assoc($r)) {
						if(preg_match('/^[a-zA-Z]+\w{2,}$/',$row['mu_link'])) {
							$row['md_id'] = $row['mu_link'];
							$row['mu_link'] = getUrl('','id',$row['md_id']);
						}
						$rset[] = $row;
					}
					return $rset;
				});
				$menus[$i == 0 ? 'header' : 'footer'] = $out;
			}
		}
		return empty($get) ? $menus : $menus[$get];
	}

	// 모듈 설정 가져오기
	function getModule($id, $get = '') {
		static $module_cfg = [];
		if(!isset($module_cfg[$id])) {
			$out = DB::get(_AF_MODULE_TABLE_, ['md_id'=>$id]);
			if(empty($out)) $out = set_error(getLang('error_founded'), 4201);
			$module_cfg[$id] = $out;
		}
		return empty($get) ? $module_cfg[$id] : $module_cfg[$id][$get];
	}

	function getMember($id, $get = '') {
		static $members = [];
		if(!isset($members[$id])) {
			$skey = is_numeric($id) ? 'mb_srl' : 'mb_id';
			$out = DB::get(_AF_MEMBER_TABLE_, [$skey => $id]);
			if(!empty($out['mb_srl'])) {
				$out['mb_icon'] = '';
				$_icon = $out['mb_srl'].'/profile_image.png';
				if(file_exists(_AF_MEMBER_DATA_.$_icon)) $out['mb_icon'] = _AF_URL_.'data/member/'.$_icon;
			}
			$members[$id] = $out;
		}
		return empty($get) ? $members[$id] : $members[$id][$get];
	}

	function getFileList($id, $target) {
		static $filelist = [];
		$key = $id.'_'.$target;
		if(!isset($filelist[$key])) {
			$out = DB::gets(_AF_FILE_TABLE_, ['md_id'=>$id,'mf_target'=>$target], 'mf_type');
			$filelist[$key] = $out;
		}
		return $filelist[$key];
	}

	function setDataListInfo($data, $total, $page, $count) {
		$result = [];
		$page = empty($page) ? 1 : $page;
		if($count>0){
			$cur_page = $page;
			$tal_page = ceil($total / $count);
			$result['current_page'] = $cur_page;
			$result['total_page'] = $tal_page;
			$cur_page--;
			$str_page = $cur_page - ($cur_page % 10);
			$end_page = ($tal_page > ($str_page + 10) ? $str_page + 10 : $tal_page);
			$result['start_page'] = ++$str_page;
			$result['end_page'] = $end_page;
		}
		$result['total_count'] = $total;
		$result['data'] = $data;
		return $result;
	}

	function setHistoryAction($act, $value, $allowdup = false, $callback = null) {
		global $_MEMBER;

		// 비회원은 기록안함
		if (empty($_MEMBER)) {
			if($callback != null) {
				$_r = $callback(['data'=>true,'mb_srl'=>0,'ipaddress'=>$_SERVER['REMOTE_ADDR']]);
				if(!empty($_r['error'])) return set_error($_r['message'], $_r['error']);
			}
			return true;
		}

		$uinfo = [];
		$uinfo['mb_srl'] = empty($_MEMBER) ? 0 : $_MEMBER['mb_srl'];
		$uinfo['ipaddress'] = $_SERVER['REMOTE_ADDR'];

		$pkey = ($uinfo['mb_srl'] > 0 ? 'mb_srl':'mb_ipaddress');
		$pval = ($uinfo['mb_srl'] > 0 ? $uinfo['mb_srl']:$uinfo['ipaddress']);

		DB::transaction();

		try {
			$uinfo['data'] = DB::get(_AF_HISTORY_TABLE_,
				[
					'hs_action'=>$act.'('.$value.')',
					$pkey=>$pval
				]
			);
			if($allowdup || empty($uinfo['data'])) {
				DB::insert(_AF_HISTORY_TABLE_,
					[
						'mb_srl'=>$uinfo['mb_srl'],
						'mb_ipaddress'=>$uinfo['ipaddress'],
						'hs_action'=>$act.'('.$value.')',
						'^hs_regdate'=>'NOW()'
					]
				);
			}
			if($callback != null) {
				$_r = $callback($uinfo);
				if(!empty($_r['error'])) throw new Exception($_r['message'], $_r['error']);
			}
		} catch (Exception $ex) {
			DB::rollback();
			return set_error($ex->getMessage(),$ex->getCode());
		}

		DB::commit();
		return true;
	}

	function setPoint($point, $mb_srl = 0) {
		if(empty($point)) return; // 필요 포인트가 없으면 리턴

		global $_MEMBER;
		if(empty($mb_srl) && !empty($_MEMBER)) $mb_srl = $_MEMBER['mb_srl'];

		$mb_srl = (int)$mb_srl;
		// 비회원인데 - 값이면 에러
		if(empty($mb_srl) && $point < 0) {
			return set_error(getLang('warning_shortage', ['point']), 3701);
		}

		if(empty($mb_srl)) return;

		$mb = DB::get(_AF_MEMBER_TABLE_, 'mb_point,mb_rank', ['mb_srl'=>$mb_srl]);
		if(empty($mb)) return set_error(getLang('error_request'),4303);

		$mb_rank = ord($mb['mb_rank']);
		// 115 초과시 에러... 115는 관리자. 109는 메니져
		if($mb_rank > 115) set_error(getLang('error_request'),4303);

		// 포인트 모자르면 에러
		if(($mb['mb_point'] + $point) < 0) {
			return set_error(getLang('warning_shortage', ['point']).' ('.($mb['mb_point']+$point).')', 3701);
		}

		$_setvals = ['^mb_point'=>'mb_point'.($point>0?'+':'').$point];

		// 99이하는 일반 회원, 포인트에 따라 계급 조정
		if($mb_rank < 100) {
			$_sum_point = $mb['mb_point'] + $point;
			$_rank = ($_sum_point > 250000) ? 50 : floor(sqrt(floor($_sum_point / 10) / 10));
			//최대 50 레벨 // 주의, 50레벨 이상은 일반 회원이 아님
			$_setvals['mb_rank'] = chr($_rank + 48);
		}

		DB::update(_AF_MEMBER_TABLE_,
			$_setvals,
			['mb_srl'=>$mb_srl]
		);

		// 현재 로그인 멤버와 같으면 만일을 대비 전역 변수 고침
		if(!DB::error() && !empty($_MEMBER) && $mb_srl === $_MEMBER['mb_srl']) {
			$_MEMBER['mb_point'] = $mb['mb_point'] + $point;
			if(isset($_setvals['mb_rank'])) $_MEMBER['mb_rank'] = $_setvals['mb_rank'];
		}
	}

	function sendNote($srl, $msg, $nick = '') {
		global $_MEMBER;
		$sender = empty($_MEMBER) ? 0 : $_MEMBER['mb_srl'];
		$nick = empty($_MEMBER) ? ($nick ? $nick : getLang('none')) : $_MEMBER['mb_nick'];
		if(empty($srl) || $srl === $sender) return false;
		DB::insert(_AF_NOTE_TABLE_, [
			'mb_srl'=>$srl,
			'nt_sender'=>$sender,
			'nt_sender_nick'=>$nick,
			'nt_content'=>xssClean($msg),
			'^nt_send_date'=>'NOW()'
		]);
	}

	function moveUpFile($file, $dest, $max_size = 0) {
		if($file['error'] === UPLOAD_ERR_OK) {
			// HTTP post로 전송된 것인지 체크합니다.
			if(!is_uploaded_file($file['tmp_name'])) return set_error(getLang('UPLOAD_ERR_CODE(-1)'),10489);

			if($file['size'] <= 0) {
				return set_error(getLang('UPLOAD_ERR_CODE(4)'),10404);
			} if ($max_size > 0 && $max_size < $file['size']) {
				return set_error(getLang('UPLOAD_ERR_CODE(2)'),10402);
			}
			// 이동 경로가 없으면 이동 안함, 오류 체크는함
			if(empty($dest)) return true;
			// 폴더 없으면 만듬
			$dir = dirname($dest);
			if(!is_dir($dir) && !mkdir($dir, _AF_DIR_PERMIT_, true)) {
				return set_error(getLang('UPLOAD_ERR_CODE(7)'),10407);
			}
			// 파일이 있으면 지움
			if(file_exists($dest)) {
				if(!unlinkFile($dest)) return set_error(getLang('UPLOAD_ERR_CODE(7)'),10407);
			}
			if (move_uploaded_file($file['tmp_name'], $dest)) {
				@chmod($dest, _AF_FILE_PERMIT_);
			} else {
				return set_error(getLang('UPLOAD_ERR_CODE(4)'),10404);
			}
		} else {
			return set_error(getLang('UPLOAD_ERR_CODE('.$file['error'].')'),10400+$file['error']);
		}
	}

	function unlinkFile($file) {
		@chmod($file, 0707);
		if(!@unlink($file)) {
			@chmod($file, _AF_FILE_PERMIT_);
			return false;
		} else {
			return true;
		}
	}

	function unlinkDir($dir) {
		@chmod($dir, 0707);
		if(!@rmdir($dir)) {
			@chmod($dir, _AF_DIR_PERMIT_);
			return false;
		} else {
			return true;
		}
	}

	function unlinkAll($dir, $subdir = true) {
		// 폴더가 없어도 성공으로 간주
		$ret = true;
		if(is_dir($dir)){
			$handle = @opendir($dir); // 절대경로
			while ($file = readdir($handle)) {
				if($file != '.' && $file != '..') {
					// 하위 폴더이면...
					if($subdir && is_dir($dir.$file.'/')) {
						unlinkAll($dir.$file.'/', $subdir);
					} else {
						unlinkFile($dir.$file);
					}
				}
			}
			@closedir($handle);
			$ret = unlinkDir($dir);
		}
		return $ret;
	}

	function isAdmin() {
		global $_MEMBER;
		return (!empty($_MEMBER['mb_srl']) && $_MEMBER['mb_rank'] == 's');
	}

	function isManager($md_id) {
		global $_MEMBER;
		static $is_manager = [];
		if(empty($_MEMBER['mb_srl'])) return false;
		if($_MEMBER['mb_rank'] == 's' || $_MEMBER['mb_rank'] == 'm') return true;
		if(empty($md_id)) return false;
		if(!isset($is_manager[$md_id])) {
			$module = getModule($md_id);
			if(!empty($module['error'])) return false;
			$is_manager[$md_id] = $module['md_manager'];
		}
		return !empty($is_manager[$md_id]) && $is_manager[$md_id] == $_MEMBER['mb_srl'];
	}

	function getGrant($chk, $md_id) {
		if(empty($md_id) || empty($chk)) return '';
		static $is_grants = [];
		$key = $md_id.'_'.$chk;
		if($md_id == '_AFOXtRASH_') {
			$is_grants[$key] = 'm'; // 휴지통은 메니져 이상
		} else if(!isset($is_grants[$key])) {
			$module = getModule($md_id);
			if(!empty($module['error'])) return '';
			$is_grants[$key] = $module['grant_'.$chk];
		}
		return $is_grants[$key];
	}

	function isGrant($chk, $md_id) {
		return checkGrant(getGrant($chk, $md_id));
	}

	function checkGrant($chk) {
		if(is_null($chk) || strlen($chk) !== 1) return false;
		global $_MEMBER;
		$rank = ord(empty($_MEMBER['mb_rank']) ? '0' : $_MEMBER['mb_rank']);
		return $rank < 116 && ord($chk) <= $rank; // 0 = 48, z = 122 // s = 115 초과시 에러
	}

	function checkProtect($key) {
		global $_PROTECT;
		$grant = $_PROTECT[$key]['grant'];
		return !is_null($grant) && checkGrant($grant);
	}

	function checkProtectData($key, $data) {
		global $_PROTECT;
		global $_MEMBER;
		$result = [];
		$grade = empty($_MEMBER['mb_grade']) ? 'guest' : $_MEMBER['mb_grade'];
		if(empty($data['mb_srl'])) $data['mb_srl'] = null;
		//자기 자신 제외
		if (!empty($_MEMBER['mb_srl']) && $_MEMBER['mb_srl'] = $data['mb_srl']) {
			$_PROTECT[$key][$grade] = '*';
		}
		if (!isset($_PROTECT[$key][$grade]) || $_PROTECT[$key][$grade] === '*') {
			$result = $data;
		} else {
			$a = explode(',', str_replace(' ', '', $_PROTECT[$key][$grade]));
			foreach ($a as $val) $result[$val] = $data[$val];
		}
		return $result;
	}

	function goUrl($url, $msg='') {
		$url = str_replace("&amp;", "&", $url);
		if (headers_sent()) {
			echo '<script>'.($msg?'alert("'.$msg.'");':'').' location.replace("'.$url.'");</script>'
				.'<noscript>'.($msg ? $msg . '<br><br><a href="'.$url.'">'.$url.'</a>'
				: '<meta http-equiv="refresh" content="0;url='.$url.'" />').'</noscript>';
		} else {
			if($msg) set_error($msg, 1);
			header('Location: '.$url);
		}
		exit;
	}

	function createHash($password) {
		return password_hash(trim($password), PASSWORD_BCRYPT);
	}

	function checkPassword($password, $hash) {
		try {
			return password_verify($password, $hash);
		} catch (InvalidHashException $ex) {
			exit($ex->getMessage());
		} catch (CannotPerformOperationException $ex) {
			exit($ex->getMessage());
		}
	}

	function escapeMKDW($str, $is_strip_tags = false) {
		if($is_strip_tags) $str = strip_tags($str, (is_string($strip_tags)?$strip_tags:''));
		return preg_replace('/([\\\`\*\_\{\}\[\]\(\)\>\#\+\-\.\!])/m', '\\\\$1', $str);
	}

	function escapeHtml($str, $strip_tags = false, $quote = ENT_COMPAT, $endouble = true) {
		if($strip_tags) $str = strip_tags($str, (is_string($strip_tags)?$strip_tags:''));
		//$str = str_replace('&', '&amp;', $str);  // double_encode = false
		return htmlspecialchars($str, $quote | ENT_HTML401, 'UTF-8', $endouble);
	}

	function xssClean($html, $chkclosed = true) {
		$html = preg_replace('#<!--.*?-->#i', '', $html);
		$html = preg_replace('#</*\w+:\w[^>]*+>#i', '', $html);
		$html = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $html);

		// remove src hack // XE removeSrcHack https://www.xpressengine.com/
		$html = preg_replace_callback('@<(/?)([a-z]+[0-9]?)((?>"[^"]*"|\'[^\']*\'|[^>])*?\b(?:on[a-z]+|data|style|background|href|(?:dyn|low)?src)\s*=[\s\S]*?)(/?)($|>|<)@i',
			function ($match) {
				$tag = strtolower($match[2]);
				if($tag == 'xmp') return "<{$match[1]}xmp>";
				if($match[1]) return $match[0];
				if($match[4]) $match[4] = ' ' . $match[4];
				$attrs = array();
				if(preg_match_all('/([\w:-]+)\s*=(?:\s*(["\']))?(?(2)(.*?)\2|([^ ]+))/s', $match[3], $m)) {
					foreach($m[1] as $idx => $name) {
						if(strlen($name) >= 2 && substr_compare($name, 'on', 0, 2) === 0) continue;
						$val = preg_replace_callback('/&#(?:x([a-fA-F0-9]+)|0*(\d+));/', function ($c) {return chr(($c[1]?'0x00'.$c[1]:$c[2])+0);}, $m[3][$idx] . $m[4][$idx]);
						$val = preg_replace('/^\s+|[\t\n\r]+/', '', $val);
						if(preg_match('/^[a-z]+script:/i', $val)) continue;
						$attrs[$name] = $val;
					}
				}
				if(isset($attrs['style']) && preg_match('@(?:/\*|\*/|\n|:\s*expression\s*\()@i', $attrs['style'])) unset($attrs['style']);
				$attr = array();
				foreach($attrs as $name => $val) {
					if($tag == 'object' || $tag == 'embed' || $tag == 'a') {
						$attribute = strtolower(trim($name));
						if($attribute == 'data' || $attribute == 'src' || $attribute == 'href') {
							if(stripos($val, 'data:') === 0) continue;
						}
					}
					if($tag == 'img') {
						$attribute = strtolower(trim($name));
						if(stripos($val, 'data:') === 0) continue;
					}
					$val = str_replace('"', '&quot;', $val);
					$attr[] = $name . "=\"{$val}\"";
				}
				$attr = count($attr) ? ' ' . implode(' ', $attr) : '';
				return "<{$match[1]}{$tag}{$attr}{$match[4]}>";
			}
		, $html);

		if($chkclosed) { // close tags
			preg_match_all('#</([a-z]+)>#iU', $html, $closeds);
			preg_match_all('#<(?!meta|link|area|img|br|hr|input\b)([a-z]+)( .*)?(?!/)>#iU', $html, $openeds);
			if (count($openeds[1]) !== count($closeds[1])) {
				$closeds = $closeds[1];
				$openeds = array_reverse($openeds[1]);
				foreach ($openeds as $val) {
					if (in_array($val, $closeds)) { unset($closeds[array_search($val, $closeds)]); }
					else { $html .= '</'.$val.'>'; }
				}
			}
		}

		return $html;
	}

	function toHTML($text, $type = 2, $class = 'current_content') {
		static $parsedown = null;

		if($type == 0) {
			$text = nl2br(escapeHtml($text, '<img><a>'));
		} else if($type == 1) {
			if($parsedown == null) {
				$parsedown = new Parsedown();
				$parsedown->setBreaksEnabled(true)->setMarkupEscaped(false);
			}
			$text =$parsedown->text($text);
			// 비디오,오디오 처리
			$patterns = '/(<a[^>]*href=[\"\']?)([^>\"\']+)([\"\']?[^>]*title=[\"\']?_)(audio|video)(\/[^>\"\']+)(_[\"\']?[^>]*>.*?<\/a>)/is';
			$replacement = '<\\4 width="100%" controls><source src="\\2" type="\\4\\5">Your browser does not support the \\4 element.</\\4>';
			$text = preg_replace($patterns, $replacement, $text);
		}

		$text = preg_replace_callback('/<img[^>]*class="afox_widget"\s*([^>]*)>/is',  function($m){
			if(preg_match_all('/([a-z0-9_-]+)="([^"]+)"/is', $m[1], $m2)) {
				$attrs = [];
				foreach ($m2[1] as $key => $val) $attrs[$val] = $m2[2][$key];
				if(!empty($attrs['widget'])) {
					return displayWidget($attrs['widget'], $attrs);
				}
			}
			return '';
		}, $text);

		// 다운로드 권한이 없으면 처리
		$_md = __MID__;
		if(!empty($_md) && !isGrant('download', $_md)) {
			$patterns = '/(<a[^>]*)(href=[\"\']?[^>\"\']*[\?\&]file=[0-9]+[^>\"\']*[\"\']?)([^>]*>)/is';
			$replacement = "\\1\\2 onclick=\"alert('".escapeHtml(getLang('error_permitted',false),true,ENT_QUOTES,false)."');return false\" \\3";
			$text = preg_replace($patterns, $replacement, $text);
		}

		return '<div class="'.$class.'">'.$text.'</div>';
	}

	function displayModule() {
		if(!__MODULE__) return;
		global $_CFG;
		global $_DATA;
		global $_MEMBER;

		$trigger = $_DATA['disp'] ? $_DATA['disp'] : 'Default';
		$callproc = 'disp'.ucwords(__MODULE__).'Default';

		if(function_exists($callproc)) {
			$_result = triggerCall('before_disp', $trigger, $_DATA);
			if(empty($_result['error'])) {
				$_result = call_user_func($callproc, $_DATA);
				triggerCall('after_disp', $trigger, $_result);
			}
		} else {
			$_result = set_error(getLang('error_request'),4303);
		}

		if(!empty($_result['error'])) {
			// 에러 번호가 4501 이면 로그인 폼 보여줌
			if($_result['error'] == 4501 && empty($_MEMBER)) {
				include _AF_MODULES_PATH_ . 'member/tpl/loginform.php';
			} else {
				messageBox($_result['message'], $_result['error']);
			}
		} else {
			$_{__MODULE__} = $_result;
			unset($_result);
			unset($trigger);
			// 테마에 스킨(tpl)이 있으면 사용
			$tpl_path = _AF_THEME_PATH_ . 'skin/' . __MODULE__ . '/';
			$tpl_file = (empty($_{__MODULE__}['tpl'])?'default':$_{__MODULE__}['tpl']).'.php';
			if(!file_exists($tpl_path . $tpl_file)) $tpl_path = _AF_MODULES_PATH_ . __MODULE__ . '/tpl/';
			@include_once $tpl_path . 'common.php';
			include $tpl_path . $tpl_file;
		}
	}

	function displayWidget($widget, $_WIDGET = []){
		global $_MEMBER;
		ob_start();
		if(file_exists(_AF_WIDGETS_PATH_ . $widget . '/index.php')) {
			include _AF_WIDGETS_PATH_ . $widget . '/index.php';
		} else {
			messageBox(getLang('error_founded'), 4201, getLang('Widget').': '.$widget);
		}
		return ob_get_clean();
	}

	function displayEditor($name, $content, $options = []) {
		@include_once _AF_MODULES_PATH_ . 'editor/index.php';
	}

	// TODO 후에 모듈쪽에서 트리거가 필요할때를 대비해 함수명 통일
	function triggerCall($position, $trigger, &$data) {
		static $triggers = null;
		static $addon_call = null;
		// 관리자 모듈은 넘어감
		if(__MODULE__ == 'admin') return $data;

		if($triggers == null) {
			$triggers = ['A'=>[],'M'=>[]];
			DB::gets(_AF_TRIGGER_TABLE_, 'tg_key,tg_id',
				[(__MOBILE__?'use_mobile':'use_pc')=>1], 'tg_key',
				function($r)use(&$triggers) {
					while ($tmp = DB::assoc($r)) {
						$triggers[$tmp['tg_key']][$tmp['tg_id']] = [];
					}
				}
			);
		}

		// TODO 모듈용 트리거 작성 준비
		//foreach ($triggers['M'] as $key => $value) {
		//}

		if($addon_call == null) {
			$addon_call = function($include_file, $called_position, $called_trigger, $_ADDON, $_DATA) {
				$r = include $include_file;
				return empty($r['error']) ? $_DATA : $r;
			};
		}

		foreach ($triggers['A'] as $key => $value) {
			$include_file = _AF_ADDONS_PATH_.'/'.$key.'/index.php';
			if(file_exists($include_file)) {

				$_extra = get_cache('_AF_ADDON_'.$key);
				if(empty($_extra)) {
					$_extra = DB::get(_AF_ADDON_TABLE_, 'ao_extra', ['ao_id'=>$key]);
					$_extra = $_extra ? unserialize($_extra['ao_extra']) : [];
					set_cache('_AF_ADDON_'.$key, $_extra);
				}

				if(!empty($_extra['access_md_ids'])) {
					$acc_md = $_extra['access_mode'];
					$_md = __MID__;
					$is_acc = !empty($_md) && in_array($_md, $_extra['access_md_ids']);
					if(($acc_md == 'include' && !$is_acc)||($acc_md == 'exclude' && $is_acc)) continue;
				}

				$result = $addon_call($include_file, strtolower($position), strtolower($trigger), $_extra, $data);
				if(!empty($result['error'])) {
					$result['redirect_url'] = isset($data['error_return_url'])?urldecode($data['error_return_url']):'';
					return $result;
				}

				$data = $result;
			}
		}
	}

	function cutstr($str, $length, $tail = '...') {
		$count = 0;
		if($length < 1) return $str;
		for ($i=$length; $i > 0; $i--) {
			if (strlen($str) <= $count) return $str;
			$count+=($d=ord($str[$count]))<0x80?1:($d<0xE0?2:($d<0xF0?3:4));
		}
		return substr($str, 0, $count) . $tail;
	}

	function timePassed($datetime) {
		$t = time() - strtotime($datetime);
		$vars1 = ['minute','hour','day', 'week', 'month','year',  ''];
		$vars2 = [60,      3600,  86400, 604800, 2592000,31536000,1];
		foreach ($vars2 as $key => $value) { if($t < $value) break; }
		if($key < 1) return 'just now'; //second
		$value = floor($t/$vars2[$key-1]);
		return $value.' '.$vars1[$key-1].($value > 1 ? 's' : '').' ago';
	}

	function shortFileSize($size) {
		$tails = ['Byte','KB','MB','GB'];
		for ($i = 0; $i < 4; $i++) {
			if($size <= 1024) break;
			$size = $size / 1024;
		}
		return round($size, 1) . $tails[$i];
	}

	function messageBox($message, $type = 1, $title = '') {
		$type = $type>2000 ? (($type>2000&&$type<4000) ? 2 : 3) : ($type>3 ? 1 : $type);
		$a_type = ['success', 'info', 'warning', 'danger'];
		if($title !== false) {
			$a_title = ['success', 'alert', 'warning', 'error'];
			$a_icon = ['ok-sign', 'exclamation-sign', 'warning-sign', 'ban-circle'];
			$title = '<i class="glyphicon glyphicon-'.$a_icon[$type].'" aria-hidden="true"></i> '.(empty($title)?getLang($a_title[$type]):$title);
		}
		echo '<div class="'. (empty($title)?'alert alert-dismissable alert-':'panel panel-') . '' . $a_type[$type] . '" role="alert">'
				. (empty($title)?'<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>':'<div class="panel-heading"><h3 class="panel-title">'.$title.'</h3></div>')
				. '<div' . (empty($title)?'':' class="panel-body"') . '>' . $message . '</div></div>';
	}

	function checkUserAgent() {
		$agent = strtolower($_SERVER['HTTP_USER_AGENT']);
		if(preg_match("/bot|daum|crawl|slurp|spider|watchmouse|pingdom\.com|feedfetcher-google|request/", $agent)) return 'BOT';
		if(preg_match("/phone|iphone|itouch|ipod|symbian|android|htc_|htc-|palmos|blackberry|opera mini|iemobile|windows ce|nokia|fennec|hiptop|kindle|mot |mot-|webos\/|samsung|sonyericsson|^sie-|nintendo/", $agent)) return 'MOBILE';
		if(preg_match("/mobile|pda;|avantgo|eudoraweb|minimo|netfront|brew|teleca|lg;|lge |wap;| wap /", $agent)) return 'MOBILE';
		return 'BROWSER';
	}

	function addCSS($src, $media = '') {
		global $_ADDELEMENTS;
		if (isset($_ADDELEMENTS['CSS'][$src])) return;
		$_ADDELEMENTS['CSS'][$src] = empty($media) ? 1 : $media;
	}

	function addJS($src) {
		global $_ADDELEMENTS;
		if (isset($_ADDELEMENTS['JS'][$src])) return;
		$_ADDELEMENTS['JS'][$src] = 1;
	}

	function addJSLang($langs) {
		global $_ADDELEMENTS;
		$_ADDELEMENTS['LANG'][] = $langs;
	}

/* End of file function.php */
/* Location: ./init/function.php */
