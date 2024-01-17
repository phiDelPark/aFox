<?php
	if(!defined('__AFOX__')) exit();

	if($_DATA['nt_read_date'] === '0000-00-00 00:00:00') {
		DB::update(_AF_NOTE_TABLE_, ['^nt_read_date'=>'NOW()'], ['nt_srl'=>$_POST['srl']]);
		$_DATA['nt_read_date'] = date("Y-m-d H:i:s");
	}
?>

<section id="inboxView" class="mb-4 border-bottom" aria-label="Contents of this post">
	<h3 class="pb-3 mb-1 border-bottom"><?php echo $_DATA['wr_title']?></h3>
	<p class="d-flex w-100 justify-content-between text-secondary"><span><?php echo date('F j, Y', strtotime($_DATA['nt_send_date'])).' by '.$_DATA['nt_sender_nick']?></span></p>
	<div class="h-md-250 mb-3">
		<?php echo toHTML($_DATA['nt_content'], 0)?>
	</div>
	<div class="text-end text-secondary"><span><?php echo date('F j, Y', strtotime($_DATA['nt_read_date']))?></span></p>
	<div class="text-end mb-3">
		<button type="button" class="btn btn-danger me-2" data-exec-ajax="member.deleteNote" data-ajax-param="nt_srl,<?php echo $_DATA['nt_srl']?>,success_return_url,<?php echo urlencode(getUrl('srl',''))?>" data-ajax-confirm="<?php echo getLang('confirm_delete',['message'])?>"><?php echo getLang('delete')?></button>
		<button type="button" class="btn btn-warning" onclick="return _popSendNoteBox(<?php echo $_DATA['nt_srl']?>)"><?php echo getLang('reply')?></button>
	</div>
</section>

<script>
	function _popSendNoteBox(srl) {
		pop_win(request_uri+'?module=member&disp=sendNoteBox&popup=1&srl='+srl,450,450,'afoxSendNoteBox');
		return false;
	}
</script>
