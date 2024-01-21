<?php
	if(!defined('__AFOX__')) exit();

	$search = '';
	$_POST['trash'] = empty($_POST['trash']) ? null : $_POST['trash'];

	if($_POST['trash'] == 'comment') {
		$cd = _AF_COMMENT_TABLE_;
		$dd = _AF_DOCUMENT_TABLE_;
		if(!empty($_POST['search'])) {
			$schkeys = ['content'=>'rp_content','nick'=>'mb_nick','date'=>'rp_regdate'];
			$search = $cd.'.rp_content LIKE \''.DB::escape('%'.$_POST['search'].'%').'\'';
		}
	} else if($_POST['trash'] == 'file') {
		$cd = _AF_FILE_TABLE_;
		$dd = _AF_DOCUMENT_TABLE_;
		if(!empty($_POST['search'])) {
			$schkeys = ['name'=>'mf_name','desc'=>'mf_description','type'=>'mf_type','date'=>'mf_regdate'];
			$search = '('.$cd.'.mf_name LIKE \''.DB::escape('%'.$_POST['search'].'%').'\' OR '.$cd.'.mf_description LIKE \''.DB::escape('%'.$_POST['search'].'%').'\')';
		}
	} else {
		$cd = _AF_DOCUMENT_TABLE_;
		$dd = _AF_DOCUMENT_TABLE_;
		if(!empty($_POST['search'])) {
			$schkeys = ['title'=>'wr_title','content'=>'wr_content','nick'=>'mb_nick','tags'=>'wr_tags','date'=>'wr_regdate'];
			$search = '(wr_title LIKE \''.DB::escape('%'.$_POST['search'].'%').'\' OR wr_content LIKE \''.DB::escape('%'.$_POST['search'].'%').'\')';
		}
	}

	if(!empty($_POST['search'])) {
		$tmp = $_POST['search'];
		$ss = explode(':', $tmp);
		if(count($ss)>1 && !empty($schkeys[$ss[0]])) {
			$tmp = trim(implode(':', array_slice($ss,1)));
			if(!empty($tmp)) $search = $cd.'.'.$schkeys[$ss[0]].' LIKE \''.DB::escape(($ss[0]==='date'?'':'%').$tmp.'%').'\'';
		}
	}

	$category = $dd.'.md_id = \'_AFOXtRASH_\''.(empty($_POST['category'])?'':' AND wr_updater = \''.DB::escape($_POST['category']).'\'');
	$where = empty($search)&&empty($category) ? '1' : '('.$category.(empty($search)||empty($category) ? '' : ' AND ').$search.')';
	$page = (int)isset($_POST['page']) ? (($_POST['page'] < 1) ? 1 : $_POST['page']) : 1;
	$count = 20;
	$start = (($page - 1) * $count);

	if($_POST['trash'] == 'comment') {
		$query = "SELECT SQL_CALC_FOUND_ROWS $cd.*, $dd.md_id, $dd.wr_srl, $dd.wr_updater, $dd.wr_update, $cd.rp_content AS wr_title, $cd.rp_status AS wr_status, $cd.rp_secret AS wr_secret, $cd.rp_regdate AS wr_regdate FROM $cd INNER JOIN $dd ON $dd.wr_srl = $cd.wr_srl WHERE $where ORDER BY $cd.rp_regdate DESC LIMIT $start,$count";
	} else if($_POST['trash'] == 'file') {
		$query = "SELECT SQL_CALC_FOUND_ROWS $cd.*, $dd.md_id, $dd.wr_srl, $dd.wr_updater, $dd.wr_update, $cd.mf_name AS wr_title, $cd.mf_download AS wr_status, $cd.mf_regdate AS wr_regdate FROM $cd INNER JOIN $dd ON $dd.wr_srl = $cd.mf_target WHERE $where ORDER BY $cd.mf_regdate DESC LIMIT $start,$count";
	} else {
		$query = "SELECT SQL_CALC_FOUND_ROWS * FROM $cd WHERE $where ORDER BY wr_regdate DESC LIMIT $start,$count";
	}

	$trash_list = DB::query($query, true);
	if($error = DB::error()) $error = set_error($error->getMessage(),$error->getCode());
	$trash_list = setDataListInfo($trash_list, $page, $count, DB::foundRows());
