<?php
	if(!defined('__AFOX__')) exit();

	$search = empty($_DATA['search'])?null:'%'.$_DATA['search'].'%';
	$member_list = getDBList(_AF_MEMBER_TABLE_,[
		'OR' =>empty($search)?[]:['mb_id{LIKE}'=>$search, 'mb_nick{LIKE}'=>$search]
	],'mb_regdate desc', empty($_DATA['page']) ? 1 : $_DATA['page'], 20);
?>

<p class="navbar">
  <button type="button" class="btn btn-primary min-width-200" data-toggle="modal.clone" data-target=".bs-admin-modal-lg"><?php echo getLang('new_member')?></button>
</p>

<table class="table table-hover table-nowrap">
<thead class="table-nowrap">
	<tr>
		<th class="col-xs-1">#<?php echo getLang('id')?></th>
		<th><?php echo getLang('nickname')?></th>
		<th class="col-xs-1"><?php echo getLang('rank')?></th>
		<th class="col-xs-1 hidden-xs hidden-sm"><?php echo getLang('point')?></th>
		<th class="col-xs-1"><?php echo getLang('status')?></th>
		<th class="col-xs-1"><?php echo getLang('login')?></th>
	</tr>
</thead>
<tbody>

<?php
	$end_page = $total_page = 0;
	$start_page = $current_page = 1;
	$rank_arr = ['61'=>getLang('manager'),'67'=>getLang('admin')];

	if(!empty($member_list['error'])) {
		echo showMessage($member_list['message'], $member_list['error'], false);
	} else {
		$current_page = $member_list['current_page'];
		$total_page = $member_list['total_page'];
		$start_page = $member_list['start_page'];
		$end_page = $member_list['end_page'];

		foreach ($member_list['data'] as $key => $value) {
			$rank = ord($value['mb_rank']) - 48;
			echo '<tr class="afox-list-item" data-exec-ajax="member.getMember" data-ajax-param="mb_id,'.$value['mb_id'].'" data-modal-target="#member_modal"><th scope="row">'.$value['mb_id'].'</th>';
			echo '<td>'.$value['mb_nick'].'</td>';
			echo '<td>'.(isset($rank_arr[$rank])?$rank_arr[$rank]:'LV. '.$rank).'</td>';
			echo '<td class="hidden-xs hidden-sm">'.$value['mb_point'].'</td>';
			echo '<td>'.$value['mb_status'].'</td>';
			echo '<td>'.date('Y/m/d', strtotime($value['mb_login'])).'</td></tr>';
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

<div id="member_modal" class="modal fade bs-admin-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
	<form class="modal-content" method="post" autocomplete="off" enctype="multipart/form-data" data-exec-ajax="member.updateMember">
	<input type="hidden" name="success_return_url" value="<?php echo getUrl()?>" />

	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="myModalLabel"><?php echo getLang('member')?></h4>
	  </div>
	  <div class="modal-body">
			<div class="form-group clearfix" style="margin-bottom:0">
				<div class="pull-left">
					<label for="id_mb_id"><?php echo getLang('id')?></label>
					<div class="form-inline">
						<input type="text" name="new_mb_id" class="form-control" id="id_mb_id" required maxlength="11" pattern="^[a-zA-Z]+\w{2,}$">
						<input type="hidden" name="mb_id" value="" />
					</div>
				</div>
				<div class="pull-right">
					<div class="form-inline">
						<input type="text" name="mb_regdate" class="form-control" style="width:160px" disabled="disabled" title="<?php echo getLang('mb_regdate')?>">
					</div>
				</div>
			</div>
			<p class="help-block"><?php echo getLang('desc_id')?></p>
			<div class="form-group">
				<label for="id_mb_password"><?php echo getLang('password')?></label>
				<div class="form-inline">
					<input type="password" name="new_mb_password" class="form-control" id="id_mb_password" placeholder="<?php echo getLang('password')?>" required>
					<input type="password" name="verify_mb_password" class="form-control" placeholder="<?php echo getLang('verify_password')?>" required>
				</div>
				<p class="help-block"><?php echo getLang('desc_change_mb_password')?></p>
			</div>
			<div class="form-group">
				<label for="id_mb_point"><?php echo getLang('point')?></label>
				<div class="form-inline">
					<input type="number" id="id_mb_point" class="form-control" name="mb_point" min="0" max="99999999999" maxlength="11" placeholder="<?php echo getLang('point')?>">
				</div>
				<p class="help-block"><?php echo getLang('desc_mb_point')?></p>
			</div>
			<div class="form-group">
				<label><?php echo getLang('rank')?></label>
				<div class="radio-group">
					<input type="hidden" name="new_mb_rank" value="0">
					<div class="radio-control radio-xs">
						<span class="radio active" data-value="0"><?php echo getLang('member')?></span>
						<span class="radio" data-value="1"><?php echo getLang('manager')?></span>
						<span class="radio" data-value="2"><?php echo getLang('admin')?></span>
					</div>
				 </div>
			</div>
			<div class="form-group">
				<label for="id_mb_nick"><?php echo getLang('nickname')?></label>
				<input type="text" name="mb_nick" class="form-control" id="id_mb_nick" maxlength="20" required>
			</div>
			<div class="form-group">
				<label for="id_mb_email"><?php echo getLang('email')?></label>
				<input type="email" name="mb_email" class="form-control" id="id_mb_email" maxlength="255" required>
			</div>
			<div class="form-group">
				<label for="id_mb_homepage"><?php echo getLang('homepage')?></label>
				<input type="url" name="mb_homepage" class="form-control" id="id_mb_homepage" maxlength="255">
			</div>
			<div class="form-group">
				<label for="id_mb_memo"><?php echo getLang('memo')?></label>
				<textarea class="form-control min-height-100 vresize" name="mb_memo" id="id_mb_memo"></textarea>
				<p class="help-block"><?php echo getLang('desc_member_memo')?></p>
			</div>
			<div class="form-group">
				<div class="fileupload-group" placeholder="<?php echo getLang('warning_permit',['png [100x100 size]'])?>">
					<div class="input-group">
						<div class="file-caption form-control"></div>
						<div class="btn btn-primary btn-file">
							<i class="glyphicon glyphicon-folder-open"><?php echo getLang('browse')?>…</i>
							<input name="mb_icon" type="file">
						</div>
					</div>
				</div>
				<p class="help-block"><?php echo getLang('desc_member_icon')?></p>
			</div>

	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo getLang('close')?></button>
		<button type="submit" class="btn btn-success"><i class="glyphicon glyphicon-ok" aria-hidden="true"></i> <?php echo getLang('save')?></button>
	  </div>
	</form>
  </div>
</div>

<?php
/* End of file member.php */
/* Location: ./module/admin/member.php */