<?php
	if(!defined('__AFOX__')) exit();

	$cd = _AF_COMMENT_TABLE_;
	$dd = _AF_DOCUMENT_TABLE_;

	$search = '';
	if(!empty($_DATA['search'])) {
		$tmp = $_DATA['search'];
		$schkeys = ['content'=>'rp_content','nick'=>'mb_nick','date'=>'rp_regdate'];
		$ss = explode(':', $tmp);
		if(count($ss)>1 && !empty($schkeys[$ss[0]])) {
			$tmp = trim(implode(':', array_slice($ss,1)));
			if(!empty($tmp)) $search = $cd.'.'.$schkeys[$ss[0]].' LIKE \''.DB::escape(($ss[0]==='date'?'':'%').$tmp.'%').'\'';
		} else {
			$search = $cd.'.rp_content LIKE \''.DB::escape('%'.$_DATA['search'].'%').'\'';
		}
	}

	$category = $dd.(empty($_DATA['category'])?'.md_id <> \'_AFOXtRASH_\'':'.md_id = \''.DB::escape($_DATA['category']).'\'');
	$where = empty($search)&&empty($category) ? '1' : '('.$category.(empty($search)||empty($category) ? '' : ' AND ').$search.')';
	$page = (int)isset($_DATA['page']) ? (($_DATA['page'] < 1) ? 1 : $_DATA['page']) : 1;
	$count = 20;
	$start = (($page - 1) * $count);
	$cmt_list = DB::query("SELECT SQL_CALC_FOUND_ROWS $cd.*, $dd.md_id FROM $cd INNER JOIN $dd ON $dd.wr_srl = $cd.wr_srl WHERE $where ORDER BY $cd.rp_regdate DESC LIMIT $start,$count", true);
	if($error = DB::error()) $error = set_error($error->getMessage(),$error->getCode());
	$cmt_list = setDataListInfo($cmt_list, $page, $count, DB::foundRows());
?>

<table class="table">
<thead>
	<tr>
		<th scope="col"><a href="#DataManageAction"><?php echo getLang('data_manage')?></a></th>
		<th scope="col" class="text-wrap"><?php echo getLang('title')?></th>
		<th scope="col"><?php echo getLang('status')?></th>
		<th scope="col"><?php echo getLang('author')?></th>
		<th scope="col" class="text-end"><?php echo getLang('date')?></th>
	</tr>
</thead>
<tbody>

<?php
	$end_page = $total_page = 0;
	$start_page = $current_page = 1;

	if($error) {
		messageBox($error['message'], $error['error'], false);
	} else {
		$current_page = $cmt_list['current_page'];
		$total_page = $cmt_list['total_page'];
		$start_page = $cmt_list['start_page'];
		$end_page = $cmt_list['end_page'];

		foreach ($cmt_list['data'] as $key => $value) {
			echo '<tr><th scope="row">'.$value['md_id'].'</th>';
			echo '<td class="text-wrap">'.escapeHtml(cutstr(strip_tags($value['rp_content']),50)).'</td>';
			echo '<td>'.escapeHtml($value['mb_nick'],true).'</td>';
			echo '<td>'.($value['wr_secret']?'S/':'--/').($value['rp_status']?$value['rp_status']:'--').'</td>';
			echo '<td>'.date('Y/m/d', strtotime($value['rp_regdate'])).'</td></tr>';
		}
	}
?>

</tbody>
</table>

