<?php
if(!defined('__AFOX__')) exit();

$is_manager = isManager(__MID__);
$is_rp_grant = isGrant('reply', __MID__);

$wr_mb_srl = $DOC['mb_srl'];
if(!empty($wr_mb_srl)) $doc_mb = getMember($wr_mb_srl);

$login_srl = empty($_MEMBER['mb_srl']) ? false : $_MEMBER['mb_srl'];
$wr_secret = $DOC['wr_secret'] == '1';
$wr_grant_view = $DOC['grant_view'];
$wr_grant_write = $DOC['grant_write'];

$show_column = $CONFIGS[$use_style=='review'?'show_rv_column':'show_column'];
$is_btn_download = array_search('btn_download',$show_column)!==false;
$is_col_update = $use_style!='timeline'&&($use_style=='gallery'||array_search('wr_update',$show_column)!==false);
?>

<section id="bdView">
	<header>
		<h3 class="clearfix"><?php echo ($wr_secret?'<i class="glyphicon glyphicon-lock" aria-hidden="true"></i> ':'').$DOC['wr_title']?></h3>
		<hr class="divider">
		<div class="clearfix">
			<span class="pull-left"><?php echo '<span class="mb_nick" data-srl="'.$wr_mb_srl.'" data-rank="'.(ord($DOC['mb_rank']) - 48).'">'.$DOC['mb_nick'].'</span>'?></span>
			<span class="pull-right"><?php echo date((__MOBILE__?'y':'Y').getLang('year').' m'.getLang('month').' d'.getLang('day').' A h:i', strtotime($DOC[$is_col_update?'wr_update':'wr_regdate']))?></span>
		</div>
	</header>
	<article>

	<?php
		$md_extra_keys = empty($_CFG['md_extra']['keys']) ? [] : $_CFG['md_extra']['keys'];
		if (!empty($md_extra_keys)) {
			echo '<div class="wr_extra_vars">';
			foreach($md_extra_keys as $ex_key=>$ex_name) {
				$tmp = @$DOC['wr_extra']['vars'][$ex_key];
				if(preg_match('/^https?:\/\/.+/', $tmp)) $tmp = '<a href="'.escapeHtml($tmp).'" target="_blank">'.$tmp.'</a>';
	?>
			<div class="text-ellipsis clearfix">
				<strong class="col-sm-2"><?php echo $ex_name?></strong>
				<span><?php echo $tmp?></span>
			</div>
	<?php
			}
			echo '</div>';
		}

		if($is_btn_download) {
			$_files = DB::gets(_AF_FILE_TABLE_, ['md_id'=>__MID__,'mf_target'=>$DOC['wr_srl'],'mf_type{LIKE}'=>'application%','(_OR_)'=>['^'=>'LOWER(`mf_name`)LIKE\'%.zip\'OR LOWER(`mf_name`)LIKE\'%.7z\'']]);
			if (!empty($_files)) {
				echo '<div class="wr_extra_vars">';
				foreach($_files as $_file) {
					echo '<div class="text-ellipsis clearfix"><strong class="col-sm-2">'.getLang('download').'</strong> <span><a href="./?file='.$_file['mf_srl'].'"><code>'.$_file['mf_name'].'</code></a></span></div>';
				}
				echo '</div>';
			}
		}
	?>

	<?php
		$wr_content = ($wr_grant_view || !$wr_secret) ? $DOC['wr_content'] : getLang('error_permitted');
		$wr_content = toHTML($wr_content, $DOC['wr_type']);
		$wr_content = preg_replace('/(<img[^>]*\s+)(src)(\s*=[^>]*>)/is', '\\1data-scroll-src\\3', $wr_content);
		echo empty($_DATA['search']) ? $wr_content : highlightText($_DATA['search'], $wr_content);
	?>

	<?php if(!empty($CONFIGS['show_button'])) { ?>
	<div class="show_buttons">
		<?php
			foreach ($CONFIGS['show_button'] as $val) {
				$_ajax = empty($login_srl) ? '' : ' data-exec-ajax="board.update'.ucfirst($val).'" data-ajax-param="wr_srl,'.$DOC['wr_srl'].',success_return_url,'.urlencode(getUrl()).'"';
				echo '<button type="button" class="btn btn-default" style="color:'.($val=='good'?'#3c763d':'#a94442').'"'.$_ajax.'><i class="glyphicon glyphicon-thumbs-'.($val=='good'?'up':'down').'" aria-hidden="true"></i> <span>'.getLang($val).'</span><br><span>'.$DOC['wr_'.$val].'</span></button>';
			}
		?>
	</div>
	<?php } ?>
	<?php if(!empty($DOC['wr_tags'])) { ?>
	<div class="hashtags">
		<?php
			$hashtags = explode(',', $DOC['wr_tags']);
			foreach ($hashtags as $val) {
				echo '<a href="'.getUrl('','id',__MID__,'search','tag:'.$val).'"><strong>#</strong>'.$val.'</a>'."\n";
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
	<?php if(!__POPUP__) { ?>
		<div class="pull-left">
			<a class="btn btn-default btn-sm" href="<?php echo getUrl('disp','','srl','','cpage','','rp','') ?>" role="button"><i class="glyphicon glyphicon-list" aria-hidden="true"></i> <?php echo getLang('list') ?></a>
		</div>
		<div class="pull-right">
			<?php
				$not_edit_str = '#" style="text-decoration:line-through" data-msg-box="warning" data-title="'.getLang('error_permitted');
			?>
			<a class="btn btn-default btn-sm" href="<?php echo $wr_grant_write?(empty($wr_mb_srl)&&!$is_manager?'#requirePassword" data-srl="'.$DOC['wr_srl'].'" data-param="srl,'.$DOC['wr_srl'].',disp,writeDocument':getUrl('disp','writeDocument', 'srl', $_DATA['srl'])):$not_edit_str?>" role="button"><i class="glyphicon glyphicon-edit" aria-hidden="true"></i> <?php echo getLang('edit') ?></a>
			<a class="btn btn-default btn-sm" href="<?php echo $wr_grant_write?(empty($wr_mb_srl)&&!$is_manager?'#requirePassword" data-srl="'.$DOC['wr_srl'].'" data-param="srl,'.$DOC['wr_srl'].',disp,deleteDocument':getUrl('disp','deleteDocument', 'srl', $_DATA['srl'])):$not_edit_str?>" role="button"><i class="glyphicon glyphicon-remove" aria-hidden="true"></i> <?php echo getLang('delete') ?></a>
		</div>
	<?php } else { ?>
		<div class="pull-right">
			<button type="button" class="btn btn-default btn-sm" data-dismiss="modal" aria-label="Close"><i class="glyphicon glyphicon-chevron-down" aria-hidden="true"></i> <?php echo getLang('close') ?></button>
		</div>
	<?php } ?>
	</footer>
</section>

<?php
	if(!__POPUP__) {
		include 'reply.php';
		include 'list.php';
	}
?>
