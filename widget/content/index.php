<?php
if(!defined('__AFOX__')) exit();
if(empty($_WIDGET['module']) || _MODULE_ != 'page') return;

$title = empty($_WIDGET['title']) ? '' : $_WIDGET['title'];
$target = empty($_WIDGET['target']) ? '' : $_WIDGET['target'];
$category = empty($_WIDGET['category']) ? '' : $_WIDGET['category'];
$order = empty($_WIDGET['order']) ? 'wr_regdate' : $_WIDGET['order'];
$type = isset($_WIDGET['type']) ? $_WIDGET['type'] : 'default';
$count = isset($_WIDGET['count']) ? (int)$_WIDGET['count'] : 5;
$class = isset($_WIDGET['class']) ? ' '.$_WIDGET['class'] : '';
$style = isset($_WIDGET['style']) ? 'style="'.$_WIDGET['style'].'"' : '';
$style = _MOBILE_ && isset($_WIDGET['mstyle']) ? 'style="'.$_WIDGET['mstyle'].'"' : $style;


$md_title = getModule($_WIDGET['module'], 'md_title');
if($type === 'gallery') {
	$fl = _AF_FILE_TABLE_;
	$dd = _AF_DOCUMENT_TABLE_;
	$_list = DB::query("SELECT f.*, d.md_id FROM $fl as f INNER JOIN $dd as d ON d.wr_srl=f.mf_target AND d.md_id = f.md_id WHERE f.md_id=:1 AND f.mf_size>:2 AND f.mf_type LIKE :3 GROUP BY f.mf_target ORDER BY rand() DESC LIMIT 5", [$_WIDGET['module'],500, 'image%'], true);
} else {
	$select = ['md_id'=>$_WIDGET['module']];
	if(is_numeric($target)){
		$select["wr_srl"] = $target;
		$_list = DB::get(_AF_DOCUMENT_TABLE_, 'wr_content', $select);
	} else {
		if(!empty($category)) $select['wr_category'] = $category;
		$_list = DB::gets(_AF_DOCUMENT_TABLE_,$select,$order,$count);
	}
}
?>
<div class="content_widget<?php echo $class?>" <?php echo $style?>>
	<h5><?php echo empty($title) ? (empty($md_title)?'':$md_title) : $title ?>
	<a class="float-end text-decoration-none" style="font-size:large" href="<?php echo getUrl('','id',$_WIDGET['module'],'category',$category)?>">&hellip;</a></h5>
	<div class="clearfix mt-1"></div>
	<?php if($type === 'gallery') { ?>
		<div class="p-2 border rounded" role="list">
		<?php
			$w = (100 / $count);
			foreach ($_list as $val) {
				echo '<a href="'.getUrl('','id',$val['md_id'],'srl',$val['mf_target']).'"'.(empty($target)?'':' target="'.$target.'"').'><img class="d-inline-block p-2" src="./?file='.$val['mf_srl'].'&thumb=100x100" width="'.$w.'%" style="max-height:150px;"></a>';
			}
		?>
		</div>
	<?php } else if(is_numeric($target)) { echo toHTML($_list['wr_content'], $type == 'text' ? 0 : 1); } else { ?>
		<div class="list-group" role="list">
		<?php
			foreach ($_list as $val) {
				echo '<a class="list-group-item d-inline-block text-truncate" href="'.getUrl('','id',$val['md_id'],'srl',$val['wr_srl']).'" target="'.$target.'">'.$val['wr_title'].'</a>';
			}
		?>
		</div >
	<?php } ?>
</div>

<?php

/* End of file index.php */
/* Location: ./widget/content/index.php */
