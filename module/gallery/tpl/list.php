<?php if(!defined('__AFOX__')) exit();
addJSLang(['confirm_page_left', 'confirm_page_right', 'confirm_delete', 'prompt_modify_item', 'item']);
addCSS(_AF_URL_ . 'module/gallery/tpl/gallery' . (__DEBUG__ ? '.css?' . _AF_SERVER_TIME_ : '.min.css'));
addJS(_AF_URL_ . 'module/gallery/tpl/gallery' . (__DEBUG__ ? '.js?' . _AF_SERVER_TIME_ : '.min.js'));

$current_page = $_DATA['current_page'];
$total_page = $_DATA['total_page'];
$start_page = $_DATA['start_page'];
$end_page = $_DATA['end_page'];
$total_count = $_DATA['total_count'];
$srl = @$_GET['srl']?$_GET['srl']:0;

$is_manager = isManager(_MID_);
$login_srl = empty($_MEMBER['mb_srl']) ? false : $_MEMBER['mb_srl'];
$asc = isset($_GET['asc']);

$_CFG['thumb_width'] = $_CFG['thumb_width'] ? $_CFG['thumb_width'] : 'auto';
$_CFG['thumb_height'] = $_CFG['thumb_height'] ? $_CFG['thumb_height'] : 'auto';
?>

<section id="galleryList" class="gallery">
	<h2 class="pb-3 mb-2 border-bottom"><?php echo $_CFG['md_title']?></h2>
<div class="w-100 d-flex justify-content-between">
<?php if(empty($_GET['srl']) && !empty($_CFG['md_category'])){ ?>
	<ol class="list-unstyled" aria-label="Category of the list">
	<?php
		$tmp = explode(',', $_CFG['md_category']);
		foreach ($tmp as $val) {
			$isEqual = $val == @$_GET['search'];
			$cateurl = getUrl('','id',_MID_,'search', urlencode($val)).($isEqual&&!$asc?'&asc':'');
			echo '<li class="d-inline mx-1"><a class="badge text-bg-secondary text-decoration-none'.($isEqual?' active" aria-current="page':'').'" href="'.$cateurl.'">'.$val.($isEqual?($asc?'▴':'▾'):'').'</a></li>';
		}
	?>
	</ol>
<?php } if($is_manager) echo '<a href="#setup" onclick="return themeShowCheckItems(this)" class="text-decoration-none" style="font-size:large">…</a>';?>
</div>
<div class="list-group list-group-flush mb-4" aria-label="List of post">
<?php
	$close_div = '';
	$w_cnt = _MOBILE_ ? 2 : $_CFG['horizontal_count'];
	foreach ($_DATA['list'] as $key => $val) {
		if((($key % $w_cnt) === 0)){
			echo $close_div.'<div class="w-100 d-flex justify-content-between">';
			$close_div = '</div>';
		}
		echo '<a href="'.getUrl('','id',_MID_).'" data-bs-toggle="modal" data-bs-target="#galleryContentModal">';
		echo '<img loading="lazy" width="'.$_CFG['thumb_width'].'" height="'.$_CFG['thumb_height'].'" src="./?file='.$val['mf_srl'].'&thumb=x"><div class="details"><span class="title">'.$val['mf_name'].'</span>';
		echo '<span class="info">'.date('F j, Y', strtotime($val['mf_regdate'])).'</span></div></a>';
		echo '<input class="form-check-input d-none" type="checkbox" value="'.$val['mf_srl'].'">';
	}
	if($close_div) echo $close_div;
	$start_page = $current_page - 4;
	if ($start_page < 1) $start_page = 1;
	$end_page = 9 + $start_page;
	if ($end_page > $total_page) $end_page = $total_page;
