<?php
	if(!defined('__AFOX__')) exit();

	if (empty($_DATA['mid'])) {
		include_once dirname(__FILE__) . '/module.ls.php';
	} else {

	@include _AF_MODULES_PATH_ . $_DATA['mid'] . '/info.php';
?>

		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header">
					<?php echo $_MODULE_INFO['title']?>
					<small><?php echo getLang('setup')?></small>
				</h1>
				<ol class="breadcrumb">
					<li class="active">
						<i class="glyphicon glyphicon-th-large" aria-hidden="true"></i>
						<?php echo $_MODULE_INFO['description']?>
					</li>
				</ol>
			</div>
		</div>

<?php
		include_once _AF_MODULES_PATH_ . $_DATA['mid'] . '/setup.php';
	}
/* End of file module.php */
/* Location: ./module/admin/module.php */
