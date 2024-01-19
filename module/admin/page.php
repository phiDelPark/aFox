<?php
	if(!defined('__AFOX__')) exit();

	$md = _AF_MODULE_TABLE_;
	$pg = _AF_PAGE_TABLE_;

	$search = empty($_POST['search'])?'':'\''.DB::escape('%'.$_POST['search'].'%').'\'';
	$where = empty($search) ? '1' : '('.$md.'.md_title LIKE '.$search.' OR '.$pg.'.pg_content LIKE '.$search.')';
	$page = (int)isset($_POST['page']) ? (($_POST['page'] < 1) ? 1 : $_POST['page']) : 1;
	$count = 20;
	$start = (($page - 1) * $count);

	$page_list = DB::query("SELECT SQL_CALC_FOUND_ROWS * FROM $pg INNER JOIN $md ON $md.md_id = $pg.md_id WHERE $where ORDER BY $pg.pg_regdate DESC LIMIT $start,$count",true);
	if($error = DB::error()) $error = set_error($error->getMessage(),$error->getCode());
	$page_list = setDataListInfo($page_list, $page, $count, DB::foundRows());

	$_type = ['TEXT','MARKDOWN','HTML'];

?>

<a class="btn btn-primary mb-3" style="width:250px" href="<?php echo getUrl('mid', '.')?>"><?php echo getLang('new_page')?></a>

<table class="table">
<thead>
	<tr>
		<th scope="col">#<?php echo getLang('id')?></th>
		<th scope="col" class="text-wrap"><?php echo getLang('title')?></th>
		<th scope="col"><?php echo getLang('grant')?></th>
		<th scope="col" class="d-none d-md-table-cell"><?php echo getLang('date')?></th>
		<th scope="col" class="text-end"><?php echo getLang('setup')?></th>
	</tr>
</thead>
<tbody>

<?php
	$end_page = $total_page = 0;
	$start_page = $current_page = 1;

	if($error) {
		messageBox($error['message'], $error['error'], false);
	} else {
		if(count($page_list) > 0) {
			$current_page = $page_list['current_page'];
			$total_page = $page_list['total_page'];
			$start_page = $page_list['start_page'];
			$end_page = $page_list['end_page'];

			foreach ($page_list['data'] as $key => $value) {
				$pg_type = $_type[(int)$value['pg_type']];
				echo '<tr><th scope="row"><a href="'._AF_URL_.'?id='.$value['md_id'].'" target="_blank">'.$value['md_id'].'</a></th>';
				echo '<td class="text-wrap">'.escapeHtml(cutstr(strip_tags($value['md_title']),50)).'</td>';
				echo '<td>'.$value['grant_view'].'-'.$value['grant_reply'].'-'.$value['grant_download'].'</td>';
				echo '<td class="d-none d-md-table-cell">'.date('Y/m/d', strtotime($value['pg_update'])).'</td>';
				echo '<td><a class="btn btn-primary btn-sm" href="'.getUrl('mid', $value['md_id']).'">'.getLang('setup').'</a></td></tr>';
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
		<li class="page-item"><a class="page-link <?php echo $current_page <= 1 ? ' disabled" aria-disabled="true' : ''?>" href="<?php echo  $current_page <= 1 ? '#' : getUrl('page',$current_page-1)?>" aria-label="Previous"><span aria-hidden="true">&lsaquo;</span> <?php echo getLang('previous') ?></a></li>
		<li class="page-item d-md-none"><a class="page-link disabled" aria-disabled="true"><?php echo $current_page.' / '.$total_page?></a></li>
		<?php for ($i=$start_page; $i <= $end_page; $i++) echo '<li class="page-item d-none d-md-inline-block"><a class="page-link'.($current_page == $i ? ' active" aria-current="page' : '').'" href="'.getUrl('page',$i).'">'.$i.'</a></li>' ?>
		<li class="page-item"><a class="page-link<?php echo $current_page >= $total_page ? ' disabled" aria-disabled="true' : ''?>" href="<?php echo $current_page >= $total_page ? '#' : getUrl('page',$current_page+1)?>" aria-label="Next"><?php echo getLang('next') ?> <span aria-hidden="true">&rsaquo;</span></a></li>
		<?php if(($total_page-$end_page)>0) echo '<li class="page-item"><a class="page-link" href="'.getUrl('page',$end_page+1).'">&raquo;</a></li>' ?>
	</ul>
	</nav>
</div>

<?php
/* End of file page.php */
/* Location: ./module/admin/page.php */
