<?php
if(!defined('__AFOX__')) exit();
if(!empty($_{'board'}['CURRENT_DOCUMENT_LIST'])) $_{'board'} = $_{'board'}['CURRENT_DOCUMENT_LIST'];
$is_wr_grant = isGrant($_DATA['id'], 'write');
$use_style = ['list','review','gallery','media'];
?>

<section id="board_list" class="<?php echo $use_style[abs($_CFG['use_style'])]?>_style"<?php echo empty($_DATA['srl']) ? '' :' style="margin-top:50px"'; ?>>

<?php if (empty($_DATA['srl']) && !empty($_CFG['md_category'])) { ?>
	<header>
		<ol class="breadcrumb">
		<?php
			$tmp = explode(',', $_CFG['md_category']);
			foreach ($tmp as $val) {
				echo '<li><a href="'.getUrl('','id',$_DATA['id'],'category', urlencode($val)).'">'.$val.'</a></li>';
			}
		?>
		</ol>
	</header>
<?php } ?>

<?php include dirname(__FILE__) . '/s.' . $use_style[abs($_CFG['use_style'])] . '.php'; ?>

	<!-- Modal -->
	<div class="modal vertical-center fade" id="passwordBoxModal" tabindex="-1" role="dialog" aria-labelledby="passwordBoxModalLabel">
		<div class="modal-dialog">
			<form class="modal-content" action="<?php echo getUrl()?>" class="input-password" method="post" autocomplete="off">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="passwordBoxModalLabel"><i class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></i> <?php echo getLang('alert')?></h4>
				</div>
				<div class="modal-body">
					<p>
						<?php echo getLang('request_input', ['password'])?>
					</p>
					<div class="form-group">
						<input type="password" class="form-control" name="mb_password" placeholder="<?php echo getLang('password')?>" required /> <span class="sr-only"><?php echo getLang('password')?></span>
					</div>
				</div>
				<div class="modal-footer">
					<?php if(!__MOBILE__) { ?><button type="button" class="btn btn-default" data-dismiss="modal"> <?php echo getLang('close')?></a></button><?php } ?>
					<button type="submit" class="btn btn-primary"><?php echo getLang('ok')?></button>
				</div>
			</form>
		</div>
	</div>

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
		<form class="search-form pull-left col-xs-5 col-sm-4" action="<?php echo getUrl('') ?>" method="get">
			<div class="input-group">
				<input type="text" name="search" value="<?php echo empty($_DATA['search'])?'':$_DATA['search'] ?>" class="form-control" placeholder="<?php echo getLang('search_text') ?>" required>
				<span class="input-group-btn">
				<?php if(empty($_DATA['search']) || !__MOBILE__) {?><button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search" aria-hidden="true"></i> <?php echo getLang('search') ?></button><?php }?>
				<?php if(!empty($_DATA['search'])) {?><button class="btn btn-default" type="button" onclick="location.replace('<?php echo getUrl('search','') ?>')"><?php echo getLang('cancel') ?></button><?php }?>
				</span>
			</div>
			<input type="hidden" name="id" value="<?php echo $_DATA['id'] ?>">
		</form>
		<div class="pull-right">
			<?php if(!empty($_DATA['srl'])) {?><a class="btn btn-default" href="<?php echo getUrl('','id',$_DATA['id'],'page',$_DATA['page']) ?>" role="button"><i class="glyphicon glyphicon-list" aria-hidden="true"></i> <?php echo getLang('list') ?></a><?php }?>
			<a class="btn btn-default" href="<?php echo $is_wr_grant ? getUrl('disp','writeDocument','srl','') : '#' ?>"<?php echo $is_wr_grant ? '':' onclick="alert(\''.escapeHtml(getLang('error_permit',false),true,ENT_QUOTES).'\');return false"'?> role="button"><i class="glyphicon glyphicon-pencil" aria-hidden="true"></i> <?php echo getLang('write') ?></a>
		</div>
	</footer>
</section>

