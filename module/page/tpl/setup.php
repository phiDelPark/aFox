<?php
	if(!defined('__AFOX__')) exit();
?>

<section id="setup_page">
	<header>
		<h3 class="clearfix">
			<span class="pull-left"><i class="fa fa-pencil<?php echo getLang('-square-o')?>" aria-hidden="true"></i> <?php echo getLang('edit')?></span>
			<a class="close" href="<?php echo getUrl('disp','')?>"><span aria-hidden="true">×</span></a>
		</h3>
		<hr class="divider">
	</header>

	<article class="page-editer">
	<form onsubmit="return false" method="post" autocomplete="off" enctype="multipart/form-data" data-exec-ajax="page.updatePage">
	<input type="hidden" name="success_return_url" value="<?php echo getUrl('disp','')?>">
	<input type="hidden" name="md_id" value="<?php echo $_DATA['id']?>">
	<input type="hidden" name="grant_view" value="<?php echo $_CFG['grant_view']?>">
	<input type="hidden" name="grant_reply" value="<?php echo $_CFG['grant_reply']?>">

		<div>
			<div class="form-group">
				<label for="id_md_title"><?php echo getLang('title')?></label>
				<input type="text" name="md_title" class="form-control" id="id_md_title" maxlength="255" value="<?php echo escapeHtml($_CFG['md_title'])?>">
			</div>
			<div class="form-group">
				<label for="id_md_description"><?php echo getLang('explain')?></label>
				<input type="text" name="md_description" class="form-control" id="id_md_description" maxlength="255" value="<?php echo escapeHtml($_CFG['md_description'])?>">
			</div>
			<div class="form-group">
				<?php
					dispEditor(
						'pg_content',
						$_{'page'}['pg_content'],
						[
							'file'=>[99999, $_DATA['id'], 1],
							'statebar'=>true,
							'toolbar'=>array(
								getLang('content'),
								[
									'pg_type'=>[
										$_{'page'}['pg_type'],
										['TEXT'=>'0','MKDW'=>'1','HTML'=>'2']
									]
								]
							)
						]
					);
				?>
			</div>

			<div class="area-button">
				<button type="submit" class="btn btn-success btn-block"><i class="fa fa-check" aria-hidden="true"></i> <?php echo getLang('save')?></button>
			</div>
		</div>
	</form>
	</article>
</section>

<?php
/* End of file setup.php */
/* Location: ./theme/default/page/setup.php */