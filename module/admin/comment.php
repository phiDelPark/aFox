<?php
	if(!defined('__AFOX__')) exit();

	$search = empty($_DATA['search'])?null:'%'.$_DATA['search'].'%';
	$cmt_list = getDBList(_AF_COMMENT_TABLE_,[
		'OR' =>empty($search)?[]:['rp_content{LIKE}'=>$search]
	],'rp_regdate desc', empty($_DATA['page']) ? 1 : $_DATA['page'], 20);
?>

<table class="table table-hover table-nowrap">
<thead>
	<tr>
		<th>#<?php echo getLang('board')?></th>
		<th><?php echo getLang('title')?></th>
		<th><?php echo getLang('status')?></th>
		<th><?php echo getLang('secret')?></th>
		<th><?php echo getLang('author')?></th>
		<th><?php echo getLang('date')?></th>
	</tr>
</thead>
<tbody>

<?php
	$total_page = 0;
	$current_page = 1;

	if(!empty($cmt_list['error'])) {
		echo showMessage($cmt_list['message'], $cmt_list['error']);
	} else {
		$current_page = $cmt_list['current_page'];
		$total_page = $cmt_list['total_page'];

		foreach ($cmt_list['data'] as $key => $value) {
			echo '<tr class="afox-list-item" data-exec-ajax="board.getComment" data-ajax-param="rp_srl,'.$value['rp_srl'].'" data-modal-target="#comment_modal"><th scope="row">'.$value['rp_srl'].'</th>';
			echo '<td class="col-md-10">'.escapeHtml(cut_str(strip_tags($value['rp_content']),50)).'</td>';
			echo '<td>'.($value['rp_status']?$value['rp_status']:'-').'</td>';
			echo '<td>'.($value['rp_secret']?'Y':'N').'</td>';
			echo '<td>'.escapeHtml(strip_tags($value['mb_nick'])).'</td>';
			echo '<td>'.date('Y/m/d', strtotime($value['rp_regdate'])).'</td></tr>';
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
  <ul class="pagination pull-right">
	<li<?php echo $current_page <= 1 ? ' class="disabled"' : ''?>><a href="<?php echo  $current_page <= 1 ? '#' : getUrl('page',$current_page-1)?>" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>

<?php
	for ($i=1; $i <= $total_page; $i++) {
		echo '<li'.($current_page == $i ? ' class="active"' : '').'><a href="'.getUrl('page',$i).'">'.$i.'</a></li>';
	}
?>

	<li<?php echo $current_page >= $total_page ? ' class="disabled"' : ''?>><a href="<?php echo $current_page >= $total_page ? '#' : getUrl('page',$current_page+1)?>" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>
  </ul>
</nav>

<div id="comment_modal" class="modal fade bs-admin-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
	<form class="modal-content" method="post" autocomplete="off">
	<input type="hidden" name="success_return_url" value="<?php echo getUrl()?>" />
	<input type="hidden" name="rp_srl" value="" />

	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="myModalLabel"><?php echo getLang('comment')?></h4>
	  </div>
	  <div class="modal-body">
		<div class="form-group">
			<?php dispEditor(
					'rp_content',
					'',
					[
						'file'=>[0,'',0],
						'toolbar'=>array(getLang('content'), ['rp_type'=>['1', ['TEXT'=>'0','MKDW'=>'1','HTML'=>'2']],'rp_secret'=>[false,'Secret']])
					]
				);
			?>
		</div>
	  </div>
	  <div class="modal-footer clearfix">
		<button type="button" class="btn btn-danger pull-left" data-act-change="board.deleteComment"><?php echo getLang('permanently_delete')?></button>
		<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo getLang('close')?></button>
		<button type="submit" class="btn btn-success min-width-150"><i class="glyphicon glyphicon-ok" aria-hidden="true"></i> <?php echo getLang('save')?></button>
	  </div>
	</form>
  </div>
</div>

<?php
/* End of file comment.php */
/* Location: ./module/admin/comment.php */