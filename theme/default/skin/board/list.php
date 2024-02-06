<?php
if(!defined('__AFOX__')) exit();
@include_once dirname(__FILE__) . '/common.php';
$is_wr_grant = isGrant('write', __MID__);
$current_page = $LIST['current_page'];
$total_page = $LIST['total_page'];
?>

<section id="documentList" class="<?php echo $use_style?>">
	<h2 class="pb-3 mb-2 border-bottom"><?php echo $_CFG['md_title']?></h2>
<?php if(empty($_POST['srl']) && !empty($_CFG['md_category'])){ ?>

	<ol class="list-unstyled" aria-label="Category of the list">
	<?php
		$tmp = explode(',', $_CFG['md_category']);
		foreach ($tmp as $val) {
			$isEqual = $val == $_POST['category'];
			$cateurl = $use_style == 'gallery' ? getUrl('','id',__MID__,'search', urlencode('#'.$val)) : getUrl('','id',__MID__,'category', urlencode($val));
			echo '<li class="d-inline mx-1"><a class="badge text-bg-secondary'.($isEqual?' text-decoration-underline active" aria-current="page':' text-decoration-none').'" href="'.$cateurl.'">'.$val.'</a></li>';
		}
	?>
	</ol>

<?php }
	include dirname(__FILE__) . '/s.' . $use_style . '.php';
	$start_page = $current_page - 4;
	if ($start_page < 1) $start_page = 1;
	$end_page = 9 + $start_page;
	if ($end_page > $total_page) $end_page = $total_page;
?>
	<div class="w-100 text-end bg-body-tertiary p-1">
		<nav aria-label="Page navigation of the list">
		<ul class="pagination pagination-sm float-start">
			<li class="page-item me-1"><a class="btn btn-sm fw-bold btn-secondary<?php echo $current_page<11 ? ' disabled" aria-disabled="true' : ''?>" href="<?php echo  getUrl('page',$current_page-10)?>" aria-label="Previous-10">&lt;&lt;</a></li>
			<li class="page-item me-1 d-md-none"><a class="btn btn-sm btn-secondary<?php echo $current_page <= 1 ? ' disabled" aria-disabled="true' : ''?>" href="<?php echo  getUrl('page',$current_page-1)?>" aria-label="Previous">&lt;</a></li>
			<li class="page-item d-md-none"><a class="btn btn-sm border border-1 rounded-0 border-end-0 border-start-0 btn-outline-secondary disabled" aria-disabled="true"><?php echo $current_page.' / '.$total_page?></a></li>
			<?php for ($i=$start_page; $i <= $end_page; $i++) echo '<li class="page-item d-none d-md-block"><a class="btn btn-sm border border-1 rounded-0 border-end-0 border-start-0'.($current_page == $i ? ' text-decoration-underline fw-bold" aria-current="page' : ' btn-outline-secondary').'" href="'.getUrl('page',$i).'">'.$i.'</a></li>' ?>
			<li class="page-item ms-1 d-md-none"><a class="btn btn-sm btn-secondary<?php echo $total_page<($current_page+1) ? ' disabled" aria-disabled="true' : ''?>" href="<?php echo getUrl('page',$total_page<($current_page+1)?$total_page:$current_page+1)?>" aria-label="Next">&gt;</a></li>
			<li class="page-item ms-1"><a class="btn btn-sm fw-bold btn-secondary<?php echo $total_page <= $end_page ? ' disabled" aria-disabled="true' : ''?>" href="<?php echo getUrl('page',$total_page<($current_page+10)?$total_page:$current_page+10)?>" aria-label="Next+10">&gt;&gt;</a></li>
		</ul>
		</nav>
		<a class="btn btn-sm btn-secondary clearfix" href="<?php echo getUrl('disp','write','srl','')?>" role="button"><?php echo getLang('write') ?></a>
	</div>
</section>

<?php
/* End of file view.php */
/* Location: ./theme/default/skin/board/list.php */
