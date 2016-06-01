<?php
	if(!defined('__AFOX__')) exit();

	$search = empty($_DATA['search'])?null:'%'.$_DATA['search'].'%';
	$vs_list = getDBList(_AF_VISITOR_TABLE_,[
		'OR' =>empty($search)?[]:['vs_agent{LIKE}'=>$search, 'vs_referer{LIKE}'=>$search]
	],'vs_regdate desc', empty($_DATA['page']) ? 1 : $_DATA['page'], 20);
?>

<table class="table table-hover table-nowrap">
<thead>
	<tr>
		<th class="col-xs-1">#<?php echo getLang('ip')?></th>
		<th class="col-md-6"><?php echo getLang('agent')?></th>
		<th><?php echo getLang('referer')?></th>
		<th class="col-xs-1"><?php echo getLang('date')?></th>
	</tr>
</thead>
<tbody>

<?php
	$total_page = 0;
	$current_page = 1;

	if(!empty($vs_list['error'])) {
		echo showMessage($vs_list['message'], $vs_list['error']);
	} else {
		$current_page = $vs_list['current_page'];
		$total_page = $vs_list['total_page'];

		foreach ($vs_list['data'] as $key => $value) {
			echo '<tr><th scope="row">'.$value['mb_ipaddress'].'</th>';
			echo '<td style="white-space:normal">'.escapeHtml($value['vs_agent']).'</td>';
			echo '<td style="white-space:normal">'.escapeHtml($value['vs_referer']).'</td>';
			echo '<td>'.date('Y/m/d h:m', strtotime($value['vs_regdate'])).'</td></tr>';
		}
	}
?>

</tbody>
</table>

<nav class="navbar clearfix">
  <ul class="pagination">
	<li><form class="form-inline search-form" action="<?php echo getUrl('') ?>" method="get">
		<input type="hidden" name="admin" value="<?php echo $_DATA['admin'] ?>">
		<input type="text" name="search" value="<?php echo empty($_DATA['search'])?'':$_DATA['search'] ?>" class="form-control" placeholder="<?php echo getLang('search_text') ?>" required>
		<button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search" aria-hidden="true"></i> <?php echo getLang('search') ?></button>
		<?php if(!empty($_DATA['search'])) {?><button class="btn btn-default" type="button" onclick="location.replace('<?php echo getUrl('search','') ?>')"><?php echo getLang('cancel') ?></button><?php }?>
	</form></li>
  </ul>
  <ul class="pagination pull-right">
	<li<?php echo $current_page <= 1 ? ' class="disabled"' : ''?>><a href="<?php echo  $current_page <= 1 ? '#' : getUrl('page',$current_page-1)?>" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>
<?php
	for ($i=1; $i <= $total_page; $i++) {
		echo '<li'.($current_page == $i ? ' class="active"' : '').'><a href="'.getUrl('page',$i).'">'.$i.'</a></li>';
	}
?>
	<li<?php echo $current_page >= $total_page ? ' class="disabled"' : ''?>><a href="<?php echo $current_page >= $total_page ? '#' : getUrl('page',$current_page+1)?>" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>
  </ul>
</nav>

<?php
/* End of file visit.php */
/* Location: ./module/admin/visit.php */