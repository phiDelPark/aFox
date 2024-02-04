<?php
	if(!defined('__AFOX__')) exit();
?>

<section id="pageView">
	<h3 class="pb-3 mb-3 border-bottom"><?php echo getLang('write')?></h3>
<form id="setup" method="post" autocomplete="off" enctype="multipart/form-data" needvalidate>
	<input type="hidden" name="error_url" value="<?php echo getUrl()?>">
	<input type="hidden" name="success_url" value="<?php echo getUrl('disp', '')?>" />
	<input type="hidden" name="module" value="page" />
	<input type="hidden" name="act" value="updatePage" />
	<input type="hidden" name="md_id" value="<?php echo $_POST['id']?>" />

	<div class="mb-4">
		<?php displayEditor(
				'pg_content',
				$_DATA['pg_content'],
				[
					'file'=>[$_DATA['md_id'], 1, 99999],
					'html'=>$_DATA['pg_type'] === '2',
					'toolbar'=>true,
					'typebar'=>array(getLang('content'), ['pg_type'=>[$_DATA['pg_type'], ['TEXT'=>'0','MKDW'=>'1','HTML'=>'2']]])
				]
			);
		?>
	</div>

	<hr class="mb-4">
	<div class="d-grid">
		<button type="submit" class="btn btn-success btn-lg"><?php echo getLang('save')?></button>
	</div>
</form>
</section>

<?php
/* End of file setup.php */
/* Location: ./module/page/setup.php */
