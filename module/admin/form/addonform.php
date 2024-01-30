<?php if(!defined('__AFOX__')) exit();
$_ADDON_INFO = [];
@include_once _AF_ADDONS_PATH_ . $_POST['ao_id'] . '/lang/' . _AF_LANG_ . '.php';
@require_once _AF_ADDONS_PATH_ . $_POST['ao_id'] . '/info.php';
$_ADDON_INFO['author'] = empty($_ADDON_INFO['link'])?escapeHTML($_ADDON_INFO['author']):('<a href="'.escapeHTML($_ADDON_INFO['link']).'" target="_blank">'.escapeHTML($_ADDON_INFO['author']).'</a>');
?>

<div>
<h4><?php echo escapeHTML($_ADDON_INFO['title']) ?></h4>
<div class="row">
	<label class="col-md-2"><?php echo getLang('version') ?></label>
	<span class="col-md-auto"><?php echo escapeHTML($_ADDON_INFO['version']) ?></span>
</div>
<div class="row">
	<label class="col-md-2"><?php echo getLang('date') ?></label>
	<span class="col-md-auto"><?php echo escapeHTML($_ADDON_INFO['date']) ?></span>
</div>
<div class="row">
	<label class="col-md-2"><?php echo getLang('author') ?></label>
	<span class="col-md-auto"><?php echo $_ADDON_INFO['author'] . ' ('.escapeHTML($_ADDON_INFO['email']) . ')' ?></span>
</div>
<p class="form-text"><?php echo nl2br(escapeHTML($_ADDON_INFO['description'])) ?></p>
</div>

<?php
$_ADDON = DB::get(_AF_ADDON_TABLE_, ['ao_id'=>$_POST['ao_id']]);

$out = DB::get(_AF_TRIGGER_TABLE_, ['tg_key'=>'A','tg_id'=>$_POST['ao_id']]);
$_ADDON['use_pc'] = empty($out['use_pc']) ? 0 : $out['use_pc'];
$_ADDON['use_mobile'] = empty($out['use_mobile']) ? 0 : $out['use_mobile'];
$_ADDON['grant_access'] = empty($out['grant_access']) ? '0' : $out['grant_access'];

if(empty($_ADDON['access_mode'])) $_ADDON['access_mode'] = null;
$_ADDON['use_editor'] = empty($_ADDON['use_editor']) ? 0 : $_ADDON['use_editor'];

if(!empty($_ADDON['ao_extra'])) {
    $extra = unserialize($_ADDON['ao_extra']);
    unset($_ADDON['ao_extra']);
    $_ADDON = array_merge($_ADDON, $extra);
}
?>

<form method="post" autocomplete="off">
<input type="hidden" name="error_url" value="<?php echo getUrl()?>" />
<input type="hidden" name="success_url" value="<?php echo getUrl('ao_id','')?>" />
<input type="hidden" name="module" value="admin" />
<input type="hidden" name="act" value="updateaddon" />
<input type="hidden" name="ao_id" value="<?php echo $_POST['ao_id']?>" />

<div class="mb-2">
	<input class="form-check-input" type="checkbox" name="use_pc" id="id_use_pc" value="1"<?php echo $_ADDON['use_pc']=='1'?' checked':'' ?>>
	<label for="id_use_pc">PC</label>
	<input class="form-check-input ms-3" type="checkbox" name="use_mobile" id="id_use_mobile" value="1"<?php echo $_ADDON['use_mobile']=='1'?' checked':'' ?>>
	<label for="id_use_mobile">Mobile</label>
<?php
	if(file_exists(_AF_ADDONS_PATH_ . $_POST['ao_id'] . '/editor.php')){
		echo '<input class="form-check-input ms-3" type="checkbox" name="use_editor" id="id_use_editor" value="1"'.($_ADDON['use_editor']=='1'?' checked':'').'>'
		."\n".'<label for="id_use_editor">Editor</label>';
	}
?>
</div>

<div class="mb-2">
	<input type="radio" class="btn-check" name="grant_access" id="id_grant_access1" autocomplete="off" value="0"<?php echo $_ADDON['grant_access']==='0'?' checked':'' ?>>
	<label class="btn btn-xs btn-outline-primary w-100p" for="id_grant_access1"><?php echo getLang('all')?></label>
	<input type="radio" class="btn-check" name="grant_access" id="id_grant_access2" autocomplete="off" value="1"<?php echo $_ADDON['grant_access']==='1'?' checked':'' ?>>
	<label class="btn btn-xs btn-outline-primary w-100p" for="id_grant_access2"><?php echo getLang('member')?></label>
	<input type="radio" class="btn-check" name="grant_access" id="id_grant_access3" autocomplete="off" value="m"<?php echo $_ADDON['grant_access']==='m'?' checked':'' ?>>
	<label class="btn btn-xs btn-outline-primary w-100p" for="id_grant_access3"><?php echo getLang('admin')?></label>
</div>
<hr>

<?php
require_once _AF_ADDONS_PATH_ . $_POST['ao_id'] . '/setup.php';

$_list = DB::gets(_AF_MODULE_TABLE_,'md_id',[],'md_key');
if(!DB::error()) {
	echo '<hr>
		<label class="btn p-0 border-0 btn-link" for="advanced_setup">'.getLang('advanced_setup').'</label>
		<input type="checkbox" id="advanced_setup" class="d-none">
		<div class="advanced_setup my-2 mb-4"><div><label>'.getLang('md_id').':</label>&nbsp;&nbsp;<label><input class="form-check-input" name="access_mode" type="radio" value="include"'.($_ADDON['access_mode']!='exclude'?' checked="checked"':'').'> '.getLang('include').'</label>&nbsp;&nbsp;<label><input class="form-check-input" name="access_mode" type="radio" value="exclude"'.($_ADDON['access_mode']=='exclude'?' checked="checked"':'').'> '.getLang('exclude').'</label></div><p class="form-text">'.getLang('desc_access_md_id').'</p><div>';
	$access_md_ids = [];
	if(!empty($_ADDON['access_md_ids'])) {
		foreach ($_ADDON['access_md_ids'] as $v) {
			$access_md_ids[$v] = true;
		}
	}
	foreach ($_list as $row) {
		echo '<label><input class="form-check-input" name="access_md_ids[]" type="checkbox" value="'.$row['md_id'].'"'.(empty($access_md_ids[$row['md_id']])?'':' checked="checked"').'> '.$row['md_id'].'</label>&nbsp;&nbsp;';
	}
	echo '</div></div>';
}
?>

<hr class="mb-5">
<div class="text-end position-fixed bottom-0 end-0 p-3">
	<button type="submit" class="btn btn-success btn-lg" style="min-width:220px"><?php echo getLang('save')?></button>
</div></form>

<?php
/* End of file addonform.php */
/* Location: ./module/admin/form/addonform.php */
