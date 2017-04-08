<?php
	if(!defined('__AFOX__')) exit();
?>

<form class="sendNote-content" method="post" autocomplete="off" data-exec-ajax="member.sendNote">
<input type="hidden" name="mb_srl" value="<?php echo $_{'member'}['nt_sender']?>">
	<h5 class="clearfix" style="margin:0"><?php echo getLang('target')?> : <?php echo $_{'member'}['nt_sender_nick']?></h5>
	<hr class="divider" style="margin:10px 3px">
	<div class="form-group">
		<label for="id_nt_content"><?php echo getLang('content')?></label>
		<textarea name="nt_content" id="id_nt_content" class="form-control vresize" style="height:290px"></textarea>
	</div>
	<div class="clearfix" style="text-align:right">
		<button type="button" class="btn btn-default" onclick="javascript:window.close()"><?php echo getLang('close')?></button>
		<button type="submit" class="btn btn-success"><i class="glyphicon glyphicon-send" aria-hidden="true"></i> <?php echo getLang('send')?></button>
	</div>
</form>

<script>
	$('[data-exec-ajax="member.sendNote"]').on('success.exec.ajax', function(e, data, xhr){
		alert(data['message']);
		window.close();
	});
</script>