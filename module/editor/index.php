<?php
if(!defined('__AFOX__')) exit();
$_EDITOR['height'] = empty($_EDITOR['height']) ? '280px' : $_EDITOR['height'];
$_EDITOR['typebar'] = empty($_EDITOR['typebar']) ? false : $_EDITOR['typebar'];
$_EDITOR['toolbar'] = empty($_EDITOR['toolbar']) ? true : $_EDITOR['toolbar'];
$_EDITOR['required'] = empty($_EDITOR['required']) ? false : $_EDITOR['required'];
$_EDITOR['readonly'] = empty($_EDITOR['readonly']) ? false : $_EDITOR['readonly'];
$_EDITOR['placeholder'] = empty($_EDITOR['placeholder']) ? '' : $_EDITOR['placeholder'];
?>
<style>
#editorTypebar .bi-unchecked::before{padding-right:5px;vertical-align:-.27em;content:url(./module/editor/bi-uncheck.svg);filter:invert(50%)}
#editorTypebar .bi-unchecked.checked::before{content:url(./module/editor/bi-check.svg)}
#editorTypebar,#editorToolbar{font-size:12px;font-family:Arial}
#editorToolbar button{padding:2px;border-radius:2px;height:18px;width:18px}
#editorToolbar button>.bi{position:relative;left:-2px;top:-3px;height:16px;width:16px;vertical-align:baseline}
#uploadFiles img,#uploadedFiles img{height:24px;width:24px;margin-right:6px;background-color:var(--bs-border-color)!important}
#editorContent .focused{outline:0;box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.3)}
#editorContent .is-invalid,[name="remove_files[]"]+img{outline:0;box-shadow:0 0 0 0.25rem rgba(253, 13, 13, 0.3)}
#editorContent textarea,#editorContent iframe{min-height:<?php echo $_EDITOR['height'] ?>}
</style>

<div id="editor<?php echo ucfirst($_ID) ?>" class="w-100 editor-group">
<?php if($_EDITOR['typebar']) { ?>
	<div class="d-flex w-100 justify-content-between"<?php echo $_EDITOR['readonly']?' readonly':''?> aria-label="Editor Options">
		<label><?php echo $_EDITOR['typebar'][0]?></label>
		<div id="editorTypebar" class="user-select-none pt-1">
		<?php
			$typebar_item = '<span style="cursor:pointer" class="bi-unchecked%s ms-2" tabindex="0" data-target="%s" data-value="%s">%s</span>';
			foreach ($_EDITOR['typebar'][1] as $key=>$val) {
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
		<textarea name="<?php echo $_ID ?>" class="form-control" style="height:<?php echo $_EDITOR['height'] ?>" <?php echo ($_EDITOR['placeholder']?' placeholder="'.escapeHTML($_EDITOR['placeholder']).'"':'').($_EDITOR['readonly']?' readonly':'').($_EDITOR['required']?' required':'') ?>>
			<?php echo escapeHTML($_CONTENT) ?>
		</textarea>
	</div>
<?php if($_EDITOR['toolbar']) {
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
?>
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
			<button type="button" class="btn btn-outline-secondary" tabindex="-1" aria-label="components" aria-expanded="false"	data-bs-toggle="dropdown"><svg class="bi"><use href="<?php echo _AF_URL_ ?>module/editor/bi-icons.svg#three-dots"/></svg></button>
			<ul class="dropdown-menu dropdown-menu-end shadow">
<?php
	foreach($components as $v){
		echo '<li class="dropdown-item" style="cursor:pointer" onclick="pop_win(\''._AF_URL_.'module/editor/component.php?n='.$v[0].'&k='.$_ID.'\', null, null, \'afox_editor_components\')">'.$v[1].'</li>';
	}
?>
			</ul>
		</div>
		<div>
		<abbr title="attribute" style="font-size:.6rem;vertical-align:super">AFoX</abbr>
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
	if(!empty($_EDITOR['file']) && $_EDITOR['file'][2] > 0) {
		$file_id = $_EDITOR['file'][0];
		$file_target = $_EDITOR['file'][1];
		$file_max = empty($_EDITOR['file'][2]) ? 0 : $_EDITOR['file'][2];
		$file_accept = empty($_EDITOR['file'][3]) ? '' : $_EDITOR['file'][3];
		$fileList = empty($file_id) ? [] : getFileList($file_id, $file_target);
?>
	<div class="mt-4 d-grid gap-2">
<?php if(!empty($fileList) && count($fileList)>0) { ?>
		<div id="uploadedFiles" class="user-select-none input-group text-secondary border rounded p-2">
<?php
	foreach ($fileList as $val) {
		$es_name = escapeHTML($val['mf_name']);
		echo sprintf(
			substr($val['mf_type'], 0, 5) == 'image'
			? '<img src="%s" class="%s" title="%s" alt="%s">'
			: '<img src="%s" class="%s" title="%s" alt="%s" srcset="./module/editor/bi-binary.svg">',
			'./?file='.$val['mf_srl'],
			escapeHTML($val['mf_type']),
			$es_name.' ('.shortFileSize( $val['mf_size']). ')',
			$es_name
		);
	}
?>
		</div>
<?php } ?>
		<div id="uploadFiles" class="user-select-none input-group text-secondary border rounded p-2">
		<small class="ms-1"><?php echo $_EDITOR['readonly']?'# 첨부된 파일이 없습니다.':'# 본문 첨부는 아이콘을 잡고 끌어 옮기시면 됩니다.' ?></small>
		</div>
		<input class="form-control" name="upload_files[]" type="file"<?php echo $file_accept ? ' accept="'.$file_accept.'"' : ''?> tabindex="-1"<?php echo $file_max > 1 ? ' multiple' : ''?>>
	</div>
<?php } ?>
</div>

<script>
	load_script("<?php echo _AF_URL_?>module/editor/editor.<?php echo (__DEBUG__ ? 'js?' . _AF_SERVER_TIME_ : 'min.js')?>")
		.then(
			() => { window.AFOX_EDITOR_<?php echo strtoupper($_ID)?> =new afoxEditor("<?php echo $_ID?>", <?php echo json_encode($_EDITOR)?>) },
			() => { console.log('fail to load script') }
		);
</script>
<?php
/* End of file index.php */
/* Location: ./module/editor/index.php */
