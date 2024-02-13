<?php if(!defined('__AFOX__')) exit();?>

<section id="trashView" class="mb-4 border-bottom" aria-label="Contents of this post">
	<h3 class="pb-3 mb-1 border-bottom"><?php echo $_DATA['wr_title']?></h3>
	<p class="d-flex w-100 justify-content-between text-secondary"><span><?php echo date('F j, Y', strtotime($_DATA['wr_regdate'])).' by '.$_DATA['mb_nick']?></span></p>
	<div class="h-md-250 mb-3">
		<?php echo toHTML($_DATA['wr_content'], $_DATA['wr_type']) ?>
	</div>
	<div class="clearfix"></div>
	<form class="text-end mb-3" method="post" onsubmit="return themeRestoreInboxItem(this)">
		<input type="hidden" name="error_url" value="<?php echo getUrl()?>" />
		<input type="hidden" name="success_url" value="<?php echo getUrl('srl', '')?>" />
		<input type="hidden" name="module" value="board" />
		<input type="hidden" name="act" value="restoreDocument" />
		<input type="hidden" name="wr_srl" value="<?php echo $_DATA['wr_srl']?>" />
		<button type="submit" class="btn btn-warning"><?php echo getLang('restore')?></button>
	</form>
</section>
<script>function themeRestoreInboxItem(e){let r=function(e){exec_ajax({...e.serializeArray()}).then(e=>{location.href=e.redirect_url}).catch(e=>{alert(e)})},t=confirm($_LANG.confirm_restore.sprintf([$_LANG.document]));return"object"==typeof t?t.then(()=>{r(e)}):!0===t&&r(e),!1}</script>