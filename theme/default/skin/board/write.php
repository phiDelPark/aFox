<?php
	if(!defined('__AFOX__')) exit();
	@include_once dirname(__FILE__) . '/common.php';

	$is_manager = isManager(__MID__);
	$is = !empty($DOC)&&!empty($DOC['wr_srl']);
	$modal_image_view = $use_style=='gallery'&&!empty($CONFIGS['modal_image_view']);
?>

<section id="documentWrite" aria-label="Writing a post">
	<button type="button" class="btn-close float-end" aria-label="Back" onclick="window.history.go(-1);return false"></button>
	<h2 class="pb-3 mb-4 fst-italic border-bottom"><?php echo getLang($is?'edit':'write')?></h2>
	<form id="setup" method="post" autocomplete="off" enctype="multipart/form-data" needvalidate>
	<input type="hidden" name="error_url" value="<?php echo getUrl()?>">
	<input type="hidden" name="success_url" value="<?php echo $is?getUrl('disp','','cpage','','rp',''):getUrl('','id',__MID__)?>">
	<input type="hidden" name="module" value="board" />
	<input type="hidden" name="act" value="updateDocument" />
	<input type="hidden" name="md_id" value="<?php echo __MID__?>">
	<input type="hidden" name="wr_srl" value="<?php echo $is?$DOC['wr_srl']:''?>">

	<div class="clearfix">
		<?php if (empty($_MEMBER) || (!empty($DOC['wr_srl']) && $_MEMBER['mb_srl'] !== $DOC['mb_srl'])) { ?>
		<div class="mb-4">
			<input type="text" name="mb_nick" class="form-control mb-1"<?php echo empty($_MEMBER)?' required':''?> maxlength="20" placeholder="<?php echo getLang('id')?>" value="<?php echo $is?$DOC['mb_nick']:''?>"<?php echo empty($DOC['wr_srl'])?'':' readonly'?>>
			<?php if (!$is_manager && empty($DOC['mb_srl'])) { ?>
				<input type="password" name="mb_password" class="form-control"<?php echo empty($_MEMBER)?' required':''?> placeholder="<?php echo getLang('password')?>">
			<?php } ?>
		</div>
		<?php } ?>
		<?php if (!empty($_CFG['md_category'])) { ?>
			<div class="form-floating mb-2">
				<select name="wr_category" class="form-control" id="wrCategory" required>
				<option value=""></option>
				<?php
					$tmp = explode(',', $_CFG['md_category']);
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
				$is_required = substr($ex_caption,-1,1) == '*';
				if($is_required) $ex_caption = substr($ex_caption,0,-1);
				$check_boxs = explode('&', $ex_caption);
				$radio_boxs = explode('|', $ex_caption);
				$ex_caption = count($check_boxs)>1 ? $check_boxs[0] : $radio_boxs[0];
				$ex_value = $DOC['wr_extra']['values'][$ex_key];
		?>
		<?php if(count($check_boxs)>1||count($radio_boxs)>1){
					echo '<div class="form-floating mb-2"><div class="form-control">';
					for($i=1, $n=count($check_boxs); $i < $n; $i++){
		?>
					<input type="checkbox" name="wr_extra_<?php echo $ex_key?>[]" value="<?php echo $check_boxs[$i]?>" class="form-check-input" id="wrExtra_<?php echo $ex_key.$i?>"<?php echo $is_required?' required':''?>>
					<label class="me-2" for="wrExtra_<?php echo $ex_key.$i?>"><?php echo $check_boxs[$i]?></label>
		<?php } for($i=1, $n=count($radio_boxs); $i < $n; $i++){?>
					<input type="radio" name="wr_extra_<?php echo $ex_key?>" value="<?php echo $radio_boxs[$i]?>" class="form-check-input" id="wrExtra_<?php echo $ex_key.$i?>"<?php echo $is_required?' required':''?>>
					<label class="me-2" for="wrExtra_<?php echo $ex_key.$i?>"><?php echo $radio_boxs[$i]?></label>
		<?php } echo '</div><label>'.$ex_caption.'</label></div>';} else { ?>
				<div class="form-floating mb-2">
					<input type="text" name="wr_extra_<?php echo $ex_key?>" class="form-control" id="wrExtra_<?php echo $ex_key?>"<?php echo $is_required?' required':''?> maxlength="255" value="<?php echo $is?(empty($ex_value)?'':escapeHTML($ex_value)):''?>">
					<label for="wrExtra_<?php echo $ex_key?>"><?php echo $ex_caption?></label>
				</div>
		<?php }}} ?>
			<div class="mb-4">
				<?php
					$issecret = ($is&&$DOC['wr_secret']=='1')?'true':'false';
					$ishtml = ($is&&$DOC['wr_type']=='2')||(!$is&&($_CFG['use_type']=='3'||$_CFG['use_type']=='9'))?1:0;
					$istool = [];
					if(empty($_CFG['use_type']) || $_CFG['use_type'] > 6) $istool['wr_type'] = [$ishtml?'2':'1', ['MKDW'=>'1','HTML'=>'2']];
					if(empty($_CFG['use_secret'])) $istool['wr_secret'] = [$issecret, ['Secret'=>'true']];
					displayEditor(
						'wr_content', $is?$DOC['wr_content']:'',
						[
							'file'=>[__MID__, $is?$DOC['wr_srl']:0, $_CFG['md_file_max']],
							'required'=>getLang('request_input',['content']),
							'html'=>$ishtml,
							'typebar'=>array('<span class="border-start" style="color:rgba(var(--bs-body-color-rgb),.65);font-size:.85rem;padding-left:.75rem">'.getLang('content').'</span>', $istool),
							'toolbar'=>true
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
/* End of file bo_write.php */
/* Location: ./theme/default/bo_write.php */
