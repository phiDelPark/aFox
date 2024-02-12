<?php if(!defined('__AFOX__')) exit();
$_THEME_INFO = [];
@include_once _AF_THEMES_PATH_ . $_GET['th_id'] . '/lang/' . _AF_LANG_ . '.php';
@require_once _AF_THEMES_PATH_ . $_GET['th_id'] . '/info.php';
$_THEME_INFO['author'] = empty($_THEME_INFO['link'])?escapeHTML($_THEME_INFO['author']):('<a href="'.escapeHTML($_THEME_INFO['link']).'" target="_blank">'.escapeHTML($_THEME_INFO['author']).'</a>');

$_THEME = DB::get(_AF_THEME_TABLE_,'th_extra',['th_id'=>$_GET['th_id']]);
if(!$ex = DB::error()) $_THEME = empty($_THEME['th_extra'])?[]:unserialize($_THEME['th_extra']);
?>

<div>
<h4><?php echo escapeHTML($_THEME_INFO['title']) ?></h4>
<div class="row">
	<label class="col-md-2"><?php echo getLang('version') ?></label>
	<span class="col-md-auto"><?php echo escapeHTML($_THEME_INFO['version']) ?></span>
</div>
<div class="row">
	<label class="col-md-2"><?php echo getLang('date') ?></label>
	<span class="col-md-auto"><?php echo escapeHTML($_THEME_INFO['date']) ?></span>
</div>
<div class="row">
	<label class="col-md-2"><?php echo getLang('author') ?></label>
	<span class="col-md-auto"><?php echo $_THEME_INFO['author'] . ' ('.escapeHTML($_THEME_INFO['email']) . ')' ?></span>
</div>
<p class="form-text"><?php echo nl2br(escapeHTML($_THEME_INFO['about'])) ?></p>
</div>

<form method="post" autocomplete="off">
<input type="hidden" name="error_url" value="<?php echo getUrl()?>" />
<input type="hidden" name="success_url" value="<?php echo getUrl('th_id','')?>" />
<input type="hidden" name="module" value="admin" />
<input type="hidden" name="act" value="updatetheme" />
<input type="hidden" name="th_id" value="<?php echo $_GET['th_id']?>" />

<hr>
<?php
require_once _AF_THEMES_PATH_ . $_GET['th_id'] . '/setup.php';
?>

<hr class="mb-5">
<div class="text-end position-fixed bottom-0 end-0 p-3">
	<button type="submit" class="btn btn-success btn-lg" style="min-width:220px"><?php echo getLang('save')?></button>
</div></form>

<?php
/* End of file themeform.php */
/* Location: ./module/admin/form/themeform.php */
