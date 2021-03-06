<?php
	if(!defined('__AFOX__')) exit();

	$config = DB::get(_AF_CONFIG_TABLE_);
	if($ex=DB::error()) messageBox($ex->getMessage(), $ex->getCode(), false);
?>

<form action="<?php echo _AF_URL_ . '?admin' ?>" method="post" autocomplete="off" enctype="multipart/form-data">
	<input type="hidden" name="success_return_url" value="<?php echo getUrl('', 'admin', 'setup') ?>">
	<input type="hidden" name="error_return_url" value="<?php echo getUrl('', 'admin', 'setup') ?>">
	<input type="hidden" name="act" value="updateSetup">
	<input type="hidden" name="lang" value="ko">
	<input type="hidden" name="theme" value="<?php echo empty($config['theme'])?'default':$config['theme']?>">

	<div class="form-group">
		<div class="form-inline">
			<div class="input-group">
				<label class="input-group-addon" for="id_start_page"><?php echo getLang('start_page')?></label>
				<input type="text" name="start" class="form-control" style="width:130px" id="id_start_page" maxlength="11" value="<?php echo empty($config['start'])?'':$config['start']?>" placeholder="<?php echo getLang('page')?>" required>
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
		<div class="uploader-group" placeholder="<?php echo getLang('warning_allowable',['png'])?>">
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
		<div class="uploader-group" placeholder="<?php echo getLang('warning_allowable',['16x16 or 32x32 size, ico'])?>">
			<div class="input-group">
				<div class="file-caption form-control"><?php echo $isfile?'<i class="file-item" data-type="image">'.$tmp.'</i>':''?></div>
				<div class="btn btn-primary btn-file">
					<i class="glyphicon glyphicon-folder-open"><?php echo getLang('browse')?>…</i>
					<input name="favicon" type="file">
				</div>
			</div>
		</div>
	</div>
	<label><?php echo getLang('option')?></label>
	<div class="form-group point-group">
		<label class="sr-only"><?php echo getLang('point')?></label>
		<div class="form-inline">
			<div class="input-group">
				<label class="input-group-addon" for="id_point_login"><?php echo getLang('login')?></label>
				<input type="number" class="form-control" id="id_point_login" name="point_login" min="-9999" max="9999" maxlength="5" placeholder="<?php echo getLang('point')?>" value="<?php echo (!empty($config['point_login'])&&$config['point_login']>0)?$config['point_login']:''?>">
			</div>
		</div>
		<p class="help-block"><?php echo getLang('desc_point')?></p>
	</div>
	<div class="form-group">
		<label class="checkbox" tabindex="0">
			<input type="checkbox" name="use_full_login" value="1" <?php echo !empty($config['use_full_login'])?'checked':''?>>
			<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
			<?php echo getLang('desc_use_full_login')?>
		</label>
		<label class="checkbox" tabindex="0">
			<input type="checkbox" name="use_signup" value="1" <?php echo !empty($config['use_signup'])?'checked':''?>>
			<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
			<?php echo getLang('desc_use_signup')?>
		</label>
		<label class="checkbox" tabindex="0">
			<input type="checkbox" name="use_visit" value="1" <?php echo !empty($config['use_visit'])?'checked':''?>>
			<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
			<?php echo getLang('desc_use_visit')?>
		</label>
		<label class="checkbox" tabindex="0">
			<input type="checkbox" name="use_protect" value="1" <?php echo !empty($config['use_protect'])?'checked':''?>>
			<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
			<?php echo getLang('desc_use_protect')?>
		</label>
		<label class="checkbox" tabindex="0">
			<input type="checkbox" name="use_captcha" value="1" <?php echo !empty($config['use_captcha'])?'checked':''?>>
			<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
			<?php echo getLang('desc_use_captcha')?>
		</label>
	</div>
	<div class="form-group">
		<label for="id_base_cdn_list"><?php echo getLang('base_cdn_list')?></label>
		<textarea class="form-control mh-10 vresize" name="base_cdn_list" id="id_base_cdn_list" placeholder="<?php echo getLang('how_to_use')?>) &lt;script src=&quot;//cdn.server.com/cdn.js&quot;&gt;&lt;/script&gt;"><?php if (file_exists($tmp = _AF_CONFIG_DATA_.'base_cdn_list.php')) include $tmp; ?></textarea>
		<p class="help-block"><?php echo getLang('desc_base_cdn_list')?></p>
	</div>
	<div class="form-group">
		<?php
			$_ACCESS_IP_MODE = '';
			if (file_exists($tmp = _AF_CONFIG_DATA_.'access_ip.php')) {
				include $tmp;
			}
		?>
		<label for="id_access_ip"><?php echo getLang('ip')?></label>: &nbsp;&nbsp;<label><input type="radio" name="access_ip_mode" value="possible" <?php echo $_ACCESS_IP_MODE=='possible'?' checked':''?>> <?php echo getLang('possible')?></label>&nbsp;&nbsp;<label><input type="radio" name="access_ip_mode" value="intercept"<?php echo $_ACCESS_IP_MODE!='possible'?' checked':''?>> <?php echo getLang('intercept')?></label>
		<textarea class="form-control mh-10 vresize" name="access_ip" id="id_access_ip"><?php echo empty($_ACCESS_IPS) ? '' : str_replace("\.", ".", str_replace("[0-9\.]+", "+", implode("\n",$_ACCESS_IPS))); ?></textarea>
		<p class="help-block"><?php echo getLang('desc_access_ip')?></p>
	</div>
	<div class="form-group">
		<label for="id_prohibit_id"><?php echo getLang('prohibit_id')?></label>
		<textarea class="form-control mh-10 vresize" name="prohibit_id" id="id_prohibit_id"><?php if (file_exists($tmp = _AF_CONFIG_DATA_.'prohibit_id.php')) {include $tmp; echo implode(',',$_PROHIBIT_IDS);} ?></textarea>
		<p class="help-block"><?php echo getLang('desc_prohibit_id')?></p>
	</div>
	<div class="form-group">
		<label for="id_terms_of_use"><?php echo getLang('terms_of_use')?></label>
		<textarea class="form-control mh-10 vresize" name="terms_of_use" id="id_terms_of_use"><?php if (file_exists($tmp = _AF_CONFIG_DATA_.'terms_of_use.php')) include $tmp; ?></textarea>
		<p class="help-block"><?php echo getLang('desc_terms_of_use')?></p>
	</div>

	<div class="modal-footer">
		<button type="submit" class="btn btn-success mw-20"><i class="glyphicon glyphicon-ok" aria-hidden="true"></i> <?php echo getLang('save')?></button>
	</div>

</form>

<?php
/* End of file setup.php */
/* Location: ./module/admin/setup.php */
