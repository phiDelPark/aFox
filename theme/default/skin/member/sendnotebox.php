<?php if(!defined('__AFOX__')) exit();?>

<form method="post" autocomplete="off" onsubmit="return themeSendNote(this)">
<input type="hidden" name="module" value="member" />
<input type="hidden" name="act" value="sendNote" />
<input type="hidden" name="mb_srl" value="<?php echo $_DATA['nt_sender']?>">
	<h5 class="clearfix" style="margin:10px 3px"><?php echo getLang('target')?> : <?php echo $_DATA['nt_sender_nick']?></h5>
	<hr class="divider" style="margin:10px 3px">
	<div class="form-group mb-3">
		<label for="id_nt_content"><?php echo getLang('content')?></label>
		<textarea name="nt_content" id="id_nt_content" class="form-control vresize" style="height:290px"></textarea>
	</div>
	<div class="clearfix" style="text-align:right">
		<button class="btn btn-secondary" onclick="javascript:window.close()"><?php echo getLang('close')?></button>
		<button type="submit" class="btn btn-success"><?php echo getLang('send')?></button>
	</div>
</form>
<script>function themeSendNote(e){return exec_ajax({...e.serializeArray()}).then(e=>{let t=alert(e.message);"object"==typeof t?t.then(()=>{window.close()}):window.close()}).catch(e=>{alert(e)}),!1}</script>