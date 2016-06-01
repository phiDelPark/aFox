<?php
	if(!defined('__AFOX__')) exit();
	$mb = $_MEMBER;

	$schs = [];
	$search = empty($_DATA['search']) ? '' : $_DATA['search'];
	if(!empty($search)) {
		$schkeys = ['date'=>'nt_send_date','read'=>'nt_read_date'];
		$ss = explode(':', $search);
		if(count($ss)>1 && !empty($schkeys[$ss[0]])) {
			$search = trim(implode(':', array_slice($ss,1)));
			if(!empty($search)) $schs = [$schkeys[$ss[0]].'{LIKE}'=>$search.'%'];
		} else {
			$schs = ['nt_sender_nick{LIKE}'=>'%'.$search.'%'];
		}
	}
	$_list = getDBList(_AF_NOTE_TABLE_,['mb_srl'=>$mb['mb_srl'],'OR'=>$schs],'nt_send_date desc', empty($_DATA['page']) ? 1 : $_DATA['page'], 20);

	if(!empty($_DATA['srl'])) include 'inboxview.php';
?>

<table class="table table-hover table-nowrap">
<thead>
	<tr>
		<th class="col-xs-1"><?php echo getLang('nickname')?></th>
		<th><?php echo getLang('content')?></th>
		<th class="col-xs-1"><?php echo getLang('status')?></th>
		<th class="col-xs-1"><?php echo getLang('date')?></th>
	</tr>
</thead>
<tbody>

<?php
	$total_page = 0;
	$current_page = 1;

	if(!empty($_list['error'])) {
		echo showMessage($_list['message'], $_list['error']);
	} else {
		$current_page = $_list['current_page'];
		$total_page = $_list['total_page'];
		foreach ($_list['data'] as $key => $value) {
			echo '<tr style="cursor:pointer" onclick="location.href=\''.escapeHtml(getUrl('srl',$value['nt_srl']),true,ENT_QUOTES).'\'"><th scope="row">'.$value['nt_sender_nick'].'</th>';
			echo '<td>'.cut_str(strip_tags($value['nt_content']),90).'</td>';
			echo '<td>'.date('Y/m/d', strtotime($value['nt_read_date'])).'</td>';
			echo '<td>'.date('Y/m/d', strtotime($value['nt_send_date'])).'</td></tr>';
		}
	}
?>

</tbody>
</table>

<nav class="text-center">
	<ul class="pagination">
	<li<?php echo $current_page <= 1 ? ' class="disabled"' : ''?>><a href="<?php echo  $current_page <= 1 ? '#' : getUrl('page',$current_page-1)?>" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>
<?php
	for ($i=1; $i <= $total_page; $i++) {
		echo '<li'.($current_page == $i ? ' class="active"' : '').'><a href="'.getUrl('page',$i).'">'.$i.'</a></li>';
	}
?>
	<li<?php echo $current_page >= $total_page ? ' class="disabled"' : ''?>><a href="<?php echo $current_page >= $total_page ? '#' : getUrl('page',$current_page+1)?>" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>
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
		<?php if(!empty($_DATA['srl'])) {?><a class="btn btn-default" href="<?php echo getUrl('srl','') ?>" role="button"><i class="fa fa-list" aria-hidden="true"></i> <?php echo getLang('list') ?></a><?php }?>
		<a class="btn btn-default" href="#" data-exec-ajax="member.readAllNotes" data-ajax-param="success_return_url,<?php echo getUrl()?>" role="button"><i class="fa fa-envelope-o" aria-hidden="true"></i> <?php echo getLang('read_all') ?></a>
	</div>
</footer>