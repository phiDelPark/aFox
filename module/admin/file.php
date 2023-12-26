<?php
	if(!defined('__AFOX__')) exit();

	$duplicate = !empty($_DATA['duplicate']);
	$page = (int)isset($_DATA['page']) ? (($_DATA['page'] < 1) ? 1 : $_DATA['page']) : 1;
	$count = $duplicate ? 30 : 20;
	$start = (($page - 1) * $count);

	$fl = _AF_FILE_TABLE_;
	$dd = _AF_DOCUMENT_TABLE_;

	if($duplicate){
		$file_list = DB::query("SELECT SQL_CALC_FOUND_ROWS a.*, d.wr_title FROM $fl as a INNER JOIN $dd as d ON (d.md_id <> '_AFOXtRASH_' and d.wr_srl = a.mf_target), (select mf_target,mf_name,mf_size from $fl where mf_link<>1 and mf_size>0 group by mf_name,mf_size having count(*) > 1) as b WHERE a.mf_link<>1 and a.mf_size=b.mf_size AND a.mf_name=b.mf_name ORDER BY a.mf_name,a.mf_regdate LIMIT $start,$count" , true);
	}else {
		$search = '';
		if(!empty($_DATA['search'])) {
			$tmp = $_DATA['search'];
			$schkeys = ['name'=>'mf_name','desc'=>'mf_description','type'=>'mf_type','date'=>'mf_regdate'];
			$ss = explode(':', $tmp);
			if(count($ss)>1 && !empty($schkeys[$ss[0]])) {
				$tmp = trim(implode(':', array_slice($ss,1)));
				if(!empty($tmp)) $search = 'f.'.$schkeys[$ss[0]].' LIKE \''.DB::escape(($ss[0]==='date'?'':'%').$tmp.'%').'\'';
			} else {
				$search = '(f.mf_name LIKE \''.DB::escape('%'.$_DATA['search'].'%').'\' OR f.mf_description LIKE \''.DB::escape('%'.$_DATA['search'].'%').'\')';
			}
		}

		$category = 'd'.(empty($_DATA['category'])?'.md_id <> \'_AFOXtRASH_\'':'.md_id = \''.DB::escape($_DATA['category']).'\'');
		$where = empty($search)&&empty($category) ? '1' : '('.$category.(empty($search)||empty($category) ? '' : ' AND ').$search.')';
		$file_list = DB::query("SELECT SQL_CALC_FOUND_ROWS f.*, d.md_id FROM $fl as f INNER JOIN $dd as d ON d.wr_srl = f.mf_target WHERE $where ORDER BY f.mf_regdate DESC LIMIT $start,$count", true);
	}
	if($error = DB::error()) $error = set_error($error->getMessage(),$error->getCode());
	$file_list = setDataListInfo($file_list, $page, $count, DB::foundRows());

	if($duplicate) {
		messageBox(getLang('desc_data_combine'), 2, false);
	}
	if($error) {
		messageBox($error['message'], $error['error'], false);
	}
?>
<?php if($duplicate) { ?>
<a class="btn btn-success" href="#" onclick="return data_selected_combine()"><?php echo getLang('data_combine')?></a>
<?php } ?>
<table class="table table-hover table-nowrap">
<thead>
	<tr>
		<?php if($duplicate) { ?>
		<th class="col-xs-1"><?php echo getLang('select')?></th>
		<th class="col-xs-1"><?php echo getLang('module')?></th>
		<th class="col-xs-1">.</th>
		<?php } else { ?>
		<th class="col-xs-1">
			<i class="glyphicon glyphicon-option-vertical" aria-hidden="true"></i>
			<a href="#DataManageAction"><?php echo getLang('data_manage')?></a>
		</th>
		<?php } ?>
		<th><span class="th_title"><?php echo getLang('name')?></span>
		<span class="data_controler" style="display:none"><input type="checkbox" style="margin-right:5px" class="data_all_selecter"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i> <a href="#" onclick="return data_selected_delete()"><?php echo getLang('data_delete')?></a> <i class="glyphicon glyphicon-search" aria-hidden="true"></i> <a href="<?php echo getUrl('duplicate',1)?>"><?php echo getLang('duplicate_files')?></a></span></th>
		<?php if($duplicate) { ?>
		<th class="col-xs-1 hidden-xs"><?php echo getLang('size')?></th>
		<?php } else { ?>
		<th class="col-xs-1 hidden-xs"><?php echo getLang('download')?></th>
		<?php } ?>
		<th class="col-xs-1 hidden-xs hidden-sm"><?php echo getLang('ip')?></th>
		<th class="col-xs-1"><?php echo getLang('date')?></th>
	</tr>
