<?php
if(!defined('__AFOX__')) exit();
$use_style = ['list','review','gallery','timeline'];
$current_style = $use_style[abs($_CFG['use_style'])];
$configs = $_CFG['md_extra']['configs'];
$show_column = array_flip($configs['show_column']);
?>

<form method="post" autocomplete="off" data-exec-ajax="board.updateConfig">
	<input type="hidden" name="success_return_url" value="<?php echo getUrl()?>">
	<input type="hidden" name="md_id" value="<?php echo __MID__?>">
	<ul id="config_menus">
		<li>
			<a href="#"><?php echo getLang('show_column')?></a>
			<div class="form-group">
				<?php if($current_style == 'list') { ?>
				<label class="checkbox" tabindex="0">
					<input type="checkbox" name="show_column[]" value="wr_srl"<?php echo isset($show_column['wr_srl'])?' checked':''?>>
					<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
					<span><?php echo getLang('number')?></span>
				</label>
				<?php } ?>
				<label class="checkbox" tabindex="0">
					<input type="checkbox" name="show_column[]" value="mb_nick"<?php echo isset($show_column['mb_nick'])?' checked':''?>>
					<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
					<span><?php echo getLang('name')?></span>
				</label>
				<label class="checkbox" tabindex="0">
					<input type="checkbox" name="show_column[]" value="wr_hit"<?php echo isset($show_column['wr_hit'])?' checked':''?>>
					<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
					<span><?php echo getLang('view')?></span>
				</label>
				<label class="checkbox" tabindex="0">
					<input type="checkbox" name="show_column[]" value="wr_good"<?php echo isset($show_column['wr_good'])?' checked':''?>>
					<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
					<span><?php echo getLang('good')?></span>
				</label>
				<label class="checkbox" tabindex="0">
					<input type="checkbox" name="show_column[]" value="wr_hate"<?php echo isset($show_column['wr_hate'])?' checked':''?>>
					<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
					<span><?php echo getLang('hate')?></span>
				</label>
				<label class="checkbox" tabindex="0">
					<input type="checkbox" name="show_column[]" value="wr_regdate"<?php echo isset($show_column['wr_regdate'])?' checked':''?>>
					<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
					<span><?php echo getLang('regdate')?></span>
				</label>
				<label class="checkbox" tabindex="0">
					<input type="checkbox" name="show_column[]" value="wr_update"<?php echo isset($show_column['wr_update'])?' checked':''?>>
					<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
					<span><?php echo getLang('update')?></span>
				</label>
			</div>
		</li>
		<?php if($current_style == 'list') { ?>
		<li>
			<a href="#"><?php echo getLang('list_style')?></a>
			<div class="form-group">
				<span><?php echo getLang('none')?></span>
			</div>
		</li>
		<?php } ?>
		<?php if($current_style == 'review') { ?>
		<li>
			<a href="#"><?php echo getLang('review_style')?></a>
			<div class="form-group">
				<span><?php echo getLang('none')?></span>
			</div>
		</li>
		<?php } ?>
		<?php if($current_style == 'gallery') { ?>
		<li>
			<a href="#"><?php echo getLang('gallery_style')?></a>
			<div class="form-group">
				<span><?php echo getLang('none')?></span>
			</div>
		</li>
		<?php } ?>
	</ul>
</form>
