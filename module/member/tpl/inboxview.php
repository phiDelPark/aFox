<?php
	if(!defined('__AFOX__')) exit();
	$_item = getDBItem(_AF_NOTE_TABLE_, ['mb_srl'=>$mb['mb_srl'],'nt_srl'=>$_DATA['srl']]);
	if(empty($_item['nt_srl'])) return;
	if($_item['nt_read_date'] === '0000-00-00 00:00:00') {
		DB::update(_AF_NOTE_TABLE_, ['(nt_read_date)'=>'NOW()'], ['nt_srl'=>$_DATA['srl']]);
	}
?>
<section>
	<header>
		<h4 class="clearfix" style="margin:0"><?php echo date((__MOBILE__?'y':'Y').getLang('year').' m'.getLang('month').' d'.getLang('day').' A h:i', strtotime($_item['nt_send_date']))?></h4>
		<hr class="divider" style="margin:3px">
		<div class="clearfix">
			<span class="pull-left"><?php echo $_item['nt_sender_nick']?></span>
			<span class="pull-right"><?php echo date((__MOBILE__?'y':'Y').getLang('year').' m'.getLang('month').' d'.getLang('day').' A h:i', strtotime($_item['nt_read_date']))?></span>
		</div>
	</header>
	<article style="padding:20px 5px 30px;min-height:200px">
	<?php
		echo $_item['nt_content'];
	?>
	</article>
	<footer class="area-text-button clearfix" style="text-align:right;margin-bottom:50px">
		<button type="button" class="btn btn-danger" data-exec-ajax="board.del" data-ajax-param="wr_srl,<?php echo $_item['wr_srl']?>,is_empty,1,success_return_url,<?php echo getUrl('srl','')?>"><?php echo getLang('delete')?></button>
		<button type="button" class="btn btn-success min-width-100" data-exec-ajax="board.jj" data-ajax-param="wr_srl,<?php echo $_item['wr_srl']?>,success_return_url,<?php echo getUrl('srl','')?>"><i class="glyphicon glyphicon-refresh" aria-hidden="true"></i> <?php echo getLang('reply')?></button>
	</footer>
</section>

