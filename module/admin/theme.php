<?php
	if(!defined('__AFOX__')) exit();

	$th_list = DB::gets(_AF_THEME_TABLE_, 'th_id', [], function ($r) {
		$rset = [];
		while ($row = DB::fetch($r)) {
			$rset[$row['th_id']] = true;
		}
		return $rset;
	});
	if($ex = DB::error()) {
		messageBox($ex->getMessage(), $ex->getCode(), false);
	}
	$theme_id = empty($_CFG['theme']) ? 'default' : $_CFG['theme'];
?>

<table class="table">
<thead>
	<tr>
		<th scope="col" class="text-wrap">#<?php echo getLang('theme')?></th>
		<th scope="col" class="text-end d-none d-md-table-cell"><?php echo getLang('author')?></th>
		<th scope="col"><?php echo getLang('version')?></th>
		<th scope="col"><?php echo getLang('use')?></th>
		<th scope="col" class="text-end"><?php echo getLang('setup')?></th>
	</tr>
</thead>
<tbody>

<?php
$theme_dir = _AF_THEMES_PATH_;
if(is_dir($theme_dir)) {
	foreach(glob($theme_dir.'*', GLOB_ONLYDIR) as $dir) {
		$name = basename($dir);
		if(isset($th_list[$name])) $th_list[$name] = false;

		$_THEME_INFO = [];
		@include $theme_dir.$name.'/info.php';

		echo '<tr><th scope="row" class="text-wrap">'.(escapeHtml(empty($_THEME_INFO['title'])?$name:$_THEME_INFO['title'])).'</th>';
		echo '<td class="d-none d-md-table-cell">'.(empty($_THEME_INFO['author'])?'...':'<a href="'.(empty($_THEME_INFO['link'])?'mailto:'.$_THEME_INFO['email'].'"':$_THEME_INFO['link'].'" target="_blank"').'>'.$_THEME_INFO['author'].'</a>').'</td>';
		echo '<td>'.(empty($_THEME_INFO['version'])?'...':$_THEME_INFO['version']).'</td>';
		echo '<td><a class="btn btn-'.($theme_id == $name?'info':'primary').' btn-sm mw-10" style="width:100px" data-exec-ajax="admin.updateSetupTheme" data-ajax-param="th_id,'.$name.',success_return_url,'.urlencode(getUrl()).'">'.getLang($theme_id == $name? 'using':'use').'</a></td>';
		echo '<td><a class="btn btn-primary btn-sm mw-10" href="'.getUrl('th_id', $name, 'act', 'getThemeForm').'">'.getLang('setup').'</a></td></tr>';
	}
}
?>

</tbody>
</table>

<table class="table">
<thead>
	<tr>
		<th>#<?php echo getLang('removed_theme')?></th>
		<th class="text-end"><?php echo getLang('empty_theme')?></th>
	</tr>
</thead>
<tbody>
<?php
	foreach($th_list as $key => $value) {
		if($value) echo '<tr><td>'.$key.'</td><td><button type="button" class="btn btn-primary btn-xs mw-10" data-empty-theme="'.$key.'">'.getLang('empty_theme').'</button></td></tr>';
	}
?>
</tbody>
</table>

<div id="admin_theme_modal" class="modal fade bs-admin-modal-lg" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
	<form class="modal-content" method="post" autocomplete="off" data-exec-ajax="admin.getThemeForm">
	<input type="hidden" name="success_return_url" value="<?php echo getUrl()?>" />
	<input type="hidden" name="th_id" value="" />
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title"><?php echo getLang('theme')?></h4>
	  </div>
	  <div class="modal-body">
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo getLang('close')?></button>
		<button type="submit" class="btn btn-success"><i class="glyphicon glyphicon-ok" aria-hidden="true"></i> <?php echo getLang('save')?></button>
	  </div>
	</form>
  </div>
</div>

<?php
/* End of file theme.php */
/* Location: ./module/admin/theme.php */
