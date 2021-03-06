<?php
	if(!defined('__AFOX__')) exit();
?>

<section id="setup_page">
	<header>
		<h3 class="clearfix">
			<span class="pull-left"><i class="glyphicon glyphicon-edit" aria-hidden="true"></i> <?php echo getLang('edit')?></span>
			<a class="close" href="<?php echo getUrl('disp','')?>"><span aria-hidden="true">×</span></a>
		</h3>
		<hr class="divider">
	</header>

	<article class="page-editer">
	<form onsubmit="return false" method="post" autocomplete="off" enctype="multipart/form-data" data-exec-ajax="page.updatePage">
	<input type="hidden" name="success_return_url" value="<?php echo getUrl('disp','')?>">
	<input type="hidden" name="md_id" value="<?php echo __MID__?>">

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
					displayEditor(
						'pg_content', $PAGE['pg_content'],
						[
							'file'=>[9000, __MID__, 1],
							'html'=>$PAGE['pg_type']==='2',
							'statebar'=>true,
							'toolbar'=>array(
								getLang('content'),
								[
									'pg_type'=>[
										$PAGE['pg_type'],
										['TEXT'=>'0','MKDW'=>'1','HTML'=>'2']
									]
								]
							)
						]
					);
				?>
			</div>

			<div class="area-button">
				<button type="submit" class="btn btn-success btn-block"><i class="glyphicon glyphicon-ok" aria-hidden="true"></i> <?php echo getLang('save')?></button>
			</div>
		</div>
	</form>
	</article>
</section>

<?php
/* End of file setup.php */
/* Location: ./theme/default/page/setup.php */
