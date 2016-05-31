<?php
	if(!defined('__AFOX__')) exit();

	$config = getDBItem(_AF_CONFIG_TABLE_, []);
	if(!empty($config['error'])) {
		echo showMessage($config['message'],$config['error']);
	}

?>

<form action="<?php echo _AF_URL_ . '?admin' ?>" method="post" autocomplete="off" enctype="multipart/form-data">
	<input type="hidden" name="success_return_url" value="<?php echo getUrl('', 'admin', 'setup') ?>">
	<input type="hidden" name="error_return_url" value="<?php echo getUrl('', 'admin', 'setup') ?>">
	<input type="hidden" name="act" value="updateSetup">
	<input type="hidden" name="theme" value="<?php echo empty($config['theme'])?'':escapeHtml($config['theme'])?>">

	<div class="form-group">
		<div class="form-inline">
			<div class="input-group">
				<label class="input-group-addon" for="id_start_page"><?php echo getLang('start_page')?></label>
				<input type="text" name="start" class="form-control" style="width:130px" id="id_start_page" maxlength="11" value="<?php echo empty($config['start'])?'':escapeHtml($config['start'])?>" placeholder="<?php echo getLang('page')?>" required>
			</div>
		</div>
		<p class="help-block"><?php echo getLang('desc_start_page')?></p>
	</div>
	<div class="form-group">
		<label for="id_title"><?php echo getLang('title')?></label>
		<input type="text" name="title" class="form-control" id="id_title" maxlength="255" value="<?php echo empty($config['title'])?'':escapeHtml($config['title'])?>">
	</div>
	<div class="form-group">
		<label><?php echo getLang('logo')?></label>
		<?php $isfile = file_exists($tmp = _AF_CONFIG_DATA_.'logo.png')?>
		<div class="fileupload-group" placeholder="<?php echo getLang('warn_permit',['png'])?>">
			<div class="input-group">
				<div class="file-caption form-control"><?php echo $isfile?'<i class="file-item" data-type="image">'.$tmp.'</i>':''?></div>
				<div class="btn btn-primary btn-file">
					<i class="glyphicon glyphicon-folder-open"><?php echo getLang('browse')?>…</i>
					<input name="logo" type="file">
				</div>
			</div>
		</div>
	</div>
	<div class="form-group">
		<label><?php echo getLang('favicon')?></label>
		<?php $isfile = file_exists($tmp = _AF_CONFIG_DATA_.'favicon.ico')?>
		<div class="fileupload-group" placeholder="<?php echo getLang('warn_permit',['16x16 or 32x32 size, ico'])?>">
			<div class="input-group">
				<div class="file-caption form-control"><?php echo $isfile?'<i class="file-item" data-type="image">'.$tmp.'</i>':''?></div>
				<div class="btn btn-primary btn-file">
					<i class="glyphicon glyphicon-folder-open"><?php echo getLang('browse')?>…</i>
					<input name="favicon" type="file">
				</div>
			</div>
		</div>
	</div>
	<div>&nbsp;</div>
	<div class="form-group point-group">
		<label class="sr-only"><?php echo getLang('point')?></label>
		<div class="form-inline">
			<div class="input-group">
				<label class="input-group-addon" for="id_point_login"><?php echo getLang('login')?></label>
				<input type="number" class="form-control" id="id_point_login" name="point_login" min="0" max="1000" maxlength="11" placeholder="<?php echo getLang('point')?>" value="<?php echo (!empty($config['point_login'])&&$config['point_login']>0)?$config['point_login']:''?>">
			</div>
		</div>
		<p class="help-block"><?php echo getLang('desc_point')?></p>
	</div>
	<div>&nbsp;</div>
	<div class="form-group">
		<div class="form-inline">
			<div class="switch-group">
				<input type="hidden" name="use_signup" value="<?php echo empty($config['use_signup'])?'0':$config['use_signup']?>">
				<div class="switch-control">
					<span class="switch switch-handle-on"><?php echo getLang('use')?></span>
					<span class="switch switch-label"><?php echo getLang('member_signup')?></span>
					<span class="switch switch-handle-off"><?php echo getLang('notuse')?></span>
				</div>
			</div>&nbsp;&nbsp;
			<div class="switch-group">
				<input type="hidden" name="use_captcha" value="<?php echo empty($config['use_captcha'])?'0':$config['use_captcha']?>">
				<div class="switch-control">
					<span class="switch switch-handle-on"><?php echo getLang('use')?></span>
					<span class="switch switch-label"><?php echo getLang('captcha')?></span>
					<span class="switch switch-handle-off"><?php echo getLang('notuse')?></span>
				</div>
			</div>&nbsp;&nbsp;
			<div class="switch-group">
				<input type="hidden" name="use_visit" value="<?php echo empty($config['use_visit'])?'0':$config['use_visit']?>">
				<div class="switch-control">
					<span class="switch switch-handle-on"><?php echo getLang('use')?></span>
					<span class="switch switch-label"><?php echo getLang('visit_record')?></span>
					<span class="switch switch-handle-off"><?php echo getLang('notuse')?></span>
				</div>
			</div>&nbsp;&nbsp;
			<div class="switch-group">
				<input type="hidden" name="protect_file" value="<?php echo empty($config['protect_file'])?'0':$config['protect_file']?>">
				<div class="switch-control">
					<span class="switch switch-handle-on"><?php echo getLang('use')?></span>
					<span class="switch switch-label"><?php echo getLang('protect_file')?></span>
					<span class="switch switch-handle-off"><?php echo getLang('notuse')?></span>
				</div>
			</div>&nbsp;&nbsp;
			<div class="switch-group">
			<input type="hidden" name="use_level" value="<?php echo empty($config['use_level'])?'0':$config['use_level']?>">
				<div class="switch-control">
					<span class="switch switch-handle-on"><?php echo getLang('use')?></span>
					<span class="switch switch-label"><?php echo getLang('level_icon')?></span>
					<span class="switch switch-handle-off"><?php echo getLang('notuse')?></span>
				</div>
			</div>
		</div>
		<p class="help-block"><?php echo getLang('desc_options')?></p>
	</div>
	<div class="form-group">
		<label for="id_base_cdn_list"><?php echo getLang('base_cdn_list')?></label>
		<textarea class="form-control min-height-100 vresize" name="base_cdn_list" id="id_base_cdn_list" placeholder="<?php echo getLang('how_to_use')?>) &lt;script src=&quot;//cdn.server.com/cdn.js&quot;&gt;&lt;/script&gt;"><?php if (file_exists($tmp = _AF_CONFIG_DATA_.'base_cdn_list.php')) include $tmp; ?></textarea>
		<p class="help-block"><?php echo getLang('desc_base_cdn_list')?></p>
	</div>
	<div>&nbsp;</div>
	<div class="form-group">
		<label for="id_possible_ip"><?php echo getLang('possible_ip')?></label>
		<textarea class="form-control min-height-100 vresize" name="possible_ip" id="id_possible_ip"><?php if (file_exists($tmp = _AF_CONFIG_DATA_.'possible_ip.php')) {include $tmp; echo implode(',',$_POSSIBLE_IPS);} ?></textarea>
		<p class="help-block"><?php echo getLang('desc_possible_ip')?></p>
	</div>
	<div class="form-group">
		<label for="id_intercept_ip"><?php echo getLang('intercept_ip')?></label>
		<textarea class="form-control min-height-100 vresize" name="intercept_ip" id="id_intercept_ip"><?php if (file_exists($tmp = _AF_CONFIG_DATA_.'intercept_ip.php')) {include $tmp; echo implode(',',$_INTERCEPT_IPS);} ?></textarea>
		<p class="help-block"><?php echo getLang('desc_intercept_ip')?></p>
	</div>
	<div class="form-group">
		<label for="id_prohibit_id"><?php echo getLang('prohibit_id')?></label>
		<textarea class="form-control min-height-100 vresize" name="prohibit_id" id="id_prohibit_id"><?php if (file_exists($tmp = _AF_CONFIG_DATA_.'prohibit_id.php')) {include $tmp; echo implode(',',$_PROHIBIT_IDS);} ?></textarea>
		<p class="help-block"><?php echo getLang('desc_prohibit_id')?></p>
	</div>
	<div class="form-group">
		<label for="id_terms_of_use"><?php echo getLang('terms_of_use')?></label>
		<textarea class="form-control min-height-100 vresize" name="terms_of_use" id="id_terms_of_use"><?php if (file_exists($tmp = _AF_CONFIG_DATA_.'terms_of_use.php')) include $tmp; ?></textarea>
		<p class="help-block"><?php echo getLang('desc_terms_of_use')?></p>
	</div>

	<div class="modal-footer">
		<button type="submit" class="btn btn-success min-width-150"><i class="glyphicon glyphicon-ok" aria-hidden="true"></i> <?php echo getLang('save')?></button>
	</div>

</form>

<?php
/* End of file setup.php */
/* Location: ./module/admin/setup.php */