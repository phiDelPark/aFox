<?php

if(!defined('__AFOX__')) exit();

function proc($data) {

	if(!isset($data['md_id'])) return set_error(getLang('error_request'),4303);

	$_template_file = _AF_MODULES_PATH_ . $data['md_id'] . '/setup.php';
	if(!file_exists($_template_file)) return set_error(getLang('error_founded'),4201);

	$_MODULE_INFO = [];
	@require_once _AF_MODULES_PATH_ . $data['md_id'] . '/info.php';

	ob_start();

	$author = empty($_MODULE_INFO['link'])?escapeHtml($_MODULE_INFO['author']):('<a href="'.escapeHtml($_MODULE_INFO['link']).'" target="_blank">'.escapeHtml($_MODULE_INFO['author']).'</a>');
?>
	<div class="form-group">
		<h3 style="margin-top:0"><?php echo escapeHtml($_MODULE_INFO['title']) ?></h3>
		<div class="row">
			<label class="col-md-2"><?php echo getLang('version') ?></label>
			<?php echo escapeHtml($_MODULE_INFO['version']) ?>
		</div>
		<div class="row">
			<label class="col-md-2"><?php echo getLang('date') ?></label>
			<?php echo escapeHtml($_MODULE_INFO['date']) ?>
		</div>
		<div class="row">
			<label class="col-md-2"><?php echo getLang('author') ?></label>
			<?php echo $author . ' ('.escapeHtml($_MODULE_INFO['email']) . ')' ?>
		</div>
		<p class="help-block"><?php echo nl2br(escapeHtml($_MODULE_INFO['description'])) ?></p>
	</div>
	<hr style="margin:10px 0 25px">
<?php
	unset($data);
	require($_template_file);
	return ['tpl'=>ob_get_clean()];
}

/* End of file getmoduleform.php */
/* Location: ./module/admin/proc/getmoduleform.php */
