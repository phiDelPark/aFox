<?php
	if(!defined('__AFOX__')) exit();
	$ao_list = DB::gets(_AF_TRIGGER_TABLE_,['tg_key'=>'A'], function ($r) {
		$rset = [];
		while ($row = DB::fetch($r)) {
			$grade = ['0'=>'A','m'=>'S','s'=>'S'];
			$grade = array_key_exists($row['grant_access'], $grade) ? $grade[$row['grant_access']] : 'M';
			$rset[$row['tg_id']] = (empty($row['use_pc'])?'-':'P').(empty($row['use_mobile'])?'-/':'M/').$grade;
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
		<th scope="col" class="text-wrap">#<?php echo getLang('addon')?></th>
		<th scope="col" class="text-end d-none d-md-table-cell"><?php echo getLang('author')?></th>
		<th scope="col"><?php echo getLang('version')?></th>
		<th scope="col"><?php echo getLang('using')?></th>
		<th scope="col" class="text-end"><?php echo getLang('setup')?></th>
	</tr>
</thead>
<tbody>

<?php
if(is_dir(_AF_ADDONS_PATH_)) {
	foreach(glob(_AF_ADDONS_PATH_ . '*', GLOB_ONLYDIR) as $dir) {
		$opt = '--/-';
		$name = basename($dir);
		if(isset($ao_list[$name])) {
			$opt = $ao_list[$name];
			$ao_list[$name] = false;
		}

		$_ADDON_INFO = [];
		@include _AF_ADDONS_PATH_ . $name . '/info.php';

		echo '<tr><th scope="row" class="text-wrap">'.(escapeHtml(empty($_ADDON_INFO['title'])?$name:$_ADDON_INFO['title'])).'</th>';
		echo '<td class="d-none d-md-table-cell">'.(empty($_ADDON_INFO['author'])?'...':'<a href="'.(empty($_ADDON_INFO['link'])?'mailto:'.$_ADDON_INFO['email'].'"':$_ADDON_INFO['link'].'" target="_blank"').'>'.$_ADDON_INFO['author'].'</a>').'</td>';
		echo '<td>'.(empty($_ADDON_INFO['version'])?'...':$_ADDON_INFO['version']).'</td>';
		echo '<td class="fixed-width">'.$opt.'</td>';
		echo '<td><a class="btn btn-primary btn-sm" href="'.getUrl('ao_id', $name).'">'.getLang('setup').'</a></td></tr>';
	}
}
?>
</tbody>
</table>

<table class="table">
<thead>
	<tr>
		<th>#<?php echo getLang('removed_addon')?></th>
		<th class="text-end"><?php echo getLang('empty_addon')?></th>
	</tr>
</thead>
<tbody>
<?php
	foreach($ao_list as $key => $value) {
		if($value) echo '<tr><td>'.$key.'</td><td class="col-xs-1"><button type="button" class="btn btn-primary btn-sm" data-empty-addon="'.$key.'">'.getLang('empty_addon').'</button></td></tr>';
	}
?>
</tbody>
</table>

<?php
/* End of file addon.php */
/* Location: ./module/admin/addon.php */
