<?php
	if(!defined('__AFOX__')) exit();
	$schs = [];
	$_POST['page'] = empty($_POST['page'])?1:$_POST['page'];

	if(!empty($_POST['search'])) {
		$search = $_POST['search'];
		$schkeys = ['title'=>'wr_title','content'=>'wr_content','nick'=>'mb_nick','tag'=>'wr_tags','date'=>'wr_regdate','cate'=>'wr_category'];
		$ss = explode(':', $search);
		if(count($ss)>1 && !empty($schkeys[$ss[0]])) {
			$search = trim(implode(':', array_slice($ss,1)));
			if(!empty($search)) $schs = [$schkeys[$ss[0]].'{LIKE}'=>($ss[0]==='date'?'':'%').$search.'%'];
		} else {
			$schs = ['wr_title{LIKE}'=>'%'.$search.'%', 'wr_content{LIKE}'=>'%'.$search.'%'];
		}
	}

	$category = empty($_POST['category'])?null:$_POST['category'];
	$doc_list = DB::gets(_AF_DOCUMENT_TABLE_, 'SQL_CALC_FOUND_ROWS *', [
		'md_id'.(empty($category)?'{<>}':'')=>empty($category)?'_AFOXtRASH_':$category,
		'(_OR_)' =>$schs
	],'wr_regdate', (($_POST['page']-1)*20).',20');
	if($error = DB::error()) $error = set_error($error->getMessage(),$error->getCode());
	$doc_list = setDataListInfo($doc_list, $_POST['page'], 20, DB::foundRows());
?>

<table class="table">
<thead>
	<tr>
		<th scope="col"><a href="#DataManageAction"><?php echo getLang('data_manage')?></a></th>
		<th scope="col" class="text-wrap"><?php echo getLang('title')?></th>
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
			echo '<tr><th scope="row"><a href="'.getUrl('category',$value['md_id']).'">'.$value['md_id'].'</a></th>';
			echo '<td class="text-wrap"><a href="./?srl='.$value['wr_srl'].'" target="_blank">'.escapeHtml(cutstr(strip_tags($value['wr_title']),50)).(empty($value['wr_reply'])?'':' (<small>'.$value['wr_reply'].'</small>)').'</a></td>';
			echo '<td>'.escapeHtml($value['mb_nick'],true).'</td>';
			echo '<td>'.($value['wr_secret']?'S/':'--/').($value['wr_status']?$value['wr_status']:'--').'</td>';
			echo '<td>'.date('Y/m/d', strtotime($value['wr_regdate'])).'</td></tr>';
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
/* End of file document.php */
/* Location: ./module/admin/document.php */
