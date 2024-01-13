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

<nav class="d-flex w-100 justify-content-between mt-4" aria-label="Page navigation of the list">
	<form class="form-inline search-form" action="<?php echo getUrl('') ?>" method="get">
		<input type="hidden" name="admin" value="<?php echo $_DATA['disp'] ?>">
		<div class="input-group mb-3">
		<input type="text" name="search" value="<?php echo empty($_DATA['search'])?'':$_DATA['search'] ?>" class="form-control" style="max-width:160px" placeholder="<?php echo getLang('search_word') ?>" required>
		<button class="btn btn-default btn-outline-secondary" type="submit"><i class="glyphicon glyphicon-search" aria-hidden="true"></i> <?php echo getLang('search') ?></button>
		<?php if(!empty($_DATA['search'])) {?><button class="btn btn-default btn-outline-secondary" type="button" onclick="location.replace('<?php echo getUrl('search','') ?>')"><?php echo getLang('cancel') ?></button><?php }?>
		</div>
	</form>
	<div id="pageNavigation">
	<?php if($start_page>10) echo '<a class="btn btn-sm btn-outline-primary rounded-pill" href="'.getUrl('page',$start_page-10).'">&laquo;</a>' ?>
	<a class="btn btn-sm rounded-pill btn-outline-<?php echo $current_page <= 1 ? 'secondary disabled" aria-disabled="true' : 'primary'?>" href="<?php echo  $current_page <= 1 ? '#' : getUrl('page',$current_page-1)?>" aria-label="Previous"><span aria-hidden="true">&lsaquo;</span> <?php echo getLang('previous') ?></a>
	<a class="d-md-none btn btn-sm btn-outline-secondary rounded-pill disabled" aria-disabled="true"><?php echo $current_page.' / '.$total_page?></a>
	<?php for ($i=$start_page; $i <= $end_page; $i++) echo '<a class="d-none d-md-inline-block btn btn-sm btn-outline-primary rounded-pill'.($current_page == $i ? ' active" aria-current="page' : '').'" href="'.getUrl('page',$i).'">'.$i.'</a>' ?>
	<a class="btn btn-sm rounded-pill btn-outline-<?php echo $current_page >= $total_page ? 'secondary disabled" aria-disabled="true' : 'primary'?>" href="<?php echo $current_page >= $total_page ? '#' : getUrl('page',$current_page+1)?>" aria-label="Next"><?php echo getLang('next') ?> <span aria-hidden="true">&rsaquo;</span></a>
	<?php if(($total_page-$end_page)>0) echo '<a class="btn btn-sm btn-outline-primary rounded-pill" href="'.getUrl('page',$end_page+1).'">&raquo;</a>' ?>
	</div>
</nav>

<?php
/* End of file visit.php */
/* Location: ./module/admin/visit.php */
