<?php
if(!defined('__AFOX__')) exit();
$is_manager = isManager($_DATA['id']);
$is_rp_grant = isGrant($_DATA['id'],'reply');
if(!empty($_{'board'}['mb_srl'])) {
	$doc_mb = getMember($_{'board'}['mb_srl']);
}
$is_login_mb_srl = empty($_MEMBER['mb_srl']) ? false : $_MEMBER['mb_srl'];
?>

<section id="board_view">
	<header>
		<h3 class="clearfix"><?php echo $_{'board'}['wr_title']?></h3>
		<hr class="divider">
		<div class="clearfix">
			<span class="pull-left"><?php echo '<span class="mb_nick" data-srl="'.$_{'board'}['mb_srl'].'" data-rank="'.(ord($_{'board'}['mb_rank']) - 48).'">'.$_{'board'}['mb_nick'].'</span>'?></span>
			<span class="pull-right"><?php echo date((__MOBILE__?'y':'Y').getLang('year').' m'.getLang('month').' d'.getLang('day').' A h:i', strtotime($_{'board'}['wr_regdate']))?></span>
		</div>
	</header>
	<article>
	<?php
		$is_permit = ($_{'board'}['wr_secret']!='1' || $is_manager || $is_login_mb_srl === $_{'board'}['mb_srl']);
		if(!$is_permit) $is_permit = !empty($GLOBALS['_PERMIT_VIEW_'][md5($_{'board'}['md_id'].'_'.$_{'board'}['wr_srl'])]);
		$wr_content = $is_permit ? $_{'board'}['wr_content'] : getLang('error_permit');
		echo toHTML($_{'board'}['wr_type'], $wr_content);
	?>
	<?php if(!empty($_{'board'}['wr_tags'])) { ?>
	<div class="hashtags">
		<?php
			$hashtags = explode(',', $_{'board'}['wr_tags']);
			foreach ($hashtags as $val) {
				echo '<a href="'.getUrl('','id',$_DATA['id'],'search','tags:'.$val).'"><i class="fa fa-hashtag" aria-hidden="true"></i> '.$val.'</a>'."\n";
			}
		?>
	</div>
	<?php } ?>
	<?php if(!empty($doc_mb['mb_memo'])) {
			$_icon = $doc_mb['mb_srl'].'/profile_image.png';
			if(file_exists(_AF_MEMBER_DATA_.$_icon)) {
				$_icon = _AF_URL_ . 'data/member/' . $_icon;
			} else {
				$_icon = _AF_URL_ .'module/board/tpl/user_default.jpg';
			}
	?>
		<div class="profile-text clearfix">
			<div class="left"><img src="<?php echo $_icon ?>" alt="Profile" class="profile"></div>
			<div class="right"><?php echo toHTML(1, $doc_mb['mb_memo'], 'mb_memo') ?></div>
		</div>
	<?php } ?>
	</article>
	<footer class="area-text-button clearfix">
		<div class="pull-right">
			<?php
				$is_edit = empty($_{'board'}['mb_srl']) || $is_manager || $is_login_mb_srl === $_{'board'}['mb_srl'];
				$not_edit_str = '#" style="text-decoration:line-through" onclick="alert(\''.escapeHtml(getLang('error_permit',false),true,ENT_QUOTES).'\');return false';
			?>
			<a href="<?php echo $is_edit?getUrl('disp','writeDocument', 'srl', $_DATA['srl']):$not_edit_str?>" role="button"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> <?php echo getLang('edit') ?></a>
			<a href="<?php echo $is_edit?getUrl('disp','deleteDocument', 'srl', $_DATA['srl']):$not_edit_str?>" role="button"><i class="fa fa-trash-o" aria-hidden="true"></i> <?php echo getLang('delete') ?></a>
		</div>
	</footer>
</section>

<?php if($is_rp_grant || $_{'board'}['wr_reply'] > 0) include 'reply.php'; ?>

<?php include 'list.php'; ?>

<script>
$_LANG['ok'] = "<?php echo getLang('ok')?>";
$_LANG['close'] = "<?php echo getLang('close')?>";
$_LANG['yes'] = "<?php echo getLang('yes')?>";
$_LANG['no'] = "<?php echo getLang('no')?>";
$_LANG['comment'] = "<?php echo getLang('comment')?>";
$_LANG['password'] = "<?php echo getLang('password')?>";
$_LANG['request_input'] = "<?php echo getLang('request_input')?>";
$_LANG['confirm_select_delete'] = "<?php echo getLang('confirm_select_delete')?>";
</script>