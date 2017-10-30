<?php
	if(!defined('__AFOX__')) exit();
	$ismb = !empty($_MEMBER);
?>

<form id="member-signup" method="post" autocomplete="off" enctype="multipart/form-data" data-exec-ajax="member.updateMember">
<input type="hidden" name="success_return_url" value="<?php echo $ismb?getUrl():getUrl('')?>" />

	<h4 class="signup-title" id="afSignUpFormTitle"><?php echo getLang($ismb?'member':'member_signup')?></h4>
	<hr class="divider">
	<div class="signup-body">
<?php if(!$ismb) { ?>
	<?php if(file_exists($tmp = _AF_CONFIG_DATA_.'terms_of_use.php')) { ?>
		<div class="form-group">
			<label for="id_terms_of_use"><?php echo getLang('terms_of_use')?></label>
			<textarea class="form-control mh-10 vresize" id="id_terms_of_use"><?php include $tmp; ?></textarea>
			<p class="help-block"><?php echo getLang('desc_terms_of_use')?></p>
		</div>
	<?php } ?>
		<div class="form-group">
			<label for="id_new_mb_id"><?php echo getLang('id')?></label>
			<div class="form-inline">
				<input type="text" name="new_mb_id" class="form-control" id="id_new_mb_id" required maxlength="11" pattern="^[a-zA-Z]+\w{2,}$">
				<input type="hidden" name="mb_id" value="" />
			<p class="help-block"><?php echo getLang('desc_id')?></p>
		</div>
<?php
	} else {
	$mb_rank = ord($_MEMBER['mb_rank']);
	$rank_nicks = ['109'=>getLang('manager'),'115'=>getLang('admin')];
	$next_lv = $mb_rank > 99 ? '100' : explode('.',sprintf('%.2f',round(sqrt(floor($_MEMBER['mb_point'] / 10) / 10), 2)))[1];
?>
	<input type="hidden" name="mb_id" value="<?php echo $_MEMBER['mb_id'] ?>" />
	<div class="form-group">
		<label>LV. <?php echo empty($rank_nicks[$mb_rank]) ? ($mb_rank - 48) : $rank_nicks[$mb_rank] ?></label>
		<div class="progress">
			<div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $next_lv?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $next_lv?>%;">
			<?php echo $next_lv?>%
			</div>
		</div>
	</div>
<?php } ?>
		<div class="form-group">
			<label for="id_new_mb_password"><?php echo getLang('password')?></label>
			<div class="form-inline">
				<input type="password" name="new_mb_password" class="form-control" id="id_new_mb_password"<?php echo $ismb ?'':' required'?> placeholder="<?php echo getLang('password')?>">
				<input type="password" name="verify_mb_password" class="form-control"<?php echo $ismb ?'':' required'?> placeholder="<?php echo getLang('verify_password')?>">
			</div>
			<p class="help-block"><?php echo getLang($ismb?'desc_change_mb_password':'desc_new_mb_password')?></p>
		</div>
		<div class="form-group">
			<label for="id_mb_nick"><?php echo getLang('nickname')?></label>
			<input type="text" name="mb_nick" class="form-control" id="id_mb_nick" maxlength="20" value="<?php echo $ismb?escapeHtml($_MEMBER['mb_nick']):''?>" required>
		</div>
		<div class="form-group">
			<label for="id_mb_email"><?php echo getLang('email')?></label>
			<input type="email" name="mb_email" class="form-control" id="id_mb_email" maxlength="255" value="<?php echo $ismb?escapeHtml($_MEMBER['mb_email']):''?>" required>
		</div>
		<div class="form-group">
			<label for="id_mb_homepage"><?php echo getLang('homepage')?></label>
			<input type="url" name="mb_homepage" class="form-control" id="id_mb_homepage" value="<?php echo $ismb?escapeHtml($_MEMBER['mb_homepage']):''?>" maxlength="255">
		</div>
		<div class="form-group">
			<label for="id_mb_memo"><?php echo getLang('memo')?></label>
			<textarea class="form-control mh-10 vresize" name="mb_memo" id="id_mb_memo"><?php echo $ismb?$_MEMBER['mb_memo']:''?></textarea>
			<p class="help-block"><?php echo getLang('desc_member_memo')?></p>
		</div>
		<div class="form-group">
			<?php $isfile = $ismb&&!empty($_MEMBER['mb_icon'])?$_MEMBER['mb_icon']:''?>
			<div class="uploader-group" placeholder="<?php echo getLang('warning_allowable',['png [100x100 size]'])?>">
				<div class="input-group">
					<div class="file-caption form-control"><?php echo $isfile?'<i class="file-item" data-type="image">'.$_MEMBER['mb_icon'].'</i>':''?></div>
					<div class="btn btn-primary btn-file">
						<i class="glyphicon glyphicon-folder-open"><?php echo getLang('browse')?>…</i>
						<input name="mb_icon" type="file">
					</div>
				</div>
			</div>
			<p class="help-block"><?php echo getLang('desc_member_icon')?></p>
		</div>

	</div>
	<div class="modal-footer" style="padding:30px 0 0">
		<button type="submit" class="btn btn-success mw-10"><i class="glyphicon glyphicon-ok" aria-hidden="true"></i> <?php echo getLang($ismb?'save':'agree_signup')?></button>
	</div>
</form>
