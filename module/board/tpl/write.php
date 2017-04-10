<?php
	if(!defined('__AFOX__')) exit();
	$is = !empty($_{'board'})&&!empty($_{'board'}['wr_srl']);
	$is_manager = isManager($_DATA['id']);
?>

<section id="board_write">
	<header>
		<h3 class="clearfix">
			<span class="pull-left"><i class="fa fa-pencil<?php echo getLang($is?'-square-o':'')?>" aria-hidden="true"></i> <?php echo getLang($is?'edit':'write')?></span>
			<a class="close" href="<?php echo getUrl('disp','')?>"><span aria-hidden="true">×</span></a>
		</h3>
		<hr class="divider">
	</header>

	<article class="document-editer">
	<form onsubmit="return false" method="post" autocomplete="off" enctype="multipart/form-data" data-exec-ajax="board.updateDocument">
	<input type="hidden" name="success_return_url" value="<?php echo getUrl('disp','','rp','')?>">
	<input type="hidden" name="md_id" value="<?php echo $_DATA['id']?>">
	<input type="hidden" name="wr_srl" value="<?php echo $is?$_{'board'}['wr_srl']:''?>">

		<div>
		<?php if (empty($_MEMBER) || (!empty($_{'board'}['wr_srl']) && $_MEMBER['mb_srl'] !== $_{'board'}['mb_srl'])) { ?>
			<div class="form-group">
				<label for="id_mb_nick"><?php echo getLang('id')?></label>
				<input type="text" name="mb_nick" class="form-control" id="id_mb_nick" required maxlength="20" value="<?php echo $is?escapeHtml($_{'board'}['mb_nick']):''?>"<?php echo empty($_{'board'}['wr_srl'])?'':' disabled'?>>
			</div>
			<?php if (empty($_{'board'}['mb_srl'])) { ?>
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
						echo '<option value="'.$val.'"'.(($is&&$val==$_{'board'}['wr_category'])?' selected="selected"':'').'>'.$val.'</option>';
					}
				?>
				</select>
			</div>
		<?php } ?>
			<div class="form-group">
				<label for="id_wr_title"><?php echo getLang('title')?></label>
				<input type="text" name="wr_title" class="form-control" id="id_wr_title" required maxlength="255" value="<?php echo $is?escapeHtml($_{'board'}['wr_title']):''?>">
			</div>
			<div class="form-group">
				<?php
					$issecret = ($is&&$_{'board'}['wr_secret']==1)?1:0;
					$ishtml = ($is&&$_{'board'}['wr_type']==2)?1:0;
					$istool = [];
					if(empty($_CFG['use_type'])) $istool['wr_type'] = [$ishtml?'2':'1', ['MKDW'=>'1','HTML'=>'2']];
					if(empty($_CFG['use_secret'])) $istool['wr_secret'] = [$issecret,'Secret'];
					dispEditor(
						'wr_content', $is?$_{'board'}['wr_content']:'',
						[
							'file'=>array($_CFG['md_file_max'], $_DATA['id'], $is?$_{'board'}['wr_srl']:0),
							'required'=>getLang('request_input',['content']),
							'html'=>$ishtml,
							'toolbar'=>array(getLang('content'), $istool),
							'statebar'=>true
						]
					);
				?>
			</div>

			<div class="area-button">
				<button type="submit" class="btn btn-success btn-block"><i class="fa fa-check" aria-hidden="true"></i> <?php echo getLang('save')?></button>
			</div>
		</div>
	</form>
	</article>
</section>

<?php
/* End of file bo_write.php */
/* Location: ./theme/default/bo_write.php */