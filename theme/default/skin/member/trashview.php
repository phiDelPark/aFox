<?php
	if(!defined('__AFOX__')) exit();
?>

<section id="trashView" class="mb-4 border-bottom" aria-label="Contents of this post">
	<h3 class="pb-3 mb-1 border-bottom"><?php echo $_DATA['wr_title']?></h3>
	<p class="d-flex w-100 justify-content-between text-secondary"><span><?php echo date('F j, Y', strtotime($_DATA['wr_regdate'])).' by '.$_DATA['mb_nick']?></span></p>
	<div class="h-md-250 mb-3">
		<?php echo toHTML($_DATA['wr_content'], $_DATA['wr_type']) ?>
	</div>
	<div class="text-end mb-3">
		<button type="button" class="btn btn-danger me-2" data-exec-ajax="board.deleteDocument" data-ajax-param="wr_srl,<?php echo $_DATA['wr_srl']?>,is_empty,1,success_url,<?php echo urlencode(getUrl('srl',''))?>" data-ajax-confirm="<?php echo getLang('confirm_empty',['document'])?>"><?php echo getLang('delete')?></button>
		<button type="button" class="btn btn-warning" data-exec-ajax="board.restoredocument" data-ajax-param="wr_srl,<?php echo $_DATA['wr_srl']?>,success_url,<?php echo urlencode(getUrl('srl',''))?>"><?php echo getLang('restore')?></button>
	</div>
</section>
