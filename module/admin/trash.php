<?php
	if(!defined('__AFOX__')) exit();

	$search = '';
	$_POST['trash'] = empty($_POST['trash']) ? null : $_POST['trash'];

	if($_POST['trash'] == 'comment') {
		$cd = _AF_COMMENT_TABLE_;
		$dd = _AF_DOCUMENT_TABLE_;
		if(!empty($_POST['search'])) {
			$schkeys = ['content'=>'rp_content','nick'=>'mb_nick','date'=>'rp_regdate'];
			$search = $cd.'.rp_content LIKE \''.DB::escape('%'.$_POST['search'].'%').'\'';
		}
	} else if($_POST['trash'] == 'file') {
		$cd = _AF_FILE_TABLE_;
		$dd = _AF_DOCUMENT_TABLE_;
		if(!empty($_POST['search'])) {
			$schkeys = ['name'=>'mf_name','desc'=>'mf_description','type'=>'mf_type','date'=>'mf_regdate'];
			$search = '('.$cd.'.mf_name LIKE \''.DB::escape('%'.$_POST['search'].'%').'\' OR '.$cd.'.mf_description LIKE \''.DB::escape('%'.$_POST['search'].'%').'\')';
		}
	} else {
		$cd = _AF_DOCUMENT_TABLE_;
		$dd = _AF_DOCUMENT_TABLE_;
		if(!empty($_POST['search'])) {
			$schkeys = ['title'=>'wr_title','content'=>'wr_content','nick'=>'mb_nick','tags'=>'wr_tags','date'=>'wr_regdate'];
			$search = '(wr_title LIKE \''.DB::escape('%'.$_POST['search'].'%').'\' OR wr_content LIKE \''.DB::escape('%'.$_POST['search'].'%').'\')';
		}
	}

	if(!empty($_POST['search'])) {
		$tmp = $_POST['search'];
		$ss = explode(':', $tmp);
		if(count($ss)>1 && !empty($schkeys[$ss[0]])) {
			$tmp = trim(implode(':', array_slice($ss,1)));
			if(!empty($tmp)) $search = $cd.'.'.$schkeys[$ss[0]].' LIKE \''.DB::escape(($ss[0]==='date'?'':'%').$tmp.'%').'\'';
		}
	}

	$category = $dd.'.md_id = \'_AFOXtRASH_\''.(empty($_POST['category'])?'':' AND wr_updater = \''.DB::escape($_POST['category']).'\'');
	$where = empty($search)&&empty($category) ? '1' : '('.$category.(empty($search)||empty($category) ? '' : ' AND ').$search.')';
	$page = (int)isset($_POST['page']) ? (($_POST['page'] < 1) ? 1 : $_POST['page']) : 1;
	$count = 20;
	$start = (($page - 1) * $count);

	if($_POST['trash'] == 'comment') {
		$query = "SELECT SQL_CALC_FOUND_ROWS $cd.*, $dd.md_id, $dd.wr_srl, $dd.wr_updater, $dd.wr_update, $cd.rp_content AS wr_title, $cd.rp_status AS wr_status, $cd.rp_secret AS wr_secret, $cd.rp_regdate AS wr_regdate FROM $cd INNER JOIN $dd ON $dd.wr_srl = $cd.wr_srl WHERE $where ORDER BY $cd.rp_regdate DESC LIMIT $start,$count";
	} else if($_POST['trash'] == 'file') {
		$query = "SELECT SQL_CALC_FOUND_ROWS $cd.*, $dd.md_id, $dd.wr_srl, $dd.wr_updater, $dd.wr_update, $cd.mf_name AS wr_title, $cd.mf_download AS wr_status, $cd.mf_regdate AS wr_regdate FROM $cd INNER JOIN $dd ON $dd.wr_srl = $cd.mf_target WHERE $where ORDER BY $cd.mf_regdate DESC LIMIT $start,$count";
	} else {
		$query = "SELECT SQL_CALC_FOUND_ROWS * FROM $cd WHERE $where ORDER BY wr_regdate DESC LIMIT $start,$count";
	}

	$trash_list = DB::query($query, true);
	if($error = DB::error()) $error = set_error($error->getMessage(),$error->getCode());
	$trash_list = setDataListInfo($trash_list, $page, $count, DB::foundRows());
