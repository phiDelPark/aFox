<?php
	if(!defined('__AFOX__')) exit();
	$_GET['page'] = @$_GET['page']?$_GET['page']:1;
	$search = @$_GET['search']?'%'.$_GET['search'].'%':null;
	$board_list = DB::gets(_AF_MODULE_TABLE_, 'SQL_CALC_FOUND_ROWS *', [
		'md_key'=>'board',
		'_OR_' =>empty($search)?[]:['md_id{LIKE}'=>$search, 'md_title{LIKE}'=>$search]
	], 'md_regdate', (($_GET['page']-1)*20).',20');
	if($error = DB::error()) $error = set_error($error->getMessage(),$error->getCode());
	$board_list = setDataListInfo($board_list, $_GET['page'], 20, DB::foundRows());
?>

<a class="btn btn-primary mb-3" style="width:250px" href="<?php echo getUrl('bo_id', '@new')?>"><?php echo getLang('new_board')?></a>

<table class="table">
<thead>
	<tr>
		<th scope="col"><?php echo getLang('id')?></th>
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
		$current_page = $board_list['current_page'];
		$total_page = $board_list['total_page'];
		$start_page = $board_list['start_page'];
		$end_page = $board_list['end_page'];

		$grants = ['0'=>'A','1'=>'M','m'=>'S'];
		foreach ($board_list['data'] as $key => $value) {
			echo '<tr><th scope="row" ><a href="'._AF_URL_.'?id='.$value['md_id'].'" target="_blank">'.$value['md_id'].'</a></th>';
			echo '<td class="text-wrap">'.escapeHTML(cutstr(strip_tags($value['md_title'].(empty($value['md_about'])?'':' - '.$value['md_about'])),50)).'</td>';
			echo '<td class="fixed-width">'.$grants[$value['grant_list']].$grants[$value['grant_view']].$grants[$value['grant_write']].$grants[$value['grant_reply']].$grants[$value['grant_upload']].$grants[$value['grant_download']].'</td>';
			echo '<td class="d-none d-md-table-cell">'.date('Y/m/d', strtotime($value['md_regdate'])).'</td>';
			echo '<td><a class="btn btn-primary btn-sm" href="'.getUrl('bo_id', $value['md_id']).'">'.getLang('setup').'</a></td></tr>';
		}
	}
?>

</tbody>
</table>

<div class="d-flex w-100 justify-content-between mt-4">
	<form action="<?php echo getUrl('') ?>" method="get">
		<input type="hidden" name="admin" value="<?php echo $_GET['disp'] ?>">
		<div class="input-group mb-3">
			<label class="input-group-text bg-transparent" for="search"<?php echo @$_GET['search']?' onclick="location.replace(\''.getUrl('search','').'\')"':''?>><svg class="bi" aria-hidden="true"><use href="<?php echo _AF_URL_?>module/admin/bi-icons.svg#<?php echo @$_GET['search']?'x-lg':'search'?>"/></svg></label>
			<input type="text" name="search" id="search" value="<?php echo @$_GET['search']?$_GET['search']:''?>" class="form-control" style="max-width:140px;border-left:0" required>
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

<?php
/* End of file board.php */
/* Location: ./module/admin/board.php */