?>

<button type="button" class="btn btn-primary mb-3" style="width:250px" data-toggle="modal.clone" data-target=".bs-admin-modal-lg"<?php echo isAdmin()?'':' disabled'?>><?php echo getLang('empty_recycle_bin')?></button>

<ul class="nav nav-tabs">
  <li class="nav-item">
    <a class="nav-link<?php echo ($_POST['trash']!='comment'&&$_POST['trash']!='file')?' active" aria-current="page':''?>" href="<?php echo getUrl('trash','document')?>"><?php echo getLang('document')?></a>
  </li>
  <li class="nav-item">
    <a class="nav-link<?php echo ($_POST['trash']=='comment')?' active" aria-current="page':''?>" href="<?php echo getUrl('trash','comment')?>"><?php echo getLang('comment')?></a>
  </li>
  <li class="nav-item">
    <a class="nav-link<?php echo ($_POST['trash']=='file')?' active" aria-current="page"':''?>" href="<?php echo getUrl('trash','file')?>"><?php echo getLang('file')?></a>
  </li>
</ul>

<table class="table">
<thead>
	<tr>
		<th scope="col">#</th>
		<th scope="col" class="text-wrap"><?php echo getLang('title')?></th>
		<th scope="col"><?php echo ($_POST['trash'] == 'file'?'-':getLang('author'))?></th>
		<th scope="col"><?php echo ($_POST['trash'] == 'file'?getLang('download'):getLang('status'))?></th>
		<th scope="col"><?php echo getLang('date')?></th>
		<th scope="col"><?php echo getLang('removed_date')?></th>
	</tr>
</thead>
<tbody>

<?php
	$end_page = $total_page = 0;
	$start_page = $current_page = 1;

	if($error) {
		messageBox($error['message'], $error['error'], false);
	} else {
		$current_page = $trash_list['current_page'];
		$total_page = $trash_list['total_page'];
		$start_page = $trash_list['start_page'];
		$end_page = $trash_list['end_page'];



		foreach ($trash_list['data'] as $key => $value) {
			if($_POST['trash'] == 'comment') {
				$tmp = ' data-exec-ajax="board.getComment" data-ajax-param="rp_srl,'.$value['rp_srl'].'" data-modal-target="#comment_modal"';
			} else if($_POST['trash'] == 'file') {
				$tmp = ' data-exec-ajax="admin.getFile" data-ajax-param="mf_srl,'.$value['mf_srl'].'" data-modal-target="#file_modal"';
			} else {
				$tmp = ' data-exec-ajax="board.getDocument" data-ajax-param="wr_srl,'.$value['wr_srl'].'" data-modal-target="#trash_modal"';
			}
			echo '<tr><th scope="row"><a href="'.getUrl('category',$value['wr_updater']).'">'.$value['wr_updater'].'</a></th>';
			echo '<td class="text-wrap">'.escapeHtml(cutstr(strip_tags($value['wr_title']),50)).(empty($value['wr_reply'])?'':' (<small>'.$value['wr_reply'].'</small>)').'</td>';
			echo '<td>'.($_POST['trash'] == 'file'?'-':escapeHtml($value['mb_nick'],true)).'</td>';
			echo '<td>'.($value['wr_secret']?'S/':'--/').($value['wr_status']?$value['wr_status']:'--').'</td>';
			echo '<td>'.date('Y/m/d', strtotime($value['wr_regdate'])).'</td>';
			echo '<td>'.date('Y/m/d', strtotime($value['wr_update'])).'</td></tr>';
		}
	}
?>

</tbody>
</table>

