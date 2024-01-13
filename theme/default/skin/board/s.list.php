<?php
if(!defined('__AFOX__')) exit();

$is_manager = isManager(__MID__);

setLang('srl', getLang('number'));
setLang('hit', getLang('view'));
setLang('nick', getLang('name'));
$show_column = $CONFIGS['show_column'];

$current_page = $LIST['current_page'];
$total_page = $LIST['total_page'];
$start_page = $LIST['start_page'];
$end_page = $LIST['end_page'];

$srl = empty($_DATA['srl'])?0:$_DATA['srl'];
$login_srl = empty($_MEMBER['mb_srl']) ? false : $_MEMBER['mb_srl'];
$_tmp = '<i class="glyphicon glyphicon-lock" aria-hidden="true"></i> ';
?>

<div class="list-group list-group-flush mb-4" aria-label="Content list">
<?php
	$class1 = 'd-flex w-100 justify-content-between';
	foreach ($LIST['data'] as $key => $val) {
		$wr_secret =  $val['wr_secret'] == '1';
		$wr_permit = !$wr_secret || $is_manager || $login_srl === $val['mb_srl'];
		$href = !$wr_permit&&$wr_secret?'#requirePassword" data-srl="'.$val['wr_srl'].'" data-param="srl,'.$val['wr_srl']:getUrl('srl',$val['wr_srl'],'disp','','cpage','','rp','');
		echo '<a class="list-group-item list-group-item-action'.($val['wr_srl']==$srl?' active" aria-current="true':'').'" href="'.$href.'">';
		echo '<div class="'.$class1.'"><h5 class="mb-1">'.escapeHtml($val['wr_title'], true).'</h5>'.($wr_secret?$_tmp:'').'</div>';
		echo '<div class="'.$class1.' text-body-secondary"><small>'.date('Y/m/d', strtotime($val['wr_regdate'])).'</small><small>'.escapeHtml($val['mb_nick'], true).'</small></div></a>';
	}
?>
</div>