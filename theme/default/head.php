<?php
if(!defined('__AFOX__')) exit();
?>
<link rel="stylesheet" href="<?php echo _AF_THEME_URL_ . 'index' . (__DEBUG__ ? '.css?' . _AF_SERVER_TIME_ : '.min.css')?>">
<script src="<?php echo _AF_THEME_URL_ . 'index' . (__DEBUG__ ? '.js?' . _AF_SERVER_TIME_ : '.min.js')?>"></script>
<?php if(__FULL_LOGIN__) { ?>
	<link rel="stylesheet" href="<?php echo _AF_THEME_URL_ . 'login' . (__DEBUG__ ? '.css?' . _AF_SERVER_TIME_ : '.min.css')?>">
<?php } else { ?>
<?php } ?>