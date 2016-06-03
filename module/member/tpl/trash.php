<?php
	if(!defined('__AFOX__')) exit();
	$mb = $_MEMBER;

	$schs = [];
	$search = empty($_DATA['search']) ? '' : $_DATA['search'];
	if(!empty($search)) {
		$schkeys = ['tags'=>'wr_tags','nick'=>'mb_nick','date'=>'wr_regdate'];
		$ss = explode(':', $search);
		if(count($ss)>1 && !empty($schkeys[$ss[0]])) {
			$search = trim(implode(':', array_slice($ss,1)));
			if(!empty($search)) $schs = [$schkeys[$ss[0]].'{LIKE}'=>$search.'%'];
		} else {
			$schs = ['wr_title{LIKE}'=>'%'.$search.'%', 'wr_content{LIKE}'=>'%'.$search.'%'];
		}
	}
	$_list = getDBList(_AF_DOCUMENT_TABLE_,['md_id'=>'_AFOXtRASH_','mb_srl'=>$mb['mb_srl'],'OR'=>$schs],'wr_regdate desc', empty($_DATA['page']) ? 1 : $_DATA['page'], 20);

	if(!empty($_DATA['srl'])) include 'trashview.php';
?>

<table class="table table-hover table-nowrap">
<thead>
	<tr>
		<th class="col-xs-1">#</th>
		<th><?php echo getLang('title')?></th>
		<th class="col-xs-1"><?php echo getLang('status')?></th>
		<th class="col-xs-1"><?php echo getLang('secret')?></th>
		<th class="col-xs-2"><?php echo getLang('author')?></th>
		<th class="col-xs-1"><?php echo getLang('date')?></th>
		<th class="col-xs-1"><?php echo getLang('removed_date')?></th
	</tr>
</thead>
<tbody>

<?php
	$end_page = $total_page = 0;
	$start_page = $current_page = 1;

	if(!empty($_list['error'])) {
		echo showMessage($_list['message'], $_list['error']);
	} else {
		$current_page = $_list['current_page'];
		$total_page = $_list['total_page'];
		$start_page = $_list['start_page'];
		$end_page = $_list['end_page'];

		foreach ($_list['data'] as $key => $value) {
			echo '<tr style="cursor:pointer" onclick="location.href=\''.escapeHtml(getUrl('srl',$value['wr_srl']),true,ENT_QUOTES).'\'"><th scope="row">'.$value['wr_srl'].'</th>';
			echo '<td>'.escapeHtml(cutstr(strip_tags($value['wr_title']),50)).'</td>';
			echo '<td>'.($value['wr_status']?$value['wr_status']:'-').'</td>';
			echo '<td>'.($value['wr_secret']?'Y':'N').'</td>';
			echo '<td>'.escapeHtml($value['mb_nick'],true).'</td>';
			echo '<td>'.date('Y/m/d', strtotime($value['wr_regdate'])).'</td>';
			echo '<td>'.date('Y/m/d', strtotime($value['wr_update'])).'</td></tr>';
		}
	}
?>

</tbody>
</table>

<nav class="text-center">
	<ul class="pagination hidden-xs">
		<?php if($start_page>10) echo '<li><a href="'.getUrl('page',$start_page-10).'">&laquo;</a></li>'; ?>
		<li<?php echo $current_page <= 1 ? ' class="disabled"' : ''?>><a href="<?php echo  $current_page <= 1 ? '#" onclick="return false' : getUrl('page',$current_page-1)?>" aria-label="Previous"><span aria-hidden="true">&lsaquo;</span></a></li>
		<?php for ($i=$start_page; $i <= $end_page; $i++) echo '<li'.($current_page == $i ? ' class="active"' : '').'><a href="'.getUrl('page',$i).'">'.$i.'</a></li>'; ?>
		<li<?php echo $current_page >= $total_page ? ' class="disabled"' : ''?>><a href="<?php echo $current_page >= $total_page ? '#" onclick="return false' : getUrl('page',$current_page+1)?>" aria-label="Next"><span aria-hidden="true">&rsaquo;</span></a></li>
		<?php if(($total_page-$end_page)>0) echo '<li><a href="'.getUrl('page',$end_page+1).'">&raquo;</a></li>'; ?>
	</ul>
	<ul class="pager visible-xs-block">
		<li class="previous<?php echo $current_page <= 1?' disabled':''?>"><a href="<?php echo  $current_page <= 1 ? '#" onclick="return false' : getUrl('page',$current_page-1)?>" aria-label="Previous"><span aria-hidden="true">&lsaquo;</span> <?php echo getLang('previous') ?></a></li>
		<li><span class="col-xs-5"><?php echo $current_page.' / '.$total_page?></span></li>
		<li class="next<?php echo $current_page >= $total_page?' disabled':''?>"><a href="<?php echo $current_page >= $total_page ? '#" onclick="return false' : getUrl('page',$current_page+1)?>" aria-label="Next"><?php echo getLang('next') ?> <span aria-hidden="true">&rsaquo;</span></a></li>
	</ul>
</nav>
<footer class="clearfix">
	<form class="search-form pull-left col-xs-5 col-sm-4" action="<?php echo getUrl('') ?>" method="get" style="padding:0">
		<input type="hidden" name="module" value="member">
		<input type="hidden" name="disp" value="inbox">
		<div class="input-group" style="width:250px">
			<input type="text" name="search" value="<?php echo empty($_DATA['search'])?'':$_DATA['search'] ?>" class="form-control" placeholder="<?php echo getLang('search_text') ?>" required>
			<span class="input-group-btn">
			<?php if(empty($_DATA['search']) || !__MOBILE__) {?><button class="btn btn-default" type="submit"><i class="fa fa-search" aria-hidden="true"></i> <?php echo getLang('search') ?></button><?php }?>
			<?php if(!empty($_DATA['search'])) {?><button class="btn btn-default" type="button" onclick="location.replace('<?php echo getUrl('search','') ?>')"><?php echo getLang('cancel') ?></button><?php }?>
			</span>
		</div>
		<input type="hidden" name="id" value="<?php echo $_DATA['id'] ?>">
	</form>
	<div class="pull-right">
		<a class="btn btn-default" href="<?php echo getUrl('srl','') ?>" role="button"><i class="fa fa-list" aria-hidden="true"></i> <?php echo getLang('list') ?></a>
	</div>
</footer>