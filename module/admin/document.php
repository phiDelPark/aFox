<?php if(!defined('__AFOX__')) exit();

	$_GET['page'] = @$_GET['page']?$_GET['page']:1;
	$search = @$_GET['search'] ? trim($_GET['search']): '';
	$category = @$_GET['category'] ? $_GET['category']: null;
	$_wheres = [
		'md_id'.(empty($category)?'{<>}':'')=>empty($category)?'_AFOXtRASH_':$category,
		"_AND_" => [], "_OR_" => []
	];

	if (!empty($search)) {
		$keys = [
			"!" => "wr_title", //!title
			":" => "wr_regdate", //:202010
			"+" => "wr_tags", //+tag
			"?" => "mb_nick", //?nick
		];
		$key = array_key_exists($key = substr($search, 0, 1) , $keys) ? $keys[$key] : '';
		empty($key) ? ($key = "wr_content") : ($search = substr($search, 1));
		if ($search = explode(" ", $search)) {
			$index = 0;
			foreach ($search as $value) {
				$value = explode("&", trim($value));
				$and_or = count($value) > 1 ? "_AND_" : "_OR_";
				foreach ($value as $v) {
					$cmd = $key == "wr_tags" ? '{REGEXP}' : '{LIKE}';
					if ($key == "wr_regdate") {
						$v = str_split($v, 4);
						$v = $v[0].(empty($v[1])?"":"-".implode("-",str_split($v[1],2)))."%";
					} else if ($cmd == '{REGEXP}') {
						$v = "('(^|,)".DB::escape($v)."($|,)')";
					} else {
						$v = "%" . $v. "%";
					}
					$_wheres[$and_or][$key . $cmd . '[' . $index++ . ']'] = $v;
				}
			}
		}
	}

	$doc_list = DB::gets(_AF_DOCUMENT_TABLE_, 'SQL_CALC_FOUND_ROWS *', $_wheres,'wr_regdate', (($_GET['page']-1)*20).',20');
	if($error = DB::error()) $error = set_error($error->getMessage(),$error->getCode());
	$doc_list = setDataListInfo($doc_list, $_GET['page'], 20, DB::foundRows());
?>

<form id="af_check_items" method="post">
<input type="hidden" name="error_url" value="<?php echo getUrl()?>" />
<input type="hidden" name="success_url" value="<?php echo getUrl()?>" />
<table class="table">
<thead>
	<tr>
		<th scope="col"><a href="#" onclick="return _showCheckItems(this)"><?php echo getLang('data_manage')?></a></th>
		<th scope="col" class="text-wrap"><input class="me-3 d-none" type="checkbox" onchange="_allCheckItems(this)"><small class="d-none">[ <a href="#" onclick="return _deleteCheckItems(this)"><?php echo getLang('delete')?></a> ] [ <a href="#" onclick="return _moveCheckItems(this)"><?php echo getLang('move')?></a> ]</small><span><?php echo getLang('title')?></span></th>
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
		$current_page = $doc_list['current_page'];
		$total_page = $doc_list['total_page'];
		$start_page = $doc_list['start_page'];
		$end_page = $doc_list['end_page'];

		foreach ($doc_list['data'] as $key => $value) {
			echo '<tr><td scope="row"><a class="text-light" href="'.getUrl('category',$value['md_id']).'">'.$value['md_id'].'</a></td>';
			echo '<td class="text-wrap"><input class="me-3 d-none" type="checkbox" name="wr_srls[]" value="'.$value['wr_srl'].'"><a href="./?srl='.$value['wr_srl'].'" target="_blank">'.escapeHTML(cutstr(strip_tags($value['wr_title']),50)).'</a>'.(empty($value['wr_reply'])?'':' <small>('.$value['wr_reply'].')</small>').'</td>';
			echo '<td>'.$value['mb_nick'].'</td>';
			echo '<td>'.($value['wr_secret']?'S/':'--/').($value['wr_status']?$value['wr_status']:'--').'</td>';
			echo '<td>'.date('Y/m/d', strtotime($value['wr_regdate'])).'</td></tr>';
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
	function _moveCheckItems() {
		const id = prompt($_LANG['prompt_move_board'], '').trim()
		if (id) {
			exec_ajax({module:'admin',act:'moveDocuments',md_id:id,...document.querySelector('#af_check_items').serializeArray()})
			.then((data)=>{location.href = data['redirect_url']}).catch((error)=>{console.log(error);alert(error)})
		}
		return false;
	}
	function _deleteCheckItems() {
		if (confirm($_LANG['confirm_delete'].sprintf([$_LANG['document']])) === true) {
			exec_ajax({module:'admin',act:'deleteDocuments',...document.querySelector('#af_check_items').serializeArray()})
			.then((data)=>{location.href = data['redirect_url']}).catch((error)=>{console.log(error);alert(error)})
		}
		return false;
	}
</script>

<?php
/* End of file document.php */
/* Location: ./module/admin/document.php */
