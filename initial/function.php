<?php
if(!defined('__AFOX__')) exit();

	define('FOLLOW_REQUEST_SSL', 0);
	define('ENFORCE_SSL', 1);
	define('RELEASE_SSL', 2);

	require_once _AF_LIBS_PATH_ . 'pbkdf2/PasswordStorage.php';
	require_once _AF_LIBS_PATH_ . 'parsedown/Parsedown.php';

	function getUrlQuery() {
		$qs = [];
		$n = func_num_args();
		$a = func_get_args();
		$q = parse_url($a[0])['query'];
		preg_replace_callback('/(^|&)([^=&]+)=([^&]*)/',
			function($mc)use(&$qs) {$qs[$mc[2]]=$mc[3];return '';},
		$q);
		return $n == 2 ? $qs[$a[1]] : $qs;
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
		return getUrlQuery(getCurrentUrl(), $val);
	}

	function setQuery() {
		$a = array_merge([getCurrentUrl()], func_get_args());
		$u = call_user_func_array('setUrlQuery', $a);
		$p = strpos($u, '?');
		$q = ($p !== false) ? substr($u, $p+1) : '';
		return $_SERVER["QUERY_STRING"] = $q;
	}

	function getCurrentUrl() {
		return getUrl();
		//return ($_SERVER['REQUEST_METHOD'] == 'GET') ? getUrl() : getRequestUri();
	}

	function getRequestUri($ssl_mode = FOLLOW_REQUEST_SSL) {
		static $url = [];

		if(!isset($_SERVER['SERVER_PROTOCOL'])) return; // Check HTTP Request

		if(_AF_USE_SSL_ == 'always')  $ssl_mode = ENFORCE_SSL;
		$domain = (defined('_AF_DOMAIN_') && _AF_DOMAIN_) ? _AF_DOMAIN_ : null;
		$domain_key = $domain ? md5($domain) : 'default';

		if(isset($url[$ssl_mode][$domain_key])) return $url[$ssl_mode][$domain_key];

		$current_use_ssl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on');

		switch($ssl_mode) {
			case FOLLOW_REQUEST_SSL: $use_ssl = $current_use_ssl; break;
			case ENFORCE_SSL: $use_ssl = 1; break;
			case RELEASE_SSL: $use_ssl = 0; break;
		}

		if($domain) {
			$target_url = trim($domain);
			if(substr_compare($target_url, '/', -1) !== 0) $target_url.= '/';
		} else {
			$target_url = 'http://' . $_SERVER['HTTP_HOST'] . getScriptPath();
		}

		$url_info = parse_url($target_url);
		if($current_use_ssl != $use_ssl) unset($url_info['port']);

		$cfg_port = $use_ssl ? _AF_HTTPS_PORT_ : _AF_HTTP_PORT_;
		$def_port = $use_ssl ? 443 : 80;

		if($cfg_port != $def_port) {
			$url_info['port'] = $cfg_port;
		} elseif(isset($url_info['port']) && $url_info['port'] == $def_port) {
			unset($url_info['port']);
		}

		$url[$ssl_mode][$domain_key] = sprintf('%s://%s%s%s', $use_ssl ? 'https' : $url_info['scheme'], $url_info['host'], $url_info['port'] && $url_info['port'] != 80 ? ':' . $url_info['port'] : '', $url_info['path']);

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
		if($url == null) $url = str_ireplace('/tools/', '/', preg_replace('/index.php$/i', '', str_replace('\\', '/', $_SERVER['SCRIPT_NAME'])));
		return $url;
	}

	function getUrl() {
		if(_AF_USE_SSL_ == 'always') { // If using SSL always
			$uri = getRequestUri(ENFORCE_SSL);
		} elseif(_AF_USE_SSL_ == 'optional') { // optional SSL use
			$ssl_mode = __MODULE__ == 'admin' ? ENFORCE_SSL : RELEASE_SSL;
			$uri = getRequestUri($ssl_mode);
		} else { // no SSL
			if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') { // currently on SSL but target is not based on SSL
				$uri = getRequestUri(ENFORCE_SSL);
			} else if(defined('_AF_DOMAIN_') && _AF_DOMAIN_) { // if $domain is set
				$uri = getRequestUri(FOLLOW_REQUEST_SSL);
			} else {
				$uri = getScriptPath();
			}
		}

		$n = func_num_args();
		$a = func_get_args();

		if($n == 1 && $a[0] == '') return $uri;

		$url = $uri . ($_SERVER["QUERY_STRING"] ? '?' . $_SERVER["QUERY_STRING"] : '');
		return $n > 0 ? call_user_func_array('setUrlQuery', array_merge([$url], $a)) : $url;
	}

	// 이스케이프시 홑따옴표는 안되니 필요하면 escapeHtml 사용
	// 홑따옴표 이스케이프시 escapeHtml(getLang('msg',false),false,ENT_QUOTES)
	function getLang($key, $args1 = true, $args2 = true) {
		global $_LANG;
		if(empty($key)) return '';
		$lstr = strtolower($key);
		$result = isset($_LANG[$lstr]) ? $_LANG[$lstr] : $key;
		$escape = $args1;
		if(is_array($args1)) {
			$escape = $args2;
			$args = [$result];
			foreach ($args1 as $v) {
				$lstr = strtolower($v);
				$args[] = isset($_LANG[$lstr]) ? $_LANG[$lstr] : $v;
			}
			$result = call_user_func_array('sprintf', $args);
		}
		return $escape ? escapeHtml($result) : $result;
	}

	function getDBItem($table, $wheres = [], $field = '*') {
		$wheres = count($wheres) > 0 ? implode(' AND ', DB::quotesArray($wheres, TRUE)) : '1';
		if(empty($wheres)) $wheres = '1';
		$r = DB::get("SELECT {$field} FROM $table WHERE {$wheres}");
		return ($ex=DB::error()) ? set_error($ex->getMessage(), $ex->getCode()) : $r;
	}

	function getDBList($table, $wheres = [], $order = '', $page = 0, $count = 0, $callback = null) {
		$limit = '';

		if($count>0){
			$page = (int)($page > 0 ? $page - 1 : 0);
			$limit = ' LIMIT '.(int)($page * $count).','.(int)$count;
		}

		$order = empty($order) ? '' : ' ORDER BY '.$order;
		$wheres = count($wheres) > 0 ? implode(' AND ', DB::quotesArray($wheres, TRUE)) : '1';
		if(empty($wheres)) $wheres = '1';

		try {
			$r = DB::getList("SELECT SQL_CALC_FOUND_ROWS * FROM $table WHERE {$wheres}{$order}{$limit}", [], $callback);

			$total_count = DB::found();
			$result = [];

			if($count>0){
				$result['current_page'] = ++$page;
				$result['total_page'] = ceil($total_count / $count);
			}

			$result['total_count'] = $total_count;
			$result['data'] = $r;

			return $result;
		} catch (Exception $ex) {
			return set_error($ex->getMessage(), $ex->getCode());
		}
	}

	function getSiteMenu($get = '') {
		static $menus = [];
		if(!isset($menus['header']) || !isset($menus['footer'])) {
			for ($i=0; $i < 2; $i++) {
				$sql = 'SELECT * FROM '._AF_MENU_TABLE_.' WHERE mu_type=:1  ORDER BY mu_srl';
				$out = DB::getList($sql, $i, function($r){
					$rset = [];
					while ($row = mysqli_fetch_assoc($r)) {
						if(preg_match('/^[a-z]?[a-z0-9_]+$/i',$row['mu_link'])) {
							$row['md_id'] = $row['mu_link'];
							$row['mu_link'] = getUrl('','id',$row['md_id']);
						}
						$rset[] = $row;
					}
					return $rset;
				});
				if($ex = DB::error()) {
					$out = set_error($ex->getMessage(), $ex->getCode());
				}
				$menus[$i == 0 ? 'header' : 'footer'] = $out;
			}
		}
		return empty($get) ? $menus : $menus[$get];
	}

	function getAddon($id) {
		// TODO 캐시처리 할까?
		static $addons = [];
		if(!isset($addons[$id])) {
			$out = getDBItem(_AF_ADDON_TABLE_, ['ao_id'=>$id]);
			if(!empty($out['error']) || empty($out['extra'])) {
				$addons[$id] = $out;
			} else {
				$extra = $out['extra'];
				$extra = unserialize($extra);
				unset($out['extra']);
				$addons[$id] = array_merge($out, $extra);
			}
		}
		return $addons[$id];
	}

	// 모듈 설정 가져오기
	function getModule($id, $get = '') {
		static $module_cfg = [];
		if(!isset($module_cfg[$id])) {
			$out = getDBItem(_AF_MODULE_TABLE_, ['md_id'=>$id]);
			if(empty($out['error'])) $out = is_null($out) ? set_error(getLang('msg_not_founded'),801) : $out;
			$module_cfg[$id] = $out;
		}
		return empty($get) ? $module_cfg[$id] : $module_cfg[$id][$get];
	}

	function getMember($id, $get = '') {
		static $members = [];
		if(!isset($members[$id])) {
			$skey = is_numeric($id) ? 'mb_srl' : 'mb_id';
			$out = getDBItem(_AF_MEMBER_TABLE_, [$skey => $id]);
			if(empty($out['error']) && !empty($out['mb_srl'])) {
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
		if(!isset($filelist[$id])) {
			$out = getDBList(_AF_FILE_TABLE_, ['md_id'=>$id,'mf_target'=>$target], 'mf_type');
			$filelist[$id] = $out;
		}
		return $filelist[$id];
	}

	function getCache($key) {
		$file = _AF_CACHE_DATA_. md5($key);
		if(file_exists($file)){
			include($file);
			if(!empty($_CACHE_EXPIRE) && $_CACHE_EXPIRE < _AF_SERVER_TIME_) {
				@unlinkFile($file);
				return false;
			}
			return $_CACHE_DATA;
		} else return false;
	}

	// $expire = 0 유지 - 값이면 삭제
	function setCache($key, $value, $expire = 0) {
		$file = _AF_CACHE_DATA_. md5($key);
		if(file_exists($file)) @unlinkFile($file);
		if($expire < 0) {
			@unlinkFile($file);
		} else {
			$expire = $expire > 0 ? _AF_SERVER_TIME_ + $expire : 0;
			$str = '<?php if(!defined(\'__AFOX__\')) exit(); $_CACHE_EXPIRE='.$expire.'; $_CACHE_DATA='.var_export($value, true).'; ?>';
			file_put_contents($file, $str, LOCK_EX);
		}
	}

	function setHistoryAction($act, $value, $allowdup = false, $callback = null) {
		global $_MEMBER;

		$uinfo = [];
		$uinfo['mb_srl'] = empty($_MEMBER) ? 0 : $_MEMBER['mb_srl'];
		$uinfo['ipaddress'] = $_SERVER['REMOTE_ADDR'];

		$pkey = ($uinfo['mb_srl'] > 0 ? 'mb_srl':'hs_ipaddress');
		$pval = ($uinfo['mb_srl'] > 0 ? $uinfo['mb_srl']:$uinfo['ipaddress']);

		DB::transaction();

		try {
			$r = DB::select(_AF_HISTORY_TABLE_,
				[
					'hs_action'=>$act.'('.$value.')',
					$pkey=>$pval
				]
			);

			$uinfo['data'] = DB::assoc($r);
			if($allowdup || is_null($uinfo['data'])) {
				DB::insert(_AF_HISTORY_TABLE_,
					[
						'mb_srl'=>$uinfo['mb_srl'],
						'hs_ipaddress'=>$uinfo['ipaddress'],
						'hs_action'=>$act.'('.$value.')',
						'(hs_regdate)'=>'NOW()'
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

		// 비회원인데 - 값이면 에러
		if(empty($mb_srl) && $point < 0) {
			return set_error(getLang('msg_disallow_by_point', [abs($point), 0]), 907);
		}

		if(empty($mb_srl)) return;

		$mb = DB::get('SELECT mb_point,mb_rank FROM '._AF_MEMBER_TABLE_.' WHERE mb_srl='.$mb_srl);
		if($ex=DB::error()) return set_error($ex->getMessage(), $ex->getCode());

		$mb_rank = ord($mb['mb_rank']);
		// 115 초과시 에러... 115는 관리자. 109는 메니져
		if($mb_rank > 115) set_error(getLang('msg_invalid_request'),303);

		// 포인트 모자르면 에러
		if(($mb['mb_point'] + $point) < 0) {
			return set_error(getLang('msg_disallow_by_point', [abs($point), $mb['mb_point']]), 907);
		}

		$_setvals = ['(mb_point)'=>'mb_point'.($point>0?'+':'').$point];

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

	function dispEditor($name, $content, $options = []) {
		@include_once _AF_MODULES_PATH_ . 'editor/index.php';
	}

	function dispModuleContent() {
		if(!__MODULE__) return;
		global $_CFG;
		global $_DATA;
		global $_MEMBER;

		$triggercall = 'disp'.__MODULE__.($_DATA['disp']?$_DATA['disp']:'Default');

		if(function_exists('disp'.ucwords(__MODULE__).'Default')) {
			$_result = triggerCall($triggercall, 'before', $_DATA);
			if(!$_result) {
				$_result = call_user_func('disp'.ucwords(__MODULE__).'Default', $_DATA);
				triggerCall($triggercall, 'after', $_result);
			}
		} else {
			$_result = set_error(getLang('msg_invalid_request'),303);
		}

		if(!empty($_result['error'])) {
			if($_result['error'] == 901 && empty($_MEMBER)) {
				include _AF_MODULES_PATH_ . 'member/tpl/loginform.php';
			} else {
				echo showMessage($_result['message'], $_result['error'],
					'<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> '.getLang('warning')
				);
			}
		} else {
			$_{strtolower(__MODULE__)} = $_result;
			unset($_result);
			unset($triggercall);
			// 테마에 스킨(tpl)이 있으면 사용
			$tpl_path = _AF_THEME_PATH_ . __MODULE__ . '/';
			$tpl_file = (empty($_{__MODULE__}['tpl'])?'default':$_{__MODULE__}['tpl']).'.php';
			if(!file_exists($tpl_path . $tpl_file)) $tpl_path = _AF_MODULES_PATH_ . __MODULE__ . '/tpl/';
			include $tpl_path . $tpl_file;
		}
	}

	function setWidgetContent($text){
		static $call = null;

		if($call == null) {
			$call =	function($include_file, $_WIDGET) {
				ob_start();
				include $include_file;
				return ob_get_clean();
			};
		}

		return preg_replace_callback('/<img[^>]*class="afox_widget"\s*([^>]*)>/is',  function($m)use($call){
			if(preg_match_all('/([a-z0-9_-]+)="([^"]+)"/is', $m[1], $m2)) {
				$attrs = [];
				foreach ($m2[1] as $key => $val) $attrs[$val] = $m2[2][$key];
				if(!empty($attrs['widget'])){
					$include_file = _AF_WIDGETS_PATH_ . $attrs['widget'].'/index.php';
					if(file_exists($include_file)) {
						return $call($include_file, $attrs);
					} else {
						return showMessage(getLang('msg_not_founded'), 801);
					}
				}
			}
			return '';
		}, $text);
	}

	function sendNote($srl, $msg) {
		global $_MEMBER;
		$sender = empty($_MEMBER) ? 0 : $_MEMBER['mb_srl'];
		$nick = empty($_MEMBER) ? getLang('none') : $_MEMBER['mb_nick'];
		if(empty($srl) || $srl === $sender) return false;
		DB::insert(_AF_NOTE_TABLE_, [
			'mb_srl'=>$srl,
			'nt_sender'=>$sender,
			'nt_sender_nick'=>$nick,
			'nt_note'=>xssClean($msg),
			'(nt_send_date)'=>'NOW()'
		]);
	}

	function triggerCall($trigger, $position, &$data) {
		if(empty($trigger)) return;

		global $_ADDONS;
		static $call = null;

		if($call == null) {
			$call =	function($include_file, $_ADDON, $_DATA) {
				$r = include $include_file;
				return !empty($r['error']) && empty($r['act']) ? $r : $_DATA;
			};
		}

		$trigger = strtolower($position.'/'.$trigger.'.php');

		foreach ($_ADDONS as $key => $value) {
			$include_file = _AF_ADDONS_PATH_.'/'.$key.'/'.$trigger;
			if(file_exists($include_file)) {

				if(!is_array($_ADDONS[$key])) {
					$_ADDONS[$key] = unserialize($_ADDONS[$key]);
				}

				$result = $call($include_file, $_ADDONS[$key], $data);
				if(!empty($result['error']) && empty($result['act'])) {
					$result['redirect_url'] = isset($data['error_return_url']) ? $data['error_return_url'] : _AF_URL_;
					return $result;
				}

				$data = $result;
			}
		}
	}

	function moveUpFile($file, $dest, $max_size = 0) {
		if($file['error'] === UPLOAD_ERR_OK) {
			// HTTP post로 전송된 것인지 체크합니다.
			if(!is_uploaded_file($file['tmp_name'])) return set_error(getLang('UPLOAD_ERR_CODE(-1)'),1489);

			if($file['size'] <= 0) {
				return set_error(getLang('UPLOAD_ERR_CODE(4)'),1404);
			} if ($max_size > 0 && $max_size < $file['size']) {
				return set_error(getLang('UPLOAD_ERR_CODE(2)'),1402);
			}
			// 이동 경로가 없으면 이동 안함, 오류 체크는함
			if(empty($dest)) return true;
			// 폴더 없으면 만듬
			$dir = dirname($dest);
			if(!is_dir($dir) && !mkdir($dir, _AF_DIR_PERMIT_, true)) {
				return set_error(getLang('UPLOAD_ERR_CODE(7)'),1407);
			}
			// 파일이 있으면 지움
			if(file_exists($dest)) {
				if(!unlinkFile($dest)) return set_error(getLang('UPLOAD_ERR_CODE(7)'),1407);
			}
			if (move_uploaded_file($file['tmp_name'], $dest)) {
				@chmod($dest, _AF_FILE_PERMIT_);
			} else {
				return set_error(getLang('UPLOAD_ERR_CODE(4)'),1404);
			}
		} else {
			return set_error(getLang('UPLOAD_ERR_CODE('.$file['error'].')'),1400+$file['error']);
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

	// 권한 체크
	function isGrant($md_id, $chk) {
		if(empty($md_id) || empty($chk)) return false;

		global $_MEMBER;
		static $is_grants = [];

		$key = '_'.$md_id.'@'.$chk;
		if($md_id == '_AFOXtRASH_') {
			$is_grants[$key] = 'm'; // 휴지통은 메니져 이상
		} else if(!isset($is_grants[$key])) {
			$module = getModule($md_id);
			if(!empty($module['error'])) return false;
			$is_grants[$key] = $module['grant_'.$chk];
		}

		$grant = $is_grants[$key];

		if(!empty($grant)) {
			$rank = ord(empty($_MEMBER['mb_rank']) ? '0' : $_MEMBER['mb_rank']);
			if($rank > 115) return false; // s = 115 초과시 에러
			$grant = ord($grant);
			return $grant <= $rank; // 0 = 48, z = 122
		} else {
			return true;
		}
	}

	// 권한 체크
	function isManager($md_id) {
		if(empty($md_id)) return false;

		global $_MEMBER;
		static $is_manager = [];

		// 최고 관리자 제외
		if(!empty($_MEMBER['mb_srl']) && $_MEMBER['mb_rank'] == 's') return true;

		if(!isset($is_manager[$md_id])) {
			$module = getModule($md_id);
			if(!empty($module['error'])) return false;
			$is_manager[$md_id] = $module['md_manager'];
		}

		if(empty($is_manager[$md_id])) {
			return false;
		} else {
			return !empty($_MEMBER['mb_srl']) && ($is_manager[$md_id] == $_MEMBER['mb_srl']);
		}
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

	function verifyEncrypt($password, $hash) {
		try {
			$testResult = PasswordStorage::verify_password($password, $hash);
		} catch (InvalidHashException $ex) {
			exit($ex->getMessage());
		} catch (CannotPerformOperationException $ex) {
			exit($ex->getMessage());
		}
		return $testResult;
	}

	function encryptString($str) {
		return PasswordStorage::create_hash($str);
	}

	function toHTML($type, $text, $class='current_content') {
		global $_DATA;
		static $parsedown = null;

		if($type == 1) {
			if($parsedown == null) {
				$parsedown = new Parsedown();
				$parsedown->setBreaksEnabled(true)->setMarkupEscaped(false);
			}
			$text =$parsedown->text($text);
			// 비디오,오디오 처리
			$patterns = '/(<a[^>]*href=[\"\']?)([^>\"\']+)([\"\']?[^>]*title=[\"\']?_)(audio|video)(\/[^>\"\']+)(_[\"\']?[^>]*>.*?<\/a>)/i';
			$replacement = '<\\4 width="100%" controls><source src="\\2" type="\\4\\5">Your browser does not support the \\4 element.</\\4>';
			$text = preg_replace($patterns, $replacement, $text);
		} else if($type == 0) {
			$text =strip_tags($text, '<p><a>');
		}

		// 다운로드 권한이 없으면 처리
		if(!empty($_DATA['id']) && !isGrant($_DATA['id'],'download')) {
			$patterns = '/(<a[^>]*)(href=[\"\']?[^>\"\']*[\?\&]file=[0-9]+[^>\"\']*[\"\']?)([^>]*>)/i';
			$replacement = "\\1\\2 onclick=\"alert('".escapeHtml(getLang('msg_not_permitted',false),true,ENT_QUOTES)."');return false\" \\3";
			$text = preg_replace($patterns, $replacement, $text);
		}

		return '<div class="'.$class.'">'.$text.'</div>';
	}

	function escapeMKDW($str, $is_strip_tags = false) {
		if($is_strip_tags) $str = strip_tags($str);
		return preg_replace('/([\\\`\*\_\{\}\[\]\(\)\>\#\+\-\.\!])/m', '\\\\$1', $str);
	}

	function escapeHtml($str, $is_strip_tags = false, $quote_style = ENT_COMPAT) {
		if($is_strip_tags) $str = strip_tags($str);
		return htmlspecialchars($str, $quote_style | ENT_HTML401, 'UTF-8', false);
	}

	function shortFileSize($size) {
		$tail = 'byte';
		if($size>1024) {$size=ceil($size/1024);$tail='kb';}
		if($size>1024) {$size=ceil($size/1024);$tail='mb';}
		if($size>1024) {$size=ceil($size/1024);$tail='gb';}
		return $size.$tail;
	}

	function cut_str($str, $length, $tail = '...') {
		if($length < 1) return $str;
		$count = 0;
		$max = preg_match("@\[re\]@", $str) ? ($length + 4) : $length;
		for ($i=$max; $i > 0; $i--) {
			if (strlen($str) <= $count) return $str;
			if (ord($str[$count]) > 127) $count+=3; else $count++;
		}
		return substr($str, 0, $count) . $tail;

	}

	// http://stackoverflow.com/questions/1336776/xss-filtering-function-in-php
	function xssClean($str) {
		// Fix &entity\n;
		$str = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $str);
		$str = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $str);
		$str = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $str);
		$str = html_entity_decode($str, ENT_COMPAT, 'UTF-8');
		// Remove any attribute starting with "on" or xmlns
		$str = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $str);
		// Remove javascript: and vbscript: protocols
		$str = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $str);
		$str = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $str);
		$str = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $str);
		// Remove namespaced elements (we do not need them)
		$str = preg_replace('#</*\w+:\w[^>]*+>#i', '', $str);
		// Remove really unwanted tags
		$str = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $str);
		return $str;
	}

	function showMessage($message, $type = 0, $title = '') {
		$type = ($type > 3 || $type < 0) ? 3 : $type;
		$a_type = ['success', 'info', 'warning', 'danger'];
		return '<div class="'. (empty($title)?'alert alert-dismissable alert-':'panel panel-') . '' . $a_type[$type] . '" role="alert">'
				. (empty($title)?'<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>':'<div class="panel-heading"><h3 class="panel-title">'.$title.'</h3></div>')
				. '<div' . (empty($title)?'':' class="panel-body"') . '>' . $message . '</div></div>';
	}

	function checkUserAgent() {
		$agent = strtolower($_SERVER['HTTP_USER_AGENT']);
		if(preg_match("/googlebot|adsbot|yahooseeker|yahoobot|msnbot|watchmouse|pingdom\.com|feedfetcher-google/", $agent )) return 'BOT';
		if(preg_match("/phone|iphone|itouch|ipod|symbian|android|htc_|htc-|palmos|blackberry|opera mini|iemobile|windows ce|nokia|fennec|hiptop|kindle|mot |mot-|webos\/|samsung|sonyericsson|^sie-|nintendo/", $agent )) return 'MOBILE';
		if(preg_match("/mobile|pda;|avantgo|eudoraweb|minimo|netfront|brew|teleca|lg;|lge |wap;| wap /", $agent )) return 'MOBILE';
		return 'BROWSER';
	}

	function addJS($src) { global $_ADDELEMENTS; $_ADDELEMENTS['JS'][$src] = 1; }
	function addCSS($src) { global $_ADDELEMENTS; $_ADDELEMENTS['CSS'][$src] = 1; }

/* End of file function.php */
/* Location: ./initial/function.php */