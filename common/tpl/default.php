<?php
if(!defined('__AFOX__')) exit();
// 테마 설정 저장
$_THEME = get_cache('_AF_THEME_'._AF_THEME_);
if(empty($_THEME)) {
	$_THEME = DB::get(_AF_THEME_TABLE_, 'th_extra', ['th_id'=>_AF_THEME_]);
	if(!empty($_THEME)) $_THEME = unserialize($_THEME['th_extra']);
	set_cache('_AF_THEME_'._AF_THEME_,$_THEME);
}
addJSLang(['ok','cancel','yes','no','calling_server']);
?>
<?php if (!__MODAL__) { ?>
<!doctype html>
<html lang="ko">
<head>
<meta charset="utf-8">
<?php if (__MOBILE__) { ?>
<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=0,maximum-scale=10,user-scalable=yes">
<meta name="HandheldFriendly" content="true">
<meta name="format-detection" content="telephone=no">
<?php } else { ?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="imagetoolbar" content="no">
<meta http-equiv="X-UA-Compatible" content="IE=10,chrome=1">
<?php } ?>
<?php if (!empty($_CFG['md_title'])) { echo '<meta name="title" content="'.escapeHtml($_CFG['md_title']).'">'."\n"; } ?>
<?php if (!empty($_CFG['md_description'])) { echo '<meta name="description" content="'.escapeHtml($_CFG['md_description']).'">'."\n"; } ?>
<title><?php echo escapeHtml($_CFG['title'].(empty($_CFG['md_title']) ? '' : ' - '.$_CFG['md_title'])) ?></title>
<?php if ($_CFG['favicon']) {echo '<link rel="shortcut icon" href="'.$_CFG['favicon'].'">'."\n";} ?>
<!--[if IE]>
<script type="text/javascript" src="<?php echo _AF_URL_ ?>common/js/html5shiv.min.js"></script>
<![endif]-->
<?php if (_AF_USE_BASE_CDN_) { include _AF_USE_BASE_CDN_; } else { ?>
<link href="<?php echo _AF_URL_ ?>common/css/bootstrap.min.css" rel="stylesheet">
<script src="<?php echo _AF_URL_ ?>common/js/jquery.min.js" id="def-jQuery-JS"></script>
<script src="<?php echo _AF_URL_ ?>common/js/bootstrap.min.js" id="def-Bootstrap-JS"></script>
<?php } ?>

<link rel="stylesheet" href="<?php echo _AF_URL_ . 'common/css/common' . (__DEBUG__ ? '.css?' . _AF_SERVER_TIME_ : '.min.css') ?>">
<script src="<?php echo _AF_URL_ . 'common/js/common' . (__DEBUG__ ? '.js?' . _AF_SERVER_TIME_ : '.min.js') ?>"></script>
<script>
var language        = "<?php echo _AF_LANG_ ?>";
var current_url     = "<?php echo getUrl() ?>";
var request_uri     = "<?php echo getRequestUri() ?>";
</script>
<?php @include _AF_THEME_PATH_ . 'head.php'; ?>
</head>
<body>
<?php } ?>
<?php
	include _AF_THEME_PATH_ . (__FULL_LOGIN__ ? 'login' : (__POPUP__ ? 'popup' : 'index')) . '.php';
	echo '<script>';
	foreach ($_ADDELEMENTS['LANG'] as $key) {
		foreach ($key as $src=>$val){
			if(!empty($val) && empty($_ADDELEMENTS['LANG'][$val][0])) {
				$_ADDELEMENTS['LANG'][$val][0] = getLang($val);
				echo '$_LANG[\''.$val.'\']="'.$_ADDELEMENTS['LANG'][$val][0].'";';
			}
		}
	}
	echo '</script>'."\n";
	foreach (['M', 'A'] as $key) {
		foreach ($_ADDELEMENTS[$key.'_CSS'] as $src=>$val) {
			if ($val === 1 || is_string($val)) {
				echo '<link href="'.$src.'" rel="stylesheet"'.($val!==1?' media="'.$val.'"':'').'>';
			}
		}
		foreach ($_ADDELEMENTS[$key.'_JS'] as $src=>$val) {
			if ($val === 1) echo '<script src="'.$src.'"></script>';
		}
	}
?>
<?php if (!__MODAL__) { ?>
</body>
</html>
<?php } ?>
