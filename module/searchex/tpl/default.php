<?php
if(!defined('__AFOX__')) exit();
?>

<section id="searchexList">
	<table class="table table-hover list-table" role="list">
	<thead>
		<tr>
			<?php if(_MOBILE_) { ?>
			<th scope="col"><?php echo getLang('title')?></th>
			<?php } else { ?>
			<th scope="col" class="d-none d-md-table-cell text-nowrap" style="width:1px"><?php echo getLang('id')?></th>
			<th scope="col" class="text-wrap"><?php echo getLang('title')?></th>
			<th scope="col" class="d-none d-md-table-cell text-nowrap" style="width:1px"><?php echo getLang('name')?></th>
			<th scope="col" class="text-nowrap" style="width:1px"><?php echo getLang('date')?></th>
			<?php } ?>
		</tr>
	</thead>
	<tbody>

	<?php
		$current_page = $_DATA['current_page'];
		$total_page = $_DATA['total_page'];
		$start_page = $_DATA['start_page'];
		$end_page = $_DATA['end_page'];
		$total_count = $_DATA['total_count'];
		$srl = @$_GET['srl']?$_GET['srl']:0;

		$is_manager = isManager(_MID_);
		$login_srl = empty($_MEMBER['mb_srl']) ? false : $_MEMBER['mb_srl'];

		foreach ($_DATA['data'] as $key => $val) {
			$wr_secret =  $val['wr_secret'] == '1';
			$wr_permit = !$wr_secret || $is_manager || $login_srl === $val['mb_srl'];
			$wr_title = !$wr_permit || $wr_secret ? '<svg class="bi me-1"><use href="'._AF_THEME_URL_.'bi-icons.svg#shield-lock"/></svg>' : '';
			$wr_title .= !$wr_permit ? getLang('error_permitted') : escapeHTML(strip_tags($val['wr_title']));

			if(_MOBILE_) {
				$class1 = 'd-flex w-100 justify-content-between';
					echo '<tr><td><a class="d-block text-decoration-none" href="'.getUrl('','srl',$val['wr_srl'],'disp','','cpage','','rp','').'" target="_blank">'.$wr_title.($val['wr_reply']>0?' <small>(+'.$val['wr_reply'].')</small>':'');
					echo '<div class="'.$class1.' text-body-secondary"><span data-srl="'.$val['mb_srl'].'" data-rank="'.(ord($val['mb_rank']) - 48).'">'.$val['mb_nick'].'</span>';
					echo '<span>'.date('m/d', strtotime($val['wr_update'])).'</span></div></a></td></tr>';
			} else {
					echo '<tr><th scope="row" class="d-none d-md-table-cell text-nowrap">'.$val['md_id'].'</th>';
					echo '<td class="text-wrap"><a class="d-block" href="'.getUrl('','srl',$val['wr_srl'],'disp','','cpage','','rp','').'" target="_blank">'.$wr_title.'</a>'.($val['wr_reply']>0?' <small>(+'.$val['wr_reply'].')</small>':'').'</td>';
					echo '<td class="d-none d-md-table-cell text-nowrap"><span class="mb_nick" data-srl="'.$val['mb_srl'].'" data-rank="'.(ord($val['mb_rank']) - 48).'">'.$val['mb_nick'].'</span></td>';
					echo '<td>'.date('Y/m/d', strtotime($val['wr_update'])).'</td></tr>';
			}
		}
	?>

	</tbody>
	</table>

	<div class="w-100 text-end">
		<nav aria-label="Page navigation of the list">
		<ul class="pagination pagination-sm float-start">
			<?php if($start_page>10) echo '<li class="page-item"><a class="page-link" href="'.getUrl('page',$start_page-10).'">&laquo;</a></li>' ?>
			<li class="page-item"><a class="page-link <?php echo $current_page <= 1 ? ' disabled" aria-disabled="true' : ''?>" href="<?php echo  $current_page <= 1 ? '#' : getUrl('page',$current_page-1)?>" aria-label="Previous"><span aria-hidden="true">&lsaquo;</span> <?php echo getLang('previous') ?></a></li>
			<li class="page-item d-md-none"><a class="page-link disabled" aria-disabled="true"><?php echo $current_page.' / '.$total_page?></a></li>
			<?php for ($i=$start_page; $i <= $end_page; $i++) echo '<li class="page-item d-none d-md-inline-block"><a class="page-link'.($current_page == $i ? ' active" aria-current="page' : '').'" href="'.getUrl('page',$i).'">'.$i.'</a></li>' ?>
			<li class="page-item"><a class="page-link<?php echo $current_page >= $total_page ? ' disabled" aria-disabled="true' : ''?>" href="<?php echo $current_page >= $total_page ? '#' : getUrl('page',$current_page+1)?>" aria-label="Next"><?php echo getLang('next') ?> <span aria-hidden="true">&rsaquo;</span></a></li>
			<?php if(($total_page-$end_page)>0) echo '<li class="page-item"><a class="page-link" href="'.getUrl('page',$end_page+1).'">&raquo;</a></li>' ?>
		</ul>
		</nav>
	</div>
</section>

<?php
/* End of file default.php */
/* Location: ./module/searchex/tpl/default.php */
