<?php
	if(!defined('__AFOX__')) exit();

	$_DATA['page'] = empty($_DATA['page'])?1:$_DATA['page'];
	$search = empty($_DATA['search'])?null:'%'.$_DATA['search'].'%';
	$vs_list = DB::gets(_AF_VISITOR_TABLE_, 'SQL_CALC_FOUND_ROWS *', [
		'(_OR_)' =>empty($search)?[]:['vs_agent{LIKE}'=>$search, 'vs_referer{LIKE}'=>$search]
	],'vs_regdate', (($_DATA['page']-1)*20).',20');
	if($error = DB::error()) $error = set_error($error->getMessage(),$error->getCode());
	$vs_list = setDataListInfo($vs_list, $_DATA['page'], 20, DB::foundRows());
?>

<table class="table">
<thead>
	<tr>
		<th scope="col">#<?php echo getLang('ip')?></th>
		<th scope="col" class="text-wrap"><?php echo getLang('agent')?></th>
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
		$current_page = $vs_list['current_page'];
		$total_page = $vs_list['total_page'];
		$start_page = $vs_list['start_page'];
		$end_page = $vs_list['end_page'];

		foreach ($vs_list['data'] as $key => $value) {
			echo '<tr><th scope="row">'.$value['mb_ipaddress'].'</th>';
			echo '<td class="text-wrap">'.escapeHtml($value['vs_agent']).(empty($value['vs_referer'])?'':'<br><a href="'.$value['vs_referer'].'" target="_blank">'.escapeHtml($value['vs_referer']).'</a>').'</td>';
			echo '<td>'.date('y/m/d h:i', strtotime($value['vs_regdate'])).'</td></tr>';
		}
	}
?>

</tbody>
</table>

<div class="d-flex w-100 justify-content-between mt-4">
	<form class="form-inline search-form" action="<?php echo getUrl('') ?>" method="get">
		<input type="hidden" name="admin" value="<?php echo $_DATA['disp'] ?>">
		<div class="input-group mb-3">
			<label class="input-group-text bg-transparent" for="search"><svg class="bi" aria-hidden="true"><use xlink:href="<?php echo _AF_URL_?>module/admin/img/icons.svg#search"/></svg></label>
			<input type="text" name="search" id="search" value="<?php echo empty($_DATA['search'])?'':$_DATA['search'] ?>" class="form-control" style="max-width:140px;border-left:0" required>
			<button class="btn btn-default btn-outline-control" style="border-color:var(--bs-border-color)" type="submit"><?php echo getLang('search') ?></button>
			<?php if(!empty($_DATA['search'])) {?><button class="btn btn-default btn-outline-control" type="button" onclick="location.replace('<?php echo getUrl('search','') ?>')"><?php echo getLang('cancel') ?></button><?php }?>
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
/* End of file visit.php */
/* Location: ./module/admin/visit.php */
