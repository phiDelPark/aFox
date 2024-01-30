<?php
	if(!defined('__AFOX__')) exit();
?>

<table class="table">
<thead>
	<tr>
		<th scope="col" class="text-wrap">#<?php echo getLang('widget')?></th>
		<th scope="col" class="text-end d-none d-md-table-cell"><?php echo getLang('author')?></th>
		<th scope="col"><?php echo getLang('version')?></th>
		<th scope="col" class="text-end"><?php echo getLang('how_to_use')?></th>
	</tr>
</thead>
<tbody>

<?php
$widget_dir = _AF_WIDGETS_PATH_;
if(is_dir($widget_dir)) {
	foreach(glob($widget_dir.'*', GLOB_ONLYDIR) as $dir) {
		$name = basename($dir);

		$_WIDGET_INFO = [];
		@include $widget_dir.$name.'/info.php';

		echo '<tr><th scope="row" class="text-wrap">'.(escapeHTML(empty($_WIDGET_INFO['title'])?$name:$_WIDGET_INFO['title'])).'</th>';
		echo '<td class="d-none d-md-table-cell">'.(empty($_WIDGET_INFO['author'])?'...':'<a href="'.(empty($_WIDGET_INFO['link'])?'mailto:'.$_WIDGET_INFO['email'].'"':$_WIDGET_INFO['link'].'" target="_blank"').'>'.$_WIDGET_INFO['author'].'</a>').'</td>';
		echo '<td>'.(empty($_WIDGET_INFO['version'])?'...':$_WIDGET_INFO['version']).'</td>';
		echo '<td><a class="btn btn-primary btn-sm" href="'.getUrl('wg_id', $name).'">'.getLang('how_to_use').'</a></td></tr>';
	}
}
?>

</tbody>
</table>

<?php
/* End of file widget.php */
/* Location: ./module/admin/widget.php */
