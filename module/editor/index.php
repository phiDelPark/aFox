<?php
if(!defined('__AFOX__')) exit();

$file_options = (!empty($options['file']) && count($options['file'])==3) ? $options['file'] : false;

$ops = 'name:"'. $name . '",';
$skip_keys = ['file'=>1,'toolbar'=>1,'statebar'=>1];
foreach ($options as $key => $v) {
	if(!empty($skip_keys[$key])) continue;
	$ops .= $key . ':' . (($v === TRUE || $v === FALSE) ? (int)$v : (is_int($v) || is_float($v) ? $v : '"' . $v . '"')) . ',';
}
?>

<svg xmlns="http://www.w3.org/2000/svg" class="d-none">
	<symbol id="bi-check-square" viewBox="0 0 16 16">
		<path d="M3 14.5A1.5 1.5 0 0 1 1.5 13V3A1.5 1.5 0 0 1 3 1.5h8a.5.5 0 0 1 0 1H3a.5.5 0 0 0-.5.5v10a.5.5 0 0 0 .5.5h10a.5.5 0 0 0 .5-.5V8a.5.5 0 0 1 1 0v5a1.5 1.5 0 0 1-1.5 1.5z"/>
		<path d="m8.354 10.354 7-7a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0"/>
	</symbol>
	<symbol id="bi-unchecked-square" viewBox="0 0 16 16">
		<path d="M3 14.5A1.5 1.5 0 0 1 1.5 13V3A1.5 1.5 0 0 1 3 1.5h8a.5.5 0 0 1 0 1H3a.5.5 0 0 0-.5.5v10a.5.5 0 0 0 .5.5h10a.5.5 0 0 0 .5-.5V8a.5.5 0 0 1 1 0v5a1.5 1.5 0 0 1-1.5 1.5z"/>
	</symbol>
</svg>

