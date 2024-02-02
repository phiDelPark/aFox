<?php if(!defined('__AFOX__')) exit();
$_MODULE_INFO = [];
@include_once _AF_MODULES_PATH_ . $_POST['md_id'] . '/lang/' . _AF_LANG_ . '.php';
@require_once _AF_MODULES_PATH_ . $_POST['md_id'] . '/info.php';
$_MODULE_INFO['author'] = empty($_MODULE_INFO['link'])?escapeHTML($_MODULE_INFO['author']):('<a href="'.escapeHTML($_MODULE_INFO['link']).'" target="_blank">'.escapeHTML($_MODULE_INFO['author']).'</a>');
?>

<div>
<h4><?php echo escapeHTML($_MODULE_INFO['title']) ?></h4>
<div class="row">
	<label class="col-md-2"><?php echo getLang('version') ?></label>
	<span class="col-md-auto"><?php echo escapeHTML($_MODULE_INFO['version']) ?></span>
</div>
<div class="row">
	<label class="col-md-2"><?php echo getLang('date') ?></label>
	<span class="col-md-auto"><?php echo escapeHTML($_MODULE_INFO['date']) ?></span>
</div>
<div class="row">
	<label class="col-md-2"><?php echo getLang('author') ?></label>
	<span class="col-md-auto"><?php echo $_MODULE_INFO['author'] . ' ('.escapeHTML($_MODULE_INFO['email']) . ')' ?></span>
</div>
<p class="form-text"><?php echo nl2br(escapeHTML($_MODULE_INFO['about'])) ?></p>
</div>

<?php
$_MODULE = DB::get(_AF_MODULE_TABLE_, ['md_id'=>$_POST['md_id']]);
if(empty($_MODULE['md_id'])){
	$_MODULE = DB::get(_AF_MODULE_TABLE_, ['md_key'=>$_POST['md_id']]);
}
?>
<form method="post" autocomplete="off">
<input type="hidden" name="error_url" value="<?php echo getUrl()?>" />
<input type="hidden" name="success_url" value="<?php echo getUrl('md_id','')?>" />
<input type="hidden" name="module" value="<?php echo $_POST['md_id']?>" />
<input type="hidden" name="md_id" value="<?php echo $_POST['md_id']?>" />
<input type="hidden" name="act" value="updateSetup" />
<hr>
<?php
require_once _AF_MODULES_PATH_ . $_POST['md_id'] . '/setup.php';
?>

<hr class="mb-5">
<div class="text-end position-fixed bottom-0 end-0 p-3">
	<button type="submit" class="btn btn-success btn-lg" style="min-width:220px"><?php echo getLang('save')?></button>
</div></form>

<?php
/* End of file moduleform.php */
/* Location: ./module/admin/form/moduleform.php */
