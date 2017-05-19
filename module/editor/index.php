<?php
if(!defined('__AFOX__')) exit();

$file_options = (!empty($options['file']) && count($options['file'])==3) ? $options['file'] : false;

$ops = '';
$skip_keys = ['file'=>1,'toolbar'=>1,'statebar'=>1];
foreach ($options as $key => $v) {
	if(!empty($skip_keys[$key])) continue;
	$ops .= $key . ':' . (($v === TRUE || $v === FALSE) ? (int)$v : (is_int($v) || is_float($v) ? $v : '"' . $v . '"')) . ',';
}
?>

<div class="af-editor-group af_editor_<?php echo $name ?>">
<?php if(!empty($options['toolbar'])) { ?>
	<div class="af-editor-toolbar clearfix"<?php echo $options['readonly']?' readonly':''?>>
		<strong class="pull-left" style="margin:0 0 5px"><?php echo $options['toolbar'][0]?></strong>
		<div class="pull-right unselectable" style="cursor:pointer;color:#aaa;font-size:12px;font-family:Arial;padding:4px 0 0">
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
	<div class="af-editor-content">
		<textarea name="<?php echo $name ?>" class="form-control vresize"<?php echo ($options['placeholder']?' placeholder="'.escapeHtml($options['placeholder']).'"':'').($options['readonly']?' readonly':'') ?>><?php echo $content ?></textarea>
	</div>
<?php if(!empty($options['statebar'])) { ?>
	<div class="af-statebar-area clearfix" style="margin-top:3px;height:24px;padding:0 0 0 200px">
		<div class="btn-group btn-group-xs pull-left" role="group" aria-label="..." style="margin-left:-200px">
			<button type="button" class="btn btn-default" tabindex="-1" data-type="bold"><i class="glyphicon glyphicon-bold" aria-hidden="true"></i></button>
			<button type="button" class="btn btn-default" tabindex="-1" data-type="italic"><i class="glyphicon glyphicon-italic" aria-hidden="true"></i></button>
			<button type="button" class="btn btn-default" tabindex="-1" data-type="header"><i class="glyphicon glyphicon-header" aria-hidden="true"></i></button>
			<button type="button" class="btn btn-default" tabindex="-1" data-type="insertorderedlist"><i class="glyphicon glyphicon-list" aria-hidden="true"></i></button>
			<button type="button" class="btn btn-default" tabindex="-1" data-type="indent"><i class="glyphicon glyphicon-indent-left" aria-hidden="true"></i></button>
			<button type="button" class="btn btn-default" tabindex="-1" data-type="codeblock"><i class="glyphicon glyphicon-list-alt" aria-hidden="true"></i></button>
		</div>
		<div class="btn-group btn-group-xs pull-left" role="group" aria-label="..." style="margin-left:-54px">
			<button type="button" class="btn btn-default" tabindex="-1" data-type="link" data-toggle="popover"><i class="glyphicon glyphicon-link" aria-hidden="true"></i></button>
			<button type="button" class="btn btn-default" tabindex="-1" data-type="video" data-toggle="popover"><i class="glyphicon glyphicon-facetime-video" aria-hidden="true"></i></button>
		</div>
		<div class="form-control" style="cursor:help;overflow:hidden;white-space:nowrap;color:#aaa;font-size:12px;font-family:Arial;width:100%;height:22px;padding:2px 5px;text-align:right;margin:0 -150px 0 0">
			<strong>aFox</strong>
		</div>
	</div>
<?php } ?>
	<?php
		if($file_options && $file_options[0] > 0) {
			$file_max = $file_options[0];
			$file_id = $file_options[1];
			$file_target = $file_options[2];
			$fileList = empty($file_id) ? [] : getFileList($file_id, $file_target);

			if(!empty($fileList['data']) && count($fileList['data'])>0) {
				echo '<div class="form-group has-feedback" style="margin-bottom:5px"><div class="af-editor-uploaded uploader-group file-list form-control" style="margin-top:10px;height:auto;min-height:34px">';
				foreach ($fileList['data'] as $val) {
					echo '<i class="file-item" draggable="true" title="'.escapeHtml($val['mf_name']).' ('.shortFileSize( $val['mf_size']).')" data-type="'.$val['mf_type'].'" data-srl="'.$val['mf_srl'].'"></i>';
				}
				echo '</div><span class="glyphicon glyphicon-question-sign form-control-feedback" style="pointer-events:auto;cursor:pointer" tabindex="0"></span></div>';
			}
	?>
		<div class="af-editor-uploader uploader-group" placeholder="<?php echo getLang('file')?>" style="margin-top:10px">
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
</script>
<?php
/* End of file index.php */
/* Location: ./module/editor/index.php */