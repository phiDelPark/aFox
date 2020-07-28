<?php
	if(!defined('__AFOX__')) exit();
	if(!empty($_DATA['srl'])) include 'inboxview.php';

	$_list = &$_{'member'}['_DOCUMENT_LIST_'];
?>

<form id="af_member_remove_inbox_items" method="post">
<input type="hidden" name="success_return_url" value="<?php echo getUrl()?>" />
<table class="table table-hover table-nowrap" role="list">
<thead>
	<tr>
		<?php if(__MOBILE__) { ?>
		<th><input type="checkbox"> <?php echo getLang('content')?></th>
		<?php } else { ?>
		<th class="col-xs-1"><?php echo getLang('name')?></th>
		<th><?php echo getLang('content')?></th>
		<th class="col-xs-1"><?php echo getLang('status')?></th>
		<th class="col-xs-1"><?php echo getLang('date')?></th>
		<th style="width:30px"><input type="checkbox" onclick="_allCheckInboxItems(this)"></th>
		<?php } ?>
	</tr>
</thead>
<tbody>

<?php
	$unread_str = getLang('unread');
	$end_page = $total_page = 0;
	$start_page = $current_page = 1;

	$current_page = $_list['current_page'];
	$total_page = $_list['total_page'];
	$start_page = $_list['start_page'];
	$end_page = $_list['end_page'];
	$srl = empty($_DATA['srl'])?0:$_DATA['srl'];

	foreach ($_list['data'] as $key => $value) {
		echo '<tr'.($value['nt_srl']==$srl?' class="active"':'').' style="cursor:pointer" onclick="return _inboxItemClick(event,\''.escapeHtml(getUrl('srl',$value['nt_srl']),true,ENT_QUOTES).'\')">';
		if(__MOBILE__) {
			echo '<td><a href="#" onclick="return false">'.cutstr(strip_tags($value['nt_content']),255).'</a>';
			echo '<div class="clearfix"><input type="checkbox"> <span>'.date('y/m/d', strtotime($value['nt_read_date'])).'</span>';
			echo '<span class="pull-right">Send:'.date('y/m/d', strtotime($value['nt_send_date'])).'</span></div></td>';
		} else {
			echo '<th scope="row"'.($value['nt_sender']?'':' style="font-weight:normal"').' nowrap>'.$value['nt_sender_nick'].'</th>';
			echo '<td><a href="#" onclick="return false">'.cutstr(strip_tags($value['nt_content']),90).'</a></td>';
			echo '<td>'.($value['nt_read_date'] === '0000-00-00 00:00:00'?$unread_str:date('y/m/d', strtotime($value['nt_read_date']))).'</td>';
			echo '<td>'.date('y/m/d', strtotime($value['nt_send_date'])).'</td><td><input type="checkbox" name="nt_srl[]" value="'.$value['nt_srl'].'"></td>';
		}
		echo '</tr>';
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
			<input type="text" name="search" value="<?php echo empty($_DATA['search'])?'':$_DATA['search'] ?>" class="form-control" placeholder="<?php echo getLang('search_word') ?>" required>
			<span class="input-group-btn">
			<?php if(empty($_DATA['search']) || !__MOBILE__) {?><button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search" aria-hidden="true"></i> <?php echo getLang('search') ?></button><?php }?>
			<?php if(!empty($_DATA['search'])) {?><button class="btn btn-default" type="button" onclick="location.replace('<?php echo getUrl('search','') ?>')"><?php echo getLang('cancel') ?></button><?php }?>
			</span>
		</div>
		<input type="hidden" name="id" value="<?php echo __MID__ ?>">
	</form>
	<div class="pull-right">
		<?php if(!empty($_DATA['srl'])) {?><a class="btn btn-default" href="<?php echo getUrl('srl','') ?>" role="button"><i class="glyphicon glyphicon-list" aria-hidden="true"></i> <?php echo getLang('list') ?></a><?php }?>
		<a class="btn btn-default" href="#" onclick="return _allRemoveInboxItems()" role="button"><i class="glyphicon glyphicon-envelope" aria-hidden="true"></i> <?php echo getLang('delete') ?></a>
	</div>
</footer>

<script>
	function _inboxItemClick(e, href) {
		if(e.target.tagName === 'INPUT') return true;
		location.href = href;
		return false;
	}
	function _allCheckInboxItems(th) {
		var ck = $(th).is(':checked');
		$(th).closest('table').find('[type=checkbox]').prop('checked', ck);
	}
	function _allRemoveInboxItems() {
		msg_box($_LANG['confirm_select_delete'].sprintf([$_LANG['message']]), '', ['question', ['OK', 'cancel']], function(key){
			if (key == 'ok') {
				var data = $('#af_member_remove_inbox_items')[0].dataExport();
				exec_ajax('member.deleteNote', data);
			}
			return true;
		});
		return false;
	}
</script>
