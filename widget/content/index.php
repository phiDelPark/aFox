<?php

if(!defined('__AFOX__')) exit();

// 테마에 스킨(tpl)이 있으면 사용
$tpl_file = _AF_THEME_PATH_ . 'widget/content.php';
if(file_exists($tpl_file)) {
include $tpl_file;
} else {
	$count = isset($_WIDGET['count']) ? (int)$_WIDGET['count'] : 5;
	$_list = empty($_WIDGET['module']) ? [] : getDBList(_AF_DOCUMENT_TABLE_,['md_id'=>$_WIDGET['module']],'wr_regdate desc',1,$count);
?>

<ul class="content_widget">
<?php
	foreach ($_list['data'] as $val) {
		echo '<li><a href="'.getUrl('','id',$val['md_id'],'srl',$val['wr_srl']).'">'.$val['wr_title'].'</a></li>';
	}
?>
</ul>

<?php }

/* End of file index.php */
/* Location: ./widget/content/index.php */