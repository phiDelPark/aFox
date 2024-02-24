<?php if(!defined('__AFOX__')) exit();
	@include_once dirname(__FILE__) . '/common.php';
	$is = !empty($DOC)&&!empty($DOC['wr_srl']);
?>

<section id="documentWrite" aria-label="Writing a post">
	<button class="btn-close float-end" aria-label="Back" onclick="window.history.go(-1);return false"></button>
	<h2 class="pb-3 mb-4 border-bottom"><?php echo getLang($is?'edit':'write')?></h2>
	<form id="setup" method="post" autocomplete="off" enctype="multipart/form-data" needvalidate>
	<input type="hidden" name="error_url" value="<?php echo getUrl()?>">
	<input type="hidden" name="success_url" value="<?php echo $is?getUrl('disp','','cpage','','rp','','srl',$use_style=='gallery'?'':$DOC['wr_srl']):getUrl('','id',_MID_)?>">
	<input type="hidden" name="module" value="board" />
	<input type="hidden" name="act" value="updateDocument" />
	<input type="hidden" name="md_id" value="<?php echo _MID_?>">
	<input type="hidden" name="wr_srl" value="<?php echo $is?$DOC['wr_srl']:''?>">

		<?php if (empty($_MEMBER) || (!empty($DOC['wr_srl']) && $_MEMBER['mb_srl'] !== $DOC['mb_srl'])) { ?>
		<div class="mb-2">
			<input type="text" name="mb_nick" value="<?php echo $is?$DOC['mb_nick']:''?>" class="form-control mb-1" maxlength="20" placeholder="<?php echo getLang('id')?>"<?php echo empty($_MEMBER)?' required':''?><?php echo empty($DOC['wr_srl'])?'':' readonly'?>>
			<?php if (!$is_manager && empty($DOC['mb_srl'])) { ?>
				<input type="password" name="mb_password" class="form-control" placeholder="<?php echo getLang('password')?>"<?php echo empty($_MEMBER)?' required':''?>>
			<?php } ?>
		</div>
		<?php } ?>
		<?php if (!empty($_CFG['md_category'])) { $tmp = explode(',', $_CFG['md_category']);?>
			<div class="form-floating mb-2">
					<select name="wr_category" class="form-control" id="wrCategory" required>
					<option value=""></option>
					<?php
						foreach ($tmp as $val) {
							echo '<option value="'.$val.'"'.(($is&&$val==$DOC['wr_category'])?' selected="selected"':'').'>'.$val.'</option>';
						}
					?>
					</select>
				<label for="wrCategory"><?php echo getLang('category')?></label>
			</div>
		<?php } ?>
			<div class="form-floating mb-2">
				<input type="text" name="wr_title" class="form-control" id="wrTitle" required maxlength="255" value="<?php echo $is?escapeHTML($DOC['wr_title']):''?>">
				<label for="wrTitle"><?php echo getLang('title')?></label>
			</div>
		<?php
			if (!empty($_CFG['md_extra']['keys'])) {
			foreach($_CFG['md_extra']['keys'] as $ex_key=>$ex_caption) {
				$_boxs = explode('|', $ex_caption);
				if(!($is_radio=count($_boxs)>1))$_boxs=explode('&',$ex_caption);
				if($is_required=(substr($_boxs[0],0,1)=='*'))$_boxs[0]=substr($_boxs[0],1);
				$ex_value = $is ? @$DOC['wr_extra']['values'][$ex_key] : (count($_boxs)===2?$_boxs[1]:'');
		?>
		<?php if(count($_boxs)>2){ echo '<div class="form-floating mb-2"><div class="form-control'.(!$is_radio&&$is_required?' checkbox-group required':'').'">';
				if(!$is_radio) $is_required = false; $ex_value = $is ? explode(',', $ex_value) : [];
				for($i=1, $n=count($_boxs); $i < $n; $i++){?>
					<input type="<?php echo $is_radio?'radio':'checkbox'?>" name="wr_extra_<?php echo $ex_key.($is_radio?'':'[]')?>" value="<?php echo $_boxs[$i]?>"<?php echo in_array($_boxs[$i], $ex_value)?' checked':''?> class="form-check-input" id="wrExtra_<?php echo $ex_key.$i?>"<?php echo $is_required?' required':''?>>
					<label class="me-2" for="wrExtra_<?php echo $ex_key.$i?>"><?php echo $_boxs[$i]?></label>
		<?php } echo '</div><label>'.$_boxs[0].'</label></div>'; } else {?>
				<div class="form-floating mb-2">
					<input type="text" name="wr_extra_<?php echo $ex_key?>" value="<?php echo escapeHTML($ex_value)?>" class="form-control" id="wrExtra_<?php echo $ex_key?>"<?php echo $is_required?' required':''?> maxlength="255">
					<label for="wrExtra_<?php echo $ex_key?>"><?php echo $_boxs[0]?></label>
				</div>
		<?php }}} ?>
		<div class="clearfix my-4">
		<?php
			if(($_CFG['use_type']&&$_CFG['use_type'] < 7)){
				$istool = [];
			}else{
				$DOC['wr_type'] = $is ? $DOC['wr_type'] : ($_CFG['use_type']&&$_CFG['use_type']>7?'1':'0');
				$istool = ['wr_type'=>[$DOC['wr_type'], ['TEXT'=>'0',($_CFG['use_type']=='9'?'HTML':'MKDW')=>'1']]];
			}
			if(empty($_CFG['use_secret'])) $istool['wr_secret']=[($is&&$DOC['wr_secret']=='1')?'true':'false',['Secret'=>'true']];
			$ishtml = $_CFG['use_type']=='9'&&(!$is||$DOC['wr_type']);
			displayEditor(
				'wr_content', $_CFG['use_type']=='9'?toHTML(@$DOC['wr_content'],1,''):@$DOC['wr_content'],
				[
					'html'=>$ishtml,
					'toolbar'=>true,
					'typebar'=>array(getLang('content'), $istool),
					'required'=>getLang('request_input',['content']),
					'file'=>[_MID_, $is?$DOC['wr_srl']:0, (int)$_CFG['md_file_max'], $_CFG['md_file_accept']]
				]
			);
		?>

			<hr class="mb-4">
			<div class="d-grid">
				<button type="submit" class="btn btn-success btn-lg"><?php echo getLang('save')?></button>
			</div>
		</div>
	</form>
</section>

<?php
/* End of file write.php */
/* Location: ./theme/default/skin/board/write.php */
