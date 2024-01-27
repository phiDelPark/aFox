<?php
	if(!defined('__AFOX__')) exit();
	if(!empty($_POST['srl'])) include 'inboxview.php';
	addJSLang(['confirm_select_delete','message']);
	$_list = &$_DATA['_DOCUMENT_LIST_'];
?>

<div style="position:relative">
<input class="d-none" type="checkbox" id="searchList">
<form class="<?php echo empty($_POST['search']) ? '' : ' d-block'?>" method="get">
	<input type="hidden" name="module" value="member">
	<input type="hidden" name="disp" value="inbox">
	<input type="hidden" name="id" value="<?php echo __MID__ ?>">
	<div class="input-group input-group-sm">
		<label class="input-group-text" for="search"<?php echo empty($_POST['search'])?'':' onclick="location.replace(\''.getUrl('search','').'\')"'?>><svg class="bi"><use href="<?php echo _AF_THEME_URL_?>bi-icons.svg#<?php echo empty($_POST['search'])?'search':'x-lg'?>"/></svg></label>
		<input type="text" name="search" id="search" value="<?php echo empty($_POST['search'])?'':$_POST['search'] ?>" class="form-control" required>
		<button class="btn btn-outline-secondary" type="submit"><?php echo getLang('search') ?></button>
	</div>
</form>
</div>

<form id="af_member_remove_inbox_items" method="post">
<input type="hidden" name="success_url" value="<?php echo getUrl()?>" />
<table class="table">
<thead>
	<tr>
		<?php if(__MOBILE__) { ?>
		<th scope="col" class="text-wrap"><?php echo getLang('content')?></th>
		<?php } else { ?>
		<th scope="col" class="text-nowrap" style="width:1px;padding-left:.25rem"><label class="btn btn-sm align-baseline p-0 px-1" for="searchList"><svg class="bi"><use href="<?php echo _AF_THEME_URL_?>bi-icons.svg#search"/></svg></label> <?php echo getLang('name')?></th>
		<th scope="col" class="text-wrap"><?php echo getLang('content')?></th>
		<th scope="col" class="text-nowrap" style="width:1px"><?php echo getLang('status')?></th>
		<th scope="col" class="text-nowrap" style="width:1px"><?php echo getLang('date')?></th>
		<th scope="col" class="text-nowrap" style="width:1px"><input type="checkbox" onclick="_allCheckInboxItems(this)"></th>
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
			echo '<td class="text-wrap"><a href="#" onclick="return false">'.cutstr(strip_tags($value['nt_content']),255).'</a>';
			echo '<div class="d-flex w-100 justify-content-between"><span>'.date('y/m/d', strtotime($value['nt_read_date'])).'</span>';
			echo '<span>Send:'.date('y/m/d', strtotime($value['nt_send_date'])).'</span></div></td>';
		} else {
			echo '<th scope="row" class="text-nowrap"'.($value['nt_sender']?'':' style="font-weight:normal"').'>'.$value['nt_sender_nick'].'</th>';
			echo '<td class="text-wrap"><a href="#" onclick="return false">'.cutstr(strip_tags($value['nt_content']),90).'</a></td>';
			echo '<td class="text-nowrap">'.($value['nt_read_date'] === '0000-00-00 00:00:00'?$unread_str:date('y/m/d', strtotime($value['nt_read_date']))).'</td>';
			echo '<td>'.date('y/m/d', strtotime($value['nt_send_date'])).'</td><td><input type="checkbox" name="nt_srl[]" value="'.$value['nt_srl'].'"></td>';
		}
		echo '</tr>';
	}
?>

</tbody>
</table>
</form>

<div class="d-flex w-100 justify-content-between">
	<nav id="pageNavigation" aria-label="Page navigation of the list">
		<?php if($start_page>10) echo '<a class="btn btn-sm btn-outline-primary rounded-pill" href="'.getUrl('page',$start_page-10).'">&laquo;</a>' ?>
		<a class="btn btn-sm rounded-pill btn-outline-<?php echo $current_page <= 1 ? 'secondary disabled" aria-disabled="true' : 'primary'?>" href="<?php echo  $current_page <= 1 ? '#' : getUrl('page',$current_page-1)?>" aria-label="Previous">&lsaquo; <?php echo getLang('previous') ?></a>
		<a class="d-md-none btn btn-sm btn-outline-secondary rounded-pill disabled" aria-disabled="true"><?php echo $current_page.' / '.$total_page?></a>
		<?php for ($i=$start_page; $i <= $end_page; $i++) echo '<span class="d-none d-md-inline-block btn btn-sm btn-outline-primary"><a class="btn btn-sm'.($current_page == $i ? ' active" aria-current="page' : '').'" href="'.getUrl('page',$i).'">'.$i.'</a></span>' ?>
		<a class="btn btn-sm rounded-pill btn-outline-<?php echo $current_page >= $total_page ? 'secondary disabled" aria-disabled="true' : 'primary'?>" href="<?php echo $current_page >= $total_page ? '#' : getUrl('page',$current_page+1)?>" aria-label="Next"><?php echo getLang('next') ?> &rsaquo;</a>
		<?php if(($total_page-$end_page)>0) echo '<a class="btn btn-sm btn-outline-primary rounded-pill" href="'.getUrl('page',$end_page+1).'">&raquo;</a>' ?>
    </nav>
	<a class="btn btn-sm rounded-pill btn-danger" href="#" onclick="return _allRemoveInboxItems()" role="button"> <?php echo getLang('delete') ?></a>
</div>

<script>
	function _inboxItemClick(e, href) {
		if(e.target.tagName === 'INPUT') return true;
		location.href = href;
		return false;
	}
	function _allCheckInboxItems(el_chk) {
		let els_chk = el_chk.closest('table').querySelectorAll('tbody [type=checkbox]');
		els_chk.forEach(el => el.checked = el_chk.checked);
	}
	function _allRemoveInboxItems() {
		if (confirm($_LANG['confirm_select_delete'].sprintf([$_LANG['message']])) == true) {
			exec_ajax('member.deleteNote', document.querySelector('#af_member_remove_inbox_items').dataExport());
		}
		return false;
	}
</script>
