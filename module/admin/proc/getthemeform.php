<?php

if(!defined('__AFOX__')) exit();

function load_template($_template_file) {
	require($_template_file);
}

function proc($data) {

	if(!isset($data['th_id'])) return set_error(getLang('error_request'),4303);

	$_template_file = _AF_THEMES_PATH_ . $data['th_id'] . '/setup.php';

	if(!file_exists($_template_file)) {
		return set_error(getLang('error_founded'),4201);
	}
	$_THEME = DB::get(_AF_THEME_TABLE_,'th_extra',['th_id'=>$data['th_id']]);
	if(!$ex = DB::error()) {
		$_THEME = empty($_THEME['th_extra'])?[]:unserialize($_THEME['th_extra']);
		$_THEME['th_id'] = $data['th_id'];
	} else {
		return set_error($ex->getMessage(),$ex->getCode());
	}

	$_THEME_INFO = [];
	@require_once _AF_THEMES_PATH_ . $data['th_id'] . '/info.php';

	ob_start();

	$author = empty($_THEME_INFO['link'])?escapeHtml($_THEME_INFO['author']):('<a href="'.escapeHtml($_THEME_INFO['link']).'" target="_blank">'.escapeHtml($_THEME_INFO['author']).'</a>');

	echo '<div class="form-group"><h3 style="margin-top:0">'.escapeHtml($_THEME_INFO['title']).'</h3>'
		.'<div class="row"><label class="col-md-2">'.getLang('version').'</label> '.escapeHtml($_THEME_INFO['version']).'</div>'
		.'<div class="row"><label class="col-md-2">'.getLang('date').'</label> '.escapeHtml($_THEME_INFO['date']).'</div>'
		.'<div class="row"><label class="col-md-2">'.getLang('author').'</label> '.$author.' ('.escapeHtml($_THEME_INFO['email']).')</div>'
		.'<p class="help-block">'.nl2br(escapeHtml($_THEME_INFO['description'])).'</p></div>';

	unset($data);

	require($_template_file);

	return ['tpl'=>ob_get_clean()];
}

/* End of file getthemeform.php */
/* Location: ./module/admin/proc/getthemeform.php */
