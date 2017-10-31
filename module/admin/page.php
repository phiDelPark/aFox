<?php
	if(!defined('__AFOX__')) exit();

	$md = _AF_MODULE_TABLE_;
	$pg = _AF_PAGE_TABLE_;

	$search = empty($_DATA['search'])?'':DB::escape('%'.$_DATA['search'].'%');
	$where = empty($search) ? '1' : '('.$md.'.md_title LIKE '.$search.' OR '.$pg.'.pg_content LIKE '.$search.')';
	$page = (int)isset($_DATA['page']) ? (($_DATA['page'] < 1) ? 1 : $_DATA['page']) : 1;
	$count = 20;
	$start = (($page - 1) * $count);
	$page_list = [];

	$out = DB::getList("SELECT SQL_CALC_FOUND_ROWS * FROM $pg INNER JOIN $md ON $md.md_id = $pg.md_id WHERE $where ORDER BY $pg.pg_regdate DESC LIMIT $start,$count");
	if($ex = DB::error()) {
		messageBox($ex->getMessage(),$ex->getCode(), false);
	} else {
		$total_count = DB::found();
		$cur_page = $page;
		$tal_page = ceil($total_count / $count);
		$page_list['current_page'] = $cur_page;
		$page_list['total_page'] = $tal_page;
		$cur_page--;
		$str_page = $cur_page - ($cur_page % 10);
		$end_page = ($tal_page > ($str_page + 10) ? $str_page + 10 : $tal_page);
		$page_list['start_page'] = ++$str_page;
		$page_list['end_page'] = $end_page;
		$page_list['total_count'] = $total_count;
		$page_list['data'] = $out;
	}

	$_type = ['TEXT','MARKDOWN','HTML'];

?>

<p class="navbar">
  <button type="button" class="btn btn-primary mw-20" data-toggle="modal.clone" data-target=".bs-admin-modal-lg"><?php echo getLang('new_page')?></button>
</p>

<table class="table table-hover table-nowrap">
<thead>
	<tr>
		<th class="col-xs-1">#<?php echo getLang('id')?></th>
		<th class="col-xs-1 hidden-xs hidden-sm"><?php echo getLang('type')?></th>
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

	if(count($page_list) > 0) {
		$current_page = $page_list['current_page'];
		$total_page = $page_list['total_page'];
		$start_page = $page_list['start_page'];
		$end_page = $page_list['end_page'];

		foreach ($page_list['data'] as $key => $value) {
			echo '<tr><th scope="row"><a href="'._AF_URL_.'?id='.$value['md_id'].'" target="_blank">'.$value['md_id'].'</a></th>';
			echo '<td class="hidden-xs hidden-sm">'.$_type[$value['pg_type']].'</td>';
			echo '<td class="title">'.escapeHtml(cutstr(strip_tags($value['md_title'].(empty($value['md_description'])?'':' - '.$value['md_description'])),50)).'</td>';
			echo '<td class="hidden-xs hidden-sm">'.$value['grant_view'].'-'.$value['grant_reply'].'</td>';
			echo '<td>'.date('Y/m/d', strtotime($value['pg_update'])).'</td>';
			echo '<td><button type="button" class="btn btn-primary btn-xs mw-10" data-exec-ajax="page.getPage" data-ajax-param="md_id,'.$value['md_id'].'" data-modal-target="#page_modal">'.getLang('setup').'</button></td></tr>';
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

<div id="page_modal" class="modal fade bs-admin-modal-lg" tabindex="-1" role="dialog" aria-labelledby="adminPageModalTitle">
  <div class="modal-dialog modal-lg" role="document">
	<form class="modal-content" method="post" autocomplete="off" enctype="multipart/form-data" data-exec-ajax="page.updatePage">
	<input type="hidden" name="success_return_url" value="<?php echo getUrl()?>" />

	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="adminPageModalTitle"><?php echo getLang('page')?></h4>
	  </div>
	  <div class="modal-body">

		<div class="form-group">
			<label for="id_md_id"><?php echo getLang('id')?></label>
			<div class="form-inline">
				<input type="text" name="new_md_id" class="form-control" id="id_md_id" required maxlength="11" pattern="^[a-zA-Z]+\w{2,}$">
				<input type="hidden" name="md_id" value="" />
			</div>
			<p class="help-block"><?php echo getLang('desc_id')?></p>
		</div>
		<div class="form-group">
			<label for="id_md_title"><?php echo getLang('title')?></label>
			<input type="text" name="md_title" class="form-control" id="id_md_title" maxlength="255">
		</div>
		<div class="form-group">
			<label for="id_md_description"><?php echo getLang('explain')?></label>
			<input type="text" name="md_description" class="form-control" id="id_md_description" maxlength="255">
		</div>
		<label><?php echo getLang('view')?></label>
		<div class="form-group">
			<label class="radio btn mw-10" tabindex="0">
				<input type="radio" name="grant_view" value="0" checked>
				<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
				<span><?php echo getLang('all')?></span>
			</label>
			<label class="radio btn mw-10" tabindex="0">
				<input type="radio" name="grant_view" value="1">
				<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
				<span><?php echo getLang('member')?></span>
			</label>
			<label class="radio btn mw-10" tabindex="0">
				<input type="radio" name="grant_view" value="m">
				<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
				<span><?php echo getLang('admin')?></span>
			</label>
		</div>
		<label><?php echo getLang('reply')?></label>
		<div class="form-group">
			<label class="radio btn mw-10" tabindex="0">
				<input type="radio" name="grant_reply" value="0">
				<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
				<span><?php echo getLang('all')?></span>
			</label>
			<label class="radio btn mw-10" tabindex="0">
				<input type="radio" name="grant_reply" value="1" checked>
				<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
				<span><?php echo getLang('member')?></span>
			</label>
			<label class="radio btn mw-10" tabindex="0">
				<input type="radio" name="grant_reply" value="m">
				<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
				<span><?php echo getLang('admin')?></span>
			</label>
		</div>
		<div class="form-group">
			<?php displayEditor(
					'pg_content',
					'',
					[
						'file'=>[99999,'',0],
						'statebar'=>true,
						'toolbar'=>array(getLang('content'), ['pg_type'=>['1', ['TEXT'=>'0','MKDW'=>'1','HTML'=>'2']]])
					]
				);
			?>
		</div>

	  </div>
	  <div class="modal-footer clearfix">
		<button type="button" class="btn btn-danger pull-left hide" data-act-change="page.deletePage"><?php echo getLang('permanent_delete')?></button>
		<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo getLang('close')?></button>
		<button type="submit" class="btn btn-success"><i class="glyphicon glyphicon-ok" aria-hidden="true"></i> <?php echo getLang('save')?></button>
	  </div>
	</form>
  </div>
</div>

<?php
/* End of file page.php */
/* Location: ./module/admin/page.php */
