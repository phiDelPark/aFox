<?php
if(!defined('__AFOX__')) exit();
$CATE = getCategorys();
?>

<section id="bdList" class="s_timeline">

<?php if (!empty($CATE)) { ?>
	<header>
		<ol class="breadcrumb">
		<?php
			foreach ($CATE as $val) {
				$isEqual = $val['ca_srl']===$_DATA['category'];
				echo '<li'.($isEqual?' class="active"':'').'><a href="'.getUrl('','ledger','&','category', $val['ca_srl']).'">'.$val['ca_name'].'</a></li>';
			}
		?>
		<li class="pull-right">
		<select>
			<option value="<?php echo getUrl('','ledger','&','category', $_DATA['category']) ?>">미결</option>
			<option value="<?php echo getUrl('','ledger','1','category', $_DATA['category']) . ($_DATA['ledger']=='1'?'" selected="selected':'') ?>">현금</option>
			<option value="<?php echo getUrl('','ledger','2','category', $_DATA['category']) . ($_DATA['ledger']=='2'?'" selected="selected':'') ?>">카드</option>
			<option value="<?php echo getUrl('','ledger','3','category', $_DATA['category']) . ($_DATA['ledger']=='3'?'" selected="selected':'') ?>">은행</option>
			<option value="<?php echo getUrl('','ledger','0','category', $_DATA['category']) . ($_DATA['ledger']=='0'?'" selected="selected':'') ?>">그외</option>
		</select>
		</li>
		</ol>
	</header>
<?php } ?>

<?php include dirname(__FILE__) . '/s.timeline.php'; ?>

	<nav class="text-center">
		<ul class="pagination hidden-xs">
			<?php if($start_page>10) echo '<li><a href="'.getUrl('page',$start_page-10).'">&laquo;</a></li>'; ?>
			<li<?php echo $current_page <= 1 ? ' class="disabled"' : ''?>><a href="<?php echo  $current_page <= 1 ? '#" onclick="return false' : getUrl('page',$current_page-1)?>" aria-label="Previous"><span aria-hidden="true">&lsaquo;</span></a></li>
			<?php for ($i=$start_page; $i <= $end_page; $i++) echo '<li'.($current_page == $i ? ' class="active"' : '').'><a href="'.getUrl('page',$i).'">'.$i.'</a></li>'; ?>
			<li<?php echo $current_page >= $total_page ? ' class="disabled"' : ''?>><a href="<?php echo $current_page >= $total_page ? '#" onclick="return false' : getUrl('page',$current_page+1)?>" aria-label="Next"><span aria-hidden="true">&rsaquo;</span></a></li>
			<?php if(($total_page-$end_page)>0) echo '<li><a href="'.getUrl('page',$end_page+1).'">&raquo;</a></li>'; ?>
		</ul>
		<ul class="pager visible-xs-block">
			<li class="previous<?php echo $current_page <= 1?' disabled':''?>"><a href="<?php echo  $current_page <= 1 ? '#" onclick="return false' : getUrl('page',$current_page-1)?>" aria-label="Previous"><span aria-hidden="true">&lsaquo;</span> <?php echo getLang('previous') ?></a></li>
			<li><span class="col-xs-5"><?php echo $current_page.' / '.$total_page?></span></li>
			<li class="next<?php echo $current_page >= $total_page?' disabled':''?>"><a href="<?php echo $current_page >= $total_page ? '#" onclick="return false' : getUrl('page',$current_page+1)?>" aria-label="Next"><?php echo getLang('next') ?> <span aria-hidden="true">&rsaquo;</span></a></li>
		</ul>
	</nav>
	<footer class="clearfix">
		<form class="search-form pull-left col-xs-6 col-sm-4" action="<?php echo getUrl('') ?>" method="get">
			<div class="input-group">
				<!-- 기본적으로 OR 검색이며 AND 검색은 [&검색어] 제목 검색은 [:검색어] 또는 [title:검색어] 이며 제목 AND 검색은 [:&검색어] -->
				<input type="text" name="search" value="<?php echo empty($_DATA['search'])?'':$_DATA['search'] ?>" class="form-control" placeholder="<?php echo getLang('search_word') ?>" data-toggle="tooltip" data-placement="top" data-html="true" title="<?php echo getLang('search_description') ?>" onfocus="$(this).tooltip({trigger:'manual'}).tooltip('show')" onblur="$(this).tooltip('hide')" required>
				<span class="input-group-btn">
				<?php if(empty($_DATA['search']) || !__MOBILE__) {?><button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search" aria-hidden="true"></i> <?php echo getLang('search') ?></button><?php }?>
				<?php if(!empty($_DATA['search'])) {?><button class="btn btn-default" type="button" onclick="location.replace('<?php echo getUrl('search','') ?>')"><?php echo getLang('cancel') ?></button><?php }?>
				</span>
			</div>
			<input type="hidden" name="id" value="<?php echo __MID__ ?>">
		</form>
		<div class="pull-right">
			<a class="btn btn-default" href="#" data-toggle="modal" data-target="#ledger_write_modal" data-ledger-srl="" role="button"><i class="glyphicon glyphicon-pencil" aria-hidden="true"></i> <?php echo getLang('write') ?></a>
		</div>
	</footer>
</section>

<div id="ledger_write_modal" class="modal fade bs-ledger-modal-lg" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
	<form class="modal-content" method="post" onsubmit="return false" autocomplete="off" data-exec-ajax="ledger.getdocumentform">
	<input type="hidden" name="success_return_url" value="<?php echo getUrl()?>" />
	<input type="hidden" name="ev_srl" value="" />
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title"><?php echo getLang('new')?></h4>
	  </div>
	  <div class="modal-body">
	  </div>
	  <div class="modal-footer">
	  	<div class="pull-left">
	  	<button type="button" class="btn btn-danger" id="delete-ledger-document"><i class="glyphicon glyphicon-remove" aria-hidden="true"></i> <?php echo getLang('delete')?></button>
		</div>
		<div class="pull-right">
		<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo getLang('close')?></button>
		<button type="submit" class="btn btn-success"><i class="glyphicon glyphicon-ok" aria-hidden="true"></i> <?php echo getLang('save')?></button>
		</div>
	  </div>
	</form>
  </div>
</div>
