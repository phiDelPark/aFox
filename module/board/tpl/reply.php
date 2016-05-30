<?php
if(!defined('__AFOX__')) exit();
$cmt = empty($_{'board'}['CURRENT_COMMENT_LIST']) ? false : $_{'board'}['CURRENT_COMMENT_LIST'];
?>

<section id="board_reply">
	<article class="clearfix">
	<?php
		$current_cpage = $cmt['current_page'];
		$total_cpage = $cmt['total_page'];
		$not_edit_str = 'style="text-decoration:line-through" onclick="alert(\''.escapeHtml(getLang('msg_not_permitted')).'\');return false"';
		$is_owner_permit_view = $is_manager || $is_login_mb_srl === $_{'board'}['mb_srl'] || !empty($GLOBALS['_PERMIT_VIEW_'][md5($_{'board'}['md_id'].'_'.$_{'board'}['wr_srl'])]);
		$input_password = '<form action="%s" class="input-password" method="post" autocomplete="off">'
										.sprintf(getLang('warn_input'), getLang('password'))
										.'<div class="input-group" style="margin-top:10px"><input class="form-control" name="mb_password" type="password" placeholder="'. getLang('password').'" required>'
										.'<span class="input-group-btn"><button class="btn btn-default" type="submit">'. getLang('ok').'</button></span></div></form>';
		foreach ($cmt['data'] as $key => $value) {
			$_len = strlen($value['rp_depth']);
			$_deleted = $value['rp_status']=='4';
			$_icon = $value['mb_srl'].'/profile_image.png';
			if(file_exists(_AF_MEMBER_DATA_.$_icon)) {
				$_icon = _AF_URL_ . 'data/member/' . $_icon;
			} else {
				$_icon = _AF_URL_ .'module/board/tpl/user_default.jpg';
			}

			$is_permit = $is_owner_permit_view || $value['rp_secret']!='1' || $is_login_mb_srl === $value['mb_srl'];
			if(!$is_permit) $is_permit = !empty($GLOBALS['_PERMIT_VIEW_'][md5($_{'board'}['md_id'].'_'.$value['wr_srl'].'_'.$value['rp_srl'])]);
			$rp_content = $is_permit ? $value['rp_content'] : (!empty($value['mb_srl'])?getLang('msg_not_permitted'):sprintf($input_password,getUrl('rp',$value['rp_srl'])));

			$is_edit = empty($value['mb_srl']) || $is_manager || $is_login_mb_srl === $value['mb_srl'];

			echo '<a id="reply_'.$value['rp_srl'].'"'.(!empty($_DATA['rp'])&&$value['rp_srl']==$_DATA['rp'] ? ' class="active"':'').'></a>'
				.'<div class="reply-item" style="padding-left:'.(($_len>5?5:$_len)*30).'px"><div class="left">'
				.'<img src="'.$_icon.'" alt="Profile" class="profile"><div class="area-author-info"><h5 class="author">'.$value['mb_nick'].'</h5>'
				.'<div class="reply-date"><small>'.date('Y/m/d h:m', strtotime($value['rp_update'])).'</small></div></div></div><div class="right"><div class="content clearfix">'.toHTML($value['rp_type'], $rp_content).'</div>'
				.'<div class="area-text-button clearfix"><div class="pull-right">'
				.(!$_deleted&&$is_rp_grant&&$_len<5?('<a href="#" role="button" data-exec-act="board.updateComment" data-act-param="rp_parent,'.$value['rp_srl'].'"><i class="fa fa-reply gly-rotate-180" aria-hidden="true"></i> '.getLang('reply').'</a>'):'')
				.'<a href="#" role="button" '.($is_edit?'data-exec-act="board.getComment" data-act-param="rp_srl,'.$value['rp_srl'].'"'.(empty($value['mb_srl'])?' data-act-password="1"':''):$not_edit_str).'><i class="fa fa-pencil-square-o" aria-hidden="true"></i> '.getLang('edit').'</a>'
				.'<a href="#" role="button" '.($is_edit?'data-exec-act="board.deleteComment" data-act-param="rp_srl,'.$value['rp_srl'].'"'.(empty($value['mb_srl'])?' data-act-password="1"':''):$not_edit_str).'><i class="fa fa-trash-o" aria-hidden="true"></i> '.getLang('delete').'</a>'
				.'</div></div></div></div>';
		}
	?>
	</article>
	<?php if($total_cpage > 1) { ?>
	<nav class="text-center">
		<ul class="pagination pagination-sm">
		<li<?php echo $current_cpage <= 1 ? ' class="disabled"' : ''?>><a href="<?php echo  $current_cpage <= 1 ? '#' : getUrl('cpage',$current_cpage-1)?>" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>
	<?php
		for ($i=1; $i <= $total_cpage; $i++) {
			echo '<li'.($current_cpage == $i ? ' class="active"' : '').'><a href="'.getUrl('cpage',$i).'">'.$i.'</a></li>';
		}
	?>
		<li<?php echo $current_cpage >= $total_cpage ? ' class="disabled"' : ''?>><a href="<?php echo $current_cpage >= $total_cpage ? '#' : getUrl('cpage',$current_cpage+1)?>" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>
		</ul>
	</nav>
	<?php } ?>
	<?php if ($is_rp_grant) { ?>
	<footer class="reply-editer">
		<form method="post" autocomplete="off" data-exec-ajax="board.updateComment">
		<input type="hidden" name="success_return_url" value="<?php echo getUrl('rp','')?>">
		<input type="hidden" name="wr_srl" value="<?php echo $_DATA['srl'] ?>">
		<a class="close" href="javascrip:;" style="display:none"><span aria-hidden="true">×</span></a>
		<?php if (empty($_MEMBER)) { ?>
			<div class="form-inline">
				<span class="sr-only"><?php echo getLang('id')?></span>
				<input type="text" name="mb_nick" class="form-control" required maxlength="20" placeholder="<?php echo getLang('id')?>">
				<span class="sr-only"><?php echo getLang('password')?></span>
				<input type="password" name="mb_password" class="form-control" required placeholder="<?php echo getLang('password')?>">
			</div>
		<?php } ?>
			<dir class="area-group">
				<div class="form-group"><span class="sr-only"><?php echo getLang('content')?></span>
					<?php
						$istool = [];
						if(empty($_CFG['module']['use_type'])) $istool['rp_type'] = ['1', ['MKDW'=>'1','HTML'=>'2']];
						if(empty($_CFG['module']['use_secret'])) $istool['rp_secret'] = [false,'Secret'];

						dispEditor(
							'rp_content', '',
							[
								'required'=>sprintf(getLang('warn_input'), getLang('content')),
								'readonly'=>(!$is_rp_grant),
								'toolbar'=>(!$is_rp_grant)?[]:array(getLang('reply'), $istool)
							]
						);
					?>
				</div>
				<div class="area-button">
					<button type="submit" class="btn btn-success btn-block"<?php if (!$is_rp_grant) {echo ' disabled="disabled"';} ?>><i class="fa fa-check" aria-hidden="true"></i> <?php echo getLang('save')?></button>
				</div>
			</dir>
		</form>
	</footer>
	<?php } ?>
</section>

<?php
/* End of file reply.php */
/* Location: ./module/board/tpl/reply.php */