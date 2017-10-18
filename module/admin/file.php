<?php
	if(!defined('__AFOX__')) exit();

	$fl = _AF_FILE_TABLE_;
	$dd = _AF_DOCUMENT_TABLE_;

	$search = '';
	if(!empty($_DATA['search'])) {
		$tmp = $_DATA['search'];
		$schkeys = ['name'=>'mf_name','desc'=>'mf_description','type'=>'mf_type','date'=>'mf_regdate'];
		$ss = explode(':', $tmp);
		if(count($ss)>1 && !empty($schkeys[$ss[0]])) {
			$tmp = trim(implode(':', array_slice($ss,1)));
			if(!empty($tmp)) $search = $fl.'.'.$schkeys[$ss[0]].' LIKE '.DB::escape(($ss[0]==='date'?'':'%').$tmp.'%');
		} else {
			$search = '('.$fl.'.mf_name LIKE '.DB::escape('%'.$_DATA['search'].'%').' OR '.$fl.'.mf_description LIKE '.DB::escape('%'.$_DATA['search'].'%').')';
		}
	}

	$category = $dd.(empty($_DATA['category'])?'.md_id <> \'_AFOXtRASH_\'':'.md_id = '.DB::escape($_DATA['category']));
	$where = empty($search)&&empty($category) ? '1' : '('.$category.(empty($search)||empty($category) ? '' : ' AND ').$search.')';
	$page = (int)isset($_DATA['page']) ? (($_DATA['page'] < 1) ? 1 : $_DATA['page']) : 1;
	$count = 20;
	$start = (($page - 1) * $count);
	$file_list = [];

	$out = DB::getList("SELECT SQL_CALC_FOUND_ROWS $fl.*, $dd.md_id FROM $fl INNER JOIN $dd ON $dd.wr_srl = $fl.mf_target WHERE $where ORDER BY $fl.mf_regdate DESC LIMIT $start,$count");
	if($ex = DB::error()) {
		echo messageBox($ex->getMessage(),$ex->getCode(), false);
	} else {
		$total_count = DB::found();
		$cur_page = $page;
		$tal_page = ceil($total_count / $count);
		$file_list['current_page'] = $cur_page;
		$file_list['total_page'] = $tal_page;
		$cur_page--;
		$str_page = $cur_page - ($cur_page % 10);
		$end_page = ($tal_page > ($str_page + 10) ? $str_page + 10 : $tal_page);
		$file_list['start_page'] = ++$str_page;
		$file_list['end_page'] = $end_page;
		$file_list['total_count'] = $total_count;
		$file_list['data'] = $out;
	}
?>

<table class="table table-hover table-nowrap">
<thead>
	<tr>
		<th class="col-xs-1"><i class="glyphicon glyphicon-asterisk" aria-hidden="true"></i>
			<a href="#DataManageAction"><?php echo getLang('data_manage')?></a></th>
		<th><span class="th_title"><?php echo getLang('name')?></span>
		<span class="data_controler" style="display:none"><input type="checkbox" style="margin-right:5px" class="data_all_selecter"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i> <a href="#" onclick="return data_selected_delete()"><?php echo getLang('data_delete')?></a></span></th>
		<th class="col-xs-1 hidden-xs"><?php echo getLang('download')?></th>
		<th class="col-xs-1 hidden-xs hidden-sm"><?php echo getLang('ip')?></th>
		<th class="col-xs-1"><?php echo getLang('date')?></th>
	</tr>
</thead>
<tbody>

<?php
	$end_page = $total_page = 0;
	$start_page = $current_page = 1;

	if(!empty($file_list['error'])) {
		echo messageBox($file_list['message'], $file_list['error'], false);
	} else {
		$current_page = $file_list['current_page'];
		$total_page = $file_list['total_page'];
		$start_page = $file_list['start_page'];
		$end_page = $file_list['end_page'];

		foreach ($file_list['data'] as $key => $value) {
			echo '<tr class="afox-list-item" data-exec-ajax="admin.getFile" data-ajax-param="mf_srl,'.$value['mf_srl'].'" data-modal-target="#file_modal"><th scope="row"><a href="'.getUrl('category',$value['md_id']).'" except-event>'.$value['md_id'].'</a></th>';
			echo '<td class="title"><input type="checkbox" value="'.$value['mf_srl'].'" class="data_selecter" style="display:none;margin-right:5px" except-event>'.escapeHtml(cutstr($value['mf_name'],50)).'</td>';
			echo '<td class="hidden-xs">'.$value['mf_download'].'</td>';
			echo '<td class="hidden-xs hidden-sm">'.$value['mb_ipaddress'].'</td>';
			echo '<td>'.date('Y/m/d', strtotime($value['mf_regdate'])).'</td></tr>';
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

<div id="file_modal" class="modal fade bs-admin-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
	<form class="modal-content" method="post" autocomplete="off">
	<input type="hidden" name="success_return_url" value="<?php echo getUrl()?>" />
	<input type="hidden" name="md_id" value="" />
	<input type="hidden" name="mf_srl" value="" />
	<input type="hidden" name="mf_target" value="" />
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title" id="myModalLabel"><?php echo getLang('file')?></h4>
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
				<img style="width:auto;height:100px">
			</div>
			<div class="form-group">
				<label for="id_mf_name"><?php echo getLang('name')?></label>
				<div class="input-group">
					<input type="text" name="mf_name" class="form-control" id="id_mf_name" maxlength="255">
					<span class="input-group-btn">
						<button class="btn btn-info document_goto" type="button" title="<?php echo getLang('goto')?>..."><i class="glyphicon glyphicon-share-alt" aria-hidden="true"></i></button>
					</span>
				</div>
			</div>
			<div class="form-group">
				<label for="id_mf_description"><?php echo getLang('explain')?></label>
				<input type="text" name="mf_description" class="form-control" id="id_mf_description" maxlength="255">
			</div>
		</div>
		<div class="modal-footer clearfix">
			<button type="button" class="btn btn-danger pull-left" data-act-change="admin.deleteFile" data-add-param="is_empty,1"><?php echo getLang('permanent_delete')?></button>
			<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo getLang('close')?></button>
			<button type="submit" class="btn btn-success"><i class="glyphicon glyphicon-ok" aria-hidden="true"></i> <?php echo getLang('save')?></button>
		</div>
	</form>
  </div>
</div>

<script>
	function data_selected_delete() {
		if (confirm($_LANG['confirm_select_delete'].sprintf([$_LANG['file']]))) {
		var $a = jQuery('#ADM_DEFAULT_MODULE .table'),
			data = {};
			srls = [];
			$a.find('.data_selecter:checked').each(function(i) {
				srls[i] = jQuery(this).val();
			});
			if (srls.length < 1) {
				alert($_LANG['warning_no_selected'].sprintf([$_LANG['file']]));
				return false;
			}
			data['mf_srls'] = srls;
			data['success_return_url'] = current_url;
			exec_ajax('admin.deleteFiles', data);
		}
		return false;
	}
</script>

<?php
/* End of file file.php */
/* Location: ./module/admin/file.php */
