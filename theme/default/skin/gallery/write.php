<?php if(!defined('__AFOX__')) exit();
	$is_manager = isManager(__MID__);
	$is = !empty($DOC)&&!empty($DOC['wr_srl']);
?>

<section id="galleryWrite" aria-label="Writing a post">
	<button type="button" class="btn-close float-end" aria-label="Back" onclick="window.history.go(-1);return false"></button>
	<h2 class="pb-3 mb-4 border-bottom"><?php echo getLang('upload')?></h2>
	<form id="setup" method="post" autocomplete="off" enctype="multipart/form-data" needvalidate>
	<input type="hidden" name="error_url" value="<?php echo getUrl()?>">
	<input type="hidden" name="success_url" value="<?php echo getUrl('disp','','srl','')?>">
	<input type="hidden" name="module" value="gallery" />
	<input type="hidden" name="act" value="updateGallery" />
	<input type="hidden" name="md_id" value="<?php echo __MID__?>">

	<div class="clearfix">
		<?php if (empty($_MEMBER) || (!empty($DOC['wr_srl']) && $_MEMBER['mb_srl'] !== $DOC['mb_srl'])) { ?>
		<div class="mb-4">
			<input type="text" name="mb_nick" class="form-control mb-1"<?php echo empty($_MEMBER)?' required':''?> maxlength="20" placeholder="<?php echo getLang('id')?>" value="<?php echo $is?$DOC['mb_nick']:''?>"<?php echo empty($DOC['wr_srl'])?'':' readonly'?>>
			<?php if (!$is_manager && empty($DOC['mb_srl'])) { ?>
				<input type="password" name="mb_password" class="form-control"<?php echo empty($_MEMBER)?' required':''?> placeholder="<?php echo getLang('password')?>">
			<?php } ?>
		</div>
		<?php } ?>
		<?php if (!empty($_CFG['md_category'])) { $tmp = explode(',', $_CFG['md_category']);?>
			<div class="form-floating mb-2">
			<?php if ($use_style == 'gallery') { echo '<div class="form-control checkbox-group required">'; $tags = $is?explode(',',$DOC['wr_tags']):[]; foreach($tmp as $val){?>
					<input type="checkbox" name="wr_tags[]" value="<?php echo $val?>"<?php echo in_array($val, $tags)?' checked':''?> class="form-check-input" id="wrExtra_<?php echo $val?>">
					<label class="me-2" for="wrExtra_<?php echo $val?>"><?php echo $val?></label>
			<?php } echo '<input type="hidden" name="wr_category" value="'.$tmp[0].'"></div>';} else { ?>
					<select name="wr_category" class="form-control" id="wrCategory" required>
					<option value=""></option>
					<?php
						foreach ($tmp as $val) {
							echo '<option value="'.$val.'"'.(($is&&$val==$DOC['wr_category'])?' selected="selected"':'').'>'.$val.'</option>';
						}
					?>
					</select>
			<?php } ?>
				<label for="wrCategory"><?php echo getLang('category')?></label>
			</div>
		<?php } ?>
			<div class="mb-4">
		<?php
			displayEditor(
				'wr_content', $is?$DOC['wr_content']:'',
				[
					'file'=>[__MID__, 0, $_CFG['md_file_max'], '.jpg,.jpeg,.png'],
					'placeholder'=>getLang('gallery_content'),
					'height'=>'38px',
					'readonly'=>true
				]
			);
		?>
			</div>

			<hr class="mb-4">
			<div class="d-grid">
				<button type="submit" class="btn btn-success btn-lg"><?php echo getLang('save')?></button>
			</div>
		</div>
	</form>
</section>

<?php
/* End of file write.php */
/* Location: ./theme/default/skin/gallery/write.php */