<?php
	if(!defined('__AFOX__')) exit();

	$search = empty($_DATA['search'])?null:'%'.$_DATA['search'].'%';
	$doc_list = getDBList(_AF_DOCUMENT_TABLE_,[
		'md_id{<>}'=>'_AFOXtRASH_',
		'OR' =>empty($search)?[]:['wr_title{LIKE}'=>$search, 'wr_content{LIKE}'=>$search]
	],'wr_regdate desc', empty($_DATA['page']) ? 1 : $_DATA['page'], 20);
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

	if(!empty($doc_list['error'])) {
		echo showMessage($doc_list['message'], $doc_list['error']);
	} else {
		$current_page = $doc_list['current_page'];
		$total_page = $doc_list['total_page'];

		foreach ($doc_list['data'] as $key => $value) {
			echo '<tr class="afox-list-item" data-exec-ajax="board.getDocument" data-ajax-param="wr_srl,'.$value['wr_srl'].',with_module_config,1,with_file_list,1" data-modal-target="#document_modal"><th scope="row">'.$value['md_id'].'</th>';
			echo '<td class="col-md-10">'.escapeHtml(cut_str(strip_tags($value['wr_title']),50)).(empty($value['wr_reply'])?'':' (<small>'.$value['wr_reply'].'</small>)').'</td>';
			echo '<td>'.($value['wr_status']?$value['wr_status']:'-').'</td>';
			echo '<td>'.($value['wr_secret']?'Y':'N').'</td>';
			echo '<td>'.escapeHtml(strip_tags($value['mb_nick'])).'</td>';
			echo '<td>'.date('Y/m/d', strtotime($value['wr_regdate'])).'</td></tr>';
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

<div id="document_modal" class="modal fade bs-admin-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
	<form class="modal-content" onsubmit="return false" method="post" autocomplete="off">
	<input type="hidden" name="success_return_url" value="<?php echo getUrl()?>" />
	<input type="hidden" name="success_return_values" value="*" />
	<input type="hidden" name="md_id" value="" />
	<input type="hidden" name="wr_srl" value="" />

	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="myModalLabel"><?php echo getLang('document')?></h4>
	  </div>
	  <div class="modal-body">
		<div class="form-group" style="display:none">
			<select name="wr_category" class="form-control">
			<option value=""><?php echo getLang('category')?></option>
			</select>
		</div>
		<div class="form-group">
			<label for="id_wr_title"><?php echo getLang('title')?></label>
			<input type="text" name="wr_title" class="form-control" id="id_wr_title" maxlength="255">
		</div>
		<div class="form-group">
			<?php dispEditor(
					'wr_content',
					'',
					[
						'file'=>[99999,'',0],
						'toolbar'=>array(getLang('content'), ['wr_type'=>['1', ['TEXT'=>'0','MKDW'=>'1','HTML'=>'2']],'wr_secret'=>[false,'Secret']])
					]
				);
			?>
		</div>
	  </div>
	  <div class="modal-footer clearfix">
		<button type="button" class="btn btn-warning pull-left" data-act-change="board.deleteDocument"><?php echo getLang('recycle_bin')?></button>
		<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo getLang('close')?></button>
		<button type="submit" class="btn btn-success min-width-150"><i class="glyphicon glyphicon-ok" aria-hidden="true"></i> <?php echo getLang('save')?></button>
	  </div>
	</form>
  </div>
</div>

<?php
/* End of file document.php */
/* Location: ./module/admin/document.php */