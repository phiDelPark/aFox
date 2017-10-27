<?php
	if(!defined('__AFOX__')) exit();
	require_once 'common.php';

	$_item = getDBItem(_AF_NOTE_TABLE_, ['mb_srl'=>$mb['mb_srl'],'nt_srl'=>$_DATA['srl']]);
	if(empty($_item['nt_srl'])) return;
	if($_item['nt_read_date'] === '0000-00-00 00:00:00') {
		DB::update(_AF_NOTE_TABLE_, ['(nt_read_date)'=>'NOW()'], ['nt_srl'=>$_DATA['srl']]);
		$_item['nt_read_date'] = date("Y-m-d H:i:s");
	}
?>
<section>
	<header>
		<h4 class="clearfix" style="margin:0"><?php echo date((__MOBILE__?'y':'Y').getLang('year').' m'.getLang('month').' d'.getLang('day').' A h:i', strtotime($_item['nt_send_date']))?></h4>
		<hr class="divider" style="margin:3px">
		<div class="clearfix">
			<span class="pull-left"><?php echo $_item['nt_sender_nick']?></span>
			<span class="pull-right"><?php echo date((__MOBILE__?'y':'Y').getLang('year').' m'.getLang('month').' d'.getLang('day').' A h:i', strtotime($_item['nt_read_date']))?></span>
		</div>
	</header>
	<article style="padding:20px 5px 30px;min-height:200px">
	<?php
		echo $_item['nt_content'];
	?>
	</article>
	<footer class="area-text-button clearfix" style="text-align:right;margin-bottom:50px">
		<button type="button" class="btn btn-danger" data-exec-ajax="member.deleteNote" data-ajax-param="nt_srl,<?php echo $_item['nt_srl']?>,success_return_url,<?php echo getUrl('srl','')?>" onclick="jQuery(this).data('clicked', confirm($_LANG['confirm_delete'].sprintf([$_LANG['message']]).escapeHtml()))"><?php echo getLang('delete')?></button>
		<button type="button" class="btn btn-success min-width-100" onclick="return _popSendNoteBox(<?php echo $_item['nt_srl']?>)"><i class="glyphicon glyphicon-refresh" aria-hidden="true"></i> <?php echo getLang('reply')?></button>
	</footer>
</section>

<script>
	function _popSendNoteBox(srl) {
		pop_win(request_uri+'?module=member&disp=sendNoteBox&popup=1&srl='+srl,450,450,'af_sendNoteBox');
		return false;
	}
</script>
