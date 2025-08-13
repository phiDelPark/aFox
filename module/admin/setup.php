<?php
	if(!defined('__AFOX__')) exit();

	$config = DB::get(_AF_CONFIG_TABLE_);
	if($ex=DB::error()) messageBox($ex->getMessage(), $ex->getCode(), false);
?>
<style>
input[name=logo]::before {
	content: "<?php echo getLang('logo')?>";
}
input[name=favicon]::before {
	content: "<?php echo getLang('favicon')?>";
}
</style>

<form method="post" autocomplete="off" enctype="multipart/form-data">
	<input type="hidden" name="version" value="<?php echo _AF_VERSION_?>">
	<input type="hidden" name="success_url" value="<?php echo getUrl('', 'admin', 'setup') ?>">
	<input type="hidden" name="error_url" value="<?php echo getUrl('', 'admin', 'setup') ?>">
	<input type="hidden" name="module" value="admin">
	<input type="hidden" name="act" value="updateSetup">
	<input type="hidden" name="theme" value="<?php echo empty($config['theme'])?'default':$config['theme']?>">
	<input type="hidden" name="lang" value="<?php echo empty($config['lang'])?'ko':$config['lang']?>">

	<div class="mb-4">
		<div class="input-group">
			<label class="input-group-text w-100p" for="id_start"><?php echo getLang('start')?></label>
			<input type="text" name="start" class="form-control mw-150p" id="id_start" maxlength="11" value="<?php echo empty($config['start'])?'':$config['start']?>" placeholder="<?php echo getLang('page')?>" required>
		</div>
		<div class="form-text"><?php echo getLang('desc_start_page')?></div>
	</div>

	<div class="input-group mb-2">
		<label class="input-group-text w-100p" for="id_title"><?php echo getLang('name')?></label>
		<input type="text" name="title" class="form-control" id="id_title" maxlength="255" value="<?php echo empty($config['title'])?'':escapeHTML($config['title'])?>">
	</div>

	<div class="form-file-group mb-2">
		<div class="input-group">
			<input class="form-control" type="file" name="logo" AC accept="image/png" aria-describedby="logoLabel logoDesc">
		</div>
		<div id="logoDesc">
		<?php if($isfile = file_exists($tmp = _AF_CONFIG_DATA_.'logo.png')){ ?>
			<input class="form-check-input" type="checkbox" name="remove_files[]" id="deleteLogo" value="logo">
			<label class="file-type-image" for="deleteLogo"><?php echo $tmp ?></label>
		<?php } ?>
		</div>
	</div>

	<div class="form-file-group mb-4">
		<div class="input-group">
			<input class="form-control" type="file" name="favicon" accept=".ico" aria-describedby="faviconLabel faviconDesc">
		</div>
		<div id="faviconDesc">
		<?php if($isfile = file_exists($tmp = _AF_CONFIG_DATA_.'favicon.ico')){ ?>
			<input class="form-check-input" type="checkbox" name="remove_files[]" id="deleteFavicon" value="favicon">
			<label class="file-type-image" for="deleteFavicon"><?php echo $tmp ?></label>
		<?php } ?>
		</div>
	</div>

	<div class="mb-4">
		<label class="form-label"><?php echo getLang('option')?></label>
		<div class="input-group">
			<label class="input-group-text w-100p" for="id_point_login"><?php echo getLang('login')?></label>
			<input type="number" class="form-control mw-150p" id="id_point_login" name="point_login" min="-9999" max="9999" maxlength="5" placeholder="<?php echo getLang('point')?>" value="<?php echo (!empty($config['point_login'])&&$config['point_login']>0)?$config['point_login']:''?>">
		</div>
		<div class="form-text"><?php echo getLang('desc_point')?></div>
	</div>

	<div class="form-check">
		<input class="form-check-input" type="checkbox" name="use_signup" id="id_use_signup" value="1" <?php echo !empty($config['use_signup'])?'checked':''?>>
		<label class="form-check-label" for="id_use_signup">
			<?php echo getLang('desc_use_signup')?>
		</label>
	</div>

	<div class="form-check">
		<input class="form-check-input" type="checkbox" name="use_visit" id="id_use_visit" value="1" <?php echo !empty($config['use_visit'])?'checked':''?>>
		<label class="form-check-label" for="id_use_visit">
			<?php echo getLang('desc_use_visit')?>
		</label>
	</div>

	<div class="form-check">
		<input class="form-check-input" type="checkbox" name="use_protect" id="id_use_protect" value="1" <?php echo !empty($config['use_protect'])?'checked':''?>>
		<label class="form-check-label" for="id_use_protect">
			<?php echo getLang('desc_use_protect')?>
		</label>
	</div>

	<div class="form-check mb-4">
		<input class="form-check-input" type="checkbox" name="use_captcha" id="id_use_captcha" value="1" <?php echo !empty($config['use_captcha'])?'checked':''?>>
		<label class="form-check-label" for="id_use_captcha">
			<?php echo getLang('desc_use_captcha')?>
		</label>
	</div>

	<div class="mb-4">
		<label class="form-label" for="baseCdnList" id="baseCdnListLabel"><?php echo getLang('base_cdn_list')?></label>
		<div class="input-group">
			<textarea class="form-control" rows="3" name="base_cdn_list" id="baseCdnList" aria-describedby="baseCdnListLabel baseCdnListDesc" placeholder="<?php echo getLang('how_to_use')?>) &lt;script src=&quot;//cdn.server.com/cdn.js&quot;&gt;&lt;/script&gt;"><?php if (file_exists($tmp = _AF_CONFIG_DATA_.'base_cdn_list.php')) include $tmp; ?></textarea>
		</div>
		<div class="form-text" id="baseCdnListDesc"><?php echo getLang('desc_base_cdn_list')?></div>
	</div>

	<div class="mb-4">
		<?php
			$_ACCESS_IP_MODE = '';
			if (file_exists($tmp = _AF_CONFIG_DATA_.'access_ip.php')) {
				include $tmp;
			}
		?>
		<label class="form-label" for="idAccessIp" id="idAccessIpLabel"><?php echo getLang('ip')?></label>:
		<input class="form-check-input ms-2" type="radio" name="access_ip_mode" id="accessIpMode1" value="possible" <?php echo $_ACCESS_IP_MODE=='possible'?' checked':''?>>
		<label for="accessIpMode1"><?php echo getLang('possible')?></label>
		<input class="form-check-input ms-1" type="radio" name="access_ip_mode" id="accessIpMode2" value="intercept"<?php echo $_ACCESS_IP_MODE!='possible'?' checked':''?>>
		<label for="accessIpMode2"><?php echo getLang('intercept')?></label>
		<div class="input-group">
		<textarea class="form-control" rows="3" name="access_ip" id="idAccessIp" aria-describedby="idAccessIpLabel idAccessIpDesc"><?php echo empty($_ACCESS_IPS) ? '' : str_replace("\.", ".", str_replace("[0-9\.]+", "+", implode("\n",$_ACCESS_IPS))); ?></textarea>
		</div>
		<div class="form-text" id="idAccessIpDesc"><?php echo getLang('desc_access_ip')?></div>
	</div>

	<div class="mb-4">
		<label class="form-label" for="prohibitId"id="prohibitIdLabel"><?php echo getLang('prohibit_id')?></label>
		<div class="input-group">
		<textarea class="form-control" rows="3" name="prohibit_id" id="prohibitId" aria-describedby="prohibitIdLabel prohibitIdDesc"><?php if (file_exists($tmp = _AF_CONFIG_DATA_.'prohibit_id.php')) {include $tmp; echo implode(',',$_PROHIBIT_IDS);} ?></textarea>
		</div>
		<div class="form-text" id="prohibitIdDesc"><?php echo getLang('desc_prohibit_id')?></div>
	</div>

	<div class="mb-4">
		<label class="form-label" for="termsOfUse" id="termsOfUseLabel"><?php echo getLang('terms_of_use')?></label>
		<div class="input-group">
		<textarea class="form-control" rows="3" name="terms_of_use" id="termsOfUse" aria-describedby="termsOfUseLabel termsOfUseDesc"><?php if (file_exists($tmp = _AF_CONFIG_DATA_.'terms_of_use.php')) include $tmp; ?></textarea>
		</div>
		<div class="form-text" id="termsOfUseDesc"><?php echo getLang('desc_terms_of_use')?></div>
	</div>

	<hr class="mb-5">
	<div class="text-end position-fixed bottom-0 end-0 p-3">
		<button type="submit" class="btn btn-success btn-lg" style="min-width:220px"><?php echo getLang('save')?></button>
	</div>
</form>

<?php
/* End of file setup.php */
/* Location: ./module/admin/setup.php */
