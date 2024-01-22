<?php
	if(!defined('__AFOX__')) exit();
	@include_once dirname(__FILE__) . '/common.php';

	$is_manager = isManager(__MID__);
	$is = !empty($DOC)&&!empty($DOC['wr_srl']);
	$modal_image_view = $use_style=='gallery'&&!empty($CONFIGS['modal_image_view']);
?>

<section id="documentWrite" aria-label="Writing a post">
	<h3 class="pb-3 mb-4 fst-italic border-bottom"><?php echo getLang($is?'edit':'write')?></h3>

	<form id="setup" method="post" autocomplete="off" enctype="multipart/form-data" needvalidate>
	<input type="hidden" name="error_return_url" value="<?php echo getUrl()?>">
	<input type="hidden" name="success_return_url" value="<?php echo $is?getUrl('disp','','cpage','','rp',''):getUrl('','id',__MID__)?>">
	<input type="hidden" name="module" value="board" />
	<input type="hidden" name="act" value="updateDocument" />
	<input type="hidden" name="md_id" value="<?php echo __MID__?>">
	<input type="hidden" name="wr_srl" value="<?php echo $is?$DOC['wr_srl']:''?>">

	<div>
		<?php if (empty($_MEMBER) || (!empty($DOC['wr_srl']) && $_MEMBER['mb_srl'] !== $DOC['mb_srl'])) { ?>
		<div class="mb-4">
			<input type="text" name="mb_nick" class="form-control mb-1"<?php echo empty($_MEMBER)?' required':''?> maxlength="20" placeholder="<?php echo getLang('id')?>" value="<?php echo $is?escapeHtml($DOC['mb_nick']):''?>"<?php echo empty($DOC['wr_srl'])?'':' readonly'?>>
			<?php if (!$is_manager && empty($DOC['mb_srl'])) { ?>
				<input type="password" name="mb_password" class="form-control"<?php echo empty($_MEMBER)?' required':''?> placeholder="<?php echo getLang('password')?>">
			<?php } ?>
		</div>
		<?php } ?>
		<?php if (!empty($_CFG['md_category'])) { ?>
			<div class="mb-2">
				<select name="wr_category" class="form-control" required>
				<option value=""><?php echo getLang('category')?></option>
				<?php
					$tmp = explode(',', $_CFG['md_category']);
					foreach ($tmp as $val) {
						echo '<option value="'.$val.'"'.(($is&&$val==$DOC['wr_category'])?' selected="selected"':'').'>'.$val.'</option>';
					}
				?>
				</select>
			</div>
		<?php } ?>
			<div class="mb-2">
				<label for="wrTitle"><?php echo getLang('title')?></label>
				<input type="text" name="wr_title" class="form-control" id="wrTitle" required maxlength="255" value="<?php echo $is?escapeHtml($DOC['wr_title']):''?>">
			</div>
		<?php
			if (!empty($_CFG['md_extra']['keys'])) {
				foreach($_CFG['md_extra']['keys'] as $ex_key=>$ex_caption) {
				$is_required = substr($ex_caption,-1,1) === '*';
				$wr_extra_var = $DOC['wr_extra']['vars'][$ex_key];
		?>
				<div class="mb-2">
					<label for="wrExtraVar_<?php echo $ex_key?>"><?php echo $ex_caption?></label>
					<input type="text" name="wr_extra_var_<?php echo $ex_key?>" class="form-control" id="wrExtraVar_<?php echo $ex_key?>"<?php echo $is_required?' required':''?> maxlength="255" value="<?php echo $is?(empty($wr_extra_var)?'':escapeHtml($wr_extra_var)):''?>">
				</div>
		<?php }} ?>
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
							'typebar'=>array(getLang('content'), $istool),
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
