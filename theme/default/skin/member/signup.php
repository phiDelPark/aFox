<?php
	if(!defined('__AFOX__')) exit();
	$ismb = !empty($_MEMBER);
?>

<section id="trashView" class="mb-4" aria-label="Contents of this post">
	<h3 class="pb-3 mb-3 border-bottom"><?php echo getLang($ismb?'member':'member_signup')?></h3>
	<form id="memberSignup" method="post" autocomplete="off" enctype="multipart/form-data">
	<input type="hidden" name="error_return_url" value="<?php echo getUrl()?>" />
	<input type="hidden" name="success_return_url" value="<?php echo getUrl()?>" />
	<input type="hidden" name="module" value="member" />
	<input type="hidden" name="act" value="updateMember" />

<?php if(!$ismb) { ?>
	<?php if(file_exists($tmp = _AF_CONFIG_DATA_.'terms_of_use.php')) { ?>
		<div class="mb-3">
			<label for="id_terms_of_use"><?php echo getLang('terms_of_use')?></label>
			<textarea class="form-control"  rows="5" id="id_terms_of_use"><?php include $tmp; ?></textarea>
			<div class="form-text"><?php echo getLang('desc_terms_of_use')?></div>
		</div>
	<?php } ?>
		<div class="mb-3">
			<input type="hidden" name="mb_id" value="" />
			<div class="input-group">
				<label class="input-group-text w-100p" for="id_new_mb_id"><?php echo getLang('id')?></label>
				<input type="text" name="new_mb_id" class="form-control" id="id_new_mb_id" required minlength="4" maxlength="11" pattern="^[a-zA-Z]{1}[\w_]{3,10}$">
			</div>
			<div class="form-text"><?php echo getLang('desc_mb_id')?></div>
		</div>
<?php } else {
	$mb_rank = ord($_MEMBER['mb_rank']);
	$rank_nicks = ['109'=>getLang('manager'),'115'=>getLang('admin')];
	$next_lv = $mb_rank > 99 ? '100' : explode('.',sprintf('%.2f',round(sqrt(floor($_MEMBER['mb_point'] / 10) / 10), 2)))[1];
?>
	<input type="hidden" name="mb_id" value="<?php echo $_MEMBER['mb_id'] ?>" />
	<div class="mb-3">
		<label>LV. <?php echo empty($rank_nicks[$mb_rank]) ? ($mb_rank - 48) : $rank_nicks[$mb_rank] ?></label>
		<div class="progress">
			<div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $next_lv?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $next_lv?>%;">
			<?php echo $next_lv?>%
			</div>
		</div>
	</div>
<?php } ?>

	<div class="mb-4">
		<div>
			<input type="password" name="new_mb_password" class="form-control mb-2" id="newMbPassword"<?php echo $ismb ?'':' required'?> placeholder="<?php echo getLang('password')?>" minlength="4">
			<input type="password" name="verify_mb_password" class="form-control"<?php echo $ismb ?'':' required'?> placeholder="<?php echo getLang('verify_password')?>">
		</div>
		<div class="form-text"><?php echo getLang($ismb?'desc_change_password':'desc_mb_password')?></div>
	</div>

	<div class="input-group mb-2">
		<label class="input-group-text w-100p" for="id_mb_nick"><?php echo getLang('nickname')?></label>
		<input type="text" name="mb_nick" class="form-control" id="id_mb_nick" minlength="2" maxlength="5" value="<?php echo $ismb?escapeHtml($_MEMBER['mb_nick']):''?>" required pattern="^[a-zA-Z가-힣ぁ-んァ-ン一-龥]{2,5}$">
	</div>

	<div class="input-group mb-2">
	<label class="input-group-text w-100p" for="id_mb_email"><?php echo getLang('email')?></label>
		<input type="email" name="mb_email" class="form-control" id="id_mb_email" maxlength="255" value="<?php echo $ismb?escapeHtml($_MEMBER['mb_email']):''?>" required pattern="^[\w]+[\w._%+-]+@[\w.-]+\.[\w]+$">
	</div>

	<div class="input-group mb-4">
		<label class="input-group-text w-100p" for="id_mb_homepage"><?php echo getLang('homepage')?></label>
		<input type="url" name="mb_homepage" class="form-control" id="id_mb_homepage" value="<?php echo $ismb?escapeHtml($_MEMBER['mb_homepage']):''?>" maxlength="255">
	</div>

	<div class="mb-4">
		<label for="id_mb_memo"><?php echo getLang('memo')?></label>
		<div>
		<textarea class="form-control" name="mb_memo" id="id_mb_memo"><?php echo $ismb?$_MEMBER['mb_memo']:''?></textarea>
		</div>
		<div class="form-text"><?php echo getLang('desc_member_memo')?></div>
	</div>

	<?php
		$tmp = 'data/member/'.(empty($_MEMBER['mb_srl'])?'___':$_MEMBER['mb_srl']).'/profile_image.png';
		$tmp = _AF_URL_ . ($ismb&&file_exists(_AF_PATH_ . $tmp) ? $tmp : 'common/img/user_default.jpg');
	?>
	<div class="position-relative mb-4">
		<img class="position-absolute border rounded p-1" style="width:58px;height:58px" src="<?php echo $tmp ?>">
		<div class="form-file-group" style="margin-left:70px">
			<div class="input-group">
				<input class="form-control" type="file" name="mb_icon" aria-describedby="id_mb_icon_desc">
			</div>
			<div class="form-text"><?php echo getLang('desc_member_icon')?></div>
		</div>
	</div>
	<hr>
	<button type="submit" class="btn btn-success btn-lg w-100"><?php echo getLang($ismb?'save':'agree_signup')?></button>

</form>
</section>