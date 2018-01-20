<?php
if(!defined('__AFOX__')) exit();
if(empty($_WIDGET['module']) || __MODULE__ != 'page') return;

$type = isset($_WIDGET['type']) ? $_WIDGET['type'] : 'default';
$count = isset($_WIDGET['count']) ? (int)$_WIDGET['count'] : 5;
$class = isset($_WIDGET['class']) ? $_WIDGET['class'] : '';
$style = isset($_WIDGET['style']) ? 'style="'.$_WIDGET['style'].'"' : '';
$style = __MOBILE__ && isset($_WIDGET['mobile-style']) ? 'style="'.$_WIDGET['mobile-style'].'"' : $style;

$md_title = getModule($_WIDGET['module'], 'md_title');
if($type === 'gallery') {
	$fl = _AF_FILE_TABLE_;
	$dd = _AF_DOCUMENT_TABLE_;
	$_list = DB::query("SELECT f.*, d.md_id FROM $fl as f INNER JOIN $dd as d ON d.wr_srl=f.mf_target AND d.md_id = f.md_id WHERE f.md_id=:1 AND f.mf_size>:2 AND f.mf_type LIKE :3 GROUP BY f.mf_target ORDER BY rand() DESC LIMIT 5", [$_WIDGET['module'],500, 'image%'], true);
} else {
	$_list = DB::gets(_AF_DOCUMENT_TABLE_,['md_id'=>$_WIDGET['module']],'wr_regdate',$count);
}
?>
<div class="content_widget <?php echo $class?>" <?php echo $style?>>
<div class="panel panel-default" role="group" aria-labelledby="afWidgetTitle">
	<div class="panel-heading clearfix" id="afWidgetTitle">
	<?php echo $md_title ?>
	<a class="pull-right" href="<?php echo getUrl('','id',$_WIDGET['module'])?>"><span class="glyphicon glyphicon-option-horizontal" aria-hidden="true"></span></a>
	</div>
	<?php if($type === 'gallery') { ?>
		<div class="panel-body" style="overflow:hidden;padding:8px" role="list">
		<?php
			$w = (100 / $count);
			foreach ($_list as $val) {
				echo '<a href="'.getUrl('','id',$val['md_id'],'srl',$val['mf_target']).'"><img src="./?file='.$val['mf_srl'].'&thumb=100x100" width="'.$w.'%" style="display:inline-block;max-height:150px;margin:0;padding:8px"></a>';
			}
		?>
		</div>
	<?php } else { ?>
		<div class="list-group" role="list">
		<?php
			foreach ($_list as $val) {
				echo '<a class="list-group-item text-ellipsis" href="'.getUrl('','id',$val['md_id'],'srl',$val['wr_srl']).'">'.$val['wr_title'].'</a>';
			}
		?>
		</div >
	<?php } ?>
</div></div>

<?php

/* End of file index.php */
/* Location: ./widget/content/index.php */
