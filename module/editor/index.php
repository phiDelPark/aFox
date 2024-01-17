<?php
if(!defined('__AFOX__')) exit();

$file_options = (!empty($options['file']) && count($options['file'])==3) ? $options['file'] : false;
$min_height = empty($options['height']) ? '250px' : $options['height'];
$ops = 'name:"'. $name . '",';
$skip_keys = ['height'=>1,'file'=>1,'typebar'=>1,'toolbar'=>1,'statebar'=>1];
foreach ($options as $key => $v) {
	if(!empty($skip_keys[$key])) continue;
	$ops .= $key . ':' . (($v === TRUE || $v === FALSE) ? (int)$v : (is_int($v) || is_float($v) ? $v : '"' . $v . '"')) . ',';
}
?>

<style>
[name="remove_files[]"]+img{outline:0;box-shadow:var(--bs-focus-ring-x,0) var(--bs-focus-ring-y,0) var(--bs-focus-ring-blur,0) var(--bs-focus-ring-width) var(--bs-danger)}
#editorContent iframe{outline:0;box-shadow:var(--bs-focus-ring-x,0) var(--bs-focus-ring-y,0) var(--bs-focus-ring-blur,0) var(--bs-focus-ring-width) var(--bs-focus-ring-color)}
#editorTypebar .bi-unchecked::before{padding-right:5px;vertical-align:-.27em;content:url(./module/editor/bi-uncheck.svg);-webkit-filter:invert(50%);filter:invert(50%)}
#editorTypebar .bi-unchecked.checked::before{content:url(./module/editor/bi-check.svg)}
#editorTypebar,#editorToolbar{font-size:12px;font-family:Arial}
#editorToolbar button{padding:2px;border-radius:2px;height:18px;width:18px}
#editorToolbar button>.bi{position:relative;left:-2px;top:-5px;height:16px;width:16px}
#uploadFiles img,#uploadedFiles img{height:24px;width:24px;margin-right:6px}
#editorContent textarea{min-height:<?php echo $min_height ?>}
</style>

<div id="editor<?php echo ucfirst($name) ?>" class="w-100 editor-group">
<?php if(!empty($options['typebar'])) { ?>
	<div class="d-flex w-100 justify-content-between"<?php echo $options['readonly']?' readonly':''?> aria-label="Editor Options">
		<label><?php echo $options['typebar'][0]?></label>
		<div id="editorTypebar" class="user-select-none pt-1">
		<?php
			$typebar_item = '<span style="cursor:pointer" class="bi-unchecked%s ms-2" tabindex="0" data-target="%s" data-value="%s">%s</span>';
			foreach ($options['typebar'][1] as $key=>$val) {
				$target = $key;
				$default = $val[0];
				$item = is_array($val[1]) ? $val[1] : [$val[1]];
				foreach ($item as $k=>$v) echo sprintf($typebar_item, $v===$default?' checked':'', $target, $v, $k);
				echo '<input type="hidden" name="'.$target.'" value="'.$default.'">';
			}
		?>
		</div>
	</div>
<?php } ?>
	<div id="editorContent" role="document" aria-label="Editor Content">
		<textarea name="<?php echo $name ?>" class="form-control" <?php echo ($options['placeholder']?' placeholder="'.escapeHtml($options['placeholder']).'"':'').($options['readonly']?' readonly':'') ?>><?php echo escapeHtml($content) ?></textarea>
	</div>
