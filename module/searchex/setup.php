<?php if(!defined('__AFOX__')) exit();

$_mids = empty($_MODULE['md_extra'])?[]:unserialize($_MODULE['md_extra']);
$_count = empty($_MODULE['md_list_count'])?20:$_MODULE['md_list_count'];

$_list = DB::gets(_AF_MODULE_TABLE_, 'SQL_CALC_FOUND_ROWS *', ['md_key'=>'board']);
if($error = DB::error()) $error = set_error($error->getMessage(),$error->getCode());
?>
<form method="post" autocomplete="off">
<input type="hidden" name="error_url" value="<?php echo getUrl()?>" />
<input type="hidden" name="success_url" value="<?php echo getUrl('md_id','')?>" />
<input type="hidden" name="module" value="<?php echo $_GET['md_id']?>" />
<input type="hidden" name="act" value="updateSetup" />
<input type="hidden" name="md_id" value="searchex" />

<div class="input-group mb-4">
	<div class="input-group">
	<label class="input-group-text w-100p" for="id_md_list_count"><?php echo getLang('list_count')?></label>
		<input type="number" class="form-control mw-100p" id="id_md_list_count" name="md_list_count" min="1" max="9999" maxlength="5" value="<?php echo $_count ?>">
	</div>
	<div class="form-text"><?php echo getLang('desc_list_count')?></div>
</div>

<label class="form-label"><?php echo getLang('desc_combine_search')?></label>
<table class="table table-hover">
<thead>
	<tr>
		<th scope="col"><input type="checkbox" onclick="_allCheckTableItems(this)">
		<?php echo getLang('id')?>
		</th>
		<th scope="col" class="text-wrap"><?php echo getLang('title')?></th>
	</tr>
</thead>
<tbody>
<?php
	$end_page = $total_page = 0;
	$start_page = $current_page = 1;

	if($error) {
		messageBox($error['message'], $error['error']);
	} else {
		foreach ($_list as $key => $value) {
			echo '<tr><th scope="row"><label><input type="checkbox" name="md_ids[]" value="'.$value['md_id'].'" class="data_selecter" style="margin-right:5px"'.(empty($_mids)||array_search($value['md_id'], $_mids)===false?'':' checked').'>'.$value['md_id'].'</label></th>';
			echo '<td class="text-wrap">'.escapeHTML(cutstr(strip_tags($value['md_title'].(empty($value['md_about'])?'':' - '.$value['md_about'])),50)).'</td></tr>';
		}
	}
?>
</tbody>
</table>

<hr class="mb-5">
<div class="text-end position-fixed bottom-0 end-0 p-3">
	<button type="submit" class="btn btn-success btn-lg" style="min-width:220px"><?php echo getLang('save')?></button>
</div>
</form>

<script>
	function _allCheckTableItems(el_chk) {
		let els_chk = el_chk.closest('table').querySelectorAll('tbody [type=checkbox]');
		els_chk.forEach(el => el.checked = el_chk.checked);
	}
</script>

<?php
/* End of file setup.php */
/* Location: ./module/searchex/setup.php */
