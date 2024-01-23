<?php
	if(!defined('__AFOX__')) exit();

	$duplicate = !empty($_POST['duplicate']);
	$page = (int)isset($_POST['page']) ? (($_POST['page'] < 1) ? 1 : $_POST['page']) : 1;
	$count = $duplicate ? 30 : 20;
	$start = (($page - 1) * $count);

	$fl = _AF_FILE_TABLE_;
	$dd = _AF_DOCUMENT_TABLE_;

	if($duplicate){
		$file_list = DB::query("SELECT SQL_CALC_FOUND_ROWS a.*, d.wr_title FROM $fl as a INNER JOIN $dd as d ON (d.md_id <> '_AFOXtRASH_' and d.wr_srl = a.mf_target), (select mf_target,mf_name,mf_size from $fl where mf_link<>1 and mf_size>0 group by mf_name,mf_size having count(*) > 1) as b WHERE a.mf_link<>1 and a.mf_size=b.mf_size AND a.mf_name=b.mf_name ORDER BY a.mf_name,a.mf_regdate LIMIT $start,$count" , true);
	}else {
		$search = '';
		if(!empty($_POST['search'])) {
			$tmp = $_POST['search'];
			$schkeys = ['name'=>'mf_name','desc'=>'mf_description','type'=>'mf_type','date'=>'mf_regdate'];
			$ss = explode(':', $tmp);
			if(count($ss)>1 && !empty($schkeys[$ss[0]])) {
				$tmp = trim(implode(':', array_slice($ss,1)));
				if(!empty($tmp)) $search = 'f.'.$schkeys[$ss[0]].' LIKE \''.DB::escape(($ss[0]==='date'?'':'%').$tmp.'%').'\'';
			} else {
				$search = '(f.mf_name LIKE \''.DB::escape('%'.$_POST['search'].'%').'\' OR f.mf_description LIKE \''.DB::escape('%'.$_POST['search'].'%').'\')';
			}
		}

		$category = 'd'.(empty($_POST['category'])?'.md_id <> \'_AFOXtRASH_\'':'.md_id = \''.DB::escape($_POST['category']).'\'');
		$where = empty($search)&&empty($category) ? '1' : '('.$category.(empty($search)||empty($category) ? '' : ' AND ').$search.')';
		$file_list = DB::query("SELECT SQL_CALC_FOUND_ROWS f.*, d.md_id FROM $fl as f INNER JOIN $dd as d ON d.wr_srl = f.mf_target WHERE $where ORDER BY f.mf_regdate DESC LIMIT $start,$count", true);
	}
	if($error = DB::error()) $error = set_error($error->getMessage(),$error->getCode());
	$file_list = setDataListInfo($file_list, $page, $count, DB::foundRows());

	if($duplicate) {
		messageBox(getLang('desc_data_combine'), 2, false);
	}
	if($error) {
		messageBox($error['message'], $error['error'], false);
	}
?>
<?php if($duplicate) { ?>
<a class="btn btn-success" href="#" onclick="return data_selected_combine()"><?php echo getLang('data_combine')?></a>
<?php } ?>

<table class="table">
<thead>
	<tr>
		<?php if($duplicate) { ?>
		<th scope="col"><?php echo getLang('select')?></th>
		<th scope="col"><?php echo getLang('module')?></th>
		<th scope="col">.</th>
		<?php } else { ?>
		<th scope="col"><a href="#DataManageAction"><?php echo getLang('data_manage')?></a></th>
		<?php } ?>
		<th scope="col" class="text-wrap"><?php echo getLang('name')?></th>
		<?php if($duplicate) { ?>
		<th scope="col"><?php echo getLang('size')?></th>
		<?php } else { ?>
		<th scope="col">&raquo;</th>
		<?php } ?>
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
				$filetype = explode('/', $value['mf_type']);
				$filetype = strtolower(array_shift($filetype));
				$filetype = empty($_file_types[$filetype]) ? 'binary' : $filetype;
				$unfilename = _AF_URL_ .'data/attach/'. $filetype . '/' . $value['md_id'] . '/' . $value['mf_target'] . '/' . $value['mf_upload_name'];
				echo '<tr><th scope="row" rowspan="2"><input type="radio" name="mf_standard" value="'.$value['mf_srl'].'" class="data_standard" style="margin-right:5px" data-except-ajax><input type="checkbox" value="'.$value['mf_srl'].'" class="data_selecter" style="margin-right:5px" data-except-ajax></th>';
				echo '<td scope="row" rowspan="2" style="padding:2px"><img src="'.($unfilename).'" width="65" height="65"></td>';
				echo '<td scope="row">'.$value['md_id'].'</td>';
				echo '<td class="title">'.escapeHtml(cutstr($value['mf_name'],50)).'</td>';
				echo '<td class="hidden-xs">'.shortSize($value['mf_size']).'</td>';

			} else {
			echo '<tr><th scope="row"><a href="'.getUrl('category',$value['md_id']).'">'.$value['md_id'].'</a></th>';
			echo '<td class="text-wrap"><a href="./?srl='.$value['mf_target'].'" target="_blank">'.escapeHtml(cutstr($value['mf_name'],50)).'</a></td>';
			echo '<td>'.$value['mf_download'].'</td>';
			}
			echo '<td>'.$value['mb_ipaddress'].'</td>';
			echo '<td>'.date('Y/m/d', strtotime($value['mf_regdate'])).'</td></tr>';
			if($duplicate) {
				echo '<tr><td class="title" colspan="4" style="color:#555;text-decoration:underline"><a href="'.getUrl('','id',$value['md_id'],'srl',$value['mf_target']).'" target="_blank">'.escapeHtml(cutstr($value['wr_title'],50)).'</a></td></tr>';
			}
		}
	}
?>

</tbody>
</table>

<div class="d-flex w-100 justify-content-between mt-4">
	<form action="<?php echo getUrl('') ?>" method="get">
		<input type="hidden" name="admin" value="<?php echo $_POST['disp'] ?>">
		<div class="input-group mb-3">
			<label class="input-group-text bg-transparent" for="search"><svg class="bi" aria-hidden="true"><use href="<?php echo _AF_URL_?>module/admin/bi-icons.svg#search"/></svg></label>
			<input type="text" name="search" id="search" value="<?php echo empty($_POST['search'])?'':$_POST['search'] ?>" class="form-control" style="max-width:140px;border-left:0" required>
			<button class="btn btn-default btn-outline-control" style="border-color:var(--bs-border-color)" type="submit"><?php echo getLang('search') ?></button>
			<?php if(!empty($_POST['search'])) {?><button class="btn btn-default btn-outline-control" type="button" onclick="location.replace('<?php echo getUrl('search','') ?>')"><?php echo getLang('cancel') ?></button><?php }?>
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

<?php
/* End of file file.php */
/* Location: ./module/admin/file.php */
