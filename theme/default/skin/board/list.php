<?php
if(!defined('__AFOX__')) exit();
@include_once dirname(__FILE__) . '/common.php';
$is_wr_grant = isGrant('write', __MID__);
?>

<section id="documentList">
	<h3 class="pb-3 mb-2 fst-italic border-bottom">List of post</h3>
<?php if(empty($_DATA['srl']) && !empty($_CFG['md_category'])){ ?>

	<ol class="list-unstyled" aria-label="Category of the list">
	<?php
		$tmp = explode(',', $_CFG['md_category']);
		foreach ($tmp as $val) {
			$isEqual = $val == $_DATA['category'];
			echo '<li><a class="badge text-bg-secondary'.($isEqual?' text-decoration-underline active" aria-current="page':' text-decoration-none').'" href="'.getUrl('','id',__MID__,'category', urlencode($val)).'">'.$val.'</a></li>';
		}
	?>
	</ol>

<?php } include dirname(__FILE__) . '/s.' . $use_style . '.php'; ?>

	<nav id="pageNavigation" aria-label="Page navigation of the list">
		<?php if($start_page>10) echo '<a class="btn btn-sm btn-outline-primary rounded-pill" href="'.getUrl('page',$start_page-10).'">&laquo;</a>' ?>
		<a class="btn btn-sm rounded-pill btn-outline-<?php echo $current_page <= 1 ? 'secondary disabled" aria-disabled="true' : 'primary'?>" href="<?php echo  $current_page <= 1 ? '#' : getUrl('page',$current_page-1)?>" aria-label="Previous"><span aria-hidden="true">&lsaquo;</span> <?php echo getLang('previous') ?></a>
		<a class="d-md-none btn btn-sm btn-outline-secondary rounded-pill disabled" aria-disabled="true"><?php echo $current_page.' / '.$total_page?></a>
		<?php for ($i=$start_page; $i <= $end_page; $i++) echo '<a class="d-none d-md-inline-block btn btn-sm btn-outline-primary rounded-pill'.($current_page == $i ? ' active" aria-current="page' : '').'" href="'.getUrl('page',$i).'">'.$i.'</a>' ?>
		<a class="btn btn-sm rounded-pill btn-outline-<?php echo $current_page >= $total_page ? 'secondary disabled" aria-disabled="true' : 'primary'?>" href="<?php echo $current_page >= $total_page ? '#' : getUrl('page',$current_page+1)?>" aria-label="Next"><?php echo getLang('next') ?> <span aria-hidden="true">&rsaquo;</span></a>
		<?php if(($total_page-$end_page)>0) echo '<a class="btn btn-sm btn-outline-primary rounded-pill" href="'.getUrl('page',$end_page+1).'">&raquo;</a>' ?>
    </nav>

</section>

<?php
/* End of file view.php */
/* Location: ./theme/default/skin/board/list.php */
