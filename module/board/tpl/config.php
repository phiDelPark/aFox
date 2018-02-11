<?php
if(!defined('__AFOX__')) exit();
@include_once dirname(__FILE__) . '/lang/' . _AF_LANG_ . '.php';

$use_style = ['list','review','gallery','timeline'];
$current_style = $use_style[abs($_CFG['use_style'])];

// 개별 설정 초기화
if(!isset($_CFG['md_extra']['configs'])) {
	$_CFG['md_extra']['configs'] = [
		'show_column'=>['wr_srl','wr_title','mb_nick','wr_hit','wr_regdate'],
		'review_author'=>1
	];
}
$configs = $_CFG['md_extra']['configs'];
$show_column = array_flip($configs['show_column']);
?>

<form method="post" autocomplete="off" data-exec-ajax="board.updateConfig">
	<input type="hidden" name="success_return_url" value="<?php echo getUrl()?>">
	<input type="hidden" name="md_id" value="<?php echo __MID__?>">
	<ul id="config_menus" style="width:200px">
		<li>
			<a href="#"><?php echo getLang('list_style')?></a>
			<div class="form-group">
				<?php if($current_style == 'list') { ?>
				<label class="checkbox" tabindex="0">
					<input type="checkbox" name="show_column[]" value="wr_srl"<?php echo isset($show_column['wr_srl'])?' checked':''?>>
					<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
					<span><?php echo getLang('number')?></span>
				</label>
				<?php } elseif(isset($show_column['wr_srl'])) { ?>
					<input class="hide" type="checkbox" name="show_column[]" value="wr_srl" checked>
				<?php } ?>
				<input class="hide" type="checkbox" name="show_column[]" value="wr_title" checked>
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
				<label class="radio" tabindex="0">
					<input type="radio" name="show_column[]" value="wr_regdate"<?php echo isset($show_column['wr_regdate'])?' checked':''?>>
					<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
					<span><?php echo getLang('regdate')?></span>
				</label>
				<label class="radio" tabindex="0">
					<input type="radio" name="show_column[]" value="wr_update"<?php echo isset($show_column['wr_update'])?' checked':''?>>
					<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
					<span><?php echo getLang('update')?></span>
				</label>
			</div>
		</li>
		<li>
			<a href="#"><?php echo getLang('review_style')?></a>
			<div class="form-group">
				<label class="checkbox" tabindex="0">
					<input type="checkbox" name="review_author" value="1"<?php echo !empty($configs['review_author'])?' checked':''?>>
					<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
					<span><?php echo getLang('name')?></span>
				</label>
				<label class="checkbox" tabindex="0">
					<input type="checkbox" name="print_extra_vars" value="1"<?php echo !empty($configs['print_extra_vars'])?' checked':''?>>
					<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
					<span><?php echo getLang('print_extra_vars')?></span>
				</label>
			</div>
		</li>
		<li>
			<a href="#"><?php echo getLang('gallery_style')?></a>
			<div class="form-group">
				<label class="checkbox" tabindex="0">
					<input type="checkbox" name="gallery_author" value="1"<?php echo !empty($configs['gallery_author'])?' checked':''?>>
					<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
					<span><?php echo getLang('name')?></span>
				</label>
				<label class="checkbox" tabindex="0">
					<input type="checkbox" name="modal_image_view" value="1"<?php echo !empty($configs['modal_image_view'])?' checked':''?>>
					<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
					<span><?php echo getLang('modal_image_view')?></span>
				</label>
			</div>
		</li>
	</ul>
</form>
