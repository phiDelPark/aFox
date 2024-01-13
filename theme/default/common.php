<?php
if(!defined('__AFOX__')) exit();
addJSLang([]);
?>

<link href="<?php echo _AF_THEME_URL_ . 'index' . (__DEBUG__ ? '.css?' . _AF_SERVER_TIME_ : '.min.css') ?>" rel="stylesheet">
<?php if(__FULL_LOGIN__) { ?>
<link href="<?php echo _AF_THEME_URL_ . 'login' . (__DEBUG__ ? '.css?' . _AF_SERVER_TIME_ : '.min.css') ?>" rel="stylesheet">
<?php } ?>
<script src="<?php echo _AF_THEME_URL_ . 'index' . (__DEBUG__ ? '.js?' . _AF_SERVER_TIME_ : '.min.js') ?>"></script>

<?php
/* End of file common.php */
/* Location: ./theme/default/common.php */
/* 이 파일이 존재하면 해당 theme 파일을 읽기전 자동으로 가장 먼저 읽어들임   */
