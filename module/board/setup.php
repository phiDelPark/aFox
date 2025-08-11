<?php if(!defined('__AFOX__')) exit();

$BOARD = [];
if($is_new = (empty($_GET['bo_id']) || $_GET['bo_id'] === '@new')){
	$r = DB::query('SHOW FULL COLUMNS FROM '._AF_MODULE_TABLE_, [], true);
	foreach($r as $v) $BOARD[$v['Field']] = $v['Default'];
} else{
	$BOARD = DB::get(_AF_MODULE_TABLE_, '*', ['md_id'=>$_GET['bo_id']]);
	if(empty($BOARD['md_id'])) {
		messageBox(getLang('error_founded'), 4201);
		return;
	} else if(!isGrant('view', $BOARD['md_id'])) {
		messageBox(getLang('error_permitted'), 4501);
		return;
	}
	// 확장 변수가 있으면 unserialize
	if(!empty($BOARD['md_extra']) && !is_array($BOARD['md_extra'])) {
		$BOARD['md_extra'] = unserialize($BOARD['md_extra']);
		$md_extra_keys = empty($BOARD['md_extra']['keys'])?'':$BOARD['md_extra']['keys'];
	}
}
?>

<?php if(!$is_new){?>
<form method="post" autocomplete="off" enctype="multipart/form-data" onsubmit="return validateForm(this)">
	<input type="hidden" name="success_url" value="<?php echo getUrl('bo_id', '')?>" />
	<input type="hidden" name="module" value="board" />
	<input type="hidden" name="act" value="deleteBoard" />
	<input type="hidden" name="md_id" value="" />
	<button type="submit" class="btn btn-sm btn-danger float-end"><?php echo getLang('permanent_delete')?></button>
</form>
<script>
function validateForm(f) {
	var return_value = prompt('<?php echo getLang('confirm_delete',['board'])?>', 'Board ID?');
	if (return_value === '<?php echo $BOARD['md_id']?>') {f.md_id.value = return_value; return true;} else return false;
}
</script>
<?php }?>

