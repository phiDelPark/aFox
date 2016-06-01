<?php
	if(!defined('__AFOX__')) exit();
	$_item = getDBItem(_AF_NOTE_TABLE_, ['nt_srl'=>$_DATA['srl']]);
	if(empty($_item['nt_srl'])) return;
	if($_item['nt_read_date'] === '0000-00-00 00:00:00') {
		DB::update(_AF_NOTE_TABLE_, ['(nt_read_date)'=>'NOW()'], ['nt_srl'=>$_DATA['srl']]);
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
		echo $_item['nt_note'];
	?>
	</article>
</section>

