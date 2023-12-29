<?php
if(!defined('__AFOX__')) exit();
@include_once dirname(__FILE__) . '/config.php';

$_MD_CONFIG = getModule('@searchex');
$_mids = empty($_MD_CONFIG['md_extra'])?[]:unserialize($_MD_CONFIG['md_extra']);
$_count = empty($_MD_CONFIG['md_list_count'])?20:$_MD_CONFIG['md_list_count'];

$_list = DB::gets(_AF_MODULE_TABLE_, 'SQL_CALC_FOUND_ROWS *', ['md_key'=>'board']);
if($error = DB::error()) $error = set_error($error->getMessage(),$error->getCode());
//$_list = setDataListInfo($_list, $_DATA['page'], 20, DB::foundRows());
?>

<form action="<?php echo _AF_URL_ ?>" method="post" autocomplete="off" enctype="multipart/form-data">
	<input type="hidden" name="success_return_url" value="<?php echo getUrl('', 'admin', 'module', 'mid', $_DATA['mid']) ?>">
	<input type="hidden" name="error_return_url" value="<?php echo getUrl('', 'admin', 'module', 'mid', $_DATA['mid']) ?>">
	<input type="hidden" name="module" value="<?php echo $_DATA['mid'] ?>">
	<input type="hidden" name="act" value="setupmodule">

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

	if($error) {
		messageBox($error['message'], $error['error'], false);
	} else {
		//$current_page = $_list['current_page'];
		//$total_page = $_list['total_page'];
		//$start_page = $_list['start_page'];
		//$end_page = $_list['end_page'];

		foreach ($_list as $key => $value) {
			echo '<tr><th scope="row"><input type="checkbox" name="md_ids[]" value="'.$value['md_id'].'" class="data_selecter" style="margin-right:5px" data-except-ajax'.(empty($_mids)||array_search($value['md_id'], $_mids)===false?'':' checked').'>'.$value['md_id'].'</th>';
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
/* Location: ./module/searchex/setup.php */
