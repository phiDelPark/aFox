<?php
	if(!defined('__AFOX__')) exit();
	$is_manager = isManager(__MID__);
	$is = !empty($DOC)&&!empty($DOC['wr_srl']);
	$modal_image_view = $use_style=='gallery'&&!empty($CONFIGS['modal_image_view']);
?>

<section id="bdWrite">
	<header>
		<h3 class="clearfix">
			<span class="pull-left"><i class="glyphicon glyphicon-<?php echo $is?'edit':'pencil'?>" aria-hidden="true"></i> <?php echo getLang($is?'edit':'write')?></span>
			<a class="close" href="<?php echo getUrl('disp','','srl',(!$is||$modal_image_view?'':$DOC['wr_srl']))?>"><span aria-hidden="true">×</span></a>
		</h3>
		<hr class="divider">
	</header>

	<article class="document-editer">
	<form onsubmit="return false" method="post" autocomplete="off" enctype="multipart/form-data" data-exec-ajax="board.updateDocument" data-except="<?php echo $modal_image_view?'wr_srl':'' ?>">
	<input type="hidden" name="success_return_url" value="<?php echo $is?getUrl('disp','','cpage','','rp',''):getUrl('','id',__MID__)?>">
	<input type="hidden" name="md_id" value="<?php echo __MID__?>">
	<input type="hidden" name="wr_srl" value="<?php echo $is?$DOC['wr_srl']:''?>">

		<div>
		<?php if (empty($_MEMBER) || (!empty($DOC['wr_srl']) && $_MEMBER['mb_srl'] !== $DOC['mb_srl'])) { ?>
			<div class="form-group">
				<label for="id_mb_nick"><?php echo getLang('id')?></label>
				<input type="text" name="mb_nick" class="form-control" id="id_mb_nick" required maxlength="20" value="<?php echo $is?escapeHtml($DOC['mb_nick']):''?>"<?php echo empty($DOC['wr_srl'])?'':' readonly'?>>
			</div>
			<?php if (!$is_manager && empty($DOC['mb_srl'])) { ?>
				<div class="form-group">
					<label for="id_mb_password"><?php echo getLang('password')?></label>
					<input type="password" name="mb_password" class="form-control" id="id_mb_password" required>
				</div>
			<?php } ?>
		<?php } ?>
		<?php if (!empty($_CFG['md_category'])) { ?>
			<div class="form-group">
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
			<div class="form-group">
				<label for="id_wr_title"><?php echo getLang('title')?></label>
				<input type="text" name="wr_title" class="form-control" id="id_wr_title" required maxlength="255" value="<?php echo $is?escapeHtml($DOC['wr_title']):''?>">
			</div>
		<?php
			if (!empty($_CFG['md_extra']['keys'])) {
				foreach($_CFG['md_extra']['keys'] as $ex_key=>$ex_caption) {
				$is_required = substr($ex_caption,-1,1) === '*';
				$wr_extra_var = $DOC['wr_extra']['vars'][$ex_key];
		?>
				<div class="form-group">
					<label for="id_wr_extra_var_<?php echo $ex_key?>"><?php echo $ex_caption?></label>
					<input type="text" name="wr_extra_var_<?php echo $ex_key?>" class="form-control" id="id_wr_extra_var_<?php echo $ex_key?>"<?php echo $is_required?' required':''?> maxlength="255" value="<?php echo $is?(empty($wr_extra_var)?'':escapeHtml($wr_extra_var)):''?>">
				</div>
		<?php }} ?>
			<div class="form-group">
				<?php
					$issecret = ($is&&$DOC['wr_secret']=='1')?1:0;
					$ishtml = ($is&&$DOC['wr_type']=='2')||(!$is&&($_CFG['use_type']=='3'||$_CFG['use_type']=='9'))?1:0;
					$istool = [];
					if(empty($_CFG['use_type']) || $_CFG['use_type'] > 6) $istool['wr_type'] = [$ishtml?'2':'1', ['MKDW'=>'1','HTML'=>'2']];
					if(empty($_CFG['use_secret'])) $istool['wr_secret'] = [$issecret,'Secret'];
					displayEditor(
						'wr_content', $is?$DOC['wr_content']:'',
						[
							'file'=>array($_CFG['md_file_max'], __MID__, $is?$DOC['wr_srl']:0),
							'required'=>getLang('request_input',['content']),
							'html'=>$ishtml,
							'toolbar'=>array(getLang('content'), $istool),
							'statebar'=>true
						]
					);
				?>
			</div>

			<div class="area-button row">
				<?php if($is&&$modal_image_view){ ?>
				<button type="button" class="btn btn-warning col-xs-3" data-exec-ajax="board.deleteDocument" data-ajax-param="wr_srl,<?php echo $DOC['wr_srl']?>,success_return_url,<?php echo urlencode(getUrl('disp','','cpage','','rp',''))?>" data-ajax-confirm="<?php echo getLang('confirm_delete',['document'])?>"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i> <?php echo getLang('delete')?></button>
				<?php } ?>
				<button type="submit" class="btn btn-success <?php echo $is&&$modal_image_view?'col-xs-9':'btn-block'?>"><i class="glyphicon glyphicon-ok" aria-hidden="true"></i> <?php echo getLang('save')?></button>
			</div>
		</div>
	</form>
	</article>
</section>

<?php
/* End of file bo_write.php */
/* Location: ./theme/default/bo_write.php */
