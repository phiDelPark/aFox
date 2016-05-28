<?php
if(!defined('__AFOX__')) exit();
header('Content-Type: text/html; charset=utf-8');
header('Expires: 0'); // rfc2616 - Section 14.21
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate'); // HTTP/1.1
header('Cache-Control: pre-check=0, post-check=0, max-age=0'); // HTTP/1.1
header('Pragma: no-cache'); // HTTP/1.0
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
<?php if (!empty($_CFG['module']['md_title'])) { echo '<meta name="title" content="'.$_CFG['module']['md_title'].'">'."\n"; } ?>
<?php if (!empty($_CFG['module']['md_description'])) { echo '<meta name="description" content="'.$_CFG['module']['md_description'].'">'."\n"; } ?>
<title><?php echo $_CFG['title'].(empty($_CFG['module']['md_title']) ? '' : ' - '.$_CFG['module']['md_title']) ?></title>
<?php if ($_CFG['favicon']) {echo '<link rel="shortcut icon" href="'.$_CFG['favicon'].'">'."\n";} ?>
<!--[if IE]>
<script type="text/javascript" src="<?php echo _AF_URL_ ?>common/js/html5shiv.min.js"></script>
<![endif]-->
<?php if (__USE_BASE_CDN__) { include __USE_BASE_CDN__; } else { ?>
<link href="<?php echo _AF_URL_ ?>common/css/bootstrap.min.css" rel="stylesheet">
<link href="<?php echo _AF_URL_ ?>common/css/font-awesome.min.css" rel="stylesheet">
<script src="<?php echo _AF_URL_ ?>common/js/jquery-1.12.3.min.js" id="def-jQuery-JS"></script>
<script src="<?php echo _AF_URL_ ?>common/js/bootstrap.min.js" id="def-Bootstrap-JS"></script>
<?php } ?>
<script>
var current_url     = "<?php echo getCurrentUrl() ?>";
var request_uri     = "<?php echo getRequestUri() ?>";
var waiting_message = "<?php echo getLang('msg_call_server') ?>";
</script>
<link rel="stylesheet" href="<?php echo _AF_URL_ ?>common/css/common.css">
<script src="<?php echo _AF_URL_ ?>common/js/common.js"></script>
<?php @include _AF_THEME_PATH_ . '_head.php'; ?>
</head>
<body>
<?php  include _AF_THEME_PATH_ . 'index.php'; ?>
<?php
@include _AF_THEME_PATH_ . '_tail.php';
foreach ($_SCRIPTS as $key=>$val) {echo '<script src="'.$key.'"></script>';}
foreach ($_STYLESHEETS as $key=>$val) {echo '<link href="'.$key.'" rel="stylesheet">';}
?>
</body>
</html>
