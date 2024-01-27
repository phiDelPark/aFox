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

<form id="themeSelectForm" method="post" autocomplete="off">
<input type="hidden" name="success_url" value="<?php echo getUrl()?>" />
<input type="hidden" name="module" value="admin" />
<input type="hidden" name="act" value="selecttheme" />

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
		echo '<td><input type="radio" name="th_id" id="th_id_'.$name.'" value="'.$name.'" style="display:none"><label class="btn btn-'.($theme_id == $name?'info':'primary').' btn-sm" style="width:100px" for="th_id_'.$name.'">'.getLang($theme_id == $name? 'using':'use').'</label></td>';
		echo '<td><a class="btn btn-primary btn-sm" href="'.getUrl('th_id', $name).'">'.getLang('setup').'</a></td></tr>';
	}
}
?>

</tbody>
</table>

</form>

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

<script>
	const select_form = document.querySelector('#themeSelectForm'), th_ids = select_form.querySelectorAll('[name="th_id"]');
	th_ids.forEach(el => el.addEventListener('change', e => select_form.submit()));
</script>

<?php
/* End of file theme.php */
/* Location: ./module/admin/theme.php */
