<?php
	if(!defined('__AFOX__')) exit();
	$_item = &$_{'member'};
?>
<section>
	<header>
		<h4 class="clearfix" style="margin:0"><?php echo escapeHtml($_item['wr_title'])?></h4>
		<hr class="divider" style="margin:3px">
		<div class="clearfix">
			<span class="pull-left"><?php echo $_item['mb_nick']?></span>
			<span class="pull-right"><?php echo date((__MOBILE__?'y':'Y').getLang('year').' m'.getLang('month').' d'.getLang('day').' A h:i', strtotime($_item['wr_regdate']))?></span>
		</div>
	</header>
	<article style="padding:20px 5px 30px;min-height:200px">
	<?php
		echo toHTML($_item['wr_content'], $_item['wr_type']);
	?>
	</article>
	<footer class="area-text-button clearfix" style="text-align:right;margin-bottom:50px">
		<button type="button" class="btn btn-danger" data-exec-ajax="board.deleteDocument" data-ajax-param="wr_srl,<?php echo $_item['wr_srl']?>,is_empty,1,success_return_url,<?php echo getUrl('srl','')?>" data-ajax-confirm="<?php echo getLang('confirm_empty',['document'])?>"><?php echo getLang('delete')?></button>
		<button type="button" class="btn btn-warning mw-10" data-exec-ajax="board.restoredocument" data-ajax-param="wr_srl,<?php echo $_item['wr_srl']?>,success_return_url,<?php echo getUrl('srl','')?>"><i class="glyphicon glyphicon-refresh" aria-hidden="true"></i> <?php echo getLang('restore')?></button>
	</footer>
</section>

