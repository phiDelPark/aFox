<?php if(!defined('__AFOX__')) exit();

	$_GET['page'] = @$_GET['page']?$_GET['page']:1;

	$duplicate = @$_GET['duplicate'];
	$count = $duplicate ? 30 : 20;
	$start = (($_GET['page'] - 1) * $count);

	$fl = _AF_FILE_TABLE_;
	$dd = _AF_DOCUMENT_TABLE_;

	if($duplicate){
		$file_list = DB::query("SELECT SQL_CALC_FOUND_ROWS a.*, d.wr_title FROM $fl as a INNER JOIN $dd as d ON (d.md_id <> '_AFOXtRASH_' and d.wr_srl = a.mf_target), (select mf_target,mf_name,mf_size from $fl where mf_link<>1 and mf_size>0 group by mf_name,mf_size having count(*) > 1) as b WHERE a.mf_link<>1 and a.mf_size=b.mf_size AND a.mf_name=b.mf_name ORDER BY a.mf_name,a.mf_regdate LIMIT $start,$count" , true);
	}else {
		$search = @$_GET['search'] ? trim($_GET['search']) : '';
		if(!empty($search)) {
			$keys = [
				"!" => "mf_type", //!type
				"?" => "mb_ipaddress", //?ip
				":" => "mf_regdate", //:202010
			];
			$key = array_key_exists($key = substr($search, 0, 1) , $keys) ? $keys[$key] : '';
			empty($key) ? ($key = "mf_name") : ($search = substr($search, 1));
			if ($search = explode(" ", $search)) {
				$index = 0;
				$tmp = '';
				foreach ($search as $value) {
					$value = explode("&", trim($value));
					$and_or = count($value) > 1 ? ' AND ' : ' OR  ';
					foreach ($value as $v) {
						if ($key == "mf_regdate") {
							$v = str_split($v, 4);
							$v = $v[0] . (empty($v[1]) ? "" : "-" . implode("-", str_split($v[1], 2)));
							$tmp .= $and_or.'f.'.$key.' LIKE \''.DB::escape($v.'%').'\'';
						} else {
							$tmp .= $and_or.'f.'.$key.' LIKE \''.DB::escape('%'.$v.'%').'\'';
						}
					}
				}
				$search = '('.substr($tmp, 5).')';
			}
		}
		$category = 'd'.(@$_GET['category']?'.md_id = \''.DB::escape($_GET['category']).'\'':'.md_id <> \'_AFOXtRASH_\'');
		$where = $search||$category ? '('.$category.($search&&$category ? ' AND ' : '').$search.')' : '1';
		$file_list = DB::query("SELECT SQL_CALC_FOUND_ROWS f.*, d.md_id FROM $fl as f INNER JOIN $dd as d ON d.wr_srl = f.mf_target WHERE $where ORDER BY f.mf_regdate DESC LIMIT $start,$count", true);
		$file_list = setDataListInfo($file_list, $_GET['page'], $count, DB::foundRows());
	}
	if($error = DB::error()) messageBox($error->getMessage(), $error->getCode(), false);
	if($duplicate) messageBox(getLang('desc_data_combine'), 2, false);
?>
<?php if($duplicate) { ?>
<a class="btn btn-success" href="#" onclick="return data_selected_combine()"><?php echo getLang('data_combine')?></a>
<?php } ?>

<form id="af_check_items" method="post">
<input type="hidden" name="error_url" value="<?php echo getUrl()?>" />
<input type="hidden" name="success_url" value="<?php echo getUrl()?>" />
<table class="table">
<thead>
	<tr>
		<?php if($duplicate) { ?>
		<th scope="col"><?php echo getLang('select')?></th>
		<th scope="col"><?php echo getLang('module')?></th>
		<th scope="col">.</th>
		<?php } else { ?>
		<th scope="col"><a href="#" onclick="return _showCheckItems(this)"><?php echo getLang('data_manage')?></a></th>
		<?php } ?>
		<th scope="col" class="text-wrap"><input class="me-3 d-none" type="checkbox" onchange="_allCheckItems(this)"><small class="d-none">[ <a href="#" onclick="return _deleteCheckItems(this)">DELETE</a> ]</small><span><?php echo getLang('name')?></span></th>
		<?php if($duplicate) { ?>
		<th scope="col"><?php echo getLang('size')?></th>
		<?php } ?>
		<th scope="col">&raquo;</th>
		<th scope="col"><?php echo getLang('type')?></th>
		<th scope="col"><?php echo getLang('ip')?></th>
		<th scope="col"><?php echo getLang('date')?></th>
	</tr>
