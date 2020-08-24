<?php
if(!defined('__AFOX__')) exit();
addJSLang(['ok','cancel','yes','no','calling_server']);
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
<title><?php echo getLang('%s %s', ['afox', 'admin']) ?></title>
<!--[if IE]>
<script type="text/javascript" src="<?php echo _AF_URL_ ?>common/js/html5shiv.min.js"></script>
<![endif]-->
<?php if (_AF_USE_BASE_CDN_) { include _AF_USE_BASE_CDN_; } else { ?>
<link href="<?php echo _AF_URL_ ?>common/css/bootstrap.min.css" rel="stylesheet">
<script src="<?php echo _AF_URL_ ?>common/js/jquery.min.js" id="def-jQuery-JS"></script>
<script src="<?php echo _AF_URL_ ?>common/js/bootstrap.min.js" id="def-Bootstrap-JS"></script>
<?php } ?>
<script>
var language        = "<?php echo _AF_LANG_ ?>";
var current_url     = "<?php echo getUrl() ?>";
var request_uri     = "<?php echo getRequestUri() ?>";
</script>
<link rel="stylesheet" href="<?php echo _AF_URL_ . 'common/css/common' . (__DEBUG__ ? '.css?' . _AF_SERVER_TIME_ : '.min.css') ?>">
<link rel="stylesheet" href="<?php echo _AF_URL_ . 'module/admin/admin' . (__DEBUG__ ? '.css?' . _AF_SERVER_TIME_ : '.css') ?>">
<script src="<?php echo _AF_URL_ . 'common/js/common' . (__DEBUG__ ? '.js?' . _AF_SERVER_TIME_ : '.min.js') ?>"></script>
</head>
<body>
<?php
	include _AF_ADMIN_PATH_ . 'admin.php';
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
?>
<script src="<?php echo _AF_URL_ . 'module/admin/admin' . (__DEBUG__ ? '.js?' . _AF_SERVER_TIME_ : '.js') ?>"></script>
</body>
</html>
