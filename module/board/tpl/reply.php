<?php
if(!defined('__AFOX__')) exit();
?>

<section id="bdReply">
	<article class="clearfix" role="list">
	<?php
		$not_edit_str = ' style="text-decoration:line-through" data-msg-box="warning" data-title="'.getLang('error_permitted').'"';

		$input_password = '<form action="%s" class="input-password" method="post" autocomplete="off">'.getLang('request_input', ['password'])
										.'<div class="input-group" style="margin-top:10px"><input class="form-control" name="mb_password" type="password" placeholder="'. getLang('password').'" required>'
										.'<span class="input-group-btn"><button class="btn btn-default" type="submit">'. getLang('ok').'</button></span></div></form>';
		foreach ($REPLYS as $key => $value) {

			$_len = strlen($value['rp_depth']);

			$_icon = $value['mb_srl'].'/profile_image.png';
			$_icon = _AF_URL_ . (file_exists(_AF_MEMBER_DATA_.$_icon) ? 'data/member/' . $_icon : 'common/img/user_default.jpg');

			$rp_secret = $value['rp_secret'] == '1';
			$rp_content = $value['grant_view'] ? $value['rp_content'] : (!empty($value['mb_srl'])?getLang($rp_secret?'msg_is_secret':'error_permitted'):sprintf($input_password,getUrl('rp',$value['rp_srl'])));

			echo '<a id="reply_'.$value['rp_srl'].'"'.(!empty($_DATA['rp'])&&$value['rp_srl']==$_DATA['rp'] ? ' class="active"':'').'></a>'
				.'<div class="reply-item" style="padding-left:'.(($_len>5?5:$_len)*30).'px"><div class="left">'
				.'<img src="'.$_icon.'" alt="Profile" class="profile"><div class="area-author-info"><h5 class="mb_nick" data-srl="'.$value['mb_srl'].'" data-rank="'.(ord($value['mb_rank']) - 48).'">'.$value['mb_nick'].'</h5>'
				.'<div class="reply-date"><small>'.date('Y/m/d h:i', strtotime($value['rp_update'])).($rp_secret?' <i class="glyphicon glyphicon-lock" aria-hidden="true"></i>':'').'</small></div></div></div><div class="right"><div class="content clearfix">'.toHTML($rp_content,$value['rp_type'],'current_replies').'</div>'
				.'<div class="area-text-button clearfix"><div class="pull-right">'
				.($is_rp_grant&&$_len<5&&$value['rp_status']!='4'?('<a href="#" role="button" data-exec-act="board.updateComment" data-act-param="rp_parent,'.$value['rp_srl'].'"><i class="glyphicon glyphicon-comment" aria-hidden="true"></i> '.getLang('reply').'</a>'):'')
				.'<a href="#" role="button" '.($value['grant_write']?'data-exec-act="board.getComment" data-act-param="rp_srl,'.$value['rp_srl'].'"'.(empty($value['mb_srl'])&&!$is_manager&&$rp_secret?' data-act-password="1"':''):$not_edit_str).'><i class="glyphicon glyphicon-edit" aria-hidden="true"></i> '.getLang('edit').'</a>'
				.'<a href="#" role="button" '.($is_manager||$value['grant_write']?'data-exec-act="board.deleteComment" data-act-param="rp_srl,'.$value['rp_srl'].'"'.(empty($value['mb_srl'])&&!$is_manager?' data-act-password="1"':''):$not_edit_str).'><i class="glyphicon glyphicon-remove" aria-hidden="true"></i> '.getLang('delete').'</a>'
				.'</div></div></div></div>';
		}
	?>
	</article>

	<footer class="reply-editer">
		<form method="post" autocomplete="off" data-exec-ajax="board.updateComment">
		<input type="hidden" name="success_return_url" value="<?php echo getUrl('rp','')?>">
		<input type="hidden" name="wr_srl" value="<?php echo $_DATA['srl'] ?>">
		<a class="close" href="javascrip:;" style="display:none"><span aria-hidden="true">×</span></a>
			<div class="form-inline area-mbinfo<?php echo $is_rp_grant&&empty($_MEMBER)?'':' hide'?>">
				<input type="text" name="mb_nick" class="form-control"<?php echo empty($_MEMBER)?' required':''?> maxlength="20" placeholder="<?php echo getLang('id')?>">
				<input type="password" name="mb_password" class="form-control"<?php echo empty($_MEMBER)?' required':''?> placeholder="<?php echo getLang('password')?>">
			</div>
			<dir class="area-group">
				<div class="form-group">
					<?php
						$istool = [];
						if(empty($_CFG['use_type']) || $_CFG['use_type'] > 6) $istool['rp_type'] = ['1', ['MKDW'=>'1','HTML'=>'2']];
						if(empty($_CFG['use_secret'])) $istool['rp_secret'] = [false,'Secret'];

						displayEditor(
							'rp_content', '',
							[
								'required'=>getLang('request_input', ['content']),
								'readonly'=>(!$is_rp_grant),
								'toolbar'=>count($istool)>0?array(getLang('reply'), $istool):[]
							]
						);
					?>
				</div>
				<div class="area-button">
					<button type="submit" class="btn btn-success btn-block"<?php if (!$is_rp_grant) {echo ' disabled="disabled"';} ?>><i class="glyphicon glyphicon-ok" aria-hidden="true"></i> <?php echo getLang('save')?></button>
				</div>
			</dir>
		</form>
	</footer>

</section>

<?php
/* End of file reply.php */
/* Location: ./module/board/tpl/reply.php */
