<?php if(!defined('__AFOX__')) exit();
	$PAGE = [];
	if($is_new = (empty($_GET['md_id']) || $_GET['md_id'] === '.')){
		$r = DB::query('SHOW FULL COLUMNS FROM '._AF_PAGE_TABLE_, [], true);
		foreach($r as $v) $PAGE[$v['Field']] = $v['Default'];
		$r = DB::query('SHOW FULL COLUMNS FROM '._AF_MODULE_TABLE_, [], true);
		foreach($r as $v) $PAGE[$v['Field']] = $v['Default'];
	} else{
		$PAGE = DB::get(_AF_PAGE_TABLE_, '*', ['md_id'=>$_GET['md_id']]);
		if(empty($PAGE['md_id'])) {
			messageBox(getLang('error_founded'), 4201);
			return;
		} else if(!isGrant('view', $PAGE['md_id'])) {
			messageBox(getLang('error_permitted'), 4501);
			return;
		}
		if($PAGE['md_id']) $PAGE = array_merge(getModule($PAGE['md_id']), $PAGE);
	}
?>

<?php if(!$is_new){?>
<form method="post" autocomplete="off" enctype="multipart/form-data" onsubmit="return confirm('<?php echo getLang('confirm_delete',['page'])?>')">
	<input type="hidden" name="success_url" value="<?php echo getUrl('mid', '', 'md_id', '')?>" />
	<input type="hidden" name="module" value="page" />
	<input type="hidden" name="act" value="deletePage" />
	<input type="hidden" name="md_id" value="<?php echo $PAGE['md_id']?>" />
	<button type="submit" class="btn btn-sm btn-danger float-end"><?php echo getLang('permanent_delete')?></button>
</form>
<?php }?>

<form id="setup" method="post" autocomplete="off" enctype="multipart/form-data">
	<input type="hidden" name="success_url" value="<?php echo getUrl('mid', '','md_id', '')?>" />
	<input type="hidden" name="module" value="page" />
	<input type="hidden" name="act" value="updatePage" />
	<input type="hidden" name="md_id" value="<?php echo $is_new?'':$PAGE['md_id'] ?>" />

	<div class="mb-4 float-start">
		<div class="input-group">
			<label class="input-group-text w-100p" for="id_new_md_id"><?php echo getLang('id')?></label>
			<input type="text" value="<?php echo $is_new?'" name="new_md_id':$PAGE['md_id'] ?>" class="form-control mw-150p" id="id_new_md_id" required maxlength="11" pattern="^[a-zA-Z]+\w{2,}$"<?php echo $is_new?'':' disabled'?>>
		</div>
		<div class="form-text"><?php echo getLang('desc_mb_id')?></div>
	</div>

	<div class="input-group mb-2 clearfix">
		<label class="input-group-text w-100p" for="mdTitle"><?php echo getLang('title')?></label>
		<input type="text" name="md_title" class="form-control" id="mdTitle" maxlength="255" value="<?php echo $PAGE['md_title'] ?>">
	</div>

	<div class="input-group mb-4">
		<label class="input-group-text w-100p" for="mdAbout"><?php echo getLang('info')?></label>
		<input type="text" name="md_about" class="form-control" id="mdAbout" maxlength="255" value="<?php echo $PAGE['md_about'] ?>">
	</div>

	<div class="mb-2">
		<span class="form-label"><?php echo getLang('view')?></span>
		<div>
			<input type="radio" class="btn-check" name="grant_view" id="grantView1" autocomplete="off" value="0"<?php echo $PAGE['grant_view']==='0'?' checked':'' ?>>
			<label class="btn btn-xs btn-outline-primary w-100p" for="grantView1"><?php echo getLang('all')?></label>
			<input type="radio" class="btn-check" name="grant_view" id="grantView2" autocomplete="off" value="1"<?php echo $PAGE['grant_view']==='1'?' checked':'' ?>>
			<label class="btn btn-xs btn-outline-primary w-100p" for="grantView2"><?php echo getLang('member')?></label>
			<input type="radio" class="btn-check" name="grant_view" id="grantView3" autocomplete="off" value="m"<?php echo $PAGE['grant_view']==='m'?' checked':'' ?>>
			<label class="btn btn-xs btn-outline-primary w-100p" for="grantView3"><?php echo getLang('admin')?></label>
		</div>
	</div>

	<div class="mb-2">
		<span class="form-label"><?php echo getLang('reply')?></span>
		<div>
			<input type="radio" class="btn-check" name="grant_reply" id="grantReply1" autocomplete="off" value="0"<?php echo !$is_new&&$PAGE['grant_reply']==='0'?' checked':'' ?>>
			<label class="btn btn-xs btn-outline-primary w-100p" for="grantReply1"><?php echo getLang('all')?></label>
			<input type="radio" class="btn-check" name="grant_reply" id="grantReply2" autocomplete="off" value="1"<?php echo $is_new||$PAGE['grant_reply']==='1'?' checked':'' ?>>
			<label class="btn btn-xs btn-outline-primary w-100p" for="grantReply2"><?php echo getLang('member')?></label>
			<input type="radio" class="btn-check" name="grant_reply" id="grantReply3" autocomplete="off" value="m"<?php echo $PAGE['grant_reply']==='m'?' checked':'' ?>>
			<label class="btn btn-xs btn-outline-primary w-100p" for="grantReply3"><?php echo getLang('admin')?></label>
		</div>
	</div>

	<div class="mb-4">
		<span class="form-label"><?php echo getLang('download')?></span>
		<div>
			<input type="radio" class="btn-check" name="grant_download" id="grantDownload1" autocomplete="off" value="0"<?php echo !$is_new&&$PAGE['grant_download']==='0'?' checked':'' ?>>
			<label class="btn btn-xs btn-outline-primary w-100p" for="grantDownload1"><?php echo getLang('all')?></label>
			<input type="radio" class="btn-check" name="grant_download" id="grantDownload2" autocomplete="off" value="1"<?php echo $is_new||$PAGE['grant_download']==='1'?' checked':'' ?>>
			<label class="btn btn-xs btn-outline-primary w-100p" for="grantDownload2"><?php echo getLang('member')?></label>
			<input type="radio" class="btn-check" name="grant_download" id="grantDownload3" autocomplete="off" value="m"<?php echo $PAGE['grant_download']==='m'?' checked':'' ?>>
			<label class="btn btn-xs btn-outline-primary w-100p" for="grantDownload3"><?php echo getLang('admin')?></label>
		</div>
	</div>

	<div class="editor-group mb-4">
		<?php displayEditor(
				'pg_content',
				$PAGE['pg_content'],
				[
					'file'=>[$is_new?'':$PAGE['md_id'], 1, 99999],
					'html'=>!$is_new&&$PAGE['pg_type'] === '2',
					'toolbar'=>true,
					'typebar'=>array(getLang('content'), ['pg_type'=>[$is_new?'1':$PAGE['pg_type'], ['TEXT'=>'0','MKDW'=>'1','HTML'=>'2']]])
				]
			);
		?>
	</div>

	<hr class="mb-5">
	<div class="text-end position-fixed bottom-0 end-0 p-3">
		<button type="submit" class="btn btn-success btn-lg" style="min-width:220px"><?php echo getLang('save')?></button>
	</div>
</form>

<?php
/* End of file setup.php */
/* Location: ./module/page/setup.php */
