<?php
if(!defined('__AFOX__')) exit();
@include_once dirname(__FILE__) . '/common.php';
$is_wr_grant = isGrant('write', __MID__);
?>

<section id="documentList">
	<h3 class="pb-3 mb-2 fst-italic border-bottom">List of post</h3>
<?php if(empty($_POST['srl']) && !empty($_CFG['md_category'])){ ?>

	<ol class="list-unstyled" aria-label="Category of the list">
	<?php
		$tmp = explode(',', $_CFG['md_category']);
		foreach ($tmp as $val) {
			$isEqual = $val == $_POST['category'];
			echo '<li class="d-inline mx-1"><a class="badge text-bg-secondary'.($isEqual?' text-decoration-underline active" aria-current="page':' text-decoration-none').'" href="'.getUrl('','id',__MID__,'category', urlencode($val)).'">'.$val.'</a></li>';
		}
	?>
	</ol>

<?php } include dirname(__FILE__) . '/s.' . $use_style . '.php'; ?>
	<div class="w-100 text-end">
		<nav aria-label="Page navigation of the list">
		<ul class="pagination pagination-sm float-start">
			<?php if($start_page>10) echo '<li class="page-item"><a class="page-link" href="'.getUrl('page',$start_page-10).'">&laquo;</a></li>' ?>
			<li class="page-item"><a class="page-link <?php echo $current_page <= 1 ? ' disabled" aria-disabled="true' : ''?>" href="<?php echo  $current_page <= 1 ? '#' : getUrl('page',$current_page-1)?>" aria-label="Previous"><span aria-hidden="true">&lsaquo;</span> <?php echo getLang('previous') ?></a></li>
			<li class="page-item d-md-none"><a class="page-link disabled" aria-disabled="true"><?php echo $current_page.' / '.$total_page?></a></li>
			<?php for ($i=$start_page; $i <= $end_page; $i++) echo '<li class="page-item d-none d-md-inline-block"><a class="page-link'.($current_page == $i ? ' active" aria-current="page' : '').'" href="'.getUrl('page',$i).'">'.$i.'</a></li>' ?>
			<li class="page-item"><a class="page-link<?php echo $current_page >= $total_page ? ' disabled" aria-disabled="true' : ''?>" href="<?php echo $current_page >= $total_page ? '#' : getUrl('page',$current_page+1)?>" aria-label="Next"><?php echo getLang('next') ?> <span aria-hidden="true">&rsaquo;</span></a></li>
			<?php if(($total_page-$end_page)>0) echo '<li class="page-item"><a class="page-link" href="'.getUrl('page',$end_page+1).'">&raquo;</a></li>' ?>
		</ul>
		</nav>
		<a class="btn btn-sm btn-outline-secondary clearfix" href="<?php echo getUrl('disp','writeDocument','srl','')?>" role="button"><?php echo getLang('write') ?></a>
	</div>
</section>

<?php
/* End of file view.php */
/* Location: ./theme/default/skin/board/list.php */
