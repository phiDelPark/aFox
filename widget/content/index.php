<?php

if(!defined('__AFOX__')) exit();

// 테마에 스킨(tpl)이 있으면 사용
$tpl_file = _AF_THEME_PATH_ . 'widget/content.php';
if(file_exists($tpl_file)) {
include $tpl_file;
} else {
	if(empty($_WIDGET['module'])) return;
	$count = isset($_WIDGET['count']) ? (int)$_WIDGET['count'] : 5;
	$width = isset($_WIDGET['width']) ? $_WIDGET['width'] : '100%';
	$_list = getDBList(_AF_DOCUMENT_TABLE_,['md_id'=>$_WIDGET['module']],'wr_regdate desc',1,$count);
	$md_title = getModule($_WIDGET['module'])['md_title'];
?>
<div class="content_widget panel panel-default" style="width:<?php echo $width?>">
	<div class="panel-heading clearfix">
	<?php echo $md_title ?>
	<a class="pull-right" href="<?php echo getUrl('','id',$_WIDGET['module'])?>"><span class="glyphicon glyphicon-option-horizontal" aria-hidden="true"></span></a>
	</div>
	<div  class="list-group">
	<?php
		foreach ($_list['data'] as $val) {
			echo '<a class="list-group-item" href="'.getUrl('','id',$val['md_id'],'srl',$val['wr_srl']).'">'.$val['wr_title'].'</a>';
		}
	?>
	</div >
</div>

<?php }

/* End of file index.php */
/* Location: ./widget/content/index.php */