<nav class="d-flex w-100 justify-content-between mt-4" aria-label="Page navigation of the list">
	<form class="form-inline search-form" action="<?php echo getUrl('') ?>" method="get">
		<input type="hidden" name="admin" value="<?php echo $_DATA['disp'] ?>">
		<div class="input-group mb-3">
		<input type="text" name="search" value="<?php echo empty($_DATA['search'])?'':$_DATA['search'] ?>" class="form-control" style="max-width:160px" placeholder="<?php echo getLang('search_word') ?>" required>
		<button class="btn btn-default btn-outline-secondary" type="submit"><i class="glyphicon glyphicon-search" aria-hidden="true"></i> <?php echo getLang('search') ?></button>
		<?php if(!empty($_DATA['search'])) {?><button class="btn btn-default btn-outline-secondary" type="button" onclick="location.replace('<?php echo getUrl('search','') ?>')"><?php echo getLang('cancel') ?></button><?php }?>
		</div>
	</form>
	<div id="pageNavigation">
	<?php if($start_page>10) echo '<a class="btn btn-sm btn-outline-primary rounded-pill" href="'.getUrl('page',$start_page-10).'">&laquo;</a>' ?>
	<a class="btn btn-sm rounded-pill btn-outline-<?php echo $current_page <= 1 ? 'secondary disabled" aria-disabled="true' : 'primary'?>" href="<?php echo  $current_page <= 1 ? '#' : getUrl('page',$current_page-1)?>" aria-label="Previous"><span aria-hidden="true">&lsaquo;</span> <?php echo getLang('previous') ?></a>
	<a class="d-md-none btn btn-sm btn-outline-secondary rounded-pill disabled" aria-disabled="true"><?php echo $current_page.' / '.$total_page?></a>
	<?php for ($i=$start_page; $i <= $end_page; $i++) echo '<a class="d-none d-md-inline-block btn btn-sm btn-outline-primary rounded-pill'.($current_page == $i ? ' active" aria-current="page' : '').'" href="'.getUrl('page',$i).'">'.$i.'</a>' ?>
	<a class="btn btn-sm rounded-pill btn-outline-<?php echo $current_page >= $total_page ? 'secondary disabled" aria-disabled="true' : 'primary'?>" href="<?php echo $current_page >= $total_page ? '#' : getUrl('page',$current_page+1)?>" aria-label="Next"><?php echo getLang('next') ?> <span aria-hidden="true">&rsaquo;</span></a>
	<?php if(($total_page-$end_page)>0) echo '<a class="btn btn-sm btn-outline-primary rounded-pill" href="'.getUrl('page',$end_page+1).'">&raquo;</a>' ?>
	</div>
</nav>

<div id="comment_modal" class="modal fade bs-admin-modal-lg" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
	<form class="modal-content" method="post" autocomplete="off">
	<input type="hidden" name="success_return_url" value="<?php echo getUrl()?>" />
	<input type="hidden" name="rp_srl" value="" />
	<input type="hidden" name="wr_srl" value="" />

	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title"><?php echo getLang('comment')?></h4>
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
					<input type="text" name="rp_regdate" class="form-control" style="width:160px" disabled="disabled">
				</div>
			</div>
		</div>
		<div class="form-group">
			<label for="id_wr_title"><?php echo getLang('document')?></label>
			<div class="input-group">
				<input type="text" class="form-control" name="wr_title" id="id_wr_title" maxlength="255" disabled>
				<span class="input-group-btn">
					<button class="btn btn-info document_goto" type="button" title="<?php echo getLang('goto')?>..."><i class="glyphicon glyphicon-share-alt" aria-hidden="true"></i></button>
				</span>
			</div>
		</div>
		<div class="form-group">
			<?php displayEditor(
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
		<button type="button" class="btn btn-danger pull-left" data-act-change="board.deleteComment"<?php echo isAdmin()?'':' disabled'?>><?php echo getLang('permanent_delete')?></button>
		<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo getLang('close')?></button>
		<button type="submit" class="btn btn-success"><i class="glyphicon glyphicon-ok" aria-hidden="true"></i> <?php echo getLang('save')?></button>
	  </div>
	</form>
  </div>
</div>

<script>
	function data_selected_delete() {
		if (confirm($_LANG['confirm_select_delete'].sprintf([$_LANG['comment']]))) {
		var $a = jQuery('#ADM_DEFAULT_MODULE .table'),
			data = {};
			srls = [];
			$a.find('.data_selecter:checked').each(function(i) {
				srls[i] = jQuery(this).val();
			});
			if (srls.length < 1) {
				alert($_LANG['warning_selected'].sprintf([$_LANG['comment']]));
				return false;
			}
			data['rp_srls'] = srls;
			data['success_return_url'] = current_url;
			exec_ajax('admin.deleteComments', data);
		}
		return false;
	}
</script>

<?php
/* End of file comment.php */
/* Location: ./module/admin/comment.php */
