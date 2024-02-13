<?php
	if(!defined('__AFOX__')) exit();
	if(@$_GET['srl']) include 'trashview.php';
	addJSLang(['confirm_empty','confirm_restore','document']);
?>

<div style="position:relative">
<input class="d-none" type="checkbox" id="searchForm">
<form class="<?php echo @$_GET['search']?' d-block':''?>" method="get">
	<input type="hidden" name="module" value="member">
	<input type="hidden" name="disp" value="trash">
	<input type="hidden" name="id" value="<?php echo _MID_ ?>">
	<div class="input-group input-group-sm">
		<label class="input-group-text" for="search"<?php echo @$_GET['search']?' onclick="location.replace(\''.getUrl('search','').'\')"':''?>><svg class="bi"><use href="<?php echo _AF_THEME_URL_?>bi-icons.svg#<?php echo @$_GET['search']?'x-lg':'search'?>"/></svg></label>
		<input type="text" name="search" id="search" value="<?php echo @$_GET['search']?$_GET['search']:''?>" class="form-control" required>
		<button class="btn btn-outline-secondary" type="submit"><?php echo getLang('search') ?></button>
	</div>
</form>
</div>

<form id="af_member_remove_trash_items" method="post">
<input type="hidden" name="error_url" value="<?php echo getUrl()?>" />
<input type="hidden" name="success_url" value="<?php echo getUrl()?>" />
<table class="table">
<thead>
	<tr>
		<?php if(_MOBILE_) { ?>
		<th scope="col" class="text-wrap"><?php echo getLang('title')?></th>
		<?php } else { ?>
		<th scope="col" class="text-nowrap" style="width:1px;padding-left:.25rem"><label class="btn btn-sm align-baseline p-0 px-1" for="searchForm"><svg class="bi"><use href="<?php echo _AF_THEME_URL_?>bi-icons.svg#search"/></svg></label></th>
		<th scope="col" class="text-wrap">:<?php echo getLang('title')?></th>
		<th scope="col" class="text-nowrap" style="width:1px"><?php echo getLang('delete')?></th>
		<th scope="col" class="text-nowrap" style="width:1px">?<?php echo getLang('date')?></th>
		<th scope="col" class="text-nowrap" style="width:1px"><input type="checkbox" onchange="themeAllCheckInboxItems(this)"></th>
		<?php } ?>
	</tr>
</thead>
<?php
	$current_page = $_DATA['current_page'];
	$total_page = $_DATA['total_page'];
	$start_page = $current_page - 4;
	if ($start_page < 1) $start_page = 1;
	$end_page = 9 + $start_page;
	if ($end_page > $total_page) $end_page = $total_page;
	$srl = empty($_DATA['srl'])?0:$_DATA['srl'];

	foreach ($_DATA['list'] as $key => $value) {
		echo '<tr'.($value['wr_srl']==$srl?' class="active"':'').' style="cursor:pointer" onclick="return themeTrashItemClick(event,\''.escapeHTML(getUrl('srl',$value['wr_srl']),ENT_QUOTES).'\')">';
		if(_MOBILE_) {
			echo '<td class="text-wrap"><a href="#" onclick="return false">'.escapeHTML(strip_tags($value['wr_title'])).'</a>';
			echo '<div class="d-flex w-100 justify-content-between"><span>'.date('y/m/d', strtotime($value['wr_regdate'])).'</span>';
			echo '<span>Del:'.date('y/m/d', strtotime($value['wr_update'])).'</span></div></td>';
		} else {
			echo '<th scope="row">'.$value['wr_srl'].'</th>';
			echo '<td class="text-wrap"><a href="#" onclick="return false">'.($value['wr_secret']?'<svg class="bi me-1"><use href="'._AF_THEME_URL_.'bi-icons.svg#shield-lock"/></svg>':'').escapeHTML(cutstr(strip_tags($value['wr_title']),50)).'</a></td>';
			echo '<td>'.date('y/m/d', strtotime($value['wr_update'])).'</td>';
			echo '<td>'.date('y/m/d', strtotime($value['wr_regdate'])).'</td><td><input type="checkbox" name="wr_srl[]" value="'.$value['wr_srl'].'"></td>';
		}
		echo '</tr>';
	}
?>
</tbody>
</table>
</form>

<div class="w-100 text-end bg-body-tertiary p-1">
	<nav aria-label="Page navigation of the list">
	<ul class="pagination pagination-sm float-start">
		<li class="page-item me-1"><a class="btn btn-sm fw-bold btn-secondary<?php echo $current_page<11 ? ' disabled" aria-disabled="true' : ''?>" href="<?php echo  getUrl('page',$current_page-10)?>" aria-label="Previous-10">&lt;&lt;</a></li>
		<li class="page-item me-1 d-md-none"><a class="btn btn-sm btn-secondary<?php echo $current_page <= 1 ? ' disabled" aria-disabled="true' : ''?>" href="<?php echo  getUrl('page',$current_page-1)?>" aria-label="Previous">&lt;</a></li>
		<li class="page-item d-md-none"><a class="btn btn-sm border border-1 rounded-0 border-end-0 border-start-0 btn-outline-secondary disabled" aria-disabled="true"><?php echo $current_page.' / '.$total_page?></a></li>
		<?php for ($i=$start_page; $i <= $end_page; $i++) echo '<li class="page-item d-none d-md-block"><a class="btn btn-sm border border-1 rounded-0 border-end-0 border-start-0'.($current_page == $i ? ' text-decoration-underline fw-bold" aria-current="page' : ' btn-outline-secondary').'" href="'.getUrl('page',$i).'">'.$i.'</a></li>' ?>
		<li class="page-item ms-1 d-md-none"><a class="btn btn-sm btn-secondary<?php echo $total_page<($current_page+1) ? ' disabled" aria-disabled="true' : ''?>" href="<?php echo getUrl('page',$total_page<($current_page+1)?$total_page:$current_page+1)?>" aria-label="Next">&gt;</a></li>
		<li class="page-item ms-1"><a class="btn btn-sm fw-bold btn-secondary<?php echo $total_page <= $end_page ? ' disabled" aria-disabled="true' : ''?>" href="<?php echo getUrl('page',$total_page<($current_page+10)?$total_page:$current_page+10)?>" aria-label="Next+10">&gt;&gt;</a></li>
	</ul>
	</nav>
	<a class="btn btn-sm btn-danger clearfix" href="#" onclick="return themeAllRemoveInboxItems()" role="button"> <?php echo getLang('delete') ?></a>
</div>
<script>function themeTrashItemClick(e,t){return"INPUT"===e.target.tagName||(location.href=t,!1)}function themeAllCheckInboxItems(e){e.closest("table").querySelectorAll("tbody [type=checkbox]").forEach(t=>t.checked=e.checked)}function themeAllRemoveInboxItems(){let e=function(e){exec_ajax({module:"member",act:e,...document.querySelector("#af_member_remove_trash_items").serializeArray()}).then(e=>{location.href=e.redirect_url}).catch(e=>{alert(e)})},t=confirm($_LANG.confirm_empty.sprintf([$_LANG.document]));return"object"==typeof t?t.then(()=>{e("deletetrashes")}):!0===t&&e("deletetrashes"),!1}</script>
