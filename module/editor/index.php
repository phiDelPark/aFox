<?php
if(!defined('__AFOX__')) exit();

$file_options = (!empty($options['file']) && count($options['file'])==3) ? $options['file'] : false;

$ops = '';
$skip_keys = ['file'=>1,'toolbar'=>1];
foreach ($options as $key => $v) {
	if(!empty($skip_keys[$key])) continue;
	$ops .= $key . ':' . (($v === TRUE || $v === FALSE) ? (int)$v : (is_int($v) || is_float($v) ? $v : '"' . $v . '"')) . ',';
}
?>

<div class="af-editor-group af_editor_<?php echo $name ?>">
<?php if(!empty($options['toolbar'])) { ?>
	<div class="af-editor-toolbar clearfix">
		<strong class="pull-left" style="margin:0 0 5px"><?php echo $options['toolbar'][0]?></strong>
		<div class="pull-right" style="cursor:pointer;color:#aaa;font-size:12px;font-family:Arial;padding:4px 0 0">
		<?php
			$tool_item = '<span tabindex="0" style="margin:0 0 0 5px;padding:3px 2px 0" data-type="%s" data-target="%s" data-value="%s"><i class="glyphicon glyphicon-%s" aria-hidden="true"></i> %s</span>';
			foreach ($options['toolbar'][1] as $key=>$val) {
				$target = $key;
				$default = $val[0];
				$item = $val[1];
				if(is_array($item)) {
					foreach ($item as $k=>$v) echo sprintf($tool_item, $target, $target, $v, $v===$default?'check':'unchecked', $k);
					echo '<input type="hidden" name="'.$target.'" value="'.$default.'">';
				} else { // checkbox
					echo sprintf($tool_item, 'checkbox', $target, '1', $default?'check':'unchecked', $item);
					echo '<input type="hidden" name="'.$target.'" value="'.($default?'1':'').'">';
				}
			}
		?>
		</div>
	</div>
<?php } ?>
	<div class="af-editor-area">
		<textarea name="<?php echo $name ?>" class="form-control vresize"><?php echo $content ?></textarea>
	</div>
	<?php
		if($file_options && $file_options[0] > 0) {
			$file_max = $file_options[0];
			$file_id = $file_options[1];
			$file_target = $file_options[2];
			$fileList = empty($file_id) ? [] : getFileList($file_id, $file_target);

			if(!empty($fileList['data']) && count($fileList['data'])>0) {
				echo '<div class="form-group has-feedback" style="margin-bottom:5px"><div class="af-editor-uploaded-list fileupload-group file-list form-control" style="margin-top:10px">';
				foreach ($fileList['data'] as $val) {
					echo '<i class="file-item" draggable="true" title="'.escapeHtml($val['mf_name']).' ('.shortFileSize( $val['mf_size']).')" data-type="'.$val['mf_type'].'" data-srl="'.$val['mf_srl'].'"></i>';
				}
				echo '</div><span class="glyphicon glyphicon-question-sign form-control-feedback" style="pointer-events:auto;cursor:pointer" tabindex="0"></span></div>';
			}
	?>
		<div class="af-editor-upload-button fileupload-group" placeholder="<?php echo getLang('file')?>" style="margin-top:10px">
			<div class="input-group">
				<div class="file-caption form-control" tabindex="0"></div>
				<div class="btn btn-primary btn-file" tabindex="0">
					<i class="glyphicon glyphicon-folder-open"><?php echo getLang('browse')?>…</i>
					<input name="upload_files[]" type="file" tabindex="-1"<?php echo $file_max > 1 ? ' multiple' : '' ?>>
				</div>
			</div>
		</div>
	<?php } ?>
</div>

<script>
	var AF_EDITOR_<?php echo strtoupper($name) ?>;
	$.getScript(
		"<?php echo _AF_URL_ ?>module/editor/editor.min.js",
		function() {
			var options = {<?php echo substr($ops, 0, -1) ?>}
			AF_EDITOR_<?php echo strtoupper($name) ?> = $(".af_editor_<?php echo $name ?>").afEditor(options);
		}
	);
	$('.af-editor-group .form-control-feedback').popover({
		html:1,
		trigger:'focus',
		placement:'top',
		title:$_LANG['help_editor_attach_title'] || '첨부파일 사용법',
		content:$_LANG['help_editor_attach_content'] || '본문에 파일을 보여주려면 파일 아이콘을 잡고 끌어 본문 위로 옮기면 됩니다.<br><br>클릭시엔 삭제 모드가 토글됩니다.'
	}).on('show.bs.popover',function(){
		$(this).data("bs.popover").tip().css({'max-width':'500px','font-size':'12px'});
	});
</script>
<?php
/* End of file index.php */
/* Location: ./module/editor/index.php */