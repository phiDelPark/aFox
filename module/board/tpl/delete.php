<?php
	if(!defined('__AFOX__')) exit();
	$is = !empty($_{'board'});

	// $cfm_delete = sprintf(getLang($_LANG['confirm_select_delete']), getLang('document'));
?>

<section id="board_delete">
	<header>
		<h3 class="clearfix">
			<span class="pull-left"><i class="fa fa-trash-o" aria-hidden="true"></i> <?php echo getLang('delete')?></span>
			<a class="close" href="<?php echo getUrl('disp','')?>"><span aria-hidden="true">×</span></a>
		</h3>
		<hr class="divider">
	</header>
	<article>
		<form method="post" autocomplete="off" data-exec-ajax="board.deleteDocument">
		<input type="hidden" name="success_return_url" value="<?php echo getUrl('disp','','srl','')?>" />
		<input type="hidden" name="wr_srl" value="<?php echo $is?$_{'board'}['wr_srl']:''?>" />
			<div>
			<?php if (empty($_MEMBER)) { ?>
				<div class="form-group">
					<label for="id_mb_password"><?php echo getLang('password')?></label>
					<input type="password" name="mb_password" class="form-control" id="id_mb_password" required>
				</div>
			<?php } ?>
				<div class="form-group">
					<label for="id_wr_title"><?php echo getLang('title')?></label>
					<input type="text" class="form-control" id="id_wr_title" value="<?php echo $is?$_{'board'}['wr_title']:''?>" readonly="readonly">
				</div>
				<div class="form-group">
					<label for="id_wr_content"><?php echo getLang('content')?></label>
					<textarea class="form-control min-height-200 vresize" id="id_wr_content" readonly="readonly"><?php echo $is?$_{'board'}['wr_content']:''?></textarea>
				</div>
				<div class="area-button">
					<button type="submit" class="btn btn-warning btn-block"><i class="fa fa-trash-o" aria-hidden="true"></i> <?php echo getLang('delete')?></button>
				</div>
			</div>
		</form>
	</article>
</section>