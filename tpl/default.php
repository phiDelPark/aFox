<?php
if(!defined('__AFOX__')) exit();
// 테마 설정 저장
if(($_THEME = getCache('_AF_THEME_'._AF_THEME_)) === false) {
	$_THEME = getDBItem(_AF_THEME_TABLE_, ['th_id'=>_AF_THEME_], 'th_extra');
	if(empty($_THEME['error'])) $_THEME = unserialize($_THEME['th_extra']);
	setCache('_AF_THEME_'._AF_THEME_,$_THEME);
}
?>
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
<link href="<?php echo _AF_URL_ ?>common/css/fontawesome.min.css" rel="stylesheet">
<script src="<?php echo _AF_URL_ ?>common/js/jquery.min.js" id="def-jQuery-JS"></script>
<script src="<?php echo _AF_URL_ ?>common/js/bootstrap.min.js" id="def-Bootstrap-JS"></script>
<?php } ?>
<script>
var current_url     = "<?php echo getCurrentUrl() ?>";
var request_uri     = "<?php echo getRequestUri() ?>";
var waiting_message = "<?php echo getLang('msg_call_server') ?>";
</script>
<link rel="stylesheet" href="<?php echo _AF_URL_ . 'common/css/common' . (__DEBUG__?'':'.min') . '.css' ?>">
<script src="<?php echo _AF_URL_ . 'common/js/common' . (__DEBUG__?'':'.min') . '.js' ?>"></script>
<?php @include _AF_THEME_PATH_ . '_head.php'; ?>
</head>
<body>
<?php  include _AF_THEME_PATH_ . (__POPUP__ ? 'popup' : 'index') . '.php'; ?>
<?php
@include _AF_THEME_PATH_ . '_tail.php';
foreach ($_ADDELEMENTS['CSS'] as $key=>$val) {echo '<link href="'.$key.'" rel="stylesheet">';}
foreach ($_ADDELEMENTS['JS'] as $key=>$val) {echo '<script src="'.$key.'"></script>';}
?>
</body>
</html>
