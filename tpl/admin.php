<?php
if(!defined('__AFOX__')) exit();

// 자바스크립트에서 go(-1) 함수를 쓰면 폼값이 사라질때 해당 폼의 상단에 사용하면
// 캐쉬의 내용을 가져옴. 완전한지는 검증되지 않음
header('Content-Type: text/html; charset=utf-8');
header('Expires: 0'); // rfc2616 - Section 14.21
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate'); // HTTP/1.1
header('Cache-Control: pre-check=0, post-check=0, max-age=0'); // HTTP/1.1
header('Pragma: no-cache'); // HTTP/1.0

if(empty($_MEMBER) || $_MEMBER['mb_rank'] != 's') {
		goUrl(_AF_URL_);
		exit();
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
<title><?php echo getLang('%s %s', ['afox', 'admin']) ?></title>
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
<link rel="stylesheet" href="<?php echo _AF_URL_ ?>module/admin/admin.css">
<script src="<?php echo _AF_URL_ ?>common/js/common.js"></script>
</head>
<body>
<?php include _AF_ADMIN_PATH_ . 'admin.php'; ?>
<script src="<?php echo _AF_URL_ ?>module/admin/admin.js"></script>
</body>
</html>