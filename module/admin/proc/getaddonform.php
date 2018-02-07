<?php

if(!defined('__AFOX__')) exit();

function proc($data) {

	if(!isset($data['ao_id'])) return set_error(getLang('error_request'),4303);

	$_template_file = _AF_ADDONS_PATH_ . $data['ao_id'] . '/setup.php';
	if(!file_exists($_template_file)) return set_error(getLang('error_founded'),4201);

	$_ADDON = DB::get(_AF_ADDON_TABLE_, ['ao_id'=>$data['ao_id']]);
	if(DB::error()) return set_error($error->getMessage(),$error->getCode());

	if(empty($_ADDON['access_mode'])) $_ADDON['access_mode'] = null;

	if(!empty($_ADDON['ao_extra'])) {
		$extra = unserialize($_ADDON['ao_extra']);
		unset($_ADDON['ao_extra']);
		$_ADDON = array_merge($_ADDON, $extra);
	}

	$out = DB::get(_AF_TRIGGER_TABLE_, ['tg_key'=>'A','tg_id'=>$data['ao_id']]);
	$_ADDON['use_pc'] = empty($out['use_pc']) ? 0 : $out['use_pc'];
	$_ADDON['use_mobile'] = empty($out['use_mobile']) ? 0 : $out['use_mobile'];
	$_ADDON['grant_access'] = empty($out['grant_access']) ? '0' : $out['grant_access'];

	$_ADDON_INFO = [];
	@require_once _AF_ADDONS_PATH_ . $data['ao_id'] . '/info.php';

	ob_start();

	$author = empty($_ADDON_INFO['link'])?escapeHtml($_ADDON_INFO['author']):('<a href="'.escapeHtml($_ADDON_INFO['link']).'" target="_blank">'.escapeHtml($_ADDON_INFO['author']).'</a>');

	echo '<div class="form-group"><h3 style="margin-top:0">'.escapeHtml($_ADDON_INFO['title']).'</h3>'
		.'<div class="row"><label class="col-md-2">'.getLang('version').'</label> '.escapeHtml($_ADDON_INFO['version']).'</div>'
		.'<div class="row"><label class="col-md-2">'.getLang('date').'</label> '.escapeHtml($_ADDON_INFO['date']).'</div>'
		.'<div class="row"><label class="col-md-2">'.getLang('author').'</label> '.$author.' ('.escapeHtml($_ADDON_INFO['email']).')</div>'
		.'<p class="help-block">'.nl2br(escapeHtml($_ADDON_INFO['description'])).'</p></div>';

	echo '<div><label class="checkbox inline" tabindex="0">'
		.'<input type="checkbox" name="use_pc" value="1"'.($_ADDON['use_pc']=='1'?' checked':'').'>'
		.'<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span><strong>PC</strong></label>'
		.'<label class="checkbox inline" tabindex="0">'
		.'<input type="checkbox" name="use_mobile" value="1"'.($_ADDON['use_mobile']=='1'?' checked':'').'>'
		.'<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span><strong>Mobile</strong></label></div>'
		.'<div class="form-group"><label class="radio btn mw-10" tabindex="0">'
		.'<input type="radio" name="grant_access" value="0"'.($_ADDON['grant_access']=='0'?' checked':'').'>'
		.'<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>'
		.'<span>'.getLang('all').'</span></label><label class="radio btn mw-10" tabindex="0">'
		.'<input type="radio" name="grant_access" value="1"'.($_ADDON['grant_access']!='0'&&$_ADDON['grant_access']!='m'?' checked':'').'>'
		.'<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span><span>'.getLang('member').'</span>'
		.'</label><label class="radio btn mw-10" tabindex="0"><input type="radio" name="grant_access" value="m"'.($_ADDON['grant_access']=='m'?' checked':'').'>'
		.'<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span><span>'.getLang('admin').'</span></label></div><hr style="margin:10px 0 25px">';

	unset($data);

	require($_template_file);

	$_list = DB::gets(_AF_MODULE_TABLE_,'md_id',[],'md_key');
	if(!DB::error()) {

		echo '<hr style="margin:25px 0 20px">
			<a href="#" style="display:block;padding:5px" onclick="jQuery(this).next().removeClass(\'hide\').end().remove();return false">'.getLang('advanced_setup').'</a>
			<div class="hide"><div><label>'.getLang('md_id').':</label>&nbsp;&nbsp;<label><input name="access_mode" type="radio" value="include"'.($_ADDON['access_mode']!='exclude'?' checked="checked"':'').'> '.getLang('include').'</label>&nbsp;&nbsp;<label><input name="access_mode" type="radio" value="exclude"'.($_ADDON['access_mode']=='exclude'?' checked="checked"':'').'> '.getLang('exclude').'</label></div><p class="help-block">'.getLang('desc_access_md_id').'</p><div>';

		$access_md_ids = [];
		if(!empty($_ADDON['access_md_ids'])) {
			foreach ($_ADDON['access_md_ids'] as $v) {
				$access_md_ids[$v] = true;
			}
		}

		foreach ($_list as $row) {
			echo '<label><input name="access_md_ids[]" type="checkbox" value="'.$row['md_id'].'"'.(empty($access_md_ids[$row['md_id']])?'':' checked="checked"').'> '.$row['md_id'].'</label>&nbsp;&nbsp;';
		}

		echo '</div></div>';
	}

	return ['tpl'=>ob_get_clean()];
}

/* End of file getaddonform.php */
/* Location: ./module/admin/proc/getaddonform.php */
