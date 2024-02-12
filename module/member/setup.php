<?php if(!defined('__AFOX__')) exit();
$mb = getMember($_GET['mid']);
?>

<?php if($mb){?>
<form method="post" autocomplete="off" enctype="multipart/form-data" onsubmit="return confirm('<?php echo getLang('confirm_ban_login')?>')">
	<input type="hidden" name="error_url" value="<?php echo getUrl()?>" />
	<input type="hidden" name="success_url" value="<?php echo getUrl('mid', '', 'md_id', '')?>" />
	<input type="hidden" name="module" value="member" />
	<input type="hidden" name="act" value="banlogin" />
	<input type="hidden" name="mb_id" value="<?php echo $mb['mb_id']?>" />
	<button type="submit" class="btn btn-sm btn-danger float-end"><?php echo getLang('ban_login')?></button>
</form>
<div class="mb-4 float-start">
	<div class="input-group">
		<label class="input-group-text w-100p" for="id_new_md_id"><?php echo getLang('id')?></label>
		<input type="text" value="<?php echo $mb['mb_id'] ?>" class="form-control mw-150p" id="id_new_md_id" disabled>
	</div>
</div>
<div class="clearfix"></div>
<?php }?>

<form id="memberSignup" method="post" autocomplete="off" enctype="multipart/form-data">
<input type="hidden" name="error_url" value="<?php echo getUrl()?>" />
<input type="hidden" name="success_url" value="<?php echo getUrl('mid','')?>" />
<input type="hidden" name="module" value="member" />
<input type="hidden" name="act" value="updateMember" />

<?php  $grade = $mb ? $mb['mb_grade'] : 'guest';
	if(!$mb) { ?>
	<h4 class="pb-3 mb-3 border-bottom"><?php echo getLang('member_signup')?></h4>
	<div class="mb-4">
		<div class="input-group">
			<label class="input-group-text w-100p" for="id_mb_id"><?php echo getLang('id')?></label>
			<input type="text" name="new_mb_id" class="form-control mw-150p" id="id_mb_id" required maxlength="11" pattern="^[a-zA-Z]+\w{2,}$" value="">
		</div>
		<div class="form-text"><?php echo getLang('desc_mb_id')?></div>
	</div>
<?php } else {
	$next_lv = (($mb ? ord($mb['mb_rank']) : 48) - 48);
	if ($next_lv > 50) $next_lv = 50;
?>
	<input type="hidden" name="mb_id" value="<?php echo $mb['mb_id'] ?>" />
	<div class="mb-4">
		<label>LV. <?php echo ($grade == 'guest' || $grade == 'member') ? $next_lv : getLang($grade) ?></label>
		<div class="progress">
			<div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $next_lv?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $next_lv * 2?>%;">
			<?php echo $next_lv * 2?>%
			</div>
		</div>
	</div>
<?php } ?>

	<div class="mb-4">
		<div>
			<input type="password" name="new_mb_password" class="form-control mb-2" id="id_mb_password"<?php echo $mb ?'':' required'?> placeholder="<?php echo getLang('password')?>" minlength="4">
			<input type="password" name="verify_mb_password" class="form-control"<?php echo $mb ?'':' required'?> placeholder="<?php echo getLang('verify_password')?>">
		</div>
		<div class="form-text"><?php echo getLang($mb?'desc_change_password':'desc_mb_password')?></div>
	</div>

    <div class="mb-4">
        <div class="input-group">
            <label class="input-group-text w-100p" for="id_mb_point"><?php echo getLang('point')?></label>
            <input type="number" class="form-control mw-150p" id="id_mb_point" name="mb_point" min="-2147483648" max="2147483647" maxlength="5" placeholder="<?php echo getLang('point')?>" value="<?php echo $mb?$mb['mb_point']:0?>">
        </div>
        <div class="form-text"><?php echo getLang('desc_mb_point')?></div>
    </div>

    <div class="mb-4">
        <span class="form-label"><?php echo getLang('rank')?></span>
        <div>
            <input type="radio" class="btn-check" name="new_mb_rank" id="id_mb_rank1" autocomplete="off" value="0"<?php echo $grade!='manager'&&$grade!='admin'?' checked':'' ?>>
            <label class="btn btn-xs btn-outline-primary w-100p" for="id_mb_rank1"><?php echo getLang('member')?></label>
            <input type="radio" class="btn-check" name="new_mb_rank" id="id_mb_rank2" autocomplete="off" value="1"<?php echo $grade=='manager'?' checked':'' ?>>
            <label class="btn btn-xs btn-outline-primary w-100p" for="id_mb_rank2"><?php echo getLang('manager')?></label>
            <input type="radio" class="btn-check" name="new_mb_rank" id="id_mb_rank3" autocomplete="off" value="2"<?php echo $grade=='admin'?' checked':'' ?>>
            <label class="btn btn-xs btn-outline-primary w-100p" for="id_mb_rank3"><?php echo getLang('admin')?></label>
        </div>
    </div>

	<div class="input-group mb-2">
		<label class="input-group-text w-100p" for="id_mb_nick"><?php echo getLang('nickname')?></label>
		<input type="text" name="mb_nick" class="form-control" id="id_mb_nick" minlength="2" maxlength="5" value="<?php echo $mb?$mb['mb_nick']:''?>" required pattern="^[가-힣ぁ-んァ-ン一-龥A-Za-z][가-힣ぁ-んァ-ン一-龥\w]{1,4}$">
	</div>

	<div class="input-group mb-2">
	<label class="input-group-text w-100p" for="id_mb_email"><?php echo getLang('email')?></label>
		<input type="email" name="mb_email" class="form-control" id="id_mb_email" maxlength="255" value="<?php echo $mb?escapeHTML($mb['mb_email']):''?>" required pattern="^[\w]+[\w._%+-]+@[\w.-]+\.[\w]+$">
	</div>

	<div class="input-group mb-4">
		<label class="input-group-text w-100p" for="id_mb_homepage"><?php echo getLang('homepage')?></label>
		<input type="url" name="mb_homepage" class="form-control" id="id_mb_homepage" value="<?php echo $mb?escapeHTML($mb['mb_homepage']):''?>" maxlength="255">
	</div>

	<div class="mb-4">
		<label for="id_mb_about"><?php echo getLang('memo')?></label>
		<div>
		<textarea class="form-control" name="mb_about" id="id_mb_about"><?php echo $mb?$mb['mb_about']:''?></textarea>
		</div>
		<div class="form-text"><?php echo getLang('desc_member_memo')?></div>
	</div>

	<?php
		$tmp = 'data/member/'.(empty($mb['mb_srl'])?'___':$mb['mb_srl']).'/profile_image.png';
		$tmp = _AF_URL_ . ($mb&&file_exists(_AF_PATH_ . $tmp) ? $tmp : 'common/img/user_default.jpg');
	?>
	<div class="position-relative mb-4">
		<img class="position-absolute border rounded p-1" style="width:58px;height:58px" src="<?php echo $tmp ?>">
		<div style="margin-left:70px">
			<div class="input-group">
				<input class="form-control" type="file" name="mb_icon" aria-describedby="id_mb_icon_desc">
			</div>
			<div class="form-text"><?php echo getLang('desc_member_icon')?></div>
		</div>
	</div>

	<hr class="mb-5">
	<div class="text-end position-fixed bottom-0 end-0 p-3">
		<button type="submit" class="btn btn-success btn-lg" style="min-width:220px"><?php echo getLang('save')?></button>
	</div>
</form>
<?php
/* End of file setup.php */
/* Location: ./module/member/setup.php */
