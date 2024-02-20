<?php if(!defined('__AFOX__')) exit();
require_once _AF_LIBS_PATH_ . 'md/markdown.php';

define('ENFORCE_SSL', 1);
define('RELEASE_SSL', 2);
define('FOLLOW_REQUEST_SSL', 0);

function getUrlQuery(){
	$n = func_num_args();
	$a = func_get_args();
	$p = [];
	preg_replace_callback('/[?&]+([^=&]+)=([^&]*)/',
		function($m)use(&$p){
			$p[$m[1]] = $m[2];
			return '';
		},
	$a[0]);
	return empty($a[1]) ? $p : $p[$a[1]];
}

function setUrlQuery(){
	//$n = func_num_args();
	$a = func_get_args();
	$url = $a[0];
	$q = $a[1] == '' ? [] : getUrlQuery($url);
	$a = array_slice($a, $a[1] == '' ? 2 : 1);
	$n = count($a);
	if(is_array($a[0])) foreach($a[0] as $k => $v){$q[$k] = $v;}
	else for($i=0; $i<$n; $i+=2){$q[$a[$i]] = $a[$i+1];}
	$r = '';
	foreach ($q as $k => $v){if(isset($v) && $v!='') $r.=$k.'='.$v.'&';}
	$pos = strpos($url, '?');
	$url = ($pos !== false) ? substr($url, 0, $pos) : $url;
	return $url . ($n === 0 ? $r : substr('?'.$r, 0, -1));
}

function getQuery($val){
	return getUrlQuery(getUrl(), $val);
}

function setQuery(){
	$a = array_merge([getUrl()], func_get_args());
	$u = call_user_func_array('setUrlQuery', $a);
	$p = strpos($u, '?');
	$q = ($p !== false) ? substr($u, $p+1) : '';
	return $_SERVER["QUERY_STRING"] = $q;
}

// XE getRequestUri 참고 https://www.xpressengine.com/
function getRequestUri($ssl_mode = FOLLOW_REQUEST_SSL){
	static $__url = [];
	if(!isset($_SERVER['SERVER_PROTOCOL'])) return; // Check HTTP Request
	if(_AF_USE_SSL_ === ENFORCE_SSL) $ssl_mode = ENFORCE_SSL; // always
	$domain = _AF_DOMAIN_ ? _AF_DOMAIN_ : $_SERVER['HTTP_HOST'];
	$domain_key = md5($domain);
	if(isset($__url[$ssl_mode][$domain_key])) return $__url[$ssl_mode][$domain_key];
	$current_use_ssl = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on';
	switch($ssl_mode){
		case FOLLOW_REQUEST_SSL: $use_ssl = $current_use_ssl; break;
		case ENFORCE_SSL: $use_ssl = 1; break;
		case RELEASE_SSL: $use_ssl = 0; break;
	}
	$target_url = 'http://' . $domain . getScriptPath();
	if(substr_compare($target_url, '/', -1) !== 0) $target_url.= '/';
	$_info = parse_url($target_url);
	if($current_use_ssl != $use_ssl) unset($_info['port']);
	$cfg_port = $use_ssl ? _AF_HTTPS_PORT_ : _AF_HTTP_PORT_;
	$def_port = $use_ssl ? 443 : 80;
	if($cfg_port != $def_port) $_info['port'] = $cfg_port;
	elseif(isset($_info['port'])&& $_info['port']==$def_port){unset($_info['port']);}
	$__url[$ssl_mode][$domain_key] = sprintf(
		'%s://%s%s%s',
		$use_ssl ? 'https' : $_info['scheme'],
		$_info['host'],
		empty($_info['port']) ? '' : ':' . $_info['port'],
		$_info['path']
	);
	return $__url[$ssl_mode][$domain_key];
}

function getRequestMethod(){
	if(isset($_SERVER[($s='HTTP_X_REQUESTED_WITH')]) && $_SERVER[$s] == 'XMLHttpRequest'){
		return strpos($_SERVER['HTTP_ACCEPT'],'json')?'JSON':(strpos($_SERVER['HTTP_ACCEPT'],'xml')?'XMLRPC':'JSCALLBACK');
	} else {
		return strtoupper($_SERVER['REQUEST_METHOD']);
	}
}

