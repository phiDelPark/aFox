<?php
if(!defined('__AFOX__')) exit();

$is_manager = isManager(__MID__);
$is_rp_grant = isGrant('reply', __MID__);

$wr_mb_srl = $_{'board'}['mb_srl'];
if(!empty($wr_mb_srl)) {
	$doc_mb = getMember($wr_mb_srl);
}

$login_srl = empty($_MEMBER['mb_srl']) ? false : $_MEMBER['mb_srl'];
$wr_secret = $_{'board'}['wr_secret'] == '1';
$wr_grant_view = $_{'board'}['grant_view'];
$wr_grant_write = $_{'board'}['grant_write'];
?>

<section id="bdView">
	<header>
		<h3 class="clearfix"><?php echo ($wr_secret?'<i class="glyphicon glyphicon-lock" aria-hidden="true"></i> ':'').$_{'board'}['wr_title']?></h3>
		<hr class="divider">
		<div class="clearfix">
			<span class="pull-left"><?php echo '<span class="mb_nick" data-srl="'.$wr_mb_srl.'" data-rank="'.(ord($_{'board'}['mb_rank']) - 48).'">'.$_{'board'}['mb_nick'].'</span>'?></span>
			<span class="pull-right"><?php echo date((__MOBILE__?'y':'Y').getLang('year').' m'.getLang('month').' d'.getLang('day').' A h:i', strtotime($_{'board'}['wr_regdate']))?></span>
		</div>
	</header>
	<article>

	<?php
		$md_extra_keys = empty($_CFG['md_extra']['keys']) ? [] : $_CFG['md_extra']['keys'];
		if (!empty($md_extra_keys)) {
			echo '<div class="wr_extra_vars">';
			foreach($md_extra_keys as $ex_key=>$ex_name) {
				$tmp = @$_{'board'}['wr_extra']['vars'][$ex_key];
				if(preg_match('/^https?:\/\/.+/', $tmp)) $tmp = '<a href="'.escapeHtml($tmp).'" target="_blank">'.$tmp.'</a>';
	?>
			<div class="clearfix">
				<strong><?php echo $ex_name?></strong>
				<span><?php echo $tmp?></span>
			</div>
	<?php
			}
			echo '</div>';
		}
	?>

	<?php
		$wr_content = ($wr_grant_view || !$wr_secret) ? $_{'board'}['wr_content'] : getLang('error_permitted');
		$wr_content = toHTML($wr_content, $_{'board'}['wr_type']);
		$wr_content = preg_replace('/(<img[^>]*\s+)(src)(\s*=[^>]*>)/is', '\\1scroll-src\\3', $wr_content);
		echo empty($_DATA['search']) ? $wr_content : highlightText($_DATA['search'], $wr_content);
	?>
	<?php if(!empty($_{'board'}['wr_tags'])) { ?>
	<div class="hashtags">
		<?php
			$hashtags = explode(',', $_{'board'}['wr_tags']);
			foreach ($hashtags as $val) {
				echo '<a href="'.getUrl('','id',__MID__,'search','tags:'.$val).'"><strong>#</strong>'.$val.'</a>'."\n";
			}
		?>
	</div>
	<?php } ?>
	<?php if(!empty($doc_mb['mb_memo'])) {
			$_icon = $doc_mb['mb_srl'].'/profile_image.png';
			if(file_exists(_AF_MEMBER_DATA_.$_icon)) {
				$_icon = _AF_URL_ . 'data/member/' . $_icon;
			} else {
				$_icon = _AF_URL_ .'common/img/user_default.jpg';
			}
	?>
		<div class="profile-text clearfix">
			<div class="left"><img src="<?php echo $_icon ?>" alt="Profile" class="profile"></div>
			<div class="right"><?php echo toHTML($doc_mb['mb_memo'], 1, 'member_memo') ?></div>
		</div>
	<?php } ?>
	</article>
	<footer class="area-text-button clearfix">
		<div class="pull-left">
			<a class="btn btn-default btn-sm" href="<?php echo getUrl('disp','','srl','','cpage','','rp','') ?>" role="button"><i class="glyphicon glyphicon-list" aria-hidden="true"></i> <?php echo getLang('list') ?></a>
		</div>
		<div class="pull-right">
			<?php
				$not_edit_str = '#" style="text-decoration:line-through" onclick="return msg_box(\''.escapeHtml(getLang('error_permitted',false),true,ENT_QUOTES).'\')';
			?>
			<a class="btn btn-default btn-sm" href="<?php echo $wr_grant_write?(empty($wr_mb_srl)&&!$is_manager?'#requirePassword" data-srl="'.$_{'board'}['wr_srl'].'" data-param="srl,'.$_{'board'}['wr_srl'].',disp,writeDocument':getUrl('disp','writeDocument', 'srl', $_DATA['srl'])):$not_edit_str?>" role="button"><i class="glyphicon glyphicon-edit" aria-hidden="true"></i> <?php echo getLang('edit') ?></a>
			<a class="btn btn-default btn-sm" href="<?php echo $wr_grant_write?(empty($wr_mb_srl)&&!$is_manager?'#requirePassword" data-srl="'.$_{'board'}['wr_srl'].'" data-param="srl,'.$_{'board'}['wr_srl'].',disp,deleteDocument':getUrl('disp','deleteDocument', 'srl', $_DATA['srl'])):$not_edit_str?>" role="button"><i class="glyphicon glyphicon-remove" aria-hidden="true"></i> <?php echo getLang('delete') ?></a>
		</div>
	</footer>
</section>

<?php
	include 'reply.php';
	include 'list.php';
?>