?>

<form method="post" autocomplete="off" enctype="multipart/form-data" onsubmit="return confirm('<?php echo getLang('confirm_empty',['trash_bin'])?>')">
	<input type="hidden" name="success_url" value="<?php echo getUrl('mid', '', 'md_id', '')?>" />
	<input type="hidden" name="module" value="admin" />
	<input type="hidden" name="act" value="emptyTrashBin" />
	<button type="submit" class="btn btn-sm btn-danger float-end"><?php echo getLang('empty_trash_bin')?></button>
</form>

<ul class="nav nav-tabs">
  <li class="nav-item">
    <a class="nav-link<?php echo ($_POST['trash']!='comment'&&$_POST['trash']!='file')?' active" aria-current="page':''?>" href="<?php echo getUrl('trash','document')?>"><?php echo getLang('document')?></a>
  </li>
  <li class="nav-item">
    <a class="nav-link<?php echo ($_POST['trash']=='comment')?' active" aria-current="page':''?>" href="<?php echo getUrl('trash','comment')?>"><?php echo getLang('comment')?></a>
  </li>
  <li class="nav-item">
    <a class="nav-link<?php echo ($_POST['trash']=='file')?' active" aria-current="page"':''?>" href="<?php echo getUrl('trash','file')?>"><?php echo getLang('file')?></a>
  </li>
</ul>

<div class="clearfix"></div>
<table class="table">
<thead>
	<tr>
		<th scope="col">#</th>
		<th scope="col" class="text-wrap"><?php echo getLang('title')?></th>
		<th scope="col"><?php echo ($_POST['trash'] == 'file'?'-':getLang('author'))?></th>
		<th scope="col"><?php echo ($_POST['trash'] == 'file'?'&raquo;':getLang('status'))?></th>
		<th scope="col" class="d-none d-md-table-cell"><?php echo getLang('date')?></th>
		<th scope="col"><?php echo getLang('removed_date')?></th>
	</tr>
</thead>
<tbody>

<?php
	$end_page = $total_page = 0;
	$start_page = $current_page = 1;

	if($error) {
		messageBox($error['message'], $error['error'], false);
	} else {
		$current_page = $trash_list['current_page'];
		$total_page = $trash_list['total_page'];
		$start_page = $trash_list['start_page'];
		$end_page = $trash_list['end_page'];



		foreach ($trash_list['data'] as $key => $value) {
			if($_POST['trash'] == 'comment') {
				$tmp = 'rp='.$value['rp_srl'];
			} else if($_POST['trash'] == 'file') {
				$tmp = 'srl='.$value['mf_target'];
			} else {
				$tmp = 'srl='.$value['wr_srl'];
			}
			echo '<tr><th scope="row"><a href="'.getUrl('category',$value['wr_updater']).'">'.$value['wr_updater'].'</a></th>';
			echo '<td class="text-wrap"><a href="#./?'.$tmp.'" onclick="return (alert(\'trash\')||false)">'.escapeHtml(cutstr(strip_tags($value['wr_title']),50)).(empty($value['wr_reply'])?'':' (<small>'.$value['wr_reply'].'</small>)').'</a></td>';
			echo '<td>'.($_POST['trash'] == 'file'?'-':escapeHtml($value['mb_nick'],true)).'</td>';
			echo '<td>'.($_POST['trash'] != 'file'&&$value['wr_secret']?'S/':'--/').($value['wr_status']?$value['wr_status']:'--').'</td>';
			echo '<td class="d-none d-md-table-cell">'.date('Y/m/d', strtotime($value['wr_regdate'])).'</td>';
			echo '<td>'.date('Y/m/d', strtotime($value['wr_update'])).'</td></tr>';
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
/* End of file trash.php */
/* Location: ./module/admin/trash.php */
