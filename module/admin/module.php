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

<table class="table">
<thead>
	<tr>
		<th scope="col" class="text-wrap"><?php echo getLang('module')?></th>
		<th scope="col" class="text-end d-none d-md-table-cell"><?php echo getLang('author')?></th>
		<th scope="col"><?php echo getLang('version')?></th>
		<th scope="col" class="text-end"><?php echo getLang('setup')?></th>
	</tr>
</thead>
<tbody>

<?php
$skip_arr = ['admin'=>1,'member'=>1,'page'=>1,'board'=>1,'editor'=>1];
$module_dir = _AF_MODULES_PATH_;
$is_admin =isAdmin();
if(false && $is_adminn) {
	foreach ($skip_arr as $key => $value) {
		@include $module_dir.$key.'/info.php';

		echo '<tr><th scope="row" class="text-wrap">'.(escapeHTML(empty($_MODULE_INFO['title'])?$name:$_MODULE_INFO['title'])).'</th>';
		echo '<td class="d-none d-md-table-cell">'.(empty($_MODULE_INFO['author'])?'...':'<a href="'.(empty($_MODULE_INFO['link'])?'mailto:'.$_MODULE_INFO['email'].'"':$_MODULE_INFO['link'].'" target="_blank"').'>'.$_MODULE_INFO['author'].'</a>').'</td>';
		echo '<td>'.(empty($_MODULE_INFO['version'])?'...':$_MODULE_INFO['version']).'</td>';
		echo '<td><a class="btn btn-primary btn-sm disabled" href="#">'.getLang('none').'</a></td></tr>';
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
			echo '<tr><th scope="row" class="text-wrap">'.(escapeHTML(empty($_MODULE_INFO['title'])?$name:$_MODULE_INFO['title'])).'</th>';
			echo '<td class="d-none d-md-table-cell">'.(empty($_MODULE_INFO['author'])?'...':'<a href="'.(empty($_MODULE_INFO['link'])?'mailto:'.$_MODULE_INFO['email'].'"':$_MODULE_INFO['link'].'" target="_blank"').'>'.$_MODULE_INFO['author'].'</a>').'</td>';
			echo '<td>'.(empty($_MODULE_INFO['version'])?'...':$_MODULE_INFO['version']).'</td>';
			if($is_setup) {
			echo '<td><a class="btn btn-primary btn-sm" href="'.getUrl('md_id', $name).'">'.getLang('setup').'</a></td></tr>';
			} else {
			echo '<td><a class="btn btn-primary btn-sm disabled" href="#">'.getLang('none').'</a></td></tr>';
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

<table class="table">
<thead>
	<tr>
		<th scope="col" class="text-wrap">#<?php echo getLang('removed_module')?></th>
		<th scope="col" class="text-end"><?php echo getLang('empty_%s', [''])?></th>
	</tr>
</thead>
<tbody>
<?php
	foreach($md_list as $key => $value) {
		if($value) echo '<tr><th scope="row" class="text-wrap">'.$key.'</th><td><button type="button" class="btn btn-primary btn-xs mw-10" data-empty-module="'.$key.'">'.getLang('empty_%s', ['']).'</button></td></tr>';
	}
?>
</tbody>
</table>

<?php
/* End of file module.ls.php */
/* Location: ./module/admin/module.ls.php */
