<?php
	if(!defined('__AFOX__')) exit();

	$th_list = [];
	$out = DB::query('SELECT th_id FROM '._AF_THEME_TABLE_.' WHERE 1');
	if($ex = DB::error()) {
		echo showMessage($ex->getMessage(), $ex->getCode());
	}else {
		while ($row = DB::assoc($out)) {
			$th_list[$row['th_id']] = true;
		}
	}
	$theme_id = empty($_CFG['theme']) ? '' : $_CFG['theme']['th_id'];
?>

<table class="table table-hover">
<thead>
	<tr>
		<th>#<?php echo getLang('theme')?></th>
		<th><?php echo getLang('version')?></th>
		<th><?php echo getLang('author')?></th>
		<th><?php echo getLang('use')?></th>
		<th><?php echo getLang('setup')?></th>
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

		echo '<tr><th scope="row" class="col-md-8">'.(empty($_THEME_INFO['title'])?$name:$_THEME_INFO['title']).'</th>';
		echo '<td>'.(empty($_THEME_INFO['version'])?'...':$_THEME_INFO['version']).'</td>';
		echo '<td>'.(empty($_THEME_INFO['author'])?'...':'<a href="'.(empty($_THEME_INFO['link'])?'mailto:'.$_THEME_INFO['email'].'"':$_THEME_INFO['link'].'" target="_blank"').'>'.$_THEME_INFO['author'].'</a>').'</td>';
		echo '<td class="col-xs-1"><button type="button" class="btn btn-'.($theme_id == $name?'info':'primary').' btn-xs min-width-100" data-exec-ajax="admin.updateSetupTheme" data-ajax-param="th_id,'.$name.',success_return_url,'.urlencode(getUrl()).'">'.getLang($theme_id == $name? 'using':'use').'</button></td>';
		echo '<td class="col-xs-1"><button type="button" class="btn btn-primary btn-xs min-width-100" data-toggle="modal" data-target="#admin_theme_modal" data-theme-id="'.$name.'">'.getLang('setup').'</button></td></tr>';
	}
}
?>

</tbody>
</table>

<table class="table table-hover">
<thead>
	<tr>
		<th>#<?php echo getLang('removed_theme')?></th>
		<th><?php echo getLang('empty_theme')?></th>
	</tr>
</thead>
<tbody>
<?php
	foreach($th_list as $key => $value) {
		if($value) echo '<tr><td>'.$key.'</td><td class="col-xs-1"><button type="button" class="btn btn-primary btn-xs min-width-100" data-empty-theme="'.$key.'">'.getLang('empty_theme').'</button></td></tr>';
	}
?>
</tbody>
</table>

<div id="admin_theme_modal" class="modal fade bs-admin-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
	<form class="modal-content" method="post" autocomplete="off" data-exec-ajax="admin.getThemeForm">
	<input type="hidden" name="success_return_url" value="<?php echo getUrl()?>" />
	<input type="hidden" name="th_id" value="" />
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="myModalLabel"><?php echo getLang('theme')?></h4>
	  </div>
	  <div class="modal-body">
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo getLang('close')?></button>
		<button type="submit" class="btn btn-success min-width-150"><i class="glyphicon glyphicon-ok" aria-hidden="true"></i> <?php echo getLang('save')?></button>
	  </div>
	</form>
  </div>
</div>

<?php
/* End of file theme.php */
/* Location: ./module/admin/theme.php */