<div class="w-100 af-editor-group af_editor_<?php echo $name ?>">
<?php if(!empty($options['toolbar'])) { ?>
	<div class="d-flex w-100 justify-content-between"<?php echo $options['readonly']?' readonly':''?> aria-label="Editor Options">
		<b><?php echo $options['toolbar'][0]?></b>
		<div class="user-select-none pt-1" style="cursor:pointer;font-size:12px;font-family:Arial">
		<?php
			$tool_item = '<span class="ms-2" tabindex="0" data-type="%s" data-target="%s" data-value="%s"><svg class="bi" aria-hidden="true"><use xlink:href="#bi-%s-square"/></svg> %s</span>';
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
	<div class="af-editor-content" role="document" aria-label="Editor Content">
		<textarea name="<?php echo $name ?>" class="form-control"<?php echo ($options['placeholder']?' placeholder="'.escapeHtml($options['placeholder']).'"':'').($options['readonly']?' readonly':'') ?>><?php echo escapeHtml($content) ?></textarea>
	</div>
<?php if(!empty($options['statebar'])) { ?>
	<div class="af-statebar-area clearfix" role="toolbar" aria-label="Editor Controls" style="margin-top:3px;height:24px;padding:0 0 0 270px">
		<div class="btn-group btn-group-xs pull-left" style="margin-left:-270px">
			<button type="button" class="btn btn-default" tabindex="-1" aria-label="highlight"><i class="glyphicon glyphicon-text-background" aria-hidden="true" style="text-decoration:underline"></i></button>
			<button type="button" class="btn btn-default" tabindex="-1" aria-label="bold"><i class="glyphicon glyphicon-bold" aria-hidden="true"></i></button>
			<button type="button" class="btn btn-default" tabindex="-1" aria-label="italic"><i class="glyphicon glyphicon-italic" aria-hidden="true"></i></button>
			<button type="button" class="btn btn-default" tabindex="-1" aria-label="underline"><strong style="font-family:serif;font-size:15px;width:12px;height:12px;line-height:1;text-decoration:underline">U</strong></button>
			<button type="button" class="btn btn-default" tabindex="-1" aria-label="strikeThrough"><strong style="font-family:serif;font-size:15px;width:12px;height:12px;line-height:1;text-decoration:line-through">S</strong></button>
		</div>
		<div class="btn-group btn-group-xs pull-left" style="margin-left:-152px">
			<button type="button" class="btn btn-default" tabindex="-1" aria-label="header"><i class="glyphicon glyphicon-header" aria-hidden="true"></i></button>
			<button type="button" class="btn btn-default" tabindex="-1" aria-label="insertorderedlist"><i class="glyphicon glyphicon-list" aria-hidden="true"></i></button>
			<button type="button" class="btn btn-default" tabindex="-1" aria-label="indent"><i class="glyphicon glyphicon-indent-left" aria-hidden="true"></i></button>
			<button type="button" class="btn btn-default" tabindex="-1" aria-label="codeblock"><i class="glyphicon glyphicon-list-alt" aria-hidden="true"></i></button>
		</div>
		<div class="btn-group btn-group-xs pull-left" style="margin-left:-53px">
			<a type="button" class="btn btn-default" tabindex="-1" aria-label="link" data-toggle="popover" data-original-title="" title=""><i class="glyphicon glyphicon-link" aria-hidden="true"></i></a>
			<a type="button" class="btn btn-default" tabindex="-1" aria-label="components" data-toggle="popover" data-original-title="" title=""><i class="glyphicon glyphicon-leaf" aria-hidden="true"></i></a>
		</div>
		<div class="form-control" style="cursor:help;overflow:hidden;white-space:nowrap;color:#aaa;font-size:12px;font-family:Arial;width:100%;height:22px;padding:2px 5px;text-align:right;margin:0 -267px 0 0">
			<strong>AFoX</strong>
		</div>
	</div>
	<script>
		<?php
			echo 'var AF_EDITOR_COMPONENTS = [';
			$components = get_cache('_AF_EDITOR_COMPONENTS');
			if(is_null($components)){ //에디터 컴포넌트 목록 캐시 생성
				$components = DB::gets(_AF_ADDON_TABLE_, ['use_editor'=>'1'], [],
					function($r){
						$rset = [];
						$_ADDON_INFO = [];
						while ($row = DB::fetch($r)){
							$tmp = _AF_ADDONS_PATH_ . $row['ao_id'] . '/info.php';
							if(file_exists($tmp)){
								include $tmp;
								$rset[] = [0=>$row['ao_id'],1=>$_ADDON_INFO['title']];
							}
						}
						return $rset;
					}
				); set_cache('_AF_EDITOR_COMPONENTS', $components);
			}
			$comma = '';
			foreach($components as $v){
				echo $comma . '["' . $v[0] . '","' . str_replace(['[',']','"'], ['{','}','`'], $v[1]) . '"]';
				$comma = ',';
			} echo '];';
		?>
	</script>
<?php } ?>
	<?php
		if($file_options && $file_options[0] > 0) {
			$file_max = $file_options[0];
			$file_id = $file_options[1];
			$file_target = $file_options[2];
			$fileList = empty($file_id) ? [] : getFileList($file_id, $file_target);

			if(!empty($fileList) && count($fileList)>0) {
				echo '<div class="form-group has-feedback" style="margin-bottom:5px"><div class="af-editor-uploaded uploader-group file-list form-control" style="margin-top:10px;height:auto;min-height:34px">';
				foreach ($fileList as $val) {
					echo '<i class="file-item" draggable="true" title="'.escapeHtml($val['mf_name']).' ('.shortSize( $val['mf_size']).')" data-type="'.$val['mf_type'].'" data-srl="'.$val['mf_srl'].'"></i>';
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
		"<?php echo _AF_URL_ ?>module/editor/editor.<?php echo (__DEBUG__ ? 'js?' . _AF_SERVER_TIME_ : 'min.js') ?>",
		function() {
			var options = {<?php echo substr($ops, 0, -1) ?>}
			AF_EDITOR_<?php echo strtoupper($name) ?> = $(".af_editor_<?php echo $name ?>").afEditor(options);
		}
	);
</script>
<?php
/* End of file index.php */
/* Location: ./module/editor/index.php */
