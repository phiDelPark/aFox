<?php
	if(!defined('__AFOX__')) exit();

	$search = empty($_DATA['search'])?null:'%'.$_DATA['search'].'%';
	$board_list = getDBList(_AF_MODULE_TABLE_,[
		'md_key'=>'board',
		'OR' =>empty($search)?[]:['md_id{LIKE}'=>$search, 'md_title{LIKE}'=>$search]
	],'md_regdate desc', empty($_DATA['page']) ? 1 : $_DATA['page'], 20);
?>

<p class="navbar">
  <button type="button" class="btn btn-primary min-width-200" data-toggle="modal.clone" data-target=".bs-admin-modal-lg"><?php echo getLang('new_board')?></button>
</p>


<table class="table table-hover table-nowrap">
<thead>
	<tr>
		<th class="col-xs-1">#<?php echo getLang('id')?></th>
		<th><?php echo getLang('title')?></th>
		<th class="col-xs-1 hidden-xs hidden-sm"><?php echo getLang('grant')?></th>
		<th class="col-xs-1"><?php echo getLang('date')?></th>
		<th class="col-xs-1"><?php echo getLang('setup')?></th>
	</tr>
</thead>
<tbody>

<?php
	$end_page = $total_page = 0;
	$start_page = $current_page = 1;

	if(!empty($board_list['error'])) {
		echo messageBox($board_list['message'], $board_list['error'], false);
	} else {
		$current_page = $board_list['current_page'];
		$total_page = $board_list['total_page'];
		$start_page = $board_list['start_page'];
		$end_page = $board_list['end_page'];

		foreach ($board_list['data'] as $key => $value) {
			echo '<tr><th scope="row"><a href="'._AF_URL_.'?id='.$value['md_id'].'" target="_blank">'.$value['md_id'].'</a></th>';
			echo '<td>'.escapeHtml(cutstr(strip_tags($value['md_title'].(empty($value['md_description'])?'':' - '.$value['md_description'])),50)).'</td>';
			echo '<td class="hidden-xs hidden-sm">'.$value['grant_list'].'-'.$value['grant_view'].'-'.$value['grant_write'].'-'.$value['grant_reply'].'-'.$value['grant_upload'].'-'.$value['grant_download'].'</td>';
			echo '<td>'.date('Y/m/d', strtotime($value['md_regdate'])).'</td>';
			echo '<td><button type="button" class="btn btn-primary btn-xs min-width-100" data-exec-ajax="admin.getBoard" data-ajax-param="md_id,'.$value['md_id'].'" data-modal-target="#board_modal">'.getLang('setup').'</button></td></tr>';
		}
	}
?>

</tbody>
</table>

<nav class="navbar clearfix">
	<ul class="pager visible-xs-block visible-sm-block">
		<li class="previous<?php echo $current_page <= 1?' disabled':''?>"><a href="<?php echo  $current_page <= 1 ? '#" onclick="return false' : getUrl('page',$current_page-1)?>" aria-label="Previous"><span aria-hidden="true">&lsaquo;</span> <?php echo getLang('previous') ?></a></li>
		<li><span class="col-xs-5"><?php echo $current_page.' / '.$total_page?></span></li>
		<li class="next<?php echo $current_page >= $total_page?' disabled':''?>"><a href="<?php echo $current_page >= $total_page ? '#" onclick="return false' : getUrl('page',$current_page+1)?>" aria-label="Next"><?php echo getLang('next') ?> <span aria-hidden="true">&rsaquo;</span></a></li>
	</ul>
	<ul class="pagination hidden-xs hidden-sm pull-right">
		<?php if($start_page>10) echo '<li><a href="'.getUrl('page',$start_page-10).'">&laquo;</a></li>'; ?>
		<li<?php echo $current_page <= 1 ? ' class="disabled"' : ''?>><a href="<?php echo  $current_page <= 1 ? '#" onclick="return false' : getUrl('page',$current_page-1)?>" aria-label="Previous"><span aria-hidden="true">&lsaquo;</span></a></li>
		<?php for ($i=$start_page; $i <= $end_page; $i++) echo '<li'.($current_page == $i ? ' class="active"' : '').'><a href="'.getUrl('page',$i).'">'.$i.'</a></li>'; ?>
		<li<?php echo $current_page >= $total_page ? ' class="disabled"' : ''?>><a href="<?php echo $current_page >= $total_page ? '#" onclick="return false' : getUrl('page',$current_page+1)?>" aria-label="Next"><span aria-hidden="true">&rsaquo;</span></a></li>
		<?php if(($total_page-$end_page)>0) echo '<li><a href="'.getUrl('page',$end_page+1).'">&raquo;</a></li>'; ?>
	</ul>
	<ul class="pagination">
	<li><form class="form-inline search-form" action="<?php echo getUrl('') ?>" method="get">
		<input type="hidden" name="admin" value="<?php echo $_DATA['admin'] ?>">
		<input type="text" name="search" value="<?php echo empty($_DATA['search'])?'':$_DATA['search'] ?>" class="form-control" placeholder="<?php echo getLang('search_text') ?>" required>
		<button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search" aria-hidden="true"></i> <?php echo getLang('search') ?></button>
		<?php if(!empty($_DATA['search'])) {?><button class="btn btn-default" type="button" onclick="location.replace('<?php echo getUrl('search','') ?>')"><?php echo getLang('cancel') ?></button><?php }?>
	</form></li>
	</ul>
