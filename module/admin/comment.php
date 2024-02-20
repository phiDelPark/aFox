<?php if(!defined('__AFOX__')) exit();

	$_GET['page'] = @$_GET['page']?$_GET['page']:1;

	$cd = _AF_COMMENT_TABLE_;
	$dd = _AF_DOCUMENT_TABLE_;

	$count = 20;
	$search = @$_GET['search'] ? trim($_GET['search']) : '';
	$category = @$_GET['category'] ? $_GET['category'] : '';

	if(!empty($search)) {
		$keys = [
			":" => "wr_regdate", //:202010
			"?" => "mb_nick", //?nick
		];
		$key = array_key_exists($key = substr($search, 0, 1) , $keys) ? $keys[$key] : '';
		empty($key) ? ($key = "rp_content") : ($search = substr($search, 1));
		if ($search = explode(" ", $search)) {
			$index = 0;
			$tmp = '';
			foreach ($search as $value) {
				$value = explode("&", trim($value));
				$and_or = count($value) > 1 ? ' AND ' : ' OR  ';
				foreach ($value as $v) {
					if ($key == "rp_regdate") {
						$v = str_split($v, 4);
						$v = $v[0] . (empty($v[1]) ? "" : "-" . implode("-", str_split($v[1], 2)));
						$tmp .= $and_or.$cd.'.'.$key.' LIKE \''.DB::escape($v.'%').'\'';
					} else {
						$tmp .= $and_or.$cd.'.'.$key.' LIKE \''.DB::escape('%'.$v.'%').'\'';
					}
				}
			}
			$search = '('.substr($tmp, 5).')';
		}
	}

	$category = $dd.($category?'.md_id = \''.$category.'\'':'.md_id <> \'_AFOXtRASH_\'');
	$where = $search||$category ? '('.$category.($search&&$category ? ' AND ' : '').$search.')' : '1';
	$start = (($_GET['page'] - 1) * $count);
	$cmt_list = DB::query("SELECT SQL_CALC_FOUND_ROWS $cd.*, $dd.md_id FROM $cd INNER JOIN $dd ON $dd.wr_srl = $cd.wr_srl WHERE $where ORDER BY $cd.rp_regdate DESC LIMIT $start,$count", true);
	if($error = DB::error()) $error = set_error($error->getMessage(),$error->getCode());
	$cmt_list = setDataListInfo($cmt_list, $_GET['page'], $count, DB::foundRows());
?>

<form id="af_check_items" method="post">
<input type="hidden" name="error_url" value="<?php echo getUrl()?>" />
<input type="hidden" name="success_url" value="<?php echo getUrl()?>" />
<table class="table">
<thead>
	<tr>
		<th scope="col"><a href="#" onclick="return _showCheckItems(this)"><?php echo getLang('data_manage')?></a></th>
		<th scope="col" class="text-wrap"><input class="me-3 d-none" type="checkbox" onchange="_allCheckItems(this)"><small class="d-none">[ <a href="#" onclick="return _deleteCheckItems(this)">DELETE</a> ]</small><span><?php echo getLang('content')?></span></th>
		<th scope="col"><?php echo getLang('author')?></th>
		<th scope="col"><?php echo getLang('status')?></th>
		<th scope="col" class="text-end"><?php echo getLang('date')?></th>
	</tr>
</thead>
<tbody>

<?php
	$end_page = $total_page = 0;
	$start_page = $current_page = 1;

	if($error) {
		messageBox($error['message'], $error['error'], false);
	} else {
		$current_page = $cmt_list['current_page'];
		$total_page = $cmt_list['total_page'];
		$start_page = $cmt_list['start_page'];
		$end_page = $cmt_list['end_page'];

		foreach ($cmt_list['data'] as $key => $value) {
			echo '<tr><td scope="row"><a class="text-light" href="'.getUrl('category',$value['md_id']).'">'.$value['md_id'].'</a></td>';
			echo '<td class="text-wrap"><input class="me-3 d-none" type="checkbox" name="rp_srls[]" value="'.$value['rp_srl'].'"><a href="./?rp='.$value['rp_srl'].'" target="_blank">'.escapeHTML(cutstr(strip_tags($value['rp_content']),50)).'</a></td>';
			echo '<td>'.$value['mb_nick'].'</td>';
			echo '<td>'.($value['rp_secret']?'S/':'--/').($value['rp_status']?$value['rp_status']:'--').'</td>';
			echo '<td>'.date('Y/m/d', strtotime($value['rp_regdate'])).'</td></tr>';
		}
	}