</thead>
<tbody>

<?php
	$end_page = $total_page = 0;
	$start_page = $current_page = 1;

	if(!$error) {
		$current_page = $file_list['current_page'];
		$total_page = $file_list['total_page'];
		$start_page = $file_list['start_page'];
		$end_page = $file_list['end_page'];

		foreach ($file_list['data'] as $key => $value) {
			if($duplicate){
				$dutmp1 = isset($file_list['data'][$key + 1]) ? $file_list['data'][$key + 1] : ['mf_size'=>0,'mf_name'=>''];
				$dutmp2 = isset($file_list['data'][$key - 1]) ? $file_list['data'][$key - 1] : ['mf_size'=>0,'mf_name'=>''];
				if(!(($value['mf_size']===$dutmp1['mf_size'] && $value['mf_name']===$dutmp1['mf_name'])
					|| ($value['mf_size']===$dutmp2['mf_size'] && $value['mf_name']===$dutmp2['mf_name']))){
					continue;
				}
			}
			if($duplicate) {
				$_file_types = array('binary'=>0, 'image' => 1, 'video' => 2, 'audio' => 3);
				$filetype = explode('/', $value['mf_type']);
				$filetype = strtolower(array_shift($filetype));
				$filetype = empty($_file_types[$filetype]) ? 'binary' : $filetype;
				$unfilename = _AF_URL_ .'data/attach/'. $filetype . '/' . $value['md_id'] . '/' . $value['mf_target'] . '/' . $value['mf_upload_name'];
				echo '<tr class="afox-list-item" data-exec-ajax="admin.getFile" data-ajax-param="mf_srl,'.$value['mf_srl'].'" data-modal-target="#file_modal"><th scope="row" rowspan="2"><input type="radio" name="mf_standard" value="'.$value['mf_srl'].'" class="data_standard" style="margin-right:5px" data-except-ajax><input type="checkbox" value="'.$value['mf_srl'].'" class="data_selecter" style="margin-right:5px" data-except-ajax></th>';
				echo '<td scope="row" rowspan="2" style="padding:2px"><img src="'.($unfilename).'" width="65" height="65"></td>';
				echo '<td scope="row">'.$value['md_id'].'</td>';
				echo '<td class="title">'.escapeHtml(cutstr($value['mf_name'],50)).'</td>';
				echo '<td class="hidden-xs">'.shortSize($value['mf_size']).'</td>';

			} else {
			echo '<tr class="afox-list-item" data-exec-ajax="admin.getFile" data-ajax-param="mf_srl,'.$value['mf_srl'].'" data-modal-target="#file_modal"><th scope="row"><a href="'.getUrl('category',$value['md_id']).'" data-except-ajax>'.$value['md_id'].'</a></th>';
			echo '<td class="title"'.(empty($value['mf_size'])?' style="text-decoration:line-through"':'').'><input type="checkbox" value="'.$value['mf_srl'].'" class="data_selecter" style="display:none;margin-right:5px" data-except-ajax>'.escapeHtml(cutstr($value['mf_name'],50)).'</td>';
			echo '<td class="hidden-xs">'.$value['mf_download'].'</td>';
			}
			echo '<td class="hidden-xs hidden-sm">'.$value['mb_ipaddress'].'</td>';
			echo '<td>'.date('Y/m/d', strtotime($value['mf_regdate'])).'</td></tr>';
			if($duplicate) {
				echo '<tr><td class="title" colspan="4" style="color:#555;text-decoration:underline"><a href="'.getUrl('','id',$value['md_id'],'srl',$value['mf_target']).'" target="_blank">'.escapeHtml(cutstr($value['wr_title'],50)).'</a></td></tr>';
			}
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
	<?php if(!$duplicate) { ?>
	<ul class="pagination">
	<li><form class="form-inline search-form" action="<?php echo getUrl('') ?>" method="get">
		<input type="hidden" name="admin" value="<?php echo $_DATA['admin'] ?>">
		<?php if(!empty($_DATA['category'])) {?><input type="hidden" name="category" value="<?php echo $_DATA['category'] ?>"><?php }?>
		<input type="text" name="search" value="<?php echo empty($_DATA['search'])?'':$_DATA['search'] ?>" class="form-control" placeholder="<?php echo getLang('search_word') ?>" required>
		<button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search" aria-hidden="true"></i> <?php echo getLang('search') ?></button>
		<?php if(!empty($_DATA['search'])||!empty($_DATA['category'])) {?><button class="btn btn-default" type="button" onclick="location.replace('<?php echo getUrl('search','','category','') ?>')"><?php echo getLang('cancel') ?></button><?php }?>
	</form></li>
	</ul>
	<?php } ?>
</nav>

<div id="file_modal" class="modal fade bs-admin-modal-lg" tabindex="-1" role="dialog" aria-labelledby="adminFileModalTitle">
  <div class="modal-dialog modal-lg" role="document">
	<form class="modal-content" method="post" autocomplete="off">
	<input type="hidden" name="success_return_url" value="<?php echo getUrl()?>" />
	<input type="hidden" name="md_id" value="" />
	<input type="hidden" name="mf_srl" value="" />
	<input type="hidden" name="mf_target" value="" />
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title" id="adminFileModalTitle"><?php echo getLang('file')?></h4>
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
			<button type="button" class="btn btn-danger pull-left" data-act-change="admin.deleteFile" data-add-param="is_empty,1"<?php echo isAdmin()?'':' disabled'?>><?php echo getLang('permanent_delete')?></button>
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
			data = {},
			srls = [];
			$a.find('.data_selecter:checked').each(function(i) {
				srls[i] = jQuery(this).val();
			});
			if (srls.length < 1) {
				alert($_LANG['warning_selected'].sprintf([$_LANG['file']]));
				return false;
			}
			data['mf_srls'] = srls;
			data['success_return_url'] = current_url;
			exec_ajax('admin.deleteFiles', data);
		}
		return false;
	}
	function data_selected_combine() {
		if (confirm($_LANG['confirm_select_combine'].sprintf([$_LANG['file']]))) {
		var $a = jQuery('#ADM_DEFAULT_MODULE .table'),
			data = {},
			srls = [],
			standards = [];
			$a.find('.data_standard:checked').each(function(i) {
				standards[i] = jQuery(this).val();
			});
			if(standards.length < 1) {
				alert($_LANG['warning_selected'].sprintf([$_LANG['standard_point']]));
				return false;
			}
			$a.find('.data_selecter:checked').each(function(i) {
				srls[i] = jQuery(this).val();
			});
			if (srls.length < 1) {
				alert($_LANG['warning_selected'].sprintf([$_LANG['file']]));
				return false;
			}
			data['mf_srls'] = srls;
			data['mf_standard'] = standards[0];
			data['success_return_url'] = current_url;
			exec_ajax('admin.combineFiles', data);
		}
		return false;
	}
</script>

<?php
/* End of file file.php */
/* Location: ./module/admin/file.php */