<form id="setup" method="post" autocomplete="off">
	<input type="hidden" name="success_url" value="<?php echo getUrl('bo_id', '')?>" />
	<input type="hidden" name="module" value="board" />
	<input type="hidden" name="act" value="updateBoard" />
	<input type="hidden" name="md_id" value="<?php echo $is_new?'':$BOARD['md_id'] ?>" />

	<div class="mb-4 float-start">
		<div class="d-flex flex-row">
			<div class="input-group">
				<label class="input-group-text w-100p" for="id_new_md_id"><?php echo getLang('id')?></label>
				<input type="text" value="<?php echo $is_new?'" name="new_md_id':$BOARD['md_id'] ?>" class="form-control mw-150p" id="id_new_md_id" required maxlength="11" pattern="<?php echo _AF_PATTERN_ID_?>"<?php echo $is_new?'':' disabled'?>>
			</div>
			<div class="input-group ms-2">
				<input type="text" name="md_manager" class="form-control mw-100p" id="id_md_manager" maxlength="11" pattern="<?php echo _AF_PATTERN_ID_?>" placeholder="<?php echo getLang('manager')?> ID">
			</div>
		</div>
		<div class="form-text"><?php echo getLang('desc_mb_id')?></div>
	</div>

	<div class="input-group mb-2 clearfix">
		<label class="input-group-text w-100p" for="mdTitle"><?php echo getLang('title')?></label>
		<input type="text" name="md_title" class="form-control" id="mdTitle" maxlength="255" value="<?php echo $BOARD['md_title'] ?>">
	</div>

	<div class="input-group mb-4">
		<label class="input-group-text w-100p" for="mdAbout"><?php echo getLang('info')?></label>
		<input type="text" name="md_about" class="form-control" id="mdAbout" maxlength="255" value="<?php echo $BOARD['md_about'] ?>">
	</div>

	<div class="mb-4">
		<label class="form-label" for="mdCategory"><?php echo getLang('category')?></label>
		<div class="input-group">
			<input type="text" name="md_category" class="form-control" id="mdCategory" maxlength="255" pattern="<?php echo str_replace(array('{','}'),'',_AF_PATTERN_CATEGORY_)?>" value="<?php echo $BOARD['md_category'] ?>">
		</div>
		<div class="form-text"><?php echo str_replace('\n','<br>',getLang('desc_category'))?></div>
	</div>

	<div class="mb-4">
		<label class="form-label" for="mdExtraKeys"><?php echo getLang('extra_keys')?></label>
		<div class="input-group">
		<input type="text" name="md_extra_keys" class="form-control" id="mdExtraKeys" maxlength="255" pattern="<?php echo str_replace(array('{','}'),'',_AF_PATTERN_EXTRAKEY_)?>" value="<?php echo empty($BOARD['md_extra']['keys'])?'':implode(',', $BOARD['md_extra']['keys']) ?>">
		</div>
		<div class="form-text"><?php echo str_replace('\n','<br>',getLang('desc_extra_keys'))?></div>
	</div>

	<div class="mb-2">
		<span class="form-label"><?php echo getLang('style')?></span>
		<div>
			<input type="radio" class="btn-check" name="use_style" id="useStyle1" autocomplete="off" value="0"<?php echo $BOARD['use_style']==='0'?' checked':'' ?>>
			<label class="btn btn-xs btn-outline-primary w-100p" for="useStyle1"><?php echo getLang('Default')?></label>
			<input type="radio" class="btn-check" name="use_style" id="useStyle2" autocomplete="off" value="1"<?php echo $BOARD['use_style']==='1'?' checked':'' ?>>
			<label class="btn btn-xs btn-outline-primary w-100p" for="useStyle2"><?php echo getLang('Review')?></label>
			<input type="radio" class="btn-check" name="use_style" id="useStyle3" autocomplete="off" value="2"<?php echo $BOARD['use_style']==='2'?' checked':'' ?>>
			<label class="btn btn-xs btn-outline-primary w-100p" for="useStyle3"><?php echo getLang('Gallery')?></label>
			<input type="radio" class="btn-check" name="use_style" id="useStyle4" autocomplete="off" value="3"<?php echo $BOARD['use_style']==='3'?' checked':'' ?>>
			<label class="btn btn-xs btn-outline-primary w-100p" for="useStyle4"><?php echo getLang('Timeline')?></label>
		</div>
	</div>

	<div class="mb-2">
		<span class="form-label d-inline-block my-0" style="width:100px"><?php echo getLang('type')?></span>
		<span style="margin-left:1px"><input class="form-check-input" type="radio" name="use_default_type" value="7"<?php echo (!$BOARD['use_type']||$BOARD['use_type']==='7')?' checked':'' ?>></span>
		<span style="margin-left:85px"><input class="form-check-input" type="radio" name="use_default_type" value="8"<?php echo $BOARD['use_type']==='8'?' checked':'' ?>></span>
		<span style="margin-left:85px"><input class="form-check-input" type="radio" name="use_default_type" value="9"<?php echo $BOARD['use_type']==='9'?' checked':'' ?>></span>
		<div>
			<input type="radio" class="btn-check" name="use_type" id="useType1" autocomplete="off" value="0"<?php echo ($BOARD['use_type']>3||!$BOARD['use_type'])?' checked':'' ?>>
			<label class="btn btn-xs btn-outline-primary w-100p" for="useType1"><?php echo getLang('select')?></label>
			<input type="radio" class="btn-check" name="use_type" id="useType2" autocomplete="off" value="1"<?php echo $BOARD['use_type']==='1'?' checked':'' ?>>
			<label class="btn btn-xs btn-outline-primary w-100p" for="useType2"><?php echo getLang('TEXT')?></label>
			<input type="radio" class="btn-check" name="use_type" id="useType3" autocomplete="off" value="2"<?php echo $BOARD['use_type']==='2'?' checked':'' ?>>
			<label class="btn btn-xs btn-outline-primary w-100p" for="useType3"><?php echo getLang('MKDW')?></label>
			<input type="radio" class="btn-check" name="use_type" id="useType4" autocomplete="off" value="3"<?php echo $BOARD['use_type']==='3'?' checked':'' ?>>
			<label class="btn btn-xs btn-outline-primary w-100p" for="useType4"><?php echo getLang('HTML')?></label>
		</div>
	</div>

	<div class="mb-5">
		<span class="form-label"><?php echo getLang('secret')?></span>
		<div>
			<input type="radio" class="btn-check" name="use_secret" id="useSecret1" autocomplete="off" value="0"<?php echo $BOARD['use_secret']==='0'?' checked':'' ?>>
			<label class="btn btn-xs btn-outline-primary w-100p" for="useSecret1"><?php echo getLang('select')?></label>
			<input type="radio" class="btn-check" name="use_secret" id="useSecret2" autocomplete="off" value="1"<?php echo $BOARD['use_secret']==='1'?' checked':'' ?>>
			<label class="btn btn-xs btn-outline-primary w-100p" for="useSecret2"><?php echo getLang('notuse')?></label>
			<input type="radio" class="btn-check" name="use_secret" id="useSecret3" autocomplete="off" value="2"<?php echo $BOARD['use_secret']==='2'?' checked':'' ?>>
			<label class="btn btn-xs btn-outline-primary w-100p" for="useSecret3"><?php echo getLang('use')?></label>
			<input type="radio" class="btn-check disabled" autocomplete="off" disabled>
			<label class="btn btn-xs btn-outline-secondary disabled w-100p" disabled>...</label>
		</div>
	</div>

	<div class="input-group mb-4">
		<div class="input-group">
		<label class="input-group-text w-100p" for="listCount"><?php echo getLang('list_count')?></label>
			<input type="number" class="form-control mw-100p" id="listCount" name="md_list_count" min="1" max="9999" maxlength="5" value="<?php echo $BOARD['md_list_count'] ?>">
		</div>
		<div class="form-text"><?php echo getLang('desc_list_count')?></div>
	</div>

	<div class="mb-4">
		<label class="form-label me-1"><?php echo getLang('thumbnail')?>:</label>
		<input class="form-check-input mx-1" type="checkbox" id="thumbOption" name="thumb_option" value="1"<?php echo $BOARD['thumb_option']==='1'?' checked':'' ?>>
		<label for="thumbOption"><?php echo getLang('thumb_fit')?></label>
		<div class="d-flex flex-row">
			<div class="input-group me-2" style="width:auto">
				<label class="input-group-text w-100p" for="thumbWidth"><?php echo getLang('width')?></label>
				<input type="number" class="form-control mw-100p" id="thumbWidth" name="thumb_width" min="0" max="9999" maxlength="5" value="<?php echo empty($BOARD['thumb_width'])?'':$BOARD['thumb_width'] ?>">
			</div>
			<div class="input-group me-2" style="width:auto">
				<label class="input-group-text w-100p" for="thumbHeight"><?php echo getLang('height')?></label>
				<input type="number" class="form-control mw-100p" id="thumbHeight" name="thumb_height" min="0" max="9999" maxlength="5" value="<?php echo empty($BOARD['thumb_height'])?'':$BOARD['thumb_height'] ?>">
			</div>
		</div>
		<div class="form-text"><?php echo getLang('desc_thumbnail')?></div>
	</div>

	<div class="mb-4">
		<label class="form-label"><?php echo getLang('file')?></label>
		<div class="d-flex flex-row mb-2">
			<div class="input-group me-2" style="width:auto">
				<label class="input-group-text w-100p" for="mdFileMax"><?php echo getLang('max_file_count')?></label>
				<input type="number" class="form-control mw-100p" id="mdFileMax" name="md_file_max" min="0" max="9999" maxlength="4" value="<?php echo empty($BOARD['md_file_max'])?'':$BOARD['md_file_max'] ?>">
			</div>
			<div class="input-group me-2" style="width:auto">
				<label class="input-group-text w-100p" for="mdFileSize"><?php echo getLang('max_file_size')?></label>
				<input type="number" class="form-control mw-100p" id="mdFileSize" name="md_file_size" min="0" placeholder="KB" value="<?php echo empty($BOARD['md_file_size'])?'':($BOARD['md_file_size']/1024) ?>">
			</div>
            <div class="input-group" style="width:auto">
                <label class="input-group-text w-100p" for="mdFileExt"><?php echo getLang('file_extension')?></label>
                <input type="text" class="form-control mw-100p" id="mdFileExt" name="md_file_accept" maxlength="255" value="<?php echo $BOARD['md_file_accept'] ?>">
            </div>
		</div>
		<div class="form-text"><?php echo getLang('desc_board_file')?></div>
	</div>

	<div class="mb-4">
		<label class="form-label"><?php echo getLang('point')?></label>
		<div class="d-flex flex-row">
			<div class="input-group me-2" style="width:auto">
				<label class="input-group-text w-100p" for="pointView"><?php echo getLang('view')?></label>
				<input type="number" class="form-control mw-100p" id="pointView" name="point_view" min="-9999" max="9999" maxlength="5" value="<?php echo empty($BOARD['point_view'])?'':$BOARD['point_view'] ?>">
			</div>
			<div class="input-group me-2" style="width:auto">
				<label class="input-group-text w-100p" for="pointWrite"><?php echo getLang('write')?></label>
				<input type="number" class="form-control mw-100p" id="pointWrite" name="point_write" min="-9999" max="9999" maxlength="5" value="<?php echo empty($BOARD['point_write'])?'':$BOARD['point_write'] ?>">
			</div>
			<div class="input-group me-2" style="width:auto">
				<label class="input-group-text w-100p" for="pointReply"><?php echo getLang('reply')?></label>
				<input type="number" class="form-control mw-100p" id="pointReply" name="point_reply" min="-9999" max="9999" maxlength="5" value="<?php echo empty($BOARD['point_reply'])?'':$BOARD['point_reply'] ?>">
			</div>
			<div class="input-group" style="width:auto">
				<label class="input-group-text w-100p" for="pointDownload"><?php echo getLang('download')?></label>
				<input type="number" class="form-control mw-100p" id="pointDownload" name="point_download" min="-9999" max="9999" maxlength="5" value="<?php echo empty($BOARD['point_download'])?'':$BOARD['point_download'] ?>">
			</div>
		</div>
		<div class="form-text"><?php echo getLang('desc_point')?></div>
	</div>

	<div class="mb-2">
		<span class="form-label"><?php echo getLang('list')?></span>
		<div>
			<input type="radio" class="btn-check" name="grant_list" id="grantList1" autocomplete="off" value="0"<?php echo $BOARD['grant_list']==='0'?' checked':'' ?>>
			<label class="btn btn-xs btn-outline-primary w-100p" for="grantList1"><?php echo getLang('all')?></label>
			<input type="radio" class="btn-check" name="grant_list" id="grantList2" autocomplete="off" value="1"<?php echo $BOARD['grant_list']==='1'?' checked':'' ?>>
			<label class="btn btn-xs btn-outline-primary w-100p" for="grantList2"><?php echo getLang('member')?></label>
			<input type="radio" class="btn-check" name="grant_list" id="grantList3" autocomplete="off" value="m"<?php echo $BOARD['grant_list']==='m'?' checked':'' ?>>
			<label class="btn btn-xs btn-outline-primary w-100p" for="grantList3"><?php echo getLang('admin')?></label>
		</div>
	</div>

	<div class="mb-2">
		<span class="form-label"><?php echo getLang('view')?></span>
		<div>
			<input type="radio" class="btn-check" name="grant_view" id="grantView1" autocomplete="off" value="0"<?php echo $BOARD['grant_view']==='0'?' checked':'' ?>>
			<label class="btn btn-xs btn-outline-primary w-100p" for="grantView1"><?php echo getLang('all')?></label>
			<input type="radio" class="btn-check" name="grant_view" id="grantView2" autocomplete="off" value="1"<?php echo $BOARD['grant_view']==='1'?' checked':'' ?>>
			<label class="btn btn-xs btn-outline-primary w-100p" for="grantView2"><?php echo getLang('member')?></label>
			<input type="radio" class="btn-check" name="grant_view" id="grantView3" autocomplete="off" value="m"<?php echo $BOARD['grant_view']==='m'?' checked':'' ?>>
			<label class="btn btn-xs btn-outline-primary w-100p" for="grantView3"><?php echo getLang('admin')?></label>
		</div>
	</div>

	<div class="mb-2">
		<span class="form-label"><?php echo getLang('write')?></span>
		<div>
			<input type="radio" class="btn-check" name="grant_write" id="grantWrite1" autocomplete="off" value="0"<?php echo !$is_new&&$BOARD['grant_write']==='0'?' checked':'' ?>>
			<label class="btn btn-xs btn-outline-primary w-100p" for="grantWrite1"><?php echo getLang('all')?></label>
			<input type="radio" class="btn-check" name="grant_write" id="grantWrite2" autocomplete="off" value="1"<?php echo $is_new||$BOARD['grant_write']==='1'?' checked':'' ?>>
			<label class="btn btn-xs btn-outline-primary w-100p" for="grantWrite2"><?php echo getLang('member')?></label>
			<input type="radio" class="btn-check" name="grant_write" id="grantWrite3" autocomplete="off" value="m"<?php echo $BOARD['grant_write']==='m'?' checked':'' ?>>
			<label class="btn btn-xs btn-outline-primary w-100p" for="grantWrite3"><?php echo getLang('admin')?></label>
		</div>
	</div>

	<div class="mb-2">
		<span class="form-label"><?php echo getLang('reply')?></span>
		<div>
			<input type="radio" class="btn-check" name="grant_reply" id="grantReply1" autocomplete="off" value="0"<?php echo !$is_new&&$BOARD['grant_reply']==='0'?' checked':'' ?>>
			<label class="btn btn-xs btn-outline-primary w-100p" for="grantReply1"><?php echo getLang('all')?></label>
			<input type="radio" class="btn-check" name="grant_reply" id="grantReply2" autocomplete="off" value="1"<?php echo $is_new||$BOARD['grant_reply']==='1'?' checked':'' ?>>
			<label class="btn btn-xs btn-outline-primary w-100p" for="grantReply2"><?php echo getLang('member')?></label>
			<input type="radio" class="btn-check" name="grant_reply" id="grantReply3" autocomplete="off" value="m"<?php echo $BOARD['grant_reply']==='m'?' checked':'' ?>>
			<label class="btn btn-xs btn-outline-primary w-100p" for="grantReply3"><?php echo getLang('admin')?></label>
		</div>
	</div>

	<div class="mb-2">
		<span class="form-label"><?php echo getLang('upload')?></span>
		<div>
			<input type="radio" class="btn-check" name="grant_upload" id="grantUpload1" autocomplete="off" value="0"<?php echo !$is_new&&$BOARD['grant_upload']==='0'?' checked':'' ?>>
			<label class="btn btn-xs btn-outline-primary w-100p" for="grantUpload1"><?php echo getLang('all')?></label>
			<input type="radio" class="btn-check" name="grant_upload" id="grantUpload2" autocomplete="off" value="1"<?php echo $is_new||$BOARD['grant_upload']==='1'?' checked':'' ?>>
			<label class="btn btn-xs btn-outline-primary w-100p" for="grantUpload2"><?php echo getLang('member')?></label>
			<input type="radio" class="btn-check" name="grant_upload" id="grantUpload3" autocomplete="off" value="m"<?php echo $BOARD['grant_upload']==='m'?' checked':'' ?>>
			<label class="btn btn-xs btn-outline-primary w-100p" for="grantUpload3"><?php echo getLang('admin')?></label>
		</div>
	</div>

	<div class="mb-4">
		<span class="form-label"><?php echo getLang('download')?></span>
		<div>
			<input type="radio" class="btn-check" name="grant_download" id="grantDownload1" autocomplete="off" value="0"<?php echo !$is_new&&$BOARD['grant_download']==='0'?' checked':'' ?>>
			<label class="btn btn-xs btn-outline-primary w-100p" for="grantDownload1"><?php echo getLang('all')?></label>
			<input type="radio" class="btn-check" name="grant_download" id="grantDownload2" autocomplete="off" value="1"<?php echo $is_new||$BOARD['grant_download']==='1'?' checked':'' ?>>
			<label class="btn btn-xs btn-outline-primary w-100p" for="grantDownload2"><?php echo getLang('member')?></label>
			<input type="radio" class="btn-check" name="grant_download" id="grantDownload3" autocomplete="off" value="m"<?php echo $BOARD['grant_download']==='m'?' checked':'' ?>>
			<label class="btn btn-xs btn-outline-primary w-100p" for="grantDownload3"><?php echo getLang('admin')?></label>
		</div>
	</div>

	<hr class="mb-5">
	<div class="text-end position-fixed bottom-0 end-0 p-3">
		<button type="submit" class="btn btn-success btn-lg" style="min-width:220px"><?php echo getLang('save')?></button>
	</div>
</form>

<?php
/* End of file setup.php */
/* Location: ./module/board/setup.php */