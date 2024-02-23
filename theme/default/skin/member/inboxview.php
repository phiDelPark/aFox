<?php
	if(!defined('__AFOX__')) exit();

	if($_DATA['nt_read_date'] === '0000-00-00 00:00:00') {
		DB::update(_AF_NOTE_TABLE_, ['nt_read_date(=)'=>'NOW()'], ['nt_srl'=>$_GET['srl']]);
		$_DATA['nt_read_date'] = date("Y-m-d H:i:s");
	}
?>

<section id="inboxView" class="mb-4 border-bottom" aria-label="Contents of this post">
	<h3 class="pb-3 mb-1 border-bottom"><?php echo getLang('note')?></h3>
	<p class="d-flex w-100 justify-content-between text-secondary"><span><?php echo date('F j, Y', strtotime($_DATA['nt_send_date'])).' by '.$_DATA['nt_sender_nick']?></span></p>
	<div class="h-md-250 mb-3">
		<?php echo toHTML($_DATA['nt_content'], '1')?>
	</div>
	<div class="text-end text-secondary"><span><?php echo date('F j, Y', strtotime($_DATA['nt_read_date']))?></span></p>
	<div class="text-end mb-3"><button class="btn btn-warning" onclick="return themePopSendNoteBox(<?php echo $_DATA['nt_srl']?>)"><?php echo getLang('reply')?></button>
	</div>
</section>

<script>function themePopSendNoteBox(e){return pop_win(request_uri+"?module=member&disp=sendNoteBox&popup=1&srl="+e,450,450,"afoxSendNoteBox"),!1}</script>
