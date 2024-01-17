<?php if(!defined('__AFOX__')) exit();
$_WIDGET_INFO = [];
@include_once _AF_WIDGETS_PATH_ . $_POST['wg_id'] . '/lang/' . _AF_LANG_ . '.php';
@require_once _AF_WIDGETS_PATH_ . $_POST['wg_id'] . '/info.php';
$_WIDGET_INFO['author'] = empty($_WIDGET_INFO['link'])?escapeHtml($_WIDGET_INFO['author']):('<a href="'.escapeHtml($_WIDGET_INFO['link']).'" target="_blank">'.escapeHtml($_WIDGET_INFO['author']).'</a>');
?>

<div>
<h4><?php echo escapeHtml($_WIDGET_INFO['title']) ?></h4>
<div class="row">
	<label class="col-md-2"><?php echo getLang('version') ?></label>
	<span class="col-md-auto"><?php echo escapeHtml($_WIDGET_INFO['version']) ?></span>
</div>
<div class="row">
	<label class="col-md-2"><?php echo getLang('date') ?></label>
	<span class="col-md-auto"><?php echo escapeHtml($_WIDGET_INFO['date']) ?></span>
</div>
<div class="row">
	<label class="col-md-2"><?php echo getLang('author') ?></label>
	<span class="col-md-auto"><?php echo $_WIDGET_INFO['author'] . ' ('.escapeHtml($_WIDGET_INFO['email']) . ')' ?></span>
</div>
<p class="form-text"><?php echo nl2br(escapeHtml($_WIDGET_INFO['description'])) ?></p>
</div>

<hr>

<?php
require_once _AF_WIDGETS_PATH_ . $_POST['wg_id'] . '/setup.php';
?>
<hr>

<?php
/* End of file widgetform.php */
/* Location: ./module/admin/form/widgetform.php */
