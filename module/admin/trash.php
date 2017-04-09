<?php
	if(!defined('__AFOX__')) exit();

	$search = empty($_DATA['search'])?null:'%'.$_DATA['search'].'%';
	$doc_list = getDBList(_AF_DOCUMENT_TABLE_,[
		'md_id'=>'_AFOXtRASH_',
		'OR' =>empty($search)?[]:['wr_title{LIKE}'=>$search, 'wr_content{LIKE}'=>$search]
	],'wr_regdate desc', empty($_DATA['page']) ? 1 : $_DATA['page'], 20);
?>

<table class="table table-hover table-nowrap">
<thead class="table-nowrap">
	<tr>
		<th class="col-xs-1">#</th>
		<th><?php echo getLang('title')?></th>
		<th class="col-xs-1"><?php echo getLang('status')?></th>
		<th class="col-xs-1 hidden-xs hidden-sm"><?php echo getLang('secret')?></th>
		<th class="col-xs-1"><?php echo getLang('author')?></th>
		<th class="col-xs-1 hidden-xs hidden-sm"><?php echo getLang('date')?></th>
		<th class="col-xs-1"><?php echo getLang('removed_date')?></th>
	</tr>
</thead>
<tbody>

<?php
	$end_page = $total_page = 0;
	$start_page = $current_page = 1;

	if(!empty($doc_list['error'])) {
		echo showMessage($doc_list['message'], $doc_list['error']);
	} else {
		$current_page = $doc_list['current_page'];
		$total_page = $doc_list['total_page'];
		$start_page = $doc_list['start_page'];
		$end_page = $doc_list['end_page'];

		foreach ($doc_list['data'] as $key => $value) {
			echo '<tr class="afox-list-item" data-exec-ajax="board.getDocument" data-ajax-param="wr_srl,'.$value['wr_srl'].'" data-modal-target="#trash_modal"><th scope="row">'.$value['wr_updater'].'</th>';
			echo '<td>'.escapeHtml(cutstr(strip_tags($value['wr_title']),50)).'</td>';
			echo '<td>'.($value['wr_status']?$value['wr_status']:'-').'</td>';
			echo '<td class="hidden-xs hidden-sm">'.($value['wr_secret']?'Y':'N').'</td>';
			echo '<td>'.escapeHtml($value['mb_nick'],true).'</td>';
			echo '<td class="hidden-xs hidden-sm">'.date('Y/m/d', strtotime($value['wr_regdate'])).'</td>';
			echo '<td>'.date('Y/m/d', strtotime($value['wr_update'])).'</td></tr>';
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

<div id="trash_modal" class="modal fade bs-admin-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
	<form class="modal-content" method="post" autocomplete="off">
	<input type="hidden" name="success_return_url" value="<?php echo getUrl()?>" />
	<input type="hidden" name="md_id" value="" />
	<input type="hidden" name="wr_srl" value="" />

	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="myModalLabel"><?php echo getLang('recycle_bin')?></h4>
	  </div>
	  <div class="modal-body">
		<div class="form-group clearfix">
			<div class="pull-left">
				<label><?php echo getLang('nickname')?></label>
				<div class="form-inline">
					<input type="text" class="form-control" name="mb_nick" maxlength="20" disabled="disabled">
				</div>
			</div>
			<div class="pull-right">
				<label><?php echo getLang('regdate')?></label>
				<div class="form-inline">
					<input type="text" name="wr_regdate" class="form-control" style="width:160px" disabled="disabled">
				</div>
			</div>
		</div>
		<div class="form-group clearfix">
			<div class="pull-left">
				<label><?php echo getLang('%s %s',['module','id'])?></label>
				<div class="form-inline">
					<input type="text" class="form-control" name="wr_updater" maxlength="11" disabled="disabled">
				</div>
			</div>
			<div class="pull-right">
				<label><?php echo getLang('removed_date')?></label>
				<div class="form-inline">
					<input type="text" name="wr_update" class="form-control" style="width:160px" disabled="disabled">
				</div>
			</div>
		</div>
		<div class="form-group" style="display:none">
			<label for="id_wr_category"><?php echo getLang('category')?></label>
			<input type="text" name="wr_category" class="form-control" id="id_wr_category" maxlength="255" readonly="readonly">
		</div>
		<div class="form-group">
			<label for="id_wr_title"><?php echo getLang('title')?></label>
			<input type="text" name="wr_title" class="form-control" id="id_wr_title" maxlength="255" readonly="readonly">
		</div>
		<div class="form-group">
			<div class="form-inline">
				<div class="switch-group on" readonly="readonly">
					<input type="hidden" name="wr_type" value="1">
					<div class="switch-control switch-xs">
						<span class="switch switch-handle-on">MKDW</span>
						<span class="switch switch-label"><?php echo getLang('type')?></span>
						<span class="switch switch-handle-off">HTML</span>
					</div>
				</div>&nbsp;&nbsp;&nbsp;
				<div class="switch-group" readonly="readonly">
					<input type="hidden" name="wr_secret" value="0">
					<div class="switch-control switch-xs">
						<span class="switch switch-handle-on"><?php echo getLang('use')?></span>
						<span class="switch switch-label"><?php echo getLang('secret')?></span>
						<span class="switch switch-handle-off"><?php echo getLang('notuse')?></span>
					</div>
				</div>
			</div>
		</div>
			<div class="form-group">
				<label for="id_wr_content"><?php echo getLang('content')?></label>
				<textarea class="form-control min-height-200 vresize" name="wr_content" id="id_wr_content" readonly="readonly"></textarea>
			</div>
	  </div>
	  <div class="modal-footer clearfix">
		<button type="button" class="btn btn-danger pull-left" data-act-change="board.deleteDocument" data-add-param="is_empty,1"><?php echo getLang('permanent_delete')?></button>
		<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo getLang('close')?></button>
		<button type="submit" class="btn btn-warning"><i class="glyphicon glyphicon-refresh" aria-hidden="true"></i> <?php echo getLang('restore')?></button>
	  </div>
	</form>
  </div>
</div>

<?php
/* End of file trash.php */
/* Location: ./module/admin/trash.php */