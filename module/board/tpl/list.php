<?php
if(!defined('__AFOX__')) exit();

$is_wr_grant = isGrant('write', __MID__);
?>

<section id="bdList" class="s_<?php echo $use_style?>"<?php echo empty($_DATA['srl']) ? '' :' style="margin-top:50px"'; ?>>

<?php if (empty($_DATA['srl']) && !empty($_CFG['md_category'])) { ?>
	<header>
		<ol class="breadcrumb">
		<?php
			$tmp = explode(',', $_CFG['md_category']);
			foreach ($tmp as $val) {
				$isEqual = $val == $_DATA['category'];
				echo '<li'.($isEqual?' class="active"':'').'><a href="'.getUrl('','id',__MID__,'category', urlencode($val)).'">'.$val.'</a></li>';
			}
		?>
		</ol>
	</header>
<?php } ?>

<?php include dirname(__FILE__) . '/s.' . $use_style . '.php'; ?>

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
			<?php if(!empty($_DATA['srl'])) {?><a class="btn btn-default" href="<?php echo getUrl('disp','','srl','','cpage','','rp','') ?>" role="button"><i class="glyphicon glyphicon-list" aria-hidden="true"></i> <?php echo getLang('list') ?></a><?php }?>
			<a class="btn btn-default" href="<?php echo $is_wr_grant ? getUrl('disp','writeDocument','srl','') : '#' ?>"<?php echo $is_wr_grant ? '':' data-msg-box="warning" data-title="'.getLang('error_permitted').'"'?> role="button"><i class="glyphicon glyphicon-pencil" aria-hidden="true"></i> <?php echo getLang('write') ?></a>
		</div>
	</footer>
</section>
