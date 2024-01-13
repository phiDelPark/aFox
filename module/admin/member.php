<?php
	if(!defined('__AFOX__')) exit();

	$_DATA['page'] = empty($_DATA['page'])?1:$_DATA['page'];
	$search = empty($_DATA['search'])?null:'%'.$_DATA['search'].'%';
	$member_list = DB::gets(_AF_MEMBER_TABLE_, 'SQL_CALC_FOUND_ROWS *', [
		'(_OR_)' =>empty($search)?[]:['mb_id{LIKE}'=>$search, 'mb_nick{LIKE}'=>$search]
	],'mb_regdate', (($_DATA['page']-1)*20).',20');
	if($error = DB::error()) $error = set_error($error->getMessage(),$error->getCode());
	$member_list = setDataListInfo($member_list, $_DATA['page'], 20, DB::foundRows());
?>

<button type="button" class="btn btn-primary mb-3" style="width:250px" data-toggle="modal.clone" data-target=".bs-admin-modal-lg"<?php echo isAdmin()?'':' disabled'?>><?php echo getLang('new_member')?></button>

<table class="table">
<thead>
	<tr>
		<th scope="col">#<?php echo getLang('id')?></th>
		<th scope="col" style="width:4.5rem"><?php echo getLang('rank')?></th>
		<th scope="col" class="text-wrap"><?php echo getLang('nickname')?></th>
		<th scope="col"><?php echo getLang('point')?></th>
		<th scope="col"><?php echo getLang('status')?></th>
		<th scope="col" class="d-none d-md-table-cell"><?php echo getLang('login')?></th>
		<th scope="col" class="text-end"><?php echo getLang('setup')?></th>
	</tr>
</thead>
<tbody>

<?php
	$end_page = $total_page = 0;
	$start_page = $current_page = 1;
	$rank_arr = ['61'=>getLang('manager'),'67'=>getLang('admin')];

	if($error) {
		messageBox($error['message'], $error['error'], false);
	} else {
		$current_page = $member_list['current_page'];
		$total_page = $member_list['total_page'];
		$start_page = $member_list['start_page'];
		$end_page = $member_list['end_page'];

		foreach ($member_list['data'] as $key => $value) {
			$rank = ord($value['mb_rank']) - 48;
			echo '<tr><th scope="row">'.$value['mb_id'].'</th>';
			echo '<td>'.(isset($rank_arr[$rank])?$rank_arr[$rank]:'LV. '.$rank).'</td>';
			echo '<td class="text-wrap">'.$value['mb_nick'].'</td>';
			echo '<td>'.$value['mb_point'].'</td>';
			echo '<td>'.($value['mb_status']?$value['mb_status']:'--/--').'</td>';
			echo '<td class="d-none d-md-table-cell">'.date('Y/m/d', strtotime($value['mb_login'])).'</td>';
			echo '<td><a class="btn btn-primary btn-sm mw-10" href="'.getUrl('md_id', $value['mb_id'], 'act', 'getMember').'">'.getLang('setup').'</a></td></tr>';
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

<div id="member_modal" class="modal fade bs-admin-modal-lg" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
	<form class="modal-content" method="post" autocomplete="off" enctype="multipart/form-data" data-exec-ajax="member.updateMember">
	<input type="hidden" name="success_return_url" value="<?php echo getUrl()?>" />

	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title"><?php echo getLang('member')?></h4>
	  </div>
	  <div class="modal-body">
			<div class="form-group clearfix" style="margin-bottom:0">
				<div class="pull-left">
					<label for="id_mb_id"><?php echo getLang('id')?></label>
					<div class="form-inline">
						<input type="text" name="new_mb_id" class="form-control" id="id_mb_id" required minlength="4" maxlength="11" pattern="^[a-zA-Z]{1}[\w_]{3,10}$">
						<input type="hidden" name="mb_id" value="" />
					</div>
				</div>
				<div class="pull-right">
					<div class="form-inline">
						<input type="text" name="mb_regdate" class="form-control" style="width:160px" disabled="disabled" title="<?php echo getLang('mb_regdate')?>">
					</div>
				</div>
			</div>
			<p class="help-block"><?php echo getLang('desc_mb_id')?></p>
			<div class="form-group">
				<label for="id_mb_password"><?php echo getLang('password')?></label>
				<div class="form-inline">
					<input type="password" name="new_mb_password" class="form-control" id="id_mb_password" placeholder="<?php echo getLang('password')?>" required minlength="4">
					<input type="password" name="verify_mb_password" class="form-control" placeholder="<?php echo getLang('verify_password')?>" required>
				</div>
				<p class="help-block"><?php echo getLang('desc_mb_password')?></p>
				<p class="help-block" style="display:none"><?php echo getLang('desc_change_password')?></p>
			</div>
			<div class="form-group">
				<label for="id_mb_point"><?php echo getLang('point')?></label>
				<div class="form-inline">
					<input type="number" id="id_mb_point" class="form-control" name="mb_point" min="0" max="99999999999" maxlength="11" placeholder="<?php echo getLang('point')?>">
				</div>
				<p class="help-block"><?php echo getLang('desc_mb_point')?></p>
			</div>
			<label><?php echo getLang('rank')?></label>
			<div class="form-group">
				<label class="radio btn mw-10" tabindex="0">
					<input type="radio" name="new_mb_rank" value="0">
					<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
					<span><?php echo getLang('member')?></span>
				</label>
				<label class="radio btn mw-10" tabindex="0">
					<input type="radio" name="new_mb_rank" value="1">
					<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
					<span><?php echo getLang('manager')?></span>
				</label>
				<label class="radio btn mw-10" tabindex="0" style="display:none!important">
					<input type="radio" name="new_mb_rank" value="2">
					<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
					<span><?php echo getLang('admin')?></span>
				</label>
			</div>
			<div class="form-group">
				<label for="id_mb_nick"><?php echo getLang('nickname')?></label>
				<input type="text" name="mb_nick" class="form-control" id="id_mb_nick" minlength="2" maxlength="5" required pattern="^[a-zA-Z가-힣ぁ-んァ-ン一-龥]{2,5}$">
			</div>
			<div class="form-group">
				<label for="id_mb_email"><?php echo getLang('email')?></label>
				<input type="email" name="mb_email" class="form-control" id="id_mb_email" maxlength="255" required pattern="^[\w]+[\w._%+-]+@[\w.-]+\.[\w]+$">
			</div>
			<div class="form-group">
				<label for="id_mb_homepage"><?php echo getLang('homepage')?></label>
				<input type="url" name="mb_homepage" class="form-control" id="id_mb_homepage" maxlength="255">
			</div>
			<div class="form-group">
				<label for="id_mb_memo"><?php echo getLang('memo')?></label>
				<textarea class="form-control mh-10 vresize" name="mb_memo" id="id_mb_memo"></textarea>
				<p class="help-block"><?php echo getLang('desc_member_memo')?></p>
			</div>
			<div class="form-group">
				<div class="uploader-group" placeholder="<?php echo getLang('warning_allowable',['png [100x100 size]'])?>">
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
