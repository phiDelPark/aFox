<?php
	if(!defined('__AFOX__')) exit();

	$ao_list = [];
	$out = DB::query('SELECT ao_id FROM '._AF_ADDON_TABLE_.' WHERE 1');
	if($ex = DB::error()) {
		echo showMessage($ex->getMessage(), $ex->getCode());
	}else {
		while ($row = DB::assoc($out)) {
			$ao_list[$row['ao_id']] = true;
		}
	}
?>

<table class="table table-hover">
<thead>
	<tr>
		<th class="col-md-9">#<?php echo getLang('addon')?></th>
		<th><?php echo getLang('version')?></th>
		<th><?php echo getLang('author')?></th>
		<th class="col-xs-1"><?php echo getLang('setup')?></th>
	</tr>
</thead>
<tbody>

<?php
$addon_dir = _AF_ADDONS_PATH_;
if(is_dir($addon_dir)) {
	foreach(glob($addon_dir.'*', GLOB_ONLYDIR) as $dir) {
		$name = basename($dir);
		if(isset($ao_list[$name])) $ao_list[$name] = false;

		$_ADDON_INFO = [];
		@include $addon_dir.$name.'/info.php';

		echo '<tr><th scope="row">'.(empty($_ADDON_INFO['title'])?$name:$_ADDON_INFO['title']).'</th>';
		echo '<td>'.(empty($_ADDON_INFO['version'])?'...':$_ADDON_INFO['version']).'</td>';
		echo '<td>'.(empty($_ADDON_INFO['author'])?'...':'<a href="'.(empty($_ADDON_INFO['link'])?'mailto:'.$_ADDON_INFO['email'].'"':$_ADDON_INFO['link'].'" target="_blank"').'>'.$_ADDON_INFO['author'].'</a>').'</td>';
		echo '<td><button type="button" class="btn btn-primary btn-xs min-width-100" data-toggle="modal" data-target="#admin_addon_modal" data-addon-id="'.$name.'">'.getLang('setup').'</button></td></tr>';
	}
}
?>

</tbody>
</table>

<table class="table table-hover">
<thead>
	<tr>
		<th>#<?php echo getLang('removed_addon')?></th>
		<th><?php echo getLang('empty_addon')?></th>
	</tr>
</thead>
<tbody>
<?php
	foreach($ao_list as $key => $value) {
		if($value) echo '<tr><td>'.$key.'</td><td class="col-xs-1"><button type="button" class="btn btn-primary btn-xs min-width-100" data-empty-addon="'.$key.'">'.getLang('empty_addon').'</button></td></tr>';
	}
?>
</tbody>
</table>

<div id="admin_addon_modal" class="modal fade bs-admin-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
	<form class="modal-content" method="post" autocomplete="off" data-exec-ajax="admin.getAddonForm">
	<input type="hidden" name="success_return_url" value="<?php echo getUrl()?>" />
	<input type="hidden" name="ao_id" value="" />
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="myModalLabel"><?php echo getLang('addon')?></h4>
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
/* End of file addon.php */
/* Location: ./module/admin/addon.php */