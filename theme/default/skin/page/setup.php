<?php
	if(!defined('__AFOX__')) exit();
?>

<section id="pageView">
	<h3 class="pb-3 mb-3 fst-italic border-bottom"><?php echo getLang('write')?></h3>
<form id="setup" method="post" autocomplete="off" enctype="multipart/form-data">
	<input type="hidden" name="success_return_url" value="<?php echo getUrl('disp', '')?>" />
	<input type="hidden" name="module" value="page" />
	<input type="hidden" name="act" value="updatePage" />
	<input type="hidden" name="md_id" value="<?php echo $_DATA['id']?>" />

	<div class="mb-4">
		<?php displayEditor(
				'pg_content',
				$_{'page'}['pg_content'],
				[
					'file'=>[99999, $_{'page'}['md_id'], 1],
					'html'=>$_{'page'}['pg_type'] === '2',
					'toolbar'=>true,
					'typebar'=>array(getLang('content'), ['pg_type'=>[$_{'page'}['pg_type'], ['TEXT'=>'0','MKDW'=>'1','HTML'=>'2']]])
				]
			);
		?>
	</div>

	<hr class="mb-4">
	<div class="d-grid">
		<button type="submit" class="btn btn-success btn-lg">저장</button>
	</div>
</form>
</section>

<?php
/* End of file setup.php */
/* Location: ./module/page/setup.php */