</nav>

<div id="board_modal" class="modal fade bs-admin-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
	<form class="modal-content" method="post" autocomplete="off" data-exec-ajax="admin.updateBoard">
		<input type="hidden" name="success_return_url" value="<?php echo getUrl()?>" />
		<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="myModalLabel"><?php echo getLang('board')?></h4>
		</div>
		<div class="modal-body">
			<div class="form-group clearfix" style="margin-bottom:0">
				<div class="pull-left">
					<label for="id_md_id"><?php echo getLang('id')?></label>
					<div class="form-inline">
						<input type="text" name="new_md_id" class="form-control" id="id_md_id" required maxlength="11" pattern="^[a-zA-Z]+\w{2,}$">
						<input type="hidden" name="md_id" value="" />
					</div>
				</div>
				<div class="pull-right">
					<div class="form-inline">
						<input type="text" name="md_manager" class="form-control" style="width:120px" id="id_md_manager" maxlength="11" pattern="^[a-zA-Z]+\w{2,}$" placeholder="<?php echo getLang('board_manager')?>">
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
				<label for="id_md_extra_keys"><?php echo getLang('extra_keys')?></label>
				<input type="text" name="md_extra_keys" class="form-control" id="id_md_extra_keys" maxlength="255" pattern="^[^\x21-\x29\x2b\x2d-\x2f\x3a-\x40\x5b-\x60]+">
				<p class="help-block"><?php echo getLang('desc_extra_keys')?></p>
			</div>
			<div class="form-group">
				<label><?php echo getLang('style')?></label>
				<div class="radio-group">
					<input type="hidden" name="use_style" value="0">
					<div class="radio-control radio-xs">
						<span class="radio active" data-value="0"><?php echo getLang('Default')?></span>
						<span class="radio" data-value="1"><?php echo getLang('Review')?></span>
						<span class="radio" data-value="2"><?php echo getLang('Album')?></span>
						<span class="radio" data-value="3"><?php echo getLang('Gallery')?></span>
					</div>
				 </div>
			</div>
			<div class="form-group">
				<label><?php echo getLang('type')?></label>
				<div class="radio-group">
					<input type="hidden" name="use_type" value="0">
					<div class="radio-control radio-xs">
						<span class="radio active" data-value="0"><?php echo getLang('select')?></span>
						<span class="radio" data-value="1"><?php echo getLang('TEXT')?></span>
						<span class="radio" data-value="2"><?php echo getLang('MKDW')?></span>
						<span class="radio" data-value="3"><?php echo getLang('HTML')?></span>
					</div>
				 </div>
			</div>
			<div class="form-group">
				<label><?php echo getLang('secret')?></label>
				<div class="radio-group">
					<input type="hidden" name="use_secret" value="0">
					<div class="radio-control radio-xs">
						<span class="radio active" data-value="0"><?php echo getLang('select')?></span>
						<span class="radio" data-value="1"><?php echo getLang('notuse')?></span>
						<span class="radio" data-value="2"><?php echo getLang('use')?></span>
					</div>
				 </div>
			</div>
			<div class="form-group">
				<label><?php echo getLang('list_count')?></label>
				<div class="form-inline">
					<div class="input-group">
						<label class="input-group-addon" for="id_list_count"><?php echo getLang('document_count')?></label>
						<input type="number" class="form-control" id="id_list_count" name="md_list_count" min="1" max="9999" maxlength="5" placeholder="<?php echo getLang('Count')?>">
					</div>
				</div>
				<p class="help-block"><?php echo getLang('desc_list_count')?></p>
			</div>
			<div class="form-group">
				<label><?php echo getLang('file')?></label>
				<div class="form-inline">
					<div class="input-group">
						<label class="input-group-addon" for="id_md_file_max"><?php echo getLang('max_file_count')?></label>
						<input type="number" class="form-control" id="id_md_file_max" name="md_file_max" min="0" max="9999" maxlength="4" placeholder="<?php echo getLang('Count')?>">
					</div>
					&nbsp;&nbsp;<input type="number" class="form-control" name="md_file_size" min="0" max="99999999999" maxlength="11" placeholder="<?php echo getLang('max_file_size')?> (KB)">
					&nbsp;&nbsp;<input type="text" class="form-control" name="md_file_ext" maxlength="255" placeholder="<?php echo getLang('file_extension')?>">
				</div>
				<p class="help-block"><?php echo getLang('desc_board_file')?></p>
			</div>
			<div class="form-group">
				<label><?php echo getLang('thumbnail')?></label>
				<div class="form-inline">
					<div class="input-group">
						<label class="input-group-addon" for="id_thumb_width"><?php echo getLang('width')?></label>
						<input type="number" class="form-control" id="id_thumb_width" name="thumb_width" min="0" max="9999" maxlength="5" placeholder="<?php echo getLang('Size')?>">
					</div>
					&nbsp;&nbsp;<div class="input-group">
						<label class="input-group-addon" for="id_thumb_height"><?php echo getLang('height')?></label>
						<input type="number" class="form-control" id="id_thumb_height" name="thumb_height" min="0" max="9999" maxlength="5" placeholder="<?php echo getLang('Size')?>">
					</div>
					&nbsp;&nbsp;
					<div class="switch-group on">
						<input type="hidden" name="thumb_option" value="1">
						<div class="switch-control">
							<span class="switch switch-handle-on"><?php echo getLang('use')?></span>
							<span class="switch switch-label"><?php echo getLang('thumb_fit')?></span>
							<span class="switch switch-handle-off"><?php echo getLang('notuse')?></span>
						</div>
					</div>
				</div>
				<p class="help-block"><?php echo getLang('desc_thumbnail')?></p>
			</div>
			<div class="form-group">
				<label><?php echo getLang('point')?></label>
				<div class="form-inline">
					<div class="input-group">
						<label class="input-group-addon" for="id_read_point"><?php echo getLang('view')?></label>
						<input type="number" class="form-control" id="id_read_point" name="point_view" min="-9999" max="9999" maxlength="5" placeholder="<?php echo getLang('point')?>">
					</div>
					&nbsp;&nbsp;<div class="input-group">
						<label class="input-group-addon" for="id_write_point"><?php echo getLang('write')?></label>
						<input type="number" class="form-control" id="id_write_point" name="point_write" min="-9999" max="9999" maxlength="5" placeholder="<?php echo getLang('point')?>">
					</div>
					&nbsp;&nbsp;<div class="input-group">
						<label class="input-group-addon" for="id_reply_point"><?php echo getLang('reply')?></label>
						<input type="number" class="form-control" id="id_reply_point" name="point_reply" min="-9999" max="9999" maxlength="5" placeholder="<?php echo getLang('point')?>">
					</div>
					&nbsp;&nbsp;<div class="input-group">
						<label class="input-group-addon" for="id_download_point"><?php echo getLang('download')?></label>
						<input type="number" class="form-control" id="id_download_point" name="point_download" min="-9999" max="9999" maxlength="5" placeholder="<?php echo getLang('point')?>">
					</div>
				</div>
				<p class="help-block"><?php echo getLang('desc_point')?></p>
			</div>
			<hr>
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
			<div class="form-group">
				<label><?php echo getLang('upload')?></label>
				<div class="radio-group">
					<input type="hidden" name="grant_upload" value="0">
					<div class="radio-control radio-xs">
						<span class="radio active" data-value="0"><?php echo getLang('all')?></span>
						<span class="radio" data-value="1"><?php echo getLang('member')?></span>
						<span class="radio" data-value="m"><?php echo getLang('admin')?></span>
					</div>
				 </div>
			</div>
			<div class="form-group">
				<label><?php echo getLang('download')?></label>
				<div class="radio-group">
					<input type="hidden" name="grant_download" value="0">
					<div class="radio-control radio-xs">
						<span class="radio active" data-value="0"><?php echo getLang('all')?></span>
						<span class="radio" data-value="1"><?php echo getLang('member')?></span>
						<span class="radio" data-value="m"><?php echo getLang('admin')?></span>
					</div>
				 </div>
			</div>
		</div>
		<div class="modal-footer clearfix">
		<button type="button" class="btn btn-danger pull-left hide" data-act-change="admin.deleteBoard"><?php echo getLang('permanent_delete')?></button>
		<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo getLang('close')?></button>
		<button type="submit" class="btn btn-success"><i class="glyphicon glyphicon-ok" aria-hidden="true"></i> <?php echo getLang('save')?></button>
	  </div>
	</form>
  </div>
</div>

<?php
/* End of file board.php */
/* Location: ./module/admin/board.php */