<div class="d-flex w-100 justify-content-between mt-4">
	<form action="<?php echo getUrl('') ?>" method="get">
		<input type="hidden" name="admin" value="<?php echo $_POST['disp'] ?>">
		<div class="input-group mb-3">
			<label class="input-group-text bg-transparent" for="search"><svg class="bi" aria-hidden="true"><use href="<?php echo _AF_URL_?>module/admin/bi-icons.svg#search"/></svg></label>
			<input type="text" name="search" id="search" value="<?php echo empty($_POST['search'])?'':$_POST['search'] ?>" class="form-control" style="max-width:140px;border-left:0" required>
			<button class="btn btn-default btn-outline-control" style="border-color:var(--bs-border-color)" type="submit"><?php echo getLang('search') ?></button>
			<?php if(!empty($_POST['search'])) {?><button class="btn btn-default btn-outline-control" type="button" onclick="location.replace('<?php echo getUrl('search','') ?>')"><?php echo getLang('cancel') ?></button><?php }?>
		</div>
	</form>
	<nav aria-label="Page navigation of the list">
	<ul class="pagination">
		<?php if($start_page>10) echo '<li class="page-item"><a class="page-link" href="'.getUrl('page',$start_page-10).'">&laquo;</a></li>' ?>
		<li class="page-item"><a class="page-link <?php echo $current_page <= 1 ? ' disabled" aria-disabled="true' : ''?>" href="<?php echo  $current_page <= 1 ? '#' : getUrl('page',$current_page-1)?>" aria-label="Previous"><span aria-hidden="true">&lsaquo;</span> <?php echo getLang('previous') ?></a></li>
		<li class="page-item d-md-none"><a class="page-link disabled" aria-disabled="true"><?php echo $current_page.' / '.$total_page?></a></li>
		<?php for ($i=$start_page; $i <= $end_page; $i++) echo '<li class="page-item d-none d-md-inline-block"><a class="page-link'.($current_page == $i ? ' active" aria-current="page' : '').'" href="'.getUrl('page',$i).'">'.$i.'</a></li>' ?>
		<li class="page-item"><a class="page-link<?php echo $current_page >= $total_page ? ' disabled" aria-disabled="true' : ''?>" href="<?php echo $current_page >= $total_page ? '#' : getUrl('page',$current_page+1)?>" aria-label="Next"><?php echo getLang('next') ?> <span aria-hidden="true">&rsaquo;</span></a></li>
		<?php if(($total_page-$end_page)>0) echo '<li class="page-item"><a class="page-link" href="'.getUrl('page',$end_page+1).'">&raquo;</a></li>' ?>
	</ul>
	</nav>
</div>