?>
</div>
	<div class="w-100 text-end bg-body-tertiary p-1">
		<nav id="paginationGallery" aria-label="Page navigation of the list">
		<ul class="pagination pagination-sm float-start">
			<li class="page-item me-1"><a class="btn btn-sm fw-bold btn-secondary<?php echo $current_page<11 ? ' disabled" aria-disabled="true' : ''?>" href="<?php echo  getUrl('page',$current_page-10)?>" aria-label="Previous-10">&lt;&lt;</a></li>
			<li class="page-item me-1 d-md-none"><a class="btn btn-sm btn-secondary<?php echo $current_page <= 1 ? ' disabled" aria-disabled="true' : ''?>" href="<?php echo  getUrl('page',$current_page-1)?>" aria-label="Previous">&lt;</a></li>
			<li class="page-item d-md-none"><a class="btn btn-sm border border-1 rounded-0 border-end-0 border-start-0 btn-outline-secondary disabled" aria-disabled="true"><?php echo $current_page.' / '.$total_page?></a></li>
			<?php for ($i=$start_page; $i <= $end_page; $i++) echo '<li class="page-item page-number d-none d-md-block" '.($current_page == $i ? 'selected="true"' : '').'><a class="btn btn-sm border border-1 rounded-0 border-end-0 border-start-0'.($current_page == $i ? ' text-decoration-underline fw-bold" aria-current="page' : ' btn-outline-secondary').'" href="'.getUrl('page',$i).'">'.$i.'</a></li>' ?>
			<li class="page-item ms-1 d-md-none"><a class="btn btn-sm btn-secondary<?php echo $total_page<($current_page+1) ? ' disabled" aria-disabled="true' : ''?>" href="<?php echo getUrl('page',$total_page<($current_page+1)?$total_page:$current_page+1)?>" aria-label="Next">&gt;</a></li>
			<li class="page-item ms-1"><a class="btn btn-sm fw-bold btn-secondary<?php echo $total_page <= $end_page ? ' disabled" aria-disabled="true' : ''?>" href="<?php echo getUrl('page',$total_page<($current_page+10)?$total_page:$current_page+10)?>" aria-label="Next+10">&gt;&gt;</a></li>
		</ul>
		</nav>
		<a class="btn btn-sm btn-secondary clearfix" href="<?php echo getUrl('disp','write','srl','')?>" role="button"><?php echo getLang('write') ?></a>
	</div>

<div class="modal fade" tabindex="-1" id="galleryContentModal" aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
		<div class="modal-body">
		<div id="carouselGallery" class="carousel slide">
			<div class="carousel-inner">
			</div>
			<button class="carousel-control-prev" data-bs-target="#carouselGallery" data-bs-slide="prev">
				<span class="carousel-control-prev-icon" aria-hidden="true"></span>
				<span class="visually-hidden">Previous</span>
			</button>
			<button class="carousel-control-next" data-bs-target="#carouselGallery" data-bs-slide="next">
				<span class="carousel-control-next-icon" aria-hidden="true"></span>
				<span class="visually-hidden">Next</span>
			</button>
		</div>
		</div>
		</div>
	</div>
</div>
</section>
<script>function themeShowCheckItems(e){let t=document.querySelectorAll(".gallery .list-group [type=checkbox]");return t?.forEach(e=>e.classList.remove("d-none")),e.onclick=function(e){let n="";t.forEach(e=>{e.checked&&(n+=e.value+",")});let l=function(e,t=""){exec_ajax({module:"gallery",act:e,md_id:"<?php echo _MID_?>",mf_srls:n,mf_about:t}).then(e=>{location.reload()}).catch(e=>{alert(e)})};if("DELETE"==e.target.innerText){let i=confirm($_LANG.confirm_delete.sprintf([$_LANG.item]));"object"==typeof i?i.then(()=>{l("deleteFiles")}):!0===i&&l("deleteFiles")}else if("MODIFY"==e.target.innerText){let o=prompt($_LANG.prompt_modify_item,"<?php echo str_replace('"','\"',@$_CFG['md_category'])?>");"object"==typeof o?o.then(e=>{l("modifyFiles",e)}):o.trim()&&l("modifyFiles",o.trim())}return!1},e.innerHTML="<span>DELETE</span><span> </span><span>MODIFY</span>",!1}</script>