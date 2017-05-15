<?php

if(!defined('__AFOX__')) exit();

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

	echo '<div><label style="margin-right:20px"><input name="use_pc" type="checkbox" value="1"'.($_ADDON['use_pc']=='1'?' checked="checked"':'').'> PC</label>'
		.'<label><input name="use_mobile" type="checkbox" value="1"'.($_ADDON['use_mobile']=='1'?' checked="checked"':'').'> Mobile</label></div><hr style="margin:10px 0 25px">';

	unset($data);

	require($_template_file);

	$out = DB::query('SELECT md_id FROM '._AF_MODULE_TABLE_.' WHERE 1 ORDER BY md_key');
	if(!DB::error()) {
		echo '<hr style="margin:25px 0 20px"><a href="#" style="display:block;padding:5px" onclick="jQuery(this).next().removeClass(\'hide\').end().remove();return false">'.getLang('advanced_setup').'</a><div class="hide"><div><label>'.getLang('md_id').':</label>&nbsp;&nbsp;<label><input name="access_mode" type="radio" value="include"'.($_ADDON['access_mode']!='exclude'?' checked="checked"':'').'> '.getLang('include').'</label>&nbsp;&nbsp;<label><input name="access_mode" type="radio" value="exclude"'.($_ADDON['access_mode']=='exclude'?' checked="checked"':'').'> '.getLang('exclude').'</label></div><p class="help-block">'.getLang('desc_access_md_id').'</p><div>';

		$access_md_ids = [];
		if(!empty($_ADDON['access_md_ids'])) {
			foreach ($_ADDON['access_md_ids'] as $v) {
				$access_md_ids[$v] = true;
			}
		}

		while ($row = DB::assoc($out)) {
			echo '<label><input name="access_md_ids[]" type="checkbox" value="'.$row['md_id'].'"'.($access_md_ids[$row['md_id']]===true?' checked="checked"':'').'> '.$row['md_id'].'</label>&nbsp;&nbsp;';
		}

		echo '</div></div>';
	}

	return ['tpl'=>ob_get_clean()];
}

/* End of file getaddonform.php */
/* Location: ./module/admin/proc/getaddonform.php */