<?php if(!empty($options['toolbar'])) { ?>
	<div id="editorToolbar" class="d-flex w-100 justify-content-between border-bottom py-1" role="toolbar" aria-label="Editor Controls">
		<div>
			<button type="button" class="btn btn-outline-secondary" tabindex="-1" aria-label="highlight"><svg class="bi"><use href="<?php echo _AF_URL_ ?>module/editor/bi-icons.svg#stripe"/></svg></button>
			<button type="button" class="btn btn-outline-secondary" tabindex="-1" aria-label="bold"><svg class="bi"><use href="<?php echo _AF_URL_ ?>module/editor/bi-icons.svg#type-bold"/></svg></button>
			<button type="button" class="btn btn-outline-secondary" tabindex="-1" aria-label="italic"><svg class="bi"><use href="<?php echo _AF_URL_ ?>module/editor/bi-icons.svg#type-italic"/></svg></button>
			<button type="button" class="btn btn-outline-secondary" tabindex="-1" aria-label="underline"><svg class="bi"><use href="<?php echo _AF_URL_ ?>module/editor/bi-icons.svg#type-underline"/></svg></button>
			<button type="button" class="btn btn-outline-secondary me-2" tabindex="-1" aria-label="strikeThrough"><svg class="bi"><use href="<?php echo _AF_URL_ ?>module/editor/bi-icons.svg#type-strikethrough"/></svg></button>
			<button type="button" class="btn btn-outline-secondary" tabindex="-1" aria-label="header"><svg class="bi"><use href="<?php echo _AF_URL_ ?>module/editor/bi-icons.svg#head"/></svg></button>
			<button type="button" class="btn btn-outline-secondary" tabindex="-1" aria-label="insertorderedlist"><svg class="bi"><use href="<?php echo _AF_URL_ ?>module/editor/bi-icons.svg#list-ol"/></svg></button>
			<button type="button" class="btn btn-outline-secondary" tabindex="-1" aria-label="indent"><svg class="bi"><use href="<?php echo _AF_URL_ ?>module/editor/bi-icons.svg#blockquote-left"/></svg></button>
			<button type="button" class="btn btn-outline-secondary me-2" tabindex="-1" aria-label="codeblock"><svg class="bi"><use href="<?php echo _AF_URL_ ?>module/editor/bi-icons.svg#code-slash"/></svg></button>
			<button type="button" class="btn btn-outline-secondary" tabindex="-1" aria-label="components"><svg class="bi"><use href="<?php echo _AF_URL_ ?>module/editor/bi-icons.svg#three-dots"/></svg></button>
		</div>
		<div>
		<abbr class="initialism" title="attribute">AFoX</abbr>
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
?>
	<div class="mt-4 d-grid gap-2">
<?php if(!empty($fileList) && count($fileList)>0) { ?>
		<div id="uploadedFiles" class="user-select-none input-group text-secondary border rounded p-2">
<?php
	foreach ($fileList as $val) {
		$es_name = escapeHtml($val['mf_name']);
		echo sprintf(
			substr($val['mf_type'], 0, 5) == 'image'
			? '<img src="%s" class="%s" title="%s" alt="%s">'
			: '<img src="%s" class="%s" title="%s" alt="%s" srcset="./module/editor/bi-binary.svg">',
			_AF_URL_.'?file='.$val['mf_srl'],
			escapeHtml($val['mf_type']),
			$es_name.' ('.shortSize( $val['mf_size']). ')',
			$es_name
		);
	}
?>
		</div>
<?php } ?>
		<div id="uploadFiles" class="user-select-none input-group text-secondary border rounded p-2">
		<small class="ms-1">#### 본문 첨부는 아이콘을 잡고 끌어 옮기시면 됩니다.</small>
		</div>
		<input class="form-control" name="upload_files[]" type="file" tabindex="-1"<?php echo $file_max > 1 ? ' multiple' : '' ?>>
	</div>
<?php } ?>
</div>

<script>
	let AF_EDITOR_<?php echo strtoupper($name) ?>;
	load_script("<?php echo _AF_URL_ ?>module/editor/editor.<?php echo (__DEBUG__ ? 'js?' . _AF_SERVER_TIME_ : 'min.js') ?>")
	.then(() => {
			AF_EDITOR_<?php echo strtoupper($name) ?> =new afEditor("editor<?php echo ucfirst($name) ?>", {<?php echo substr($ops, 0, -1) ?>});
		}, () => { console.log('fail to load script'); }
	);
</script>
<?php
/* End of file index.php */
/* Location: ./module/editor/index.php */
