<?php
if(!defined('__AFOX__')) exit();
$_THEME = get_cache('_AF_THEME_'._AF_THEME_);
if(is_null($_THEME)) {
	$_THEME = DB::get(_AF_THEME_TABLE_, 'th_extra', ['th_id'=>_AF_THEME_]);
	if($_THEME) $_THEME = unserialize($_THEME['th_extra']);
	set_cache('_AF_THEME_'._AF_THEME_,$_THEME);
}
if(empty($_CFG['md_title'])) $_CFG['md_title'] = '';
if(empty($_CFG['md_about'])) $_CFG['md_about'] = '';
addJSLang(['calling_server','yes','no', 'ok','cancel','error']);
?>
<?php if(!_MODAL_) { ?>
<!doctype html>
<html lang="ko"<?php echo true ? ' data-bs-theme="dark"' : '' ?>>
<head><meta charset="utf-8">
<meta name="referrer" content="origin" />
<meta name="robots" content="index,nofollow"/>
<?php if(_MOBILE_) { ?>
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=10,user-scalable=yes">
<?php } else { ?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="imagetoolbar" content="no">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<?php }
if($_CFG['md_title']) { echo '<meta name="title" content="'.escapeHTML($_CFG['md_title']).'">'."\n"; }
if($_CFG['md_about']) { echo '<meta name="about" content="'.escapeHTML($_CFG['md_about']).'">'."\n"; }
?>
<title><?php echo escapeHTML($_CFG['title'].($_CFG['md_title'] ? ' - '.$_CFG['md_title'] : '')) ?></title>
<?php
if($_CFG['favicon']) {echo '<link rel="shortcut icon" href="'.$_CFG['favicon'].'" type="image/x-icon">'."\n";}
if(_AF_USE_BASE_CDN_) { include _AF_USE_BASE_CDN_; } else { ?>
<link href="<?php echo _AF_URL_ ?>common/css/bootstrap.min.css" rel="stylesheet">
<script src="<?php echo _AF_URL_ ?>common/js/bootstrap.bundle.min.js" id="defBootstrapJS"></script>
<?php } ?>

<script>const language="<?php echo _AF_LANG_ ?>",request_uri="<?php echo getRequestUri() ?>",current_url="<?php echo getUrl() ?>",cookie_domain="<?php echo _AF_COOKIE_DOMAIN_ ?>";</script>
<script src="<?php echo _AF_URL_ . 'common/js/common' . (__DEBUG__ ? '.js?' . _AF_SERVER_TIME_ : '.min.js') ?>"></script>
<?php if(file_exists(_AF_THEME_PATH_ . 'index' . (__DEBUG__ ? '' : '.min') . '.css')) { ?>
<link href="<?php echo _AF_THEME_URL_ . 'index' . (__DEBUG__ ? '.css?' . _AF_SERVER_TIME_ : '.min.css') ?>" rel="stylesheet">
<?php } if(file_exists(_AF_THEME_PATH_ . 'index' . (__DEBUG__ ? '' : '.min') . '.js')) { ?>
<script src="<?php echo _AF_THEME_URL_ . 'index' . (__DEBUG__ ? '.js?' . _AF_SERVER_TIME_ : '.min.js') ?>"></script>
<?php }?>
</head><body>
<?php }?>
<?php
	include _AF_THEME_PATH_ . (_POPUP_ ? 'popup' : 'index') . '.php';
	echo'<script>';foreach($_ADDELEMENTS['LANG']as$k=>$v){echo'$_LANG[\''.$k.'\']="'.$v.'";';}echo'</script>'."\n";
	foreach($_ADDELEMENTS['CSS']as$k=>$v){echo'<link href="'.$k.'" rel="stylesheet" '.$v.'>';}
	foreach($_ADDELEMENTS['JS']as$k=>$v){echo'<script src="'.$k.'" '.$v.'></script>';}
?>
<?php if(!_MODAL_) { ?></body></html><?php } ?>
