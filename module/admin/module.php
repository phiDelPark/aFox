<?php
	if(!defined('__AFOX__')) exit();
	$md_list = DB::gets(_AF_MODULE_TABLE_,'md_key',[],'md_key', function ($r) {
		$rset = [];
		while ($row = DB::fetch($r)) {
			$rset[$row['md_key']] = true;
		}
		return $rset;
	});
	if($ex = DB::error()) {
		messageBox($ex->getMessage(), $ex->getCode(), false);
	}
?>
<table class="table table-hover">
<thead class="table-nowrap">
	<tr>
		<th>#<?php echo getLang('module')?></th>
		<th class="hidden-xs"><?php echo getLang('version')?></th>
		<th class="hidden-xs hidden-sm"><?php echo getLang('author')?></th>
		<th class="col-xs-1"><?php echo getLang('setup')?></th>
	</tr>
</thead>
<tbody>
<?php
$skip_arr = ['admin'=>1,'member'=>1,'page'=>1,'board'=>1,'editor'=>1];
$module_dir = _AF_MODULES_PATH_;
$is_admin =isAdmin();
if($is_admin) {
	foreach ($skip_arr as $key => $value) {
		@include $module_dir.$key.'/info.php';

		echo '<tr><th scope="row">'.(escapeHtml(empty($_MODULE_INFO['title'])?$name:$_MODULE_INFO['title'])).'</th>';
		echo '<td class="hidden-xs">'.(empty($_MODULE_INFO['version'])?'...':$_MODULE_INFO['version']).'</td>';
		echo '<td class="hidden-xs hidden-sm">'.(empty($_MODULE_INFO['author'])?'...':'<a href="'.(empty($_MODULE_INFO['link'])?'mailto:'.$_MODULE_INFO['email'].'"':$_MODULE_INFO['link'].'" target="_blank"').'>'.$_MODULE_INFO['author'].'</a>').'</td>';
		echo '<td><button type="button" class="btn btn-primary btn-xs mw-10" '.'disabled="disabled">'.getLang('none').'</button></td></tr>';
	}
}
if(is_dir($module_dir)) {
	$tmp = $_PROTECT;

	foreach(glob($module_dir.'*', GLOB_ONLYDIR) as $dir) {
		$name = basename($dir);
		if(isset($md_list[$name])) $md_list[$name] = false;
		if(!empty($skip_arr[$name])) continue;

		$is_setup = file_exists($module_dir.$name.'/setup.php');

		$_MODULE_INFO = [];
		@include $module_dir.$name.'/info.php';
		@include $module_dir.$name . '/protect.php';
		$_MODULE_INFO['_PROTECT_'] = $_PROTECT;

		if ($is_admin || ($is_setup&&$_MODULE_INFO['_PROTECT_']['setup']['grant'] == 'm')) {
			echo '<tr><th scope="row">'.(escapeHtml(empty($_MODULE_INFO['title'])?$name:$_MODULE_INFO['title'])).'</th>';
			echo '<td class="hidden-xs">'.(empty($_MODULE_INFO['version'])?'...':$_MODULE_INFO['version']).'</td>';
			echo '<td class="hidden-xs hidden-sm">'.(empty($_MODULE_INFO['author'])?'...':'<a href="'.(empty($_MODULE_INFO['link'])?'mailto:'.$_MODULE_INFO['email'].'"':$_MODULE_INFO['link'].'" target="_blank"').'>'.$_MODULE_INFO['author'].'</a>').'</td>';
			if($is_setup) {
			echo '<td><button type="button" class="btn btn-primary btn-xs mw-10" data-toggle="modal" data-target="#admin_module_modal" data-module-id="'.$name.'">'.getLang('setup').'</button></td></tr>';
			} else {
			echo '<td><button type="button" class="btn btn-primary btn-xs mw-10" '.'disabled="disabled">'.getLang('none').'</button></td></tr>';
			}
		}
	}
	$_PROTECT = $tmp;
	unset($tmp);
	unset($_MODULE_INFO);
}
?>
</tbody>
</table>
<table class="table table-hover">
<thead class="table-nowrap">
	<tr>
		<th>#<?php echo getLang('removed_module')?></th>
		<th><?php echo getLang('empty_module')?></th>
	</tr>
</thead>
<tbody>
<?php
	foreach($md_list as $key => $value) {
		if($value) echo '<tr><td>'.$key.'</td><td class="col-xs-1"><button type="button" class="btn btn-primary btn-xs mw-10" data-empty-module="'.$key.'">'.getLang('empty_module').'</button></td></tr>';
	}
?>
</tbody>
</table>
<div id="admin_module_modal" class="modal fade bs-admin-modal-lg" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
	<form class="modal-content" method="post" autocomplete="off" data-exec-ajax="admin.getModuleForm">
	<input type="hidden" name="success_return_url" value="<?php echo getUrl()?>" />
	<input type="hidden" name="md_id" value="" />
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title"><?php echo getLang('module')?></h4>
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
/* End of file module.ls.php */
/* Location: ./module/admin/module.ls.php */
