<?php

if(!defined('__AFOX__')) exit();

function load_template($_template_file) {
	require($_template_file);
}

function proc($data) {

	if(!isset($data['ao_id'])) return set_error(getLang('error_request'),4303);

	$_template_file = _AF_ADDONS_PATH_ . $data['ao_id'] . '/setup.php';

	if(!file_exists($_template_file)) {
		return set_error(getLang('error_founded'),4201);
	}

	$_ADDON = getDBItem(_AF_ADDON_TABLE_, ['ao_id'=>$data['ao_id']]);
	if(!empty($_ADDON['error'])) {
		return set_error( $_ADDON['message'],$_ADDON['error']);
	}
	if(empty($_ADDON['use_pc'])) $_ADDON['use_pc'] = 0;
	if(empty($_ADDON['use_mobile'])) $_ADDON['use_mobile'] = 0;
	if(!empty($_ADDON['ao_extra'])) {
		$extra = unserialize($_ADDON['ao_extra']);
		unset($_ADDON['ao_extra']);
		$_ADDON = array_merge($_ADDON, $extra);
	}

	$_ADDON_INFO = [];
	@require_once _AF_ADDONS_PATH_ . $data['ao_id'] . '/info.php';

	ob_start();

	$author = empty($_ADDON_INFO['link'])?escapeHtml($_ADDON_INFO['author']):('<a href="'.escapeHtml($_ADDON_INFO['link']).'" target="_blank">'.escapeHtml($_ADDON_INFO['author']).'</a>');

	echo '<div class="form-group"><h3 style="margin-top:0">'.escapeHtml($_ADDON_INFO['title']).'</h3>'
		.'<div class="row"><label class="col-md-2">'.getLang('version').'</label> '.escapeHtml($_ADDON_INFO['version']).'</div>'
		.'<div class="row"><label class="col-md-2">'.getLang('date').'</label> '.escapeHtml($_ADDON_INFO['date']).'</div>'
		.'<div class="row"><label class="col-md-2">'.getLang('author').'</label> '.$author.' ('.escapeHtml($_ADDON_INFO['email']).')</div>'
		.'<p class="help-block">'.nl2br(escapeHtml($_ADDON_INFO['description'])).'</p></div>';

	echo '<div><label style="margin:0 20px 10px 0"><input name="use_pc" type="checkbox" value="1"'.($_ADDON['use_pc']=='1'?' checked="checked"':'').'> PC</label>'
		.'<label><input name="use_mobile" type="checkbox" value="1"'.($_ADDON['use_mobile']=='1'?' checked="checked"':'').'> Mobile</label></div>';

	unset($data);

	require($_template_file);

	return ['tpl'=>ob_get_clean()];
}

/* End of file getaddonform.php */
/* Location: ./module/admin/proc/getaddonform.php */