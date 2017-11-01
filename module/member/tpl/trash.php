<?php
	if(!defined('__AFOX__')) exit();
	require_once 'common.php';

	$mb = $_MEMBER;
	$schs = [];
	$search = empty($_DATA['search']) ? '' : $_DATA['search'];
	if(!empty($search)) {
		$schkeys = ['tag'=>'wr_tags','nick'=>'mb_nick','date'=>'wr_regdate'];
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

<form id="af_member_remove_trash_items" method="post">
<input type="hidden" name="success_return_url" value="<?php echo getUrl()?>" />
<table class="table table-hover table-nowrap" role="list">
<thead>
	<tr>
		<?php if(__MOBILE__) { ?>
		<th><input type="checkbox"> <?php echo getLang('title')?></th>
		<?php } else { ?>
		<th class="col-xs-1">#</th>
		<th><?php echo getLang('title')?></th>
		<th class="col-xs-1 hidden-xs"><?php echo getLang('secret')?></th>
		<th class="col-xs-2"><?php echo getLang('author')?></th>
		<th class="col-xs-1 hidden-xs"><?php echo getLang('date')?></th>
		<th class="col-xs-1"><?php echo getLang('delete')?></th>
		<th style="width:30px"><input type="checkbox" onclick="_allCheckTrashItems(this)"></th>
		<?php } ?>
	</tr>
</thead>
<tbody>

<?php
	$end_page = $total_page = 0;
	$start_page = $current_page = 1;

	if(!empty($_list['error'])) {
		messageBox($_list['message'], $_list['error'], false);
	} else {
		$current_page = $_list['current_page'];
		$total_page = $_list['total_page'];
		$start_page = $_list['start_page'];
		$end_page = $_list['end_page'];
		$srl = empty($_DATA['srl'])?0:$_DATA['srl'];

		foreach ($_list['data'] as $key => $value) {
			echo '<tr'.($value['wr_srl']==$srl?' class="active"':'').' style="cursor:pointer" onclick="return _trashItemClick(event,\''.escapeHtml(getUrl('srl',$value['wr_srl']),true,ENT_QUOTES).'\')">';
			if(__MOBILE__) {
				echo '<td class="wr_title"><a href="#" onclick="return false">'.escapeHtml($value['wr_title'], true).'</a>';
				echo '<div class="clearfix"><input type="checkbox"> <span>'.date('y/m/d', strtotime($value['wr_regdate'])).'</span>';
				echo '<span class="pull-right">Del:'.date('y/m/d', strtotime($value['wr_update'])).'</span></div></td>';
			} else {
				echo '<th scope="row">'.$value['wr_srl'].'</th>';
				echo '<td><a href="#" onclick="return false">'.escapeHtml(cutstr(strip_tags($value['wr_title']),50)).'</a></td>';
				echo '<td class="hidden-xs">'.($value['wr_secret']?'Y':'N').'</td>';
				echo '<td nowrap>'.escapeHtml($value['mb_nick'],true).'</td>';
				echo '<td class="hidden-xs">'.date('y/m/d', strtotime($value['wr_regdate'])).'</td>';
				echo '<td>'.date('y/m/d', strtotime($value['wr_update'])).'</td><td><input type="checkbox" name="wr_srl[]" value="'.$value['wr_srl'].'"></td>';
			}
			echo '</tr>';
		}
	}
?>

</tbody>
</table>
</form>
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
		<li><span class="col-xs-5" style="float:none"><?php echo $current_page.' / '.$total_page?></span></li>
		<li class="next<?php echo $current_page >= $total_page?' disabled':''?>"><a href="<?php echo $current_page >= $total_page ? '#" onclick="return false' : getUrl('page',$current_page+1)?>" aria-label="Next"><?php echo getLang('next') ?> <span aria-hidden="true">&rsaquo;</span></a></li>
	</ul>
</nav>

<footer class="clearfix">
	<form class="search-form pull-left col-xs-5 col-sm-4 mw-20 xw-30" action="<?php echo getUrl('') ?>" method="get" style="padding:0">
		<input type="hidden" name="module" value="member">
		<input type="hidden" name="disp" value="inbox">
		<div class="input-group">
			<input type="text" name="search" value="<?php echo empty($_DATA['search'])?'':$_DATA['search'] ?>" class="form-control" placeholder="<?php echo getLang('search_text') ?>" required>
			<span class="input-group-btn">
			<?php if(empty($_DATA['search']) || !__MOBILE__) {?><button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search" aria-hidden="true"></i> <?php echo getLang('search') ?></button><?php }?>
			<?php if(!empty($_DATA['search'])) {?><button class="btn btn-default" type="button" onclick="location.replace('<?php echo getUrl('search','') ?>')"><?php echo getLang('cancel') ?></button><?php }?>
			</span>
		</div>
		<input type="hidden" name="id" value="<?php echo __MID__ ?>">
	</form>
	<div class="pull-right">
		<?php if(!empty($_DATA['srl'])) {?><a class="btn btn-default" href="<?php echo getUrl('srl','') ?>" role="button"><i class="glyphicon glyphicon-list" aria-hidden="true"></i> <?php echo getLang('list') ?></a><?php }?>
		<a class="btn btn-default" href="#" onclick="_allRemoveTrashItems()" role="button"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i> <?php echo getLang('delete') ?></a>
	</div>
</footer>

<script>
	function _trashItemClick(e, href) {
		if(e.target.tagName === 'INPUT') return true;
		location.href = href;
		return false;
	}
	function _allCheckTrashItems(th) {
		var ck = $(th).is(':checked');
		$(th).closest('table').find('[type=checkbox]').prop('checked', ck);
	}
	function _allRemoveTrashItems() {
		var data = $('#af_member_remove_trash_items')[0].dataExport();
		if (!confirm($_LANG['confirm_select_empty'].sprintf([$_LANG['document']]))) return false;
		exec_ajax('member.deleteTrash', data);
		return false;
	}
	jQuery('[role="heading"][aria-labelledby="mdMemberTitle"]').each(function() {
		jQuery(this).prepend('<span><?php echo getLang('recycle_bin') ?></span>');
	});
</script>
