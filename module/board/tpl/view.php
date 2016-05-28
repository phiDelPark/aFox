<?php
if(!defined('__AFOX__')) exit();
$is_manager = isManager($_DATA['id']);
$is_rp_grant = isGrant($_DATA['id'],'reply');
if(!empty($_{'board'}['mb_srl'])) {
	$mb = getMember($_{'board'}['mb_srl']);
}
?>

<section id="board_view">
	<header>
		<h3 class="clearfix"><?php echo $_{'board'}['wr_title']?></h3>
		<hr class="divider">
		<div class="clearfix">
			<span class="pull-left"><?php echo $_{'board'}['mb_nick']?></span>
			<span class="pull-right"><?php echo date((__MOBILE__?'y':'Y').getLang('year').' m'.getLang('month').' d'.getLang('day').' A h:i', strtotime($_{'board'}['wr_regdate']))?></span>
		</div>
	</header>
	<article>
	<?php echo toHTML($_{'board'}['wr_type'], $_{'board'}['wr_content'])?>
	<?php if(!empty($_{'board'}['wr_tags'])) { ?>
	<div calss="hashtags">
		<?php
			$hashtags = explode(',', $_{'board'}['wr_tags']);
			foreach ($hashtags as $val) {
				echo '<a href="'.getUrl('','id',$_DATA['id'],'search','tags:'.$val).'"><i class="fa fa-hashtag" aria-hidden="true"></i> '.$val.'</a>'."\n";
			}
		?>
	</div>
	<?php } ?>
	<?php if(!empty($mb['mb_memo'])) {
			$_icon = $mb['mb_srl'].'/profile_image.png';
			if(file_exists(_AF_MEMBER_DATA_.$_icon)) {
				$_icon = _AF_URL_ . 'data/member/' . $_icon;
			} else {
				$_icon = _AF_URL_ .'module/board/tpl/user_default.jpg';
			}
	?>
		<div class="profile-text clearfix">
			<div class="left"><img src="<?php echo $_icon ?>" alt="Profile" class="profile"></div>
			<div class="right"><?php echo toHTML(1, $mb['mb_memo'], 'mb_memo') ?></div>
		</div>
	<?php } ?>
	</article>
	<footer class="area-text-button clearfix">
		<div class="pull-right">
			<?php if(empty($_{'board'}['mb_srl'])||$is_manager) { ?>
			<a href="<?php echo getUrl('disp','writeDocument', 'srl', $_DATA['srl']) ?>" role="button"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> <?php echo getLang('edit') ?></a>
			<a href="<?php echo getUrl('disp','deleteDocument', 'srl', $_DATA['srl']) ?>" role="button"><i class="fa fa-trash-o" aria-hidden="true"></i> <?php echo getLang('delete') ?></a>
			<?php } ?>
		</div>
	</footer>
</section>

<?php if($is_rp_grant || $_{'board'}['wr_reply'] > 0) include 'reply.php'; ?>

<?php include 'list.php'; ?>

<script>
$_LANG['comment'] = "<?php echo getLang('comment')?>";
$_LANG['password'] = "<?php echo getLang('password')?>";
$_LANG['warn_input'] = "<?php echo getLang('warn_input')?>";
$_LANG['confirm_select_delete'] = "<?php echo getLang('confirm_select_delete')?>";
</script>