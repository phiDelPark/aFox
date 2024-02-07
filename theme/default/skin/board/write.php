<?php
	if(!defined('__AFOX__')) exit();
	@include_once dirname(__FILE__) . '/common.php';

	$is_manager = isManager(__MID__);
	$is = !empty($DOC)&&!empty($DOC['wr_srl']);
	$modal_image_view = $use_style=='gallery'&&!empty($CONFIGS['modal_image_view']);
?>

<section id="documentWrite" aria-label="Writing a post">
	<button type="button" class="btn-close float-end" aria-label="Back" onclick="window.history.go(-1);return false"></button>
	<h2 class="pb-3 mb-4 border-bottom"><?php echo getLang($is?'edit':'write')?></h2>
	<form id="setup" method="post" autocomplete="off" enctype="multipart/form-data" needvalidate>
	<input type="hidden" name="error_url" value="<?php echo getUrl()?>">
	<input type="hidden" name="success_url" value="<?php echo $is?getUrl('disp','','cpage','','rp','','srl',$use_style=='gallery'?'':$DOC['wr_srl']):getUrl('','id',__MID__)?>">
	<input type="hidden" name="module" value="board" />
	<input type="hidden" name="act" value="updateDocument" />
	<input type="hidden" name="md_id" value="<?php echo __MID__?>">
	<input type="hidden" name="wr_srl" value="<?php echo $is?$DOC['wr_srl']:''?>">

	<div class="clearfix">
		<?php if (empty($_MEMBER) || (!empty($DOC['wr_srl']) && $_MEMBER['mb_srl'] !== $DOC['mb_srl'])) { ?>
		<div class="mb-4">
			<input type="text" name="mb_nick" value="<?php echo $is?$DOC['mb_nick']:''?>" class="form-control mb-1" maxlength="20" placeholder="<?php echo getLang('id')?>"<?php echo empty($_MEMBER)?' required':''?><?php echo empty($DOC['wr_srl'])?'':' readonly'?>>
			<?php if (!$is_manager && empty($DOC['mb_srl'])) { ?>
				<input type="password" name="mb_password" class="form-control" placeholder="<?php echo getLang('password')?>"<?php echo empty($_MEMBER)?' required':''?>>
			<?php } ?>
		</div>
		<?php } ?>
		<?php if (!empty($_CFG['md_category'])) { $tmp = explode(',', $_CFG['md_category']);?>
			<div class="form-floating mb-2">
			<?php if ($use_style == 'gallery') { echo '<div class="form-control checkbox-group required">'; $tags = $is?explode(',',$DOC['wr_tags']):[]; foreach($tmp as $val){?>
					<input type="checkbox" name="wr_tags[]" value="<?php echo $val?>"<?php echo in_array($val, $tags)?' checked':''?> class="form-check-input" id="wrExtra_<?php echo $val?>">
					<label class="me-2" for="wrExtra_<?php echo $val?>"><?php echo $val?></label>
			<?php } echo '<input type="hidden" name="wr_category" value="'.$tmp[0].'"></div>';} else { ?>
					<select name="wr_category" class="form-control" id="wrCategory" required>
					<option value=""></option>
					<?php
						foreach ($tmp as $val) {
							echo '<option value="'.$val.'"'.(($is&&$val==$DOC['wr_category'])?' selected="selected"':'').'>'.$val.'</option>';
						}
					?>
					</select>
			<?php } ?>
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
				$_boxs = explode('|', $ex_caption); $ex_caption = $_boxs[0]; $is_required = substr($ex_caption, 0, 1) == '*';
				if($is_required) $ex_caption = substr($ex_caption, 1);
				if(!($is_radio = count($_boxs) > 2) && count($_boxs) > 1) {
					$ex_value = $_boxs[1];
					$_boxs = explode('&', $_boxs[1]);
					$is_radio = count($_boxs) > 1 ? 2 : 0;
				}
				$ex_value = $is ? @$DOC['wr_extra']['values'][$ex_key] : $ex_value;
		?>
		<?php if($is_radio){ echo '<div class="form-floating mb-2"><div class="form-control'.($is_radio===2&&$is_required?' checkbox-group required':'').'">';
				if($is_radio===2) $is_required = false; $ex_value = $is ? explode(',', $ex_value) : [];
				for($i=($is_radio===2?0:1), $n=count($_boxs); $i < $n; $i++){?>
					<input type="<?php echo $is_radio===2?'checkbox':'radio'?>" name="wr_extra_<?php echo $ex_key.($is_radio===2?'[]':'')?>" value="<?php echo $_boxs[$i]?>"<?php echo in_array($_boxs[$i], $ex_value)?' checked':''?> class="form-check-input" id="wrExtra_<?php echo $ex_key.$i?>"<?php echo $is_required?' required':''?>>
					<label class="me-2" for="wrExtra_<?php echo $ex_key.$i?>"><?php echo $_boxs[$i]?></label>
		<?php } echo '</div><label>'.$ex_caption.'</label></div>'; } else {?>
				<div class="form-floating mb-2">
					<input type="text" name="wr_extra_<?php echo $ex_key?>" value="<?php echo empty($ex_value)?'':escapeHTML($ex_value)?>" class="form-control" id="wrExtra_<?php echo $ex_key?>"<?php echo $is_required?' required':''?> maxlength="255">
					<label for="wrExtra_<?php echo $ex_key?>"><?php echo $ex_caption?></label>
				</div>
		<?php }}} ?>
			<div class="mb-4">
		<?php
			$issecret = ($is&&$DOC['wr_secret']=='1')?'true':'false';
			$ishtml = ($is&&$DOC['wr_type']=='2')||(!$is&&($_CFG['use_type']=='3'||$_CFG['use_type']=='9'))?1:0;
			$istool = [];
			if(empty($_CFG['use_type']) || $_CFG['use_type'] > 6) $istool['wr_type'] = $use_style == 'gallery'?[0,[]]:[$ishtml?'2':'1', ['MKDW'=>'1','HTML'=>'2']];
			if(empty($_CFG['use_secret'])) $istool['wr_secret'] = [$issecret, ['Secret'=>'true']];
			displayEditor(
				'wr_content', $is?$DOC['wr_content']:'',
				[
					'file'=>[__MID__, $is?$DOC['wr_srl']:0, $_CFG['md_file_max'], $_CFG['md_file_accept']],
					'html'=>$use_style != 'gallery' && $ishtml,
					'placeholder'=>$use_style != 'gallery' ?0:getLang('gallery_content'),
					'height'=>$use_style == 'gallery' ? '38px' : '350px',
					'required'=>$use_style == 'gallery'?'':getLang('request_input',['content']),
					'readonly'=>$use_style == 'gallery',
					'toolbar'=>$use_style != 'gallery',
					'typebar'=>$use_style == 'gallery' ?0:array(getLang('content'), $istool)
				]
			);
		?>
			</div>

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
