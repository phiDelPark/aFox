<?php
if(!defined('__AFOX__')) exit();

@include_once _AF_MODULES_PATH_ . $_DATA['mid'] . '/lang/' . _AF_LANG_ . '.php';

	$search = empty($_DATA['search'])?null:'%'.$_DATA['search'].'%';
	$_list = getDBList(_AF_MODULE_TABLE_,[
		'md_key'=>'example',
		'OR' =>['md_id{LIKE}'=>$search, 'md_title{LIKE}'=>$search]
	],'md_regdate desc', empty($_DATA['page']) ? 1 : $_DATA['page'], 20);
?>

<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">
			예제 <small><?php echo getLang('setup')?></small>
		</h1>
		<ol class="breadcrumb">
			<li class="active">
				<i class="glyphicon glyphicon-th-large" aria-hidden="true"></i> 예제 모듈입니다.
			</li>
		</ol>
	</div>
</div>

<p class="navbar">
  <button type="button" class="btn btn-primary min-width-200" data-toggle="modal" data-target=".bs-example-modal-lg">예제 입니다.</button>
</p>

<table class="table table-hover table-nowrap">
<thead>
	<tr>
		<th>#<?php echo getLang('test')?></th>
	</tr>
</thead>
<tbody>

<?php
	$end_page = $total_page = 0;
	$start_page = $current_page = 1;

	if(!empty($_list['error'])) {
		echo showMessage($_list['message'], $_list['error']);
	} else {
		$current_page = $_list['current_page'];
		$total_page = $_list['total_page'];
		$start_page = $_list['start_page'];
		$end_page = $_list['end_page'];

		foreach ($_list['data'] as $key => $value) {
			echo '<tr class="afox-list-item" data-exec-ajax="example.getExample" data-ajax-param="md_id,'.$value['md_id'].'" data-modal-target="#example_modal"><th scope="row">'.$value['md_id'].'</th>';
		}
	}
?>

</tbody>
</table>

<nav class="navbar clearfix">
  <ul class="pagination">
	<li><form class="form-inline search-form" action="<?php echo getUrl('') ?>" method="get">
		<input type="hidden" name="admin" value="<?php echo $_DATA['admin'] ?>">
		<input type="text" name="search" value="<?php echo empty($_DATA['search'])?'':$_DATA['search'] ?>" class="form-control" placeholder="<?php echo getLang('search_text') ?>" required>
		<button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search" aria-hidden="true"></i> <?php echo getLang('search') ?></button>
		<?php if(!empty($_DATA['search'])) {?><button class="btn btn-default" type="button" onclick="location.replace('<?php echo getUrl('search','') ?>')"><?php echo getLang('cancel') ?></button><?php }?>
	</form></li>
  </ul>
	<ul class="pagination">
	<?php if($start_page>10) echo '<li><a href="'.getUrl('page',$start_page-10).'">&laquo;</a></li>'; ?>
	<li<?php echo $current_page <= 1 ? ' class="disabled"' : ''?>><a href="<?php echo  $current_page <= 1 ? '#" onclick="return false' : getUrl('page',$current_page-1)?>" aria-label="Previous"><span aria-hidden="true">&lsaquo;</span></a></li>
	<?php for ($i=$start_page; $i <= $end_page; $i++) echo '<li'.($current_page == $i ? ' class="active"' : '').'><a href="'.getUrl('page',$i).'">'.$i.'</a></li>'; ?>
	<li<?php echo $current_page >= $total_page ? ' class="disabled"' : ''?>><a href="<?php echo $current_page >= $total_page ? '#" onclick="return false' : getUrl('page',$current_page+1)?>" aria-label="Next"><span aria-hidden="true">&rsaquo;</span></a></li>
	<?php if(($total_page-$end_page)>0) echo '<li><a href="'.getUrl('page',$end_page+1).'">&raquo;</a></li>'; ?>
	</ul>
</nav>

