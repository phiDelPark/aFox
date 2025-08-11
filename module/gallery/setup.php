<?php if(!defined('__AFOX__')) exit();

$GALLERY = [];
if($is_new = (empty($_GET['sub_id']) || $_GET['sub_id'] === '@new')){
	$r = DB::query('SHOW FULL COLUMNS FROM '._AF_MODULE_TABLE_, [], true);
	foreach($r as $v) $GALLERY[$v['Field']] = $v['Default'];
} else {
	$GALLERY = DB::get(_AF_MODULE_TABLE_, ['md_key'=>'gallery', 'md_id'=>$_GET['sub_id']]);
}

if(empty($_GET['sub_id'])){
	$_list = DB::gets(_AF_MODULE_TABLE_, 'SQL_CALC_FOUND_ROWS *', ['md_key'=>'gallery']);
	if($error = DB::error()) $error = set_error($error->getMessage(),$error->getCode());
?>
<a class="btn btn-primary mb-3" style="width:250px" href="<?php echo getUrl('sub_id', '@new')?>"><?php echo getLang('new_gallery')?></a>

<table class="table table-hover">
<thead>
	<tr>
		<th scope="col"><?php echo getLang('id')?></th>
		<th scope="col" class="text-wrap"><?php echo getLang('title')?></th>
		<th scope="col"><?php echo getLang('grant')?></th>
		<th scope="col" class="d-none d-md-table-cell"><?php echo getLang('date')?></th>
		<th scope="col" class="text-end"><?php echo getLang('setup')?></th>
	</tr>
</thead>
<tbody>
<?php

	if($error) {
		messageBox($error['message'], $error['error'], false);
	} else {

		$grants = ['0'=>'A','1'=>'M','m'=>'S'];
		foreach ($_list as $key => $value) {
			echo '<tr><th scope="row" ><a href="'._AF_URL_.'?id='.$value['md_id'].'" target="_blank">'.$value['md_id'].'</a></th>';
			echo '<td class="text-wrap">'.escapeHTML(cutstr(strip_tags($value['md_title'].(empty($value['md_about'])?'':' - '.$value['md_about'])),50)).'</td>';
			echo '<td class="fixed-width">'.$grants[$value['grant_list']].$grants[$value['grant_view']].$grants[$value['grant_upload']].'</td>';
			echo '<td class="d-none d-md-table-cell">'.date('Y/m/d', strtotime($value['md_regdate'])).'</td>';
			echo '<td><a class="btn btn-primary btn-sm" href="'.getUrl('sub_id', $value['md_id']).'">'.getLang('setup').'</a></td></tr>';
		}
	}
?>
</tbody>
</table>

<?php } else { ?>

<?php if(!$is_new){?>
<form method="post" autocomplete="off" enctype="multipart/form-data" onsubmit="return validateForm(this)">
	<input type="hidden" name="success_url" value="<?php echo getUrl('sub_id', '', 'md_id', '')?>" />
	<input type="hidden" name="module" value="gallery" />
	<input type="hidden" name="act" value="deleteGallery" />
	<input type="hidden" name="md_id" value="" />
	<button type="submit" class="btn btn-sm btn-danger float-end"><?php echo getLang('permanent_delete')?></button>
</form>
<script>
function validateForm(f) {
	var return_value = prompt('<?php echo getLang('confirm_delete',['gallery'])?>', 'Gallery ID?');
	if (return_value === '<?php echo $GALLERY['md_id']?>') {f.md_id.value = return_value; return true;} else return false;
}
</script>
<?php }?>

<form method="post" autocomplete="off">
	<input type="hidden" name="error_url" value="<?php echo getUrl()?>" />
	<input type="hidden" name="success_url" value="<?php echo getUrl('sub_id', '')?>" />
	<input type="hidden" name="module" value="<?php echo $_GET['md_id']?>" />
	<input type="hidden" name="act" value="updateSetup" />
	<input type="hidden" name="md_id" value="<?php echo $is_new?'':$GALLERY['md_id'] ?>" />

	<div class="mb-4 float-start">
		<div class="d-flex flex-row">
			<div class="input-group">
				<label class="input-group-text w-100p" for="id_new_md_id"><?php echo getLang('id')?></label>
				<input type="text" value="<?php echo $is_new?'" name="new_md_id':$GALLERY['md_id'] ?>" class="form-control mw-150p" id="id_new_md_id" required maxlength="11" pattern="<?php echo _AF_PATTERN_ID_?>"<?php echo $is_new?'':' disabled'?>>
			</div>
			<div class="input-group ms-2">
				<input type="text" name="md_manager" class="form-control mw-100p" id="id_md_manager" maxlength="11" pattern="<?php echo _AF_PATTERN_ID_?>" placeholder="<?php echo getLang('manager')?> ID">
			</div>
		</div>
		<div class="form-text"><?php echo getLang('desc_mb_id')?></div>
	</div>

	<div class="input-group mb-2 clearfix">
		<label class="input-group-text w-100p" for="mdTitle"><?php echo getLang('title')?></label>
		<input type="text" name="md_title" class="form-control" id="mdTitle" maxlength="255" value="<?php echo  $GALLERY['md_title'] ?>">
	</div>

	<div class="input-group mb-4">
		<label class="input-group-text w-100p" for="mdAbout"><?php echo getLang('info')?></label>
		<input type="text" name="md_about" class="form-control" id="mdAbout" maxlength="255" value="<?php echo  $GALLERY['md_about'] ?>">
	</div>

	<div class="mb-4">
		<label class="form-label" for="mdCategory"><?php echo getLang('category')?></label>
		<div class="input-group">
			<input type="text" name="md_category" class="form-control" id="mdCategory" maxlength="255" pattern="<?php echo str_replace(array('{','}'),'',_AF_PATTERN_CATEGORY_)?>" value="<?php echo $GALLERY['md_category'] ?>">
		</div>
		<div class="form-text"><?php echo getLang('desc_category')?></div>
	</div>

	<div class="input-group mb-4">
		<div class="input-group">
		<label class="input-group-text w-100p" for="listCount"><?php echo getLang('list_count')?></label>
			<input type="number" class="form-control mw-100p" id="listCount" name="md_list_count" min="1" max="9999" maxlength="5" value="<?php echo $GALLERY['md_list_count'] ?>">
		</div>
		<div class="form-text"><?php echo getLang('desc_list_count')?></div>
	</div>

	<div class="mb-4">
		<label class="form-label me-1"><?php echo getLang('thumbnail')?>:</label>
		<input class="form-check-input mx-1" type="checkbox" id="thumbOption" name="thumb_option" value="1"<?php echo $GALLERY['thumb_option']==='1'?' checked':'' ?>>
		<label for="thumbOption"><?php echo getLang('thumb_fit')?></label>
		<div class="d-flex flex-row">
			<div class="input-group me-2" style="width:auto">
				<label class="input-group-text w-100p" for="thumbWidth"><?php echo getLang('width')?></label>
				<input type="number" class="form-control mw-100p" id="thumbWidth" name="thumb_width" min="0" max="9999" maxlength="5" value="<?php echo empty($GALLERY['thumb_width'])?'':$GALLERY['thumb_width'] ?>">
			</div>
			<div class="input-group me-2" style="width:auto">
				<label class="input-group-text w-100p" for="thumbHeight"><?php echo getLang('height')?></label>
				<input type="number" class="form-control mw-100p" id="thumbHeight" name="thumb_height" min="0" max="9999" maxlength="5" value="<?php echo empty($GALLERY['thumb_height'])?'':$GALLERY['thumb_height'] ?>">
			</div>
		</div>
		<div class="form-text"><?php echo getLang('desc_thumbnail')?></div>
	</div>

	<div class="mb-4">
		<label class="form-label"><?php echo getLang('file')?></label>
		<div class="d-flex flex-row mb-2">
			<div class="input-group me-2" style="width:auto">
				<label class="input-group-text w-100p" for="mdFileMax"><?php echo getLang('max_file_count')?></label>
				<input type="number" class="form-control mw-100p" id="mdFileMax" name="md_file_max" min="0" max="9999" maxlength="4" value="<?php echo empty($GALLERY['md_file_max'])?'3':$GALLERY['md_file_max'] ?>">
			</div>
			<div class="input-group me-2" style="width:auto">
				<label class="input-group-text w-100p" for="mdFileSize"><?php echo getLang('max_file_size')?></label>
				<input type="number" class="form-control mw-100p" id="mdFileSize" name="md_file_size" min="0" placeholder="KB" value="<?php echo empty($GALLERY['md_file_size'])?'1024':($GALLERY['md_file_size']/1024) ?>">
			</div>
		</div>
		<div class="form-text"><?php echo getLang('desc_board_file')?></div>
	</div>

	<div class="mb-4">
		<label class="form-label"><?php echo getLang('point')?></label>
		<div class="d-flex flex-row">
			<div class="input-group me-2" style="width:auto">
				<label class="input-group-text w-100p" for="pointView"><?php echo getLang('view')?></label>
				<input type="number" class="form-control mw-100p" id="pointView" name="point_view" min="-9999" max="9999" maxlength="5" value="<?php echo empty($GALLERY['point_view'])?'':$GALLERY['point_view'] ?>">
			</div>
			<div class="input-group me-2" style="width:auto">
				<label class="input-group-text w-100p" for="pointWrite"><?php echo getLang('upload')?></label>
				<input type="number" class="form-control mw-100p" id="pointWrite" name="point_write" min="-9999" max="9999" maxlength="5" value="<?php echo empty($GALLERY['point_write'])?'':$GALLERY['point_write'] ?>">
			</div>
		</div>
		<div class="form-text"><?php echo getLang('desc_point')?></div>
	</div>

	<div class="mb-2">
		<span class="form-label"><?php echo getLang('list')?></span>
		<div>
			<input type="radio" class="btn-check" name="grant_list" id="grantList1" autocomplete="off" value="0"<?php echo $GALLERY['grant_list']==='0'?' checked':'' ?>>
			<label class="btn btn-xs btn-outline-primary w-100p" for="grantList1"><?php echo getLang('all')?></label>
			<input type="radio" class="btn-check" name="grant_list" id="grantList2" autocomplete="off" value="1"<?php echo $GALLERY['grant_list']==='1'?' checked':'' ?>>
			<label class="btn btn-xs btn-outline-primary w-100p" for="grantList2"><?php echo getLang('member')?></label>
			<input type="radio" class="btn-check" name="grant_list" id="grantList3" autocomplete="off" value="m"<?php echo $GALLERY['grant_list']==='m'?' checked':'' ?>>
			<label class="btn btn-xs btn-outline-primary w-100p" for="grantList3"><?php echo getLang('admin')?></label>
		</div>
	</div>

	<div class="mb-2">
		<span class="form-label"><?php echo getLang('view')?></span>
		<div>
			<input type="radio" class="btn-check" name="grant_view" id="grantView1" autocomplete="off" value="0"<?php echo $GALLERY['grant_view']==='0'?' checked':'' ?>>
			<label class="btn btn-xs btn-outline-primary w-100p" for="grantView1"><?php echo getLang('all')?></label>
			<input type="radio" class="btn-check" name="grant_view" id="grantView2" autocomplete="off" value="1"<?php echo $GALLERY['grant_view']==='1'?' checked':'' ?>>
			<label class="btn btn-xs btn-outline-primary w-100p" for="grantView2"><?php echo getLang('member')?></label>
			<input type="radio" class="btn-check" name="grant_view" id="grantView3" autocomplete="off" value="m"<?php echo $GALLERY['grant_view']==='m'?' checked':'' ?>>
			<label class="btn btn-xs btn-outline-primary w-100p" for="grantView3"><?php echo getLang('admin')?></label>
		</div>
	</div>

	<div class="mb-4">
		<span class="form-label"><?php echo getLang('upload')?></span>
		<div>
			<input type="radio" class="btn-check" name="grant_upload" id="grantUpload1" autocomplete="off" value="0"<?php echo !$is_new&&$GALLERY['grant_upload']==='0'?' checked':'' ?>>
			<label class="btn btn-xs btn-outline-primary w-100p" for="grantUpload1"><?php echo getLang('all')?></label>
			<input type="radio" class="btn-check" name="grant_upload" id="grantUpload2" autocomplete="off" value="1"<?php echo $is_new||$GALLERY['grant_upload']==='1'?' checked':'' ?>>
			<label class="btn btn-xs btn-outline-primary w-100p" for="grantUpload2"><?php echo getLang('member')?></label>
			<input type="radio" class="btn-check" name="grant_upload" id="grantUpload3" autocomplete="off" value="m"<?php echo $GALLERY['grant_upload']==='m'?' checked':'' ?>>
			<label class="btn btn-xs btn-outline-primary w-100p" for="grantUpload3"><?php echo getLang('admin')?></label>
		</div>
	</div>

	<hr class="mb-5">
	<div class="text-end position-fixed bottom-0 end-0 p-3">
		<button type="submit" class="btn btn-success btn-lg" style="min-width:220px"><?php echo getLang('save')?></button>
	</div>
<?php }
/* End of file setup.php */
/* Location: ./module/gallery/setup.php */
