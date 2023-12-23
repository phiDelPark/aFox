<?php
if(!defined('__AFOX__')) exit();

?>
<section id="bdList">

<article class="clearfix">
	<table class="table table-hover list-table" role="list">
	<thead>
		<tr>
			<?php if(__MOBILE__) { ?>
			<th><?php echo getLang('title')?></th>
			<?php } else { ?>
			<th class="col-xs-1 hidden-xs"><?php echo getLang('id')?></th>
			<th><?php echo getLang('title')?></th>
			<th class="col-xs-3 col-md-2"><?php echo getLang('name')?></th>
			<th class="col-xs-1 hidden-xs"><?php echo getLang('view')?></th>
			<th class="col-xs-1"><?php echo getLang('date')?></th>
			<?php } ?>
		</tr>
	</thead>
	<tbody>

	<?php
		$current_page = $_{'search'}['current_page'];
		$total_page = $_{'search'}['total_page'];
		$start_page = $_{'search'}['start_page'];
		$end_page = $_{'search'}['end_page'];
		$total_count = $_{'search'}['total_count'];
		$srl = empty($_DATA['srl'])?0:$_DATA['srl'];
		$_tmp = '<i class="glyphicon glyphicon-lock" aria-hidden="true"></i> ';

		$is_manager = isManager(__MID__);
		$login_srl = empty($_MEMBER['mb_srl']) ? false : $_MEMBER['mb_srl'];

		if(__MOBILE__) {
			foreach ($_{'search'}['data'] as $key => $val) {
				$wr_secret =  $val['wr_secret'] == '1';
				$wr_permit = !$wr_secret || $is_manager || $login_srl === $value['mb_srl'];
				echo '<tr data-hot-track style="cursor:pointer"><td class="wr_title"><a href="'.getUrl('','srl',$val['wr_srl'],'disp','','cpage','','rp','').'" target="_blank" onclick="return false">'.($wr_secret?$_tmp:'').escapeHtml($val['wr_title'], true).'</a>'.($val['wr_reply']>0?' <small>(+'.$val['wr_reply'].')</small>':'');
				echo '<div class="clearfix"><span class="mb_nick" data-srl="'.$val['mb_srl'].'" data-rank="'.(ord($val['mb_rank']) - 48).'">'.escapeHtml($val['mb_nick'], true).'</span>';
				echo '<span class="pull-right">'.date('m/d', strtotime($val['wr_update'])).'</span></div></td></tr>';
			}
		} else {
			foreach ($_{'search'}['data'] as $key => $val) {
				$wr_secret =  $val['wr_secret'] == '1';
				$wr_permit = !$wr_secret || $is_manager || $login_srl === $value['mb_srl'];
				echo '<tr data-hot-track style="cursor:pointer"><th class="hidden-xs" scope="row">'.$val['md_id'].'</th>';
				echo '<td class="wr_title"><a href="'.getUrl('','srl',$val['wr_srl'],'disp','','cpage','','rp','').'" target="_blank" onclick="return false">'.($wr_secret?$_tmp:'').escapeHtml($val['wr_title'], true).'</a>'.($val['wr_reply']>0?' <small>(+'.$val['wr_reply'].')</small>':'').'</td>';
				echo '<td nowrap><span class="mb_nick" data-srl="'.$val['mb_srl'].'" data-rank="'.(ord($val['mb_rank']) - 48).'">'.escapeHtml($val['mb_nick'], true).'</span></td>';
				echo '<td class="hidden-xs">'.$val['wr_hit'].'</td>';
				echo '<td>'.date('Y/m/d', strtotime($val['wr_update'])).'</td></tr>';
			}
		}
	?>

	</tbody>
	</table>
</article>

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
	<footer class="clearfix"></footer>
</section>

<script>
	jQuery('.list-table tr[data-hot-track]').click(function() {
		var $i = jQuery(this);
		if ($i[0].tagName == 'TR') {
			var href = $i.find('.wr_title a').attr('href');
			window.open(href, '_blank');
		}
	});
	jQuery('[role="heading"]+[role="description"]').each(function() {
		var $i = jQuery(this)[0];
		$i.innerText = $i.innerText.replace('%s', '<?php echo $total_count ?>');
	});
</script>

<?php
/* End of file default.php */
/* Location: ./module/search/tpl/default.php */