<div id="example_modal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
	<form class="modal-content" method="post" autocomplete="off" data-exec-ajax="admin.updateExample">
	<input type="hidden" name="success_return_url" value="<?php echo getUrl()?>" />
	<input type="hidden" name="rp_srl" value="" />

	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="myModalLabel"><?php echo getLang('example')?></h4>
	  </div>
	  <div class="modal-body">
			<div class="form-group clearfix" style="margin-bottom:0">
				<div class="pull-left">
					<label for="id_md_id"><?php echo getLang('id')?></label>
					<div class="form-inline">
						<input type="text" name="new_md_id" class="form-control" id="id_md_id" required maxlength="11" pattern="^[a-zA-Z]+[a-zA-Z0-9_]{2,}">
						<input type="hidden" name="md_id" value="" />
					</div>
				</div>
				<div class="pull-right">
					<div class="form-inline">
						<input type="text" name="md_manager" class="form-control" style="width:120px" id="id_md_manager" maxlength="11" pattern="^[0-9]+" placeholder="<?php echo getLang('admin')?>">
						<button type="button" class="btn btn-primary"><?php echo getLang('find')?></button>
					</div>
				</div>
			</div>
			<p class="help-block"><?php echo getLang('desc_id')?></p>
			<div class="form-group">
				<label for="id_md_title"><?php echo getLang('title')?></label>
				<input type="text" name="md_title" class="form-control" id="id_md_title" maxlength="255">
			</div>
			<div class="form-group">
				<label for="id_md_description"><?php echo getLang('explain')?></label>
				<input type="text" name="md_description" class="form-control" id="id_md_description" maxlength="255">
			</div>
			<div class="form-group">
				<label for="id_md_category"><?php echo getLang('category')?></label>
				<input type="text" name="md_category" class="form-control" id="id_md_category" maxlength="255" pattern="^[^\x21-\x2b\x2d-\x2f\x3a-\x40\x5b-\x60]+">
				<p class="help-block"><?php echo getLang('desc_category')?></p>
			</div>
			<div class="form-group">
				<label><?php echo getLang('file')?></label>
				<div class="form-inline">
					<input type="number" class="form-control" name="md_file_max" min="0" max="9000" maxlength="11" placeholder="<?php echo getLang('max_file_count')?>">
					<input type="number" class="form-control" name="md_file_size" min="0" max="9000" maxlength="11" placeholder="<?php echo getLang('max_file_size')?>">
				</div>
				<p class="help-block"><?php echo getLang('desc_board_file')?></p>
			</div>
			<div class="form-group">
				<label><?php echo getLang('list')?></label>
				<div class="radio-group">
					<input type="hidden" name="grant_list" value="0">
					<div class="radio-control radio-xs">
						<span class="radio active" data-value="0"><?php echo getLang('all')?></span>
						<span class="radio" data-value="1"><?php echo getLang('member')?></span>
						<span class="radio" data-value="m"><?php echo getLang('admin')?></span>
					</div>
				 </div>
			</div>
			<div class="form-group">
				<label><?php echo getLang('view')?></label>
				<div class="radio-group">
					<input type="hidden" name="grant_view" value="0">
					<div class="radio-control radio-xs">
						<span class="radio active" data-value="0"><?php echo getLang('all')?></span>
						<span class="radio" data-value="1"><?php echo getLang('member')?></span>
						<span class="radio" data-value="m"><?php echo getLang('admin')?></span>
					</div>
				 </div>
			</div>
			<div class="form-group">
				<label><?php echo getLang('write')?></label>
				<div class="radio-group">
					<input type="hidden" name="grant_write" value="0">
					<div class="radio-control radio-xs">
						<span class="radio active" data-value="0"><?php echo getLang('all')?></span>
						<span class="radio" data-value="1"><?php echo getLang('member')?></span>
						<span class="radio" data-value="m"><?php echo getLang('admin')?></span>
					</div>
				 </div>
			</div>
			<div class="form-group">
				<label><?php echo getLang('reply')?></label>
				<div class="radio-group">
					<input type="hidden" name="grant_reply" value="0">
					<div class="radio-control radio-xs">
						<span class="radio active" data-value="0"><?php echo getLang('all')?></span>
						<span class="radio" data-value="1"><?php echo getLang('member')?></span>
						<span class="radio" data-value="m"><?php echo getLang('admin')?></span>
					</div>
				 </div>
			</div>
	  </div>
	  <div class="modal-footer clearfix">
		<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo getLang('close')?></button>
		<button type="submit" class="btn btn-success min-width-150"><i class="glyphicon glyphicon-ok" aria-hidden="true"></i> <?php echo getLang('save')?></button>
	  </div>
	</form>
  </div>
</div>

<script src="<?php echo _AF_URL_ . 'module/example/example.js' ?>"></script>

<?php
/* End of file info.php */
/* Location: ./module/example/info.php */