<?php
	if(!defined('__AFOX__')) exit();
	$_item = DB::get(_AF_DOCUMENT_TABLE_, ['md_id'=>'_AFOXtRASH_','mb_srl'=>$mb['mb_srl'],'wr_srl'=>$_DATA['srl']]);
	if(empty($_item['wr_srl'])) return;
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
		<button type="button" class="btn btn-danger" data-exec-ajax="board.deleteDocument" data-ajax-param="wr_srl,<?php echo $_item['wr_srl']?>,is_empty,1,success_return_url,<?php echo getUrl('srl','')?>" onclick="jQuery(this).data('clicked', confirm($_LANG['confirm_empty'].sprintf([$_LANG['document']]).escapeHtml()))"><?php echo getLang('delete')?></button>
		<button type="button" class="btn btn-warning mw-10" data-exec-ajax="board.restoredocument" data-ajax-param="wr_srl,<?php echo $_item['wr_srl']?>,success_return_url,<?php echo getUrl('srl','')?>"><i class="glyphicon glyphicon-refresh" aria-hidden="true"></i> <?php echo getLang('restore')?></button>
	</footer>
</section>