?>

</tbody>
</table>
</form>

<div class="d-flex w-100 justify-content-between mt-4">
	<form action="<?php echo getUrl('') ?>" method="get">
		<input type="hidden" name="admin" value="<?php echo $_GET['disp'] ?>">
		<div class="input-group mb-3">
			<label class="input-group-text bg-transparent" for="search"<?php echo @$_GET['search']?' onclick="location.replace(\''.getUrl('search','').'\')"':''?>><svg class="bi" aria-hidden="true"><use href="<?php echo _AF_URL_?>module/admin/bi-icons.svg#<?php echo @$_GET['search']?'x-lg':'search'?>"/></svg></label>
			<input type="text" name="search" id="search" value="<?php echo @$_GET['search']?$_GET['search']:''?>" class="form-control" style="max-width:140px;border-left:0" oninvalid="this.setCustomValidity('<?php echo getLang('search_help_'.$admin)?>')" oninput="this.setCustomValidity('')" required>
			<button class="btn btn-default btn-outline-control" style="border-color:var(--bs-border-color)" type="submit"><?php echo getLang('search') ?></button>
		</div>
	</form>
	<nav aria-label="Page navigation of the list">
	<ul class="pagination">
		<?php if($start_page>10) echo '<li class="page-item"><a class="page-link" href="'.getUrl('page',$start_page-10).'">&laquo;</a></li>' ?>
		<li class="page-item text-nowrap"><a class="page-link <?php echo $current_page <= 1 ? ' disabled" aria-disabled="true' : ''?>" href="<?php echo  $current_page <= 1 ? '#' : getUrl('page',$current_page-1)?>" aria-label="Previous"><span aria-hidden="true">&lsaquo;</span> <?php echo getLang('previous') ?></a></li>
		<li class="page-item d-lg-none"><a class="page-link disabled" aria-disabled="true"><?php echo $current_page.' / '.$total_page?></a></li>
		<?php for ($i=$start_page; $i <= $end_page; $i++) echo '<li class="page-item d-none d-lg-inline-block"><a class="page-link'.($current_page == $i ? ' active" aria-current="page' : '').'" href="'.getUrl('page',$i).'">'.$i.'</a></li>' ?>
		<li class="page-item text-nowrap"><a class="page-link<?php echo $current_page >= $total_page ? ' disabled" aria-disabled="true' : ''?>" href="<?php echo $current_page >= $total_page ? '#' : getUrl('page',$current_page+1)?>" aria-label="Next"><?php echo getLang('next') ?> <span aria-hidden="true">&rsaquo;</span></a></li>
		<?php if(($total_page-$end_page)>0) echo '<li class="page-item"><a class="page-link" href="'.getUrl('page',$end_page+1).'">&raquo;</a></li>' ?>
	</ul>
	</nav>
</div>

<script>
	function _showCheckItems(el_chk) {
		const tb = el_chk.closest('table'), first_chk = tb.querySelector('[type=checkbox]')
		tb.querySelectorAll('[type=checkbox]')?.forEach(el => el.classList.remove('d-none'))
		first_chk.parentNode?.lastChild.classList.add('d-none')
		first_chk.parentNode?.childNodes[1].classList.remove('d-none')
		return false
	}
	function _allCheckItems(el_chk) {
		el_chk.closest('table').querySelectorAll('tbody [type=checkbox]')?.forEach(el => el.checked = el_chk.checked)
	}
	function _deleteCheckItems() {
		if (confirm($_LANG['confirm_delete'].sprintf([$_LANG['comment']])) === true) {
			exec_ajax({module:'admin',act:'deleteComments',...document.querySelector('#af_check_items').serializeArray()})
			.then((data)=>{location.href = data['redirect_url']}).catch((error)=>{console.log(error);alert(error)})
		}
		return false;
	}
</script>

<?php
/* End of file comment.php */
/* Location: ./module/admin/comment.php */
