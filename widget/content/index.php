<?php

if(!defined('__AFOX__')) exit();

// 테마에 스킨(tpl)이 있으면 사용
$tpl_file = _AF_THEME_PATH_ . 'widget/content.php';
if(file_exists($tpl_file)) {
include $tpl_file;
} else {
	if(empty($_WIDGET['module'])) return;

	$type = isset($_WIDGET['type']) ? $_WIDGET['type'] : 'default';
	$count = isset($_WIDGET['count']) ? (int)$_WIDGET['count'] : 5;
	$style = isset($_WIDGET['style']) ? $_WIDGET['style'] : 'width:100%';

	$md_title = getModule($_WIDGET['module'], 'md_title');
	if($type === 'gallery') {
		$_list = DB::getList('SELECT * FROM '._AF_FILE_TABLE_.' WHERE md_id=:1 GROUP BY mf_target ORDER BY mf_regdate desc LIMIT 1,'.$count, [$_WIDGET['module']]);
	} else {
		$_list = getDBList(_AF_DOCUMENT_TABLE_,['md_id'=>$_WIDGET['module']],'wr_regdate desc',1,$count);
	}
?>
<div class="content_widget panel panel-default" style="<?php echo $style?>">
	<div class="panel-heading clearfix">
	<?php echo $md_title ?>
	<a class="pull-right" href="<?php echo getUrl('','id',$_WIDGET['module'])?>"><span class="glyphicon glyphicon-option-horizontal" aria-hidden="true"></span></a>
	</div>
	<?php if($type === 'gallery') { ?>
		<div class="panel-body" style="height:150px">
		<?php
			$w = (100 / $count);
			foreach ($_list as $val) {
				echo '<a href="'.getUrl('','id',$val['md_id'],'srl',$val['mf_target']).'" style="margin:0;padding:0"><img src="./?file='.$val['mf_srl'].'&thumb=100x100" width="'.$w.'%" height="100%" style="margin:0;padding:3px"></a>';
			}
		?>
		</div>
	<?php } else { ?>
		<div  class="list-group">
		<?php
			foreach ($_list['data'] as $val) {
				echo '<a class="list-group-item" href="'.getUrl('','id',$val['md_id'],'srl',$val['wr_srl']).'">'.$val['wr_title'].'</a>';
			}
		?>
		</div >
	<?php } ?>
</div>

<?php }

/* End of file index.php */
/* Location: ./widget/content/index.php */