function getScriptPath(){
	static $__url = null;
	if($__url == null) $__url = preg_replace('/index.php$/i', '', str_replace('\\', '/', $_SERVER['SCRIPT_NAME']));
	return $__url;
}

function getUrl(){
	if(_AF_USE_SSL_ === ENFORCE_SSL || (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on'))
		$uri = getRequestUri(ENFORCE_SSL); // If using SSL always
	elseif(_AF_USE_SSL_ === RELEASE_SSL) // optional SSL use
		$uri = getRequestUri(_MODULE_ == 'admin' || _MODULE_ == 'member' ? ENFORCE_SSL : RELEASE_SSL);
	else $uri = _AF_DOMAIN_ ? getRequestUri(FOLLOW_REQUEST_SSL) : getScriptPath(); // no SSL
	$n = func_num_args();
	$a = func_get_args();
	if($n === 1 && $a[0] == '') return $uri;
	$url = $uri . (isset($_SERVER["QUERY_STRING"]) ? '?' . $_SERVER["QUERY_STRING"] : '');
	return $n > 0 ? call_user_func_array('setUrlQuery', array_merge([$url], $a)) : $url;
}

function getSiteMenu($get = ''){
	static $__menus = [];
	if(!isset($__menus['header']) || !isset($__menus['footer'])){
		$i_act = -1;
		for ($i=0; $i < 2; $i++){
			$out = DB::gets(
				_AF_MENU_TABLE_, ['mu_type'=>$i], ['mu_srl'=>'ASC'],
				function($rs)use(&$i_act){ $rset = []; $u = getUrl();
					while ($r = DB::fetch($rs)){
						if(preg_match('/^[a-zA-Z]+\w{2,}$/',$r['mu_link'])){
							$r['md_id'] = $r['mu_link'];
							$r['mu_link'] = getUrl('','id',$r['md_id']);
						}
						$r['active'] = (!empty($r['md_id']) && $r['md_id'] == _MID_)
						|| (empty($r['md_id'])&&$r['mu_link']&&strpos($u,$r['mu_link'])===0);
						if($i_act < 0 && $r['active']) $i_act = count($rset);
						$rset[] = $r;
					}
					return $rset;
				}
			);
			$__menus[$i === 0 ? 'header' : 'footer'] = $out;
			if($i === 0 && $i_act > -1){
				for($i = $i_act; $i > -1; $i--){
					$__menus['header'][$i]['active'] = true;
					if(!$__menus['header'][$i]['mu_parent']) break;
				}
			}
		}
	}
	return $get ? $__menus[$get] : $__menus;
}

// 모듈 설정 가져오기
function getModule($id, $get = ''){
	static $__md_cfg = [];
	if(!isset($__md_cfg[$id])){
		$out = DB::get(_AF_MODULE_TABLE_, ['md_id'=>$id]);
		$__md_cfg[$id] = empty($out['md_id']) ? [] : $out;
	}
	return $get ? $__md_cfg[$id][$get] : $__md_cfg[$id];
}

function getMember($id, $get = ''){
	static $__members = [];
	if(!isset($__members[$id])){
		$skey = is_numeric($id) ? 'mb_srl' : 'mb_id';
		$out = DB::get(_AF_MEMBER_TABLE_, [$skey => $id]);
		if(empty($out['mb_srl'])) return $get ? '' : [];
		$_icon = $out['mb_srl'].'/profile_image.png';
		$out['mb_icon'] = file_exists(_AF_MEMBER_DATA_.$_icon) ? _AF_URL_.'data/member/'.$_icon : '';
		$grade = ['0'=>'guest','m'=>'manager','s'=>'admin'];
		$out['mb_grade'] = array_key_exists($out['mb_rank'], $grade) ? $grade[$out['mb_rank']] : 'member';
		unset($out['mb_password']);
		$__members[$id] = $out;
	}
	return $get ? $__members[$id][$get] : $__members[$id];
}

function getFileList($id, $target){
	static $__file_list = [];
	$key = $id.'_'.$target;
	if(!isset($__file_list[$key])){
		$out = DB::gets(_AF_FILE_TABLE_, ['md_id'=>$id,'mf_target'=>$target], 'mf_type');
		$__file_list[$key] = $out;
	}
	return $__file_list[$key];
}

// 활동 체크를 위해 기록
function setHistory($act, $value, $allowdup = false){ global $_MEMBER;
	if(!($mb_srl=@$_MEMBER['mb_srl'])) return false;
	if(!$allowdup && DB::count(_AF_HISTORY_TABLE_,
		['hs_action{LIKE}'=>'%::'.$act.'::%', 'mb_srl'=>$mb_srl]
	)) return false;

	DB::transaction();
	try {
		DB::insert(_AF_HISTORY_TABLE_, [
			'mb_srl'=>$mb_srl,
			'hs_action'=>'::'.$act.'::',
			'hs_value'=>$value,
			'^hs_regdate'=>'NOW()'
		]);
	} catch (Exception $ex){
		DB::rollback();
		return false;
	}
	DB::commit();
	return true;
}
function getHistoryList($act, $select = 'hs_value'){ global $_MEMBER;
	if(!($mb_srl=@$_MEMBER['mb_srl'])) return [];
	return DB::gets(_AF_HISTORY_TABLE_, $select,
		['hs_action{LIKE}'=>'%::'.$act.'::%', 'mb_srl'=>$mb_srl],
		'hs_regdate'
	);
}
function getHistory($act){
	return count($his=getHistoryList($act)) ? $his[0]['hs_value'] : null;
}

function setPoint($point, $mb_srl = 0){ global $_MEMBER;
	if(!($mb_srl = $mb_srl ? $mb_srl : @$_MEMBER['mb_srl']) || !$point) return;
	$mb = DB::get(_AF_MEMBER_TABLE_, 'mb_point,mb_rank', ['mb_srl'=>$mb_srl]);
	// 115 초과시 에러... 115는 관리자. 109는 메니져
	if(($mb_rank=($mb?ord($mb['mb_rank']):255))>115) return set_error(getLang('error_request'),4303);
	if(($mb['mb_point'] + $point) < 0){ // 모자르면 에러
		return set_error(getLang('warn_shortage', ['point']).' ('.($mb['mb_point']+$point).')', 3701);
	}
	$_setvals = ['^mb_point'=>'mb_point'.($point>0?'+':'').$point];
	if($mb_rank < 100){ // 99이하 일반회원, 포인트만큼계급조정, 최대50
		$_sum_point = $mb['mb_point'] + $point;
		$_rank = ($_sum_point > 250000) ? 50 : floor(sqrt(floor($_sum_point / 10) / 10));
		$_setvals['mb_rank'] = chr($_rank + 48);
	}
	DB::update(_AF_MEMBER_TABLE_, $_setvals, ['mb_srl'=>$mb_srl]);
	// 현재 로그인 멤버와 같으면 만일을 대비 전역 변수 고침
	if(!DB::error() && !empty($_MEMBER) && $mb_srl === $_MEMBER['mb_srl']){
		$_MEMBER['mb_point'] = $mb['mb_point'] + $point;
		if(isset($_setvals['mb_rank'])) $_MEMBER['mb_rank'] = $_setvals['mb_rank'];
	}
}

function isAdmin(){ global $_MEMBER;
	return !empty($_MEMBER['mb_srl']) && $_MEMBER['mb_rank'] == 's';
}

function isManager($md_id){ global $_MEMBER;
	if(!$md_id || empty($_MEMBER['mb_srl'])) return false;
	if($_MEMBER['mb_rank'] == 's' || $_MEMBER['mb_rank'] == 'm') return true;
	if(!($module = getModule($md_id)) || empty($module['md_manager'])) return false;
	return $module['md_manager'] === $_MEMBER['mb_srl'];
}

function isGrant($chk, $md_id){
	if(!$md_id || !$chk) return false;
	if($md_id == '_AFOXtRASH_') $grant = 'm'; // 휴지통은 메니져 이상
	else {
		if(!($module = getModule($md_id))) return false;
		$grant =  $module['grant_'.$chk];
	}
	return checkGrant($grant);
}

function checkGrant($chk){ global $_MEMBER;
	if(is_null($chk) || strlen($chk) !== 1) return false;
	$rank = ord(empty($_MEMBER['mb_rank']) ? '0' : $_MEMBER['mb_rank']);
	// [s = admin, m = manager] // 0 = 48, m = 109, s = 115, 초과시 에러
	return $rank < 116 && ord($chk) <= $rank;
}

function checkProtect($key){ global $_PROTECT;
	$grant = $_PROTECT[$key]['grant'];
	return !is_null($grant) && checkGrant($grant);
}

function checkProtectData($key, $data){ global $_MEMBER; global $_PROTECT;
	$grade = empty($_MEMBER) ? 'guest' : $_MEMBER['mb_grade'];
	if(!empty($_MEMBER['mb_srl']) && !empty($data['mb_srl']) && $_MEMBER['mb_srl'] === $data['mb_srl']){
		$_PROTECT[$key][$grade] = '*'; //자기 자신 제외
	}
	if(!isset($_PROTECT[$key][$grade]) || $_PROTECT[$key][$grade] === '*') return $data;
	$result = []; $a = explode(',', str_replace(' ', '', $_PROTECT[$key][$grade]));
	foreach ($a as $val) $result[$val] = $data[$val];
	return $result;
}

function checkPassword($password, $hash){
	try {
		$password = trim($password);
		if(_AF_PASSWORD_ALGORITHM_ == 'BCRYPT')
			return password_verify($password, $hash);
		else {
			$password = createHash($password);
			return $hash && $password === $hash;
		}
	} catch (Exception $ex) {
		exit($ex->getMessage());
	}
}

function createHash($password){
	try {
		$password = trim($password);
		if(_AF_PASSWORD_ALGORITHM_ == 'BCRYPT')
			return password_hash($password, PASSWORD_BCRYPT);
		else {
			$password =  DB::escape($password);
			$result = DB::query("SELECT password('$password') as pass", true);
			return $result[0]['pass'];
		}
	} catch (Exception $ex) {
		exit($ex->getMessage());
	}
}

function sendNote($srl, $msg){ global $_MEMBER;
	if(!$srl || !@$_MEMBER['mb_srl'] || $srl === $_MEMBER['mb_srl']) return false;
	DB::insert(_AF_NOTE_TABLE_, [
		'mb_srl'=>$srl,
		'nt_sender'=>$_MEMBER['mb_srl'],
		'nt_sender_nick'=>$_MEMBER['mb_nick'],
		'nt_content'=>strip_tags($msg), // xssClean($msg)
		'^nt_send_date'=>'NOW()'
	]);
}

function unlinkFile($file){
	@chmod($file, 0777);
	if(!@unlink($file)){
		@chmod($file, _AF_FILE_PERMIT_);
		return false;
	} else return true;
}

function unlinkDir($dir){
	@chmod($dir, 0777);
	if(!@rmdir($dir)){
		@chmod($dir, _AF_DIR_PERMIT_);
		return false;
	} else return true;
}

function unlinkAll($dir, $subdir = true){
	$ret = true; // 폴더가 없어도 성공으로 간주
	if(is_dir($dir)){
		$handle = @opendir($dir); // 절대경로
		while ($file = readdir($handle)){
			if($file != '.' && $file != '..'){ // 하위 폴더면
				if($subdir && is_dir($dir.$file.'/'))
					unlinkAll($dir.$file.'/', $subdir);
				else unlinkFile($dir.$file);
			}
		}
		@closedir($handle);
		$ret = unlinkDir($dir);
	}
	return $ret;
}

function moveUploadedFile($file, $dest, $max_size = 0){
	if($file['error'] === UPLOAD_ERR_OK){
		// HTTP post로 전송된 것인지 체크합니다.
		if(!is_uploaded_file($file['tmp_name'])) return set_error(getLang('error_upload(-1)'),10489);
		if($file['size'] <= 0) return set_error(getLang('error_upload(4)'),10404);
		if($max_size > 0 && $max_size < $file['size']) return set_error(getLang('error_upload(2)'),10402);
		if(!$dest) return true; // 이동 경로가 없으면 이동 안함, 오류 체크는함
		if(!is_dir($dir=dirname($dest)) && !mkdir($dir, _AF_DIR_PERMIT_, true)) return set_error(getLang('error_upload(7)'),10407);
		if(file_exists($dest) && !unlinkFile($dest)) return set_error(getLang('error_upload(7)'),10407);
		if(move_uploaded_file($file['tmp_name'], $dest)) @chmod($dest, _AF_ATTACH_PERMIT_);
		else return set_error(getLang('error_upload(4)'),10404);
	} else return set_error(getLang('error_upload('.$file['error'].')'),10400+$file['error']);
}

function escapeMKDW($str){
	return preg_replace('/([\\\`\*\_\{\}\[\]\(\)\>\#\+\-\.\!])/m', '\\\\$1', $str);
}

function escapeHTML($str, $quote = ENT_COMPAT, $endouble = true){
	//$str = str_replace('&', '&amp;', $str);  // double_encode = false
	return htmlspecialchars($str, $quote | ENT_HTML401, 'UTF-8', $endouble);
}

function xssClean($html, $tomd = false){ // to MD
	// html 타입이면 toMKDW
	if($tomd) return MD::toMKDW($html, isAdmin());
	else {
		return strip_tags($html, (isAdmin()?'<widget><script><style>':'').'<br>');
	}
}

function toHTML($text, $type, $class = 'current_content'){
	if(!(int)$type) $text = nl2br(escapeHTML($text));
	else {
		$text = MD::toHTML($text);
		$text = preg_replace_callback('/<widget\s+([^>]*)>([\w]+)<\/widget>/is', function($m){
			//$m[1] = htmlspecialchars_decode($m[1]);
			$attrs = [];
			if(preg_match_all('/([a-z0-9_-]+)="([^"]+)"/is', $m[1], $m2)){
				foreach ($m2[1] as $key => $val) $attrs[$val] = $m2[2][$key];
			}
			return $m[2] ? displayWidget($m[2], $attrs) : '';
		}, $text);
		// 다운로드 권한이 없으면 처리
		if(_MID_ && !isGrant('download', _MID_)){
			$pattern = '/(<a[^>]*)(href=[\"\']?[^>\"\']*[\?\&]file=[0-9]+[^>\"\']*[\"\']?)([^>]*>)/is';
			$replacement = "\\1\\2 onclick=\"alert('".escapeHTML(getLang('error_permitted'),ENT_QUOTES,false)."');return false\" \\3";
			$text = preg_replace($pattern, $replacement, $text);
		}
	}
	return $class ? '<div class="'.$class.'">'.$text.'</div>' : $text;
}

function triggerAddonCall($addons, $position, $trigger, &$data){
	static $__triggerAddonCall = null;
	if($__triggerAddonCall == null){
		$__triggerAddonCall = function($_CALLED, $_ADDON, &$_DATA){
			include $_CALLED['file'];
		};
	}
	foreach ($addons as $key => $value){
		$_file = _AF_ADDONS_PATH_.'/'.$key.'/index.php';
		if(file_exists($_file)){
			$_ex = get_cache('_AF_ADDON_'.$key);
			if(is_null($_ex)){
				$_ex = DB::get(_AF_ADDON_TABLE_, 'ao_extra', ['ao_id'=>$key]);
				$_ex = $_ex ? unserialize($_ex['ao_extra']) : [];
				set_cache('_AF_ADDON_'.$key, $_ex);
			}
			if(!empty($_ex['access_md_ids'])){
				$_acc_md = $_ex['access_mode'];
				$_is_acc = _MID_ && in_array(_MID_, $_ex['access_md_ids']);
				if(($_acc_md == 'include' && !$_is_acc)||($_acc_md == 'exclude' && $_is_acc)) continue;
			}
			$called = ['file'=>$_file, 'position'=>$position, 'trigger'=>$trigger];
			$__triggerAddonCall($called, $_ex, $data);
		}
	}
	return true;
}

function triggerModuleCall($modules, $position, $trigger, &$data){
	static $__triggerModuleCall = null;
	if($__triggerModuleCall == null){
		$__triggerModuleCall = function($_CALLED, &$_DATA){
			include $_CALLED['file'];
		};
	}
	foreach ($modules as $key => $value){
		if(($_file=_AF_MODULES_PATH_.'/'.$key.'/trigger/'.$trigger.'.php') && file_exists($_file)){
			$called = ['file'=>$_file, 'position'=>$position, 'trigger'=>$trigger];
			$__triggerModuleCall($called, $data);
		}
	}
	return true;
}

// TODO 후에 모듈쪽에서 트리거가 필요할때를 대비해 함수명 통일
function triggerCall($position, $trigger, &$data){
	global $_MEMBER; if(_MODULE_ == 'admin') return true; //skip admin
	static $__triggerCall = null;
	if($__triggerCall == null && ($__triggerCall = ['M'=>[], 'A'=>[]])){
		$rank = ord(empty($_MEMBER['mb_rank']) ? '0' : $_MEMBER['mb_rank']);
		DB::gets(_AF_TRIGGER_TABLE_, 'tg_key,tg_id',
			[(_MOBILE_?'use_mobile':'use_pc')=>1,'^'=>'ASCII(grant_access)<='.$rank], 'tg_key',
			function($r)use(&$__triggerCall){
				while($tmp = DB::fetch($r)) $__triggerCall[$tmp['tg_key']][$tmp['tg_id']] = [];
			}
		);
	}
	$trigger=strtolower($trigger); $position=strtolower($position);
	if(count($__triggerCall['M']) > 0) triggerModuleCall($__triggerCall['M'], $position, $trigger, $data);
	if(count($__triggerCall['A']) > 0) triggerAddonCall($__triggerCall['A'], $position, $trigger, $data);
	return true;
}

function installModuleTrigger($id, $access){
	if(DB::count(_AF_TRIGGER_TABLE_,
		['tg_key'=>'M','tg_id'=>$id,'use_pc'=>1,'use_mobile'=>1,'grant_access'=>$access]
	)!==1){
		DB::delete(_AF_TRIGGER_TABLE_,['tg_id'=>$id]);
		DB::insert(_AF_TRIGGER_TABLE_,
			['tg_key'=>'M','tg_id'=>$id,'use_pc'=>1,'use_mobile'=>1,'grant_access'=>$access]
		);
	}
}

function displayModule(){
	if(!_MODULE_) return;
	global $_CFG; global $_MEMBER;
	function __module_call($tpl_path, $tpl_file, $_DATA){
		global $_CFG; global $_MEMBER;
		include $tpl_path . $tpl_file;
	};
	$trigger = @$_GET['disp'] ? $_GET['disp'] : 'default';
	if(function_exists($callproc='disp'.ucwords(_MODULE_).'Default')){
		if(triggerCall('before_disp', $trigger, $_GET)){
			$_result = call_user_func($callproc, $_GET);
			triggerCall('after_disp', $trigger, $_result);
		} else $_result = get_error();
	} else {
		$_result = set_error(getLang('error_request'),4303);
	}
	if(empty($_result['error'])){ // If the theme has skin(tpl), I use it
		$tpl_file = (empty($_result['tpl']) ? 'default' : $_result['tpl']).'.php';
		$tpl_path = _AF_THEME_PATH_ . 'skin/' . _MODULE_ . '/';
		if(!file_exists($tpl_path . $tpl_file)) $tpl_path = _AF_MODULES_PATH_ . _MODULE_ . '/tpl/';
		__module_call($tpl_path, $tpl_file, $_result);
	} else { // If the error number is 4501, show the login form
		if($_result['error'] == 4501 && empty($_MEMBER)){
			global $_LANG;
			$tpl_file = 'signin.php';
			$tpl_path = _AF_THEME_PATH_ . 'skin/member/';
			if(!file_exists($tpl_path . $tpl_file)) $tpl_path = _AF_MODULES_PATH_ . 'member/tpl/';
			@include_once _AF_MODULES_PATH_ . 'member/lang/' . _AF_LANG_ . '.php';
			include $tpl_path . $tpl_file;
		} else messageBox($_result['message'], $_result['error']);
	}
}

function displayWidget($_ID, $_WIDGET = []){
	global $_MEMBER; ob_start();
	if(file_exists(_AF_WIDGETS_PATH_ . $_ID . '/index.php')){
		include _AF_WIDGETS_PATH_ . $_ID . '/index.php';
	} else messageBox(getLang('error_founded'), 4201, getLang('Widget').': '.$_ID);
	return ob_get_clean();
}

function displayEditor($_ID, $_CONTENT, $_EDITOR = []){
	@include_once _AF_MODULES_PATH_ . 'editor/index.php';
}

function cutstr($str, $length, $tail = '...'){
	if($length < 1) return $str;
	$count = 0;
	for ($i=$length; $i > 0; $i--){
		if (strlen($str) <= $count) return $str;
		$count+=($d=ord($str[$count]))<0x80?1:($d<0xE0?2:($d<0xF0?3:4));
	}
	return substr($str, 0, $count) . $tail;
}

function shortFileSize($size){
	$tails = ['B','K','M','G','T'];
	for ($i = 0; $i < 4; $i++){
		if($size <= 1024) break;
		$size = $size / 1024;
	}
	return round($size, 1) . $tails[$i];
}

function timePassed($datetime){
	$t = time() - strtotime($datetime);
	$vs1 = ['minute','hour','day', 'week', 'month','year',  ''];
	$vs2 = [60,	  3600,  86400, 604800, 2592000,31536000,1];
	foreach($vs2 as $key => $value){ if($t < $value) break; }
	if($key < 1) return 'just now'; //second
	$value = floor($t/$vs2[$key-1]);
	return $value.' '.$vs1[$key-1].($value > 1 ? 's' : '').' ago';
}

function isMobilePhone(){
	$agent = $_SERVER['HTTP_USER_AGENT'];
	// Check if user-agent is a tablet PC as iPad or Andoid tablet.
	if(preg_match("/iPad|Android|webOS|hp-tablet|PlayBook/", $agent)){
		//if(strpos($agent, 'Android') !== FALSE && strpos($agent, 'Mobile') === FALSE) return 'TABLET';
		if(!preg_match("/Opera Mini|Opera Mobi/", $agent)) return true;
	} // Detect mobile device by user agent
	if(preg_match("/iPod|iPhone|Android|BlackBerry|SymbianOS|Bada|Tizen|Kindle|Wii|SCH-|SPH-|CANU-|Windows Phone|Windows CE|POLARIS|Palm|Dorothy Browser|Mobile|Opera Mobi|Opera Mini|Minimo|AvantGo|NetFront|Nokia|LGPlayer|SonyEricsson|HTC/i", $agent)) return true;
	return false;
}

function isCrawler(){
	$agent = strtolower($_SERVER['HTTP_USER_AGENT']);
	return preg_match("/bot|crawl|slurp|spider|facebook|fetch|twikle|wotbox|pingdom|yahooseeker|google keyword|curl|request/", $agent);
}

function insertVisitorHistory(){
	$addr=strip_tags($_SERVER['REMOTE_ADDR']);
	if($addr && DB::count(_AF_VISITOR_TABLE_,['mb_ipaddress'=>$addr,'^'=>'TIMESTAMPDIFF(HOUR,`vs_regdate`,DATE_ADD(NOW(),INTERVAL -1 HOUR))<1'])===0){
		DB::insert(_AF_VISITOR_TABLE_,[
			'mb_ipaddress'=>$addr,'vs_agent'=>strip_tags($_SERVER['HTTP_USER_AGENT']), 'vs_referer'=>strip_tags($_SERVER['HTTP_REFERER']),'^vs_regdate'=>'NOW()'
		]);
	}
}

function goUrl($url, $msg=NULL){
	$url = str_replace("&amp;", "&", $url);
	if (headers_sent()){
		echo '<script>'.($msg?'alert("'.$msg.'");':'').' location.replace("'.$url.'");</script>'
			.'<noscript>'.($msg ? $msg . '<br><br><a href="'.$url.'">'.$url.'</a>'
			: '<meta http-equiv="refresh" content="0;url='.$url.'" />').'</noscript>';
	} else {
		if($msg) set_error($msg, 1);
		header('Location: '.$url);
	}
	if(is_string($msg)) exit($msg); //메세지 있으면 중단
}

function messageBox($message, $type = 1){
	$a_type = ['success', 'info', 'warning', 'danger'];
	$type = ($type>2000 && $type<6000) ? ($type<4000 ? 2 : 3) : ($type>3 ? 1 : $type);
	echo '<div class="alert alert-'.$a_type[$type].'" role="alert">'.$message.'</div>';
}

function addJS($src, $opt = ''){ global $_ADDELEMENTS;$_ADDELEMENTS['JS'][$src] = $opt; }
function addCSS($src, $opt = ''){ global $_ADDELEMENTS;$_ADDELEMENTS['CSS'][$src] = $opt; }
function addJSLang($langs){ global $_ADDELEMENTS;foreach($langs as $k=>$v)$_ADDELEMENTS['LANG'][$v]=getLang($v); }

function encode64($v){ //Because of js without encryption library
	return str_replace(array('=','/'),array('%3d','%2f'),base64_encode(rawurlencode($v)));
}
function decode64($v){
	return rawurldecode(base64_decode(str_replace(array('%3d','%2f'),array('=','/'),$v)));
}

function getLang($key, $args = []){ global $_LANG;
	$a = [isset($_LANG[$l = strtolower($key)]) ? $_LANG[$l] : $key];
	foreach($args as $v) $a[] = isset($_LANG[$l=strtolower($v)])?$_LANG[$l]:$v;
	return $args ? call_user_func_array('sprintf', $a) : $a[0];
}
function setLang($key, $value){ global $_LANG; $_LANG[strtolower($key)] = $value;}
function set_session($key, $val){$_SESSION[$key]=$val;}
function get_session($key){return isset($_SESSION[$key])?$_SESSION[$key]:'';}
function set_cookie($key, $val, $exp = 0){ //If 0, remove it when exit the browser //if -, remove
	setcookie(encode64($key),encode64($val),($exp>0?_AF_SERVER_TIME_+$exp:$exp),'/',_AF_COOKIE_DOMAIN_);
}
function get_cookie($key){
	return array_key_exists($cki=encode64($key),$_COOKIE)?decode64($_COOKIE[$cki]):'';
}
function set_cache($key, $val, $exp = 0){ //If 0, keep //if -, remove
	if(!is_dir(_AF_CACHE_DATA_)&&!mkdir(_AF_CACHE_DATA_,_AF_DIR_PERMIT_,true)) return;
	$s = '<?php if(!defined(\'__AFOX__\'))exit();$_EXPIRE=%s;$_CACHE=%s;?>';
	$s = sprintf($s,($exp?_AF_SERVER_TIME_+$exp:$exp),var_export($val,true));
	file_put_contents(_AF_CACHE_DATA_.encode64($key).'.php', $s, LOCK_EX);
}
function get_cache($key){ static $__af_caches = null;
	if(!empty($__af_caches[$key])) return $__af_caches[$key];
	if(!file_exists($f=(_AF_CACHE_DATA_.encode64($key).'.php'))) return;
	if((@include $f)!==1||(!empty($_EXPIRE)&&$_EXPIRE<_AF_SERVER_TIME_))
	{@chmod($f,0707);@unlink($f);return;} return ($__af_caches[$key] = $_CACHE);
}
function set_error($msg, $err = 3){
	return $_SESSION['AF_VALIDATOR_ERROR']=['error'=>$err,'message'=>$msg];
}
function get_error(){
	return isset($_SESSION[($key='AF_VALIDATOR_ERROR')])?$_SESSION[$key]:'';
}
function debugPrint($o = null){ if(!(__DEBUG__ & 1)) return;
	file_put_contents(_AF_PATH_.'_debug.php',implode(PHP_EOL,[date('== Y-m-d H:i:s =='),
	in_array(($type=gettype($o)),['array','object','resource'])?print_r($o,true):$type.'('.var_export($o,true).')'
	]).PHP_EOL.PHP_EOL,FILE_APPEND|LOCK_EX);
}

/* End of file function.php */
/* Location: ./init/function.php */