<?php
if(!defined('__AFOX__')) exit();

@include_once _AF_MODULES_PATH_ . $_DATA['mid'] . '/lang/' . _AF_LANG_ . '.php';

	$_mids = [];
	$_count = 20;
	$_this = getModule('search');

	if(!empty($_this)) {
		$_mids = empty($_this['md_extra'])?[]:unserialize($_this['md_extra']);
		if(!empty($_mids)) $_mids = $_mids['md_ids'];
		$_count = empty($_this['md_list_count'])?20:$_this['md_list_count'];
	}

	$_list = getDBList(_AF_MODULE_TABLE_, ['md_key'=>'board']);
?>

<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">
			<?php echo getLang('combine_search')?>
			<small><?php echo getLang('setup')?></small>
		</h1>
		<ol class="breadcrumb">
			<li class="active">
				<i class="glyphicon glyphicon-th-large" aria-hidden="true"></i>
				<?php echo getLang('info_combine_search')?>
			</li>
		</ol>
	</div>
</div>

<form action="<?php echo _AF_URL_ ?>" method="post" autocomplete="off" enctype="multipart/form-data">
	<input type="hidden" name="success_return_url" value="<?php echo getUrl('', 'admin', 'module', 'mid', 'search') ?>">
	<input type="hidden" name="error_return_url" value="<?php echo getUrl('', 'admin', 'module', 'mid', 'search') ?>">
	<input type="hidden" name="module" value="search">
	<input type="hidden" name="act" value="updateSetup">

<div class="form-group">
	<label><?php echo getLang('list_count')?></label>
	<div class="form-inline">
		<div class="input-group">
			<label class="input-group-addon" for="id_list_count"><?php echo getLang('document_count')?></label>
			<input type="number" class="form-control" id="id_list_count" name="md_list_count" min="1" max="9999" maxlength="5" placeholder="<?php echo getLang('Count')?>" value="<?php echo $_count ?>">
		</div>
	</div>
	<p class="help-block"><?php echo getLang('desc_list_count')?></p>
</div>

<p>
<div class="panel panel-info" role="alert">
	<div class="panel-body">
	<?php echo getLang('desc_combine_search')?>
	</div>
</div>
</p>

<table class="table table-hover table-nowrap">
<thead>
	<tr>
		<th class="col-xs-1"><input type="checkbox" style="margin-right:5px" onclick="$(this).closest('table').find('.data_selecter').prop('checked', $(this).is(':checked'))"><?php echo getLang('id')?></th>
		<th><?php echo getLang('title')?></th>
	</tr>
</thead>
<tbody>

<?php
	$end_page = $total_page = 0;
	$start_page = $current_page = 1;

	if(!empty($_list['error'])) {
		messageBox($_list['message'], $_list['error'], false);
	} else {
		$current_page = $_list['current_page'];
		$total_page = $_list['total_page'];
		$start_page = $_list['start_page'];
		$end_page = $_list['end_page'];

		foreach ($_list['data'] as $key => $value) {
			echo '<tr><th scope="row"><input type="checkbox" name="md_ids[]" value="'.$value['md_id'].'" class="data_selecter" style="margin-right:5px" except-event'.(array_search($value['md_id'], $_mids)===false?'':' checked').'>'.$value['md_id'].'</th>';
			echo '<td>'.escapeHtml(cutstr(strip_tags($value['md_title'].(empty($value['md_description'])?'':' - '.$value['md_description'])),50)).'</td></tr>';
		}
	}
?>

</tbody>
</table>

	<div class="modal-footer">
		<button type="submit" class="btn btn-success mw-20"><i class="glyphicon glyphicon-ok" aria-hidden="true"></i> <?php echo getLang('save')?></button>
	</div>

</form>

<?php
/* End of file setup.php */
/* Location: ./module/search/setup.php */