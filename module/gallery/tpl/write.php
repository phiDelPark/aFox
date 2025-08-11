<?php if(!defined('__AFOX__')) exit();
	$is_manager = isManager(_MID_);
?>

<section id="galleryWrite" aria-label="Writing a post">
	<button class="btn-close float-end" aria-label="Back" onclick="window.history.go(-1);return false"></button>
	<h2 class="pb-3 mb-4 border-bottom"><?php echo getLang('upload')?></h2>
	<form id="setup" method="post" autocomplete="off" enctype="multipart/form-data" needvalidate>
	<input type="hidden" name="error_url" value="<?php echo getUrl()?>">
	<input type="hidden" name="success_url" value="<?php echo getUrl('disp','','srl','')?>">
	<input type="hidden" name="module" value="gallery" />
	<input type="hidden" name="act" value="updateGallery" />
	<input type="hidden" name="md_id" value="<?php echo _MID_?>">

	<div class="clearfix">
		<?php if (empty($_MEMBER)) { ?>
		<div class="mb-4">
			<input type="text" name="mb_nick" class="form-control mb-1" maxlength="20" placeholder="<?php echo getLang('id')?>" required>
			<input type="password" name="mb_password" class="form-control" placeholder="<?php echo getLang('password')?>" required>
		</div>
		<?php } if (!empty($_CFG['md_category'])) { $tmp = explode(',', $_CFG['md_category']);?>
		<div class="form-floating mb-2"><div class="form-control checkbox-group required">
			<?php $tags = [];
				foreach($tmp as $val){?>
					<input type="checkbox" name="wr_tags[]" value="<?php echo $val?>"<?php echo in_array($val, $tags)?' checked':''?> class="form-check-input" id="wrExtra_<?php echo $val?>">
					<label class="me-2" for="wrExtra_<?php echo $val?>"><?php echo $val?></label>
			<?php } ?>
		</div><label for="wrCategory"><?php echo getLang('category')?></label></div>
		<?php } ?>
		<div class="mb-4">
		<?php
			displayEditor(
				'wr_content', '',
				[
					'file'=>[_MID_, 0, $_CFG['md_file_max'], 'jpg,jpeg,png'],
					'placeholder'=>getLang('add_image'),
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
<style>#uploadFiles{min-height:114px}</style>

<?php
/* End of file write.php */
/* Location: ./theme/default/skin/gallery/write.php */