</thead>
<tbody>

<?php
	$end_page = $total_page = 0;
	$start_page = $current_page = 1;

	if(!$error) {
		$current_page = $file_list['current_page'];
		$total_page = $file_list['total_page'];
		$start_page = $file_list['start_page'];
		$end_page = $file_list['end_page'];

		foreach ($file_list['data'] as $key => $value) {
			if($duplicate){
				$dutmp1 = isset($file_list['data'][$key + 1]) ? $file_list['data'][$key + 1] : ['mf_size'=>0,'mf_name'=>''];
				$dutmp2 = isset($file_list['data'][$key - 1]) ? $file_list['data'][$key - 1] : ['mf_size'=>0,'mf_name'=>''];
				if(!(($value['mf_size']===$dutmp1['mf_size'] && $value['mf_name']===$dutmp1['mf_name'])
					|| ($value['mf_size']===$dutmp2['mf_size'] && $value['mf_name']===$dutmp2['mf_name']))){
					continue;
				}
			}
			if($duplicate) {
				$_file_types = array('binary'=>0, 'image' => 1, 'video' => 2, 'audio' => 3);
				$filetype = explode('/', strtolower($value['mf_type']));
				$filetype = empty($_file_types[$filetype[0]]) ? 'binary' : $filetype[0];
				$unfilename = _AF_ATTACH_DATA_. $filetype . '/' . $value['md_id'] . '/' . $value['mf_target'] . '/' . $value['mf_upload_name'];
				echo '<tr><th scope="row" rowspan="2"><input type="radio" name="mf_standard" value="'.$value['mf_srl'].'" class="data_standard" style="margin-right:5px" data-except-ajax><input type="checkbox" value="'.$value['mf_srl'].'" class="data_selecter" style="margin-right:5px" data-except-ajax></th>';
				echo '<td scope="row" rowspan="2" style="padding:2px"><img src="'.($unfilename).'" width="65" height="65"></td>';
				echo '<td scope="row">'.$value['md_id'].'</td>';
				echo '<td class="title">'.escapeHTML(cutstr($value['mf_name'],50)).'</td>';
				echo '<td class="hidden-xs">'.shortFileSize($value['mf_size']).'</td>';

			} else {
			echo '<tr><td scope="row"><a class="text-light" href="'.getUrl('category',$value['md_id']).'">'.$value['md_id'].'</a></td>';
			echo '<td class="text-wrap"><input class="me-3 d-none" type="checkbox" name="mf_srls[]" value="'.$value['mf_srl'].'"><a href="./?srl='.$value['mf_target'].'" target="_blank">'.escapeHTML(cutstr($value['mf_name'],50)).'</a></td>';
			}
			echo '<td><small>'.$value['mf_download'].'</small></td>';
			echo '<td><small>'.$value['mf_type'].'</small></td>';
			echo '<td><small>'.$value['mb_ipaddress'].'</small></td>';
			echo '<td>'.date('Y/m/d', strtotime($value['mf_regdate'])).'</td></tr>';
			if($duplicate) {
				echo '<tr><td class="title" colspan="4" style="color:#555;text-decoration:underline"><a href="'.getUrl('','id',$value['md_id'],'srl',$value['mf_target']).'" target="_blank">'.escapeHTML(cutstr($value['wr_title'],50)).'</a></td></tr>';
			}
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
			<label class="input-group-text bg-transparent" for="search"<?php echo @$_GET['search']?' onclick="location.replace(\''.getUrl('search','').'\')"':''?>><svg class="bi" aria-hidden="true"><use href="<?php echo _AF_URL_?>module/admin/bi-icons.svg#<?php echo empty($_GET['search'])?'search':'x-lg'?>"/></svg></label>
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
		if (confirm($_LANG['confirm_delete'].sprintf([$_LANG['file']])) === true) {
			exec_ajax({module:'admin',act:'deleteFiles',...document.querySelector('#af_check_items').serializeArray()})
			.then((data)=>{location.href = data['redirect_url']}).catch((error)=>{console.log(error);alert(error)})
		}
		return false;
	}
</script>

<?php
/* End of file file.php */
/* Location: ./module/admin/file.php */
