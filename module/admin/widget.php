<?php
	if(!defined('__AFOX__')) exit();
?>

<table class="table table-hover">
<thead class="table-nowrap">
	<tr>
		<th>#<?php echo getLang('widget')?></th>
		<th class="hidden-xs"><?php echo getLang('version')?></th>
		<th class="hidden-xs hidden-sm"><?php echo getLang('author')?></th>
		<th class="col-xs-1">?</th>
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

		echo '<tr><th scope="row">'.(escapeHtml(empty($_WIDGET_INFO['title'])?$name:$_WIDGET_INFO['title'])).'</th>';
		echo '<td class="hidden-xs">'.(empty($_WIDGET_INFO['version'])?'...':$_WIDGET_INFO['version']).'</td>';
		echo '<td class="hidden-xs hidden-sm">'.(empty($_WIDGET_INFO['author'])?'...':'<a href="'.(empty($_WIDGET_INFO['link'])?'mailto:'.$_WIDGET_INFO['email'].'"':$_WIDGET_INFO['link'].'" target="_blank"').'>'.$_WIDGET_INFO['author'].'</a>').'</td>';
		echo '<td><button type="button" class="btn btn-primary btn-xs mw-10" data-toggle="modal" data-target="#admin_widget_modal" data-widget-id="'.$name.'">'.getLang('how_to_use').'</button></td></tr>';
	}
}
?>

</tbody>
</table>

<div id="admin_widget_modal" class="modal fade bs-admin-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
	<form class="modal-content" method="post" autocomplete="off" data-exec-ajax="admin.getWidgetForm">
	<input type="hidden" name="success_return_url" value="<?php echo getUrl()?>" />
	<input type="hidden" name="wg_id" value="" />
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="myModalLabel"><?php echo getLang('widget')?></h4>
	  </div>
	  <div class="modal-body">
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo getLang('close')?></button>
	  </div>
	</form>
  </div>
</div>

<?php
/* End of file widget.php */
/* Location: ./module/admin/widget.php */
