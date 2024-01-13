<?php
if(!defined('__AFOX__')) exit();
?>

<article class="clearfix">
	<ul class="timeline" role="list">
	<?php
		$current_page = $LIST['current_page'];
		$total_page = $LIST['total_page'];
		$start_page = $LIST['start_page'];
		$end_page = $LIST['end_page'];
		$srl = empty($_DATA['srl'])?0:$_DATA['srl'];
		$paytypes = array('그외','현금','카드','은행');

		foreach ($LIST['data'] as $key => $val) {

			$_icon = $_MEMBER['mb_srl'].'/profile_image.png';
			$_icon = _AF_URL_ . (file_exists(_AF_MEMBER_DATA_.$_icon) ? 'data/member/' . $_icon : 'common/img/user_default.jpg');
			$amount = ((int)$val['ev_amount'] - (int)$val['ev_payment']);
			?>
			<li>
				<time datetime="<?php echo $val['ev_reserve'] ?>"><span><?php echo substr($val['ev_reserve'], 0, 4) ?></span> <span><?php echo str_replace('-', '/', substr($val['ev_reserve'], 5, 5)) ?></span></time>
				<div class="tmicon<?php echo $val['ev_srl']==$srl?' active':'' ?>"></div>
				<div class="tmlabel" data-toggle="modal" data-target="#ledger_write_modal" data-ledger-srl="<?php echo $val['ev_srl'] ?>">
					<h3 class="text-ellipsis"><?php echo escapeHtml($val['ev_title'], true) ?></h3>
					<p class='clearfix'>
						<div class="pull-left">[<?php echo $amount > 0 ? '미결' : $paytypes[$val['ev_paytype']] ?>]</div>
						<div class="pull-right"<?php echo $amount > 0 ? 'style="color:darkred"' : '' ?>><?php echo number_format($amount > 0 ? $amount : $val['ev_payment']) ?></div>
					</p>
					<div class='clearfix'></div>
				</div>
			</li>
			<?php
		}
	?>
	</ul>
</article>
