<?php if(!defined('__AFOX__')) exit();
$_MODULE_INFO = [];
@include_once _AF_MODULES_PATH_ . $_GET['md_id'] . '/lang/' . _AF_LANG_ . '.php';
@require_once _AF_MODULES_PATH_ . $_GET['md_id'] . '/info.php';
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
	$_MODULE = DB::get(_AF_MODULE_TABLE_, ['md_id'=>$_GET['md_id']]);
	if(empty($_MODULE['md_id'])){
		$_MODULE = DB::get(_AF_MODULE_TABLE_, ['md_key'=>$_GET['md_id']]);
	}
	echo '<hr>';
	require_once _AF_MODULES_PATH_ . $_GET['md_id'] . '/setup.php';
?>
<?php
/* End of file moduleform.php */
/* Location: ./module/admin/form/moduleform.php */
