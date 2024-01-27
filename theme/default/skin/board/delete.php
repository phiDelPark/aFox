<?php
	if(!defined('__AFOX__')) exit();
	@include_once dirname(__FILE__) . '/common.php';
	$is_manager = isManager(__MID__);
?>
<section id="documentDelete" aria-label="Delete this post?">
	<h3 class="pb-3 mb-4 fst-italic border-bottom"><?php echo getLang('delete')?></h3>
	<form id="setup" method="post" autocomplete="off" enctype="multipart/form-data">
	<input type="hidden" name="error_url" value="<?php echo getUrl()?>">
	<input type="hidden" name="success_url" value="<?php echo getUrl('disp','','srl','','cpage','','rp','')?>" />
	<input type="hidden" name="module" value="board" />
	<input type="hidden" name="act" value="deleteDocument" />
	<input type="hidden" name="wr_srl" value="<?php echo $DOC['wr_srl']?>" />

		<?php if (empty($_MEMBER) || ($_MEMBER['mb_srl'] !== $DOC['mb_srl'])) { ?>
		<div class="mb-4">
			<input type="text" name="mb_nick" class="form-control mb-1" maxlength="20" value="<?php echo escapeHtml($DOC['mb_nick'])?>" required readonly>
			<?php if (!$is_manager) { ?>
				<input type="password" name="mb_password" class="form-control" placeholder="<?php echo getLang('password')?>">
			<?php } ?>
		</div>
		<?php } ?>
		<div class="mb-2">
			<label><?php echo getLang('title')?></label>
			<input type="text" class="form-control" maxlength="255" value="<?php echo escapeHtml($DOC['wr_title'])?>" readonly>
		</div>
		<div class="mb-4">
			<label><?php echo getLang('content')?></label>
			<textarea class="form-control" style="height:150px" readonly><?php echo escapeHtml(preg_replace('#<br\s*/?>|\s*\r?\n\s*\r?\n#i', "\n", trim(str_replace('&nbsp;', ' ', strip_tags($DOC['wr_content'], '<br>')))), 0)?></textarea>
		</div>
		<hr class="mb-4">
		<div class="d-grid">
			<button type="submit" class="btn btn-danger btn-lg"><?php echo getLang('delete')?></button>
		</div>
	</form>
</section>
