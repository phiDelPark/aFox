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
	$cmt_list = setDataListInfo($cmt_list, DB::found(), $page, $count);
?>

<table class="table table-hover table-nowrap">
<thead>
	<tr>
		<th class="col-xs-1"><i class="glyphicon glyphicon-option-vertical" aria-hidden="true"></i>
			<a href="#DataManageAction"><?php echo getLang('data_manage')?></a></th>
		<th><span class="th_title"><?php echo getLang('title')?></span>
		<span class="data_controler" style="display:none"><input type="checkbox" style="margin-right:5px" class="data_all_selecter"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i> <a href="#" onclick="return data_selected_delete()"><?php echo getLang('data_delete')?></a></span></th>
		<th class="col-xs-1"><?php echo getLang('status')?></th>
		<th class="col-xs-1 hidden-xs hidden-sm"><?php echo getLang('secret')?></th>
		<th class="col-xs-1"><?php echo getLang('author')?></th>
		<th class="col-xs-1"><?php echo getLang('date')?></th>
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
			echo '<tr class="afox-list-item" data-exec-ajax="board.getComment" data-ajax-param="rp_srl,'.$value['rp_srl'].'" data-modal-target="#comment_modal"><th scope="row"><a href="'.getUrl('category',$value['md_id']).'" except-exec-event>'.$value['md_id'].'</a></th>';
			echo '<td class="title"><input type="checkbox" value="'.$value['rp_srl'].'" class="data_selecter" style="display:none;margin-right:5px" except-exec-event>'.escapeHtml(cutstr(strip_tags($value['rp_content']),50)).'</td>';
			echo '<td>'.($value['rp_status']?$value['rp_status']:'-').'</td>';
			echo '<td class="hidden-xs hidden-sm">'.($value['rp_secret']?'Y':'N').'</td>';
			echo '<td>'.escapeHtml($value['mb_nick'],true).'</td>';
			echo '<td>'.date('Y/m/d', strtotime($value['rp_regdate'])).'</td></tr>';
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
		<?php if(!empty($_DATA['category'])) {?><input type="hidden" name="category" value="<?php echo $_DATA['category'] ?>"><?php }?>
		<input type="text" name="search" value="<?php echo empty($_DATA['search'])?'':$_DATA['search'] ?>" class="form-control" placeholder="<?php echo getLang('search_text') ?>" required>
		<button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search" aria-hidden="true"></i> <?php echo getLang('search') ?></button>
		<?php if(!empty($_DATA['search'])||!empty($_DATA['category'])) {?><button class="btn btn-default" type="button" onclick="location.replace('<?php echo getUrl('search','','category','') ?>')"><?php echo getLang('cancel') ?></button><?php }?>
	</form></li>
	</ul>
</nav>

<div id="comment_modal" class="modal fade bs-admin-modal-lg" tabindex="-1" role="dialog" aria-labelledby="adminCommentModalTitle">
  <div class="modal-dialog modal-lg" role="document">
	<form class="modal-content" method="post" autocomplete="off">
	<input type="hidden" name="success_return_url" value="<?php echo getUrl()?>" />
	<input type="hidden" name="rp_srl" value="" />
	<input type="hidden" name="wr_srl" value="" />

	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="adminCommentModalTitle"><?php echo getLang('comment')?></h4>
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