<?php if($_POST['trash'] == 'comment') {?>
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
			<input type="text" class="form-control" name="wr_title" id="id_wr_title" maxlength="255" disabled>
		</div>
		<div class="form-group">
			<textarea class="form-control mh-20 vresize" name="rp_content" id="id_rp_content" readonly="readonly"></textarea>
		</div>
	  </div>
	  <div class="modal-footer clearfix">
		<button type="button" class="btn btn-danger pull-left" data-act-change="board.deleteComment"<?php echo isAdmin()?'':' disabled'?>><?php echo getLang('permanent_delete')?></button>
		<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo getLang('close')?></button>
	  </div>
	</form>
  </div>
</div>
<?php } else if($_POST['trash'] == 'file') {?>
<div id="file_modal" class="modal fade bs-admin-modal-lg" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
	<form class="modal-content" method="post" autocomplete="off">
	<input type="hidden" name="success_return_url" value="<?php echo getUrl()?>" />
	<input type="hidden" name="md_id" value="" />
	<input type="hidden" name="mf_srl" value="" />
	<input type="hidden" name="mf_target" value="" />
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title"><?php echo getLang('file')?></h4>
		</div>
		<div class="modal-body">
			<div class="form-group clearfix">
				<div class="pull-left">
					<label><?php echo getLang('%s %s',['module','id'])?></label>
					<div class="form-inline">
						<input type="text" class="form-control" name="md_id" maxlength="11" disabled="disabled">
					</div>
				</div>
				<div class="pull-right">
					<label><?php echo getLang('regdate')?></label>
					<div class="form-inline">
						<input type="text" name="mf_regdate" class="form-control" style="width:160px" disabled="disabled" title="<?php echo getLang('regdate')?>">
					</div>
				</div>
			</div>
			<div class="form-group clearfix">
				<div class="pull-left">
					<label><?php echo getLang('type')?></label>
					<div class="form-inline">
						<input type="text" class="form-control" name="mf_type" maxlength="11" disabled="disabled">
					</div>
				</div>
				<div class="pull-right">
					<label><?php echo getLang('size')?>(Byte)</label>
					<div class="form-inline">
						<input type="text" name="mf_size" class="form-control" style="width:160px" disabled="disabled" title="<?php echo getLang('regdate')?>">
					</div>
				</div>
			</div>
			<div class="form-group imagebox" style="display:none">
				<label><?php echo getLang('preview')?></label>
				<br>
				<img style="height:100px;max-width:100%;width:auto">
			</div>
			<div class="form-group">
				<label for="id_mf_name"><?php echo getLang('name')?></label>
				<input type="text" name="mf_name" class="form-control" id="id_mf_name" maxlength="255" disabled="disabled">
			</div>
			<div class="form-group">
				<label for="id_mf_description"><?php echo getLang('explain')?></label>
				<input type="text" name="mf_description" class="form-control" id="id_mf_description" maxlength="255" disabled="disabled">
			</div>
		</div>
		<div class="modal-footer clearfix">
			<button type="button" class="btn btn-danger pull-left" data-act-change="admin.deleteFile" data-add-param="is_empty,1"<?php echo isAdmin()?'':' disabled'?>><?php echo getLang('permanent_delete')?></button>
			<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo getLang('close')?></button>
		</div>
	</form>
  </div>
</div>
<?php } else {?>
<div id="trash_modal" class="modal fade bs-admin-modal-lg" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
	<form class="modal-content" method="post" autocomplete="off">
	<input type="hidden" name="success_return_url" value="<?php echo getUrl()?>" />
	<input type="hidden" name="md_id" value="" />
	<input type="hidden" name="wr_srl" value="" />

	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title"><?php echo getLang('recycle_bin')?></h4>
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
			<label for="id_wr_content"><?php echo getLang('content')?></label>
			<div class="pull-right">
			<label class="radio inline" tabindex="0" style="margin-top:0;margin-bottom:5px">
				<input type="radio" name="wr_type" value="0" disabled>
				<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
				TEXT
			</label>
			<label class="radio inline" tabindex="0" style="margin-top:0;margin-bottom:5px">
				<input type="radio" name="wr_type" value="1" disabled>
				<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
				MKDW
			</label>
			<label class="radio inline" tabindex="0" style="margin-top:0;margin-bottom:5px">
				<input type="radio" name="wr_type" value="2" disabled>
				<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
				HTML
			</label>
			<label class="checkbox inline" tabindex="0" style="margin-top:0;margin-bottom:5px;margin-left:.8em">
				<input type="checkbox" name="wr_secret" value="1" disabled>
				<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
				<?php echo getLang('secret')?>
			</label>
			</div>
			<textarea class="form-control mh-20 vresize clearfix" name="wr_content" id="id_wr_content" readonly="readonly"></textarea>
		</div>
	  </div>
	  <div class="modal-footer clearfix">
		<button type="button" class="btn btn-danger pull-left" data-act-change="board.deleteDocument" data-add-param="is_empty,1"<?php echo isAdmin()?'':' disabled'?>><?php echo getLang('permanent_delete')?></button>
		<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo getLang('close')?></button>
		<button type="submit" class="btn btn-warning"><i class="glyphicon glyphicon-refresh" aria-hidden="true"></i> <?php echo getLang('restore')?></button>
	  </div>
	</form>
  </div>
</div>
<?php } ?>

<script>
	function empty_recycle_bin() {
		if (confirm($_LANG['confirm_empty'].sprintf([$_LANG['recycle_bin']]))) {
			var data = {};
			data['success_return_url'] = current_url;
			exec_ajax('admin.emptyRecyclebin', data);
		}
	}
</script>

<?php
/* End of file trash.php */
/* Location: ./module/admin/trash.php */
