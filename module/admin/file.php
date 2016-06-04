<?php
	if(!defined('__AFOX__')) exit();

	$search = empty($_DATA['search'])?null:'%'.$_DATA['search'].'%';
	$file_list = getDBList(_AF_FILE_TABLE_,[
		'OR' =>empty($search)?[]:['mf_name{LIKE}'=>$search, 'mf_type{LIKE}'=>$search]
	],'mf_regdate desc', empty($_DATA['page']) ? 1 : $_DATA['page'], 20);
?>

<table class="table table-hover table-nowrap">
<thead class="table-nowrap">
	<tr>
		<th class="col-xs-1">#<?php echo getLang('id')?></th>
		<th><?php echo getLang('name')?></th>
		<th class="col-xs-1 hidden-xs"><?php echo getLang('download')?></th>
		<th class="col-xs-1 hidden-xs hidden-sm"><?php echo getLang('ip')?></th>
		<th class="col-xs-1"><?php echo getLang('date')?></th>
	</tr>
</thead>
<tbody>

<?php
	$end_page = $total_page = 0;
	$start_page = $current_page = 1;

	if(!empty($file_list['error'])) {
		echo showMessage($file_list['message'], $file_list['error']);
	} else {
		$current_page = $file_list['current_page'];
		$total_page = $file_list['total_page'];
		$start_page = $file_list['start_page'];
		$end_page = $file_list['end_page'];

		foreach ($file_list['data'] as $key => $value) {
			echo '<tr class="afox-list-item"><th scope="row">'.$value['md_id'].'</th>';
			echo '<td>'.escapeHtml(cutstr($value['mf_name'],50)).'</td>';
			echo '<td class="hidden-xs">'.$value['mf_download'].'</td>';
			echo '<td class="hidden-xs hidden-sm">'.$value['mb_ipaddress'].'</td>';
			echo '<td>'.date('Y/m/d', strtotime($value['mf_regdate'])).'</td></tr>';
		}
	}
?>

</tbody>
</table>

<nav class="navbar clearfix">
	<ul class="pager visible-xs-block visible-sm-block">
		<li class="previous<?php echo $current_page <= 1?' disabled':''?>"><a href="<?php echo  $current_page <= 1 ? '#" onclick="return false' : getUrl('page',$current_page-1)?>" aria-label="Previous"><span aria-hidden="true">&lsaquo;</span> <?php echo getLang('previous') ?></a></li>
		<li><span class="col-xs-5"><?php echo $current_page.' / '.$total_page?></span></li>
		<li class="next<?php echo $current_page >= $total_page?' disabled':''?>"><a href="<?php echo $current_page >= $total_page ? '#" onclick="return false' : getUrl('page',$current_page+1)?>" aria-label="Next"><?php echo getLang('next') ?> <span aria-hidden="true">&rsaquo;</span></a></li>
	</ul>
	<ul class="pagination hidden-xs hidden-sm pull-right">
		<?php if($start_page>10) echo '<li><a href="'.getUrl('page',$start_page-10).'">&laquo;</a></li>'; ?>
		<li<?php echo $current_page <= 1 ? ' class="disabled"' : ''?>><a href="<?php echo  $current_page <= 1 ? '#" onclick="return false' : getUrl('page',$current_page-1)?>" aria-label="Previous"><span aria-hidden="true">&lsaquo;</span></a></li>
		<?php for ($i=$start_page; $i <= $end_page; $i++) echo '<li'.($current_page == $i ? ' class="active"' : '').'><a href="'.getUrl('page',$i).'">'.$i.'</a></li>'; ?>
		<li<?php echo $current_page >= $total_page ? ' class="disabled"' : ''?>><a href="<?php echo $current_page >= $total_page ? '#" onclick="return false' : getUrl('page',$current_page+1)?>" aria-label="Next"><span aria-hidden="true">&rsaquo;</span></a></li>
		<?php if(($total_page-$end_page)>0) echo '<li><a href="'.getUrl('page',$end_page+1).'">&raquo;</a></li>'; ?>
	</ul>
	<ul class="pagination">
	<li><form class="form-inline search-form" action="<?php echo getUrl('') ?>" method="get">
		<input type="hidden" name="admin" value="<?php echo $_DATA['admin'] ?>">
		<input type="text" name="search" value="<?php echo empty($_DATA['search'])?'':$_DATA['search'] ?>" class="form-control" placeholder="<?php echo getLang('search_text') ?>" required>
		<button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search" aria-hidden="true"></i> <?php echo getLang('search') ?></button>
		<?php if(!empty($_DATA['search'])) {?><button class="btn btn-default" type="button" onclick="location.replace('<?php echo getUrl('search','') ?>')"><?php echo getLang('cancel') ?></button><?php }?>
	</form></li>
	</ul>
</nav>

<?php
/* End of file file.php */
/* Location: ./module/admin/file.php */