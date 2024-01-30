<?php
if(!defined('__AFOX__')) exit();
?>

<section id="documentReply" class="list-group list-group-flush mb-4" aria-label="Replies to post">
<?php
	$rp = empty($_POST['rp'])?0:$_POST['rp'];
	$location_hash = $rp&&$REPLYS?'documentReply':'';
	foreach ($REPLYS as $key => $val) {
		$_len = strlen($val['rp_depth']);
		$rp_secret =  $val['rp_secret'] == '1';
		$rp_permit = !$rp_secret || $value['grant_view'] || $is_manager || $login_srl === $val['mb_srl'];
		$rp_content = !$rp_permit || $rp_secret ? '<svg class="bi me-1"><use href="'._AF_THEME_URL_.'bi-icons.svg#shield-lock"/></svg>' : '';
		$rp_content .= !$rp_permit ? getLang('error_permitted') : toHTML($val['rp_content'], $val['rp_type']);
		echo sprintf(
			'<div id="reply-%s" class="d-flex flex-lg-row gap-3 p-2 border-bottom" style="margin-left:%spx"><svg class="bi bi-lg mt-1"><use href="%s"/></svg>
			<div class="w-100"><div>%s</div><div class="d-flex justify-content-between text-body-secondary mt-1"><small>%s</small><small>%s:
			<a href="%s" onclick="return confirm(\'%s\')" class="text-decoration-none">&Chi;</a></small></div></div></div>',
			$val['rp_srl'],
			($_len>5?5:$_len)*30,
			_AF_THEME_URL_.'bi-icons.svg#person-bounding-box',
			$rp_content,
			$val['mb_nick'],
			date('Y/m/d', strtotime($val['rp_regdate'])),
			getUrl(
				'module','board','act','deleteComment','rp_srl',$val['rp_srl'],
				'error_url',urlencode(getUrl()),
				'success_url',urlencode(getUrl('srl',$val['wr_srl'],'rp',$val['rp_srl']))
			),
			getLang('confirm_delete',['reply'])
		);
		if($rp === $val['rp_srl']) $location_hash = 'reply-'.$val['rp_srl'];
	}
	if($location_hash) echo '<script>location.hash="'.$location_hash.'"</script>'
?>
</section>

<section id="replyEditer" class="mb-5" aria-label="Write a reply to this post">
	<form method="post" autocomplete="off" needvalidate>
		<input type="hidden" name="error_url" value="<?php echo getUrl()?>">
		<input type="hidden" name="success_url" value="<?php echo getUrl('rp','')?>">
		<input type="hidden" name="module" value="board">
		<input type="hidden" name="act" value="updateComment">
		<input type="hidden" name="wr_srl" value="<?php echo $_POST['srl'] ?>">
		<div class="mb-3<?php echo $is_rp_grant&&empty($_MEMBER)?'':' d-none'?>">
			<input type="text" name="mb_nick" class="form-control mb-1"<?php echo empty($_MEMBER)?' required':''?> maxlength="20" placeholder="<?php echo getLang('id')?>">
			<input type="password" name="mb_password" class="form-control"<?php echo empty($_MEMBER)?' required':''?> placeholder="<?php echo getLang('password')?>">
		</div>
		<div class="d-flex w-100 justify-content-between">
		<?php
			$istool = [];
			if(empty($_CFG['use_type']) || $_CFG['use_type'] > 6) $istool['rp_type'] = ['1', ['TEXT'=>'0','MKDW'=>'1']];
			if(empty($_CFG['use_secret'])) $istool['rp_secret'] = ['false', ['Secret'=>'true']];

			displayEditor(
				'rp_content', '',
				[
					'height'=>'70px',
					'required'=>getLang('request_input', ['content']),
					'readonly'=>(!$is_rp_grant),
					'typebar'=>count($istool)>0?array(getLang('reply'), $istool):[]
				]
			);
		?>
		<button type="submit" style="width:20%" class="btn btn-success ms-2 mt-4"<?php if (!$is_rp_grant) {echo ' disabled="disabled"';} ?>><?php echo getLang('save')?></button>
		</div>
	</form>
</section>

<?php
/* End of file reply.php */
/* Location: ./theme/default/skin/board/reply.php */
