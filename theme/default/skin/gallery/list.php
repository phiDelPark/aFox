<?php if(!defined('__AFOX__')) exit();
addJSLang(['confirm_delete', 'prompt_modify_file', 'file']);
addJS(_AF_THEME_URL_ . 'skin/gallery/gallery' . (__DEBUG__ ? '.js?' . _AF_SERVER_TIME_ : '.min.js'));
// 구버전 sql 용 초기화
$_POST['category'] = empty($_POST['category']) ? null : $_POST['category'];

$current_page = $_DATA['current_page'];
$total_page = $_DATA['total_page'];
$start_page = $_DATA['start_page'];
$end_page = $_DATA['end_page'];
$total_count = $_DATA['total_count'];
$srl = empty($_POST['srl'])?0:$_POST['srl'];

$is_manager = isManager(__MID__);
$login_srl = empty($_MEMBER['mb_srl']) ? false : $_MEMBER['mb_srl'];
?>

<section id="galleryList" class="gallery">
	<h2 class="pb-3 mb-2 border-bottom"><?php echo $_CFG['md_title']?></h2>
<div class="w-100 d-flex justify-content-between">
<?php if(empty($_POST['srl']) && !empty($_CFG['md_category'])){ ?>
	<ol class="list-unstyled" aria-label="Category of the list">
	<?php
		$tmp = explode(',', $_CFG['md_category']);
		foreach ($tmp as $val) {
			$isEqual = $val == $_POST['category'];
			$cateurl = getUrl('','id',__MID__,'search', urlencode($val));
			echo '<li class="d-inline mx-1"><a class="badge text-bg-secondary'.($isEqual?' text-decoration-underline active" aria-current="page':' text-decoration-none').'" href="'.$cateurl.'">'.$val.'</a></li>';
		}
	?>
	</ol>
<?php } if($is_manager) echo '<a href="#setup" onclick="return _showCheckItems(this)" class="text-decoration-none" style="font-size:large">…</a>';?>
</div>
<div class="list-group list-group-flush mb-4" aria-label="List of post">
<?php
	$close_div = '';
	foreach ($_DATA['data'] as $key => $val) {
		if((($key % 4) === 0)){
			echo $close_div.'<div class="w-100 d-flex justify-content-between">';
			$close_div = '</div>';
		}
		echo '<div style="position:relative">';
		echo '<a href="'.getUrl('','id',__MID__).'" data-bs-toggle="modal" data-bs-target="#galleryContentModal">';
		echo '<img src="./?file='.$val['mf_srl'].'&thumb=x"><div class="details"><span class="title">'.$val['mf_name'].'</span>';
		echo '<span class="info">'.date('F j, Y', strtotime($val['mf_regdate'])).'</span></div></a>';
		echo '<input class="form-check-input d-none" type="checkbox" value="'.$val['mf_srl'].'"></div>';
	}
	if($close_div) echo $close_div;
	$start_page = $current_page - 4;
	if ($start_page < 1) $start_page = 1;
	$end_page = 9 + $start_page;
	if ($end_page > $total_page) $end_page = $total_page;
?>
</div>
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

<div class="modal fade" tabindex="-1" id="galleryContentModal" aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
		<div class="modal-body">
		<div id="carouselGallery" class="carousel slide">
			<div class="carousel-inner">
			</div>
			<button class="carousel-control-prev" type="button" data-bs-target="#carouselGallery" data-bs-slide="prev">
				<span class="carousel-control-prev-icon" aria-hidden="true"></span>
				<span class="visually-hidden">Previous</span>
			</button>
			<button class="carousel-control-next" type="button" data-bs-target="#carouselGallery" data-bs-slide="next">
				<span class="carousel-control-next-icon" aria-hidden="true"></span>
				<span class="visually-hidden">Next</span>
			</button>
		</div>
		</div>
		</div>
	</div>
</div>
</section>
<script>
	function _showCheckItems(t) {
		const id = '<?php echo __MID__?>'
		const ckboxs = document.querySelectorAll('.gallery .list-group [type=checkbox]')
		ckboxs?.forEach(el => el.classList.remove('d-none'))
		t.onclick = function(e) {
			if(e.target.innerText == 'DELETE'){
				if (confirm($_LANG['confirm_delete'].sprintf([$_LANG['file']])) === true) {
					srls = ''; ckboxs.forEach(el => {if(el.checked) srls += el.value + ','})
					exec_ajax({module:'gallery',act:'deleteFiles',md_id:id,mf_srls:srls})
					.then((data)=>{location.reload()}).catch((error)=>{console.log(error);alert(error)})
				}
			}else{
				const s = prompt($_LANG['prompt_modify_file'], '').trim()
				if (s) {
					srls = ''; ckboxs.forEach(el => {if(el.checked) srls += el.value + ','})
					exec_ajax({module:'gallery',act:'modifyFiles',md_id:id,mf_about:s,mf_srls:srls})
					.then((data)=>{location.reload()}).catch((error)=>{console.log(error);alert(error)})
				}
			}
			return false
		}
		t.innerHTML = '<span>DELETE</span>, <span>MODIFY</span>';
		return false
	}
</script>