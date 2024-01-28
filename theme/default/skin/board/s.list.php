<?php
if(!defined('__AFOX__')) exit();

$is_manager = isManager(__MID__);

setLang('srl', getLang('number'));
setLang('hit', getLang('view'));
setLang('nick', getLang('name'));
$show_column = $CONFIGS['show_column'];

$srl = empty($_POST['srl'])?0:$_POST['srl'];
$login_srl = empty($_MEMBER['mb_srl']) ? false : $_MEMBER['mb_srl'];
?>

<div class="list-group list-group-flush mb-4" aria-label="List of post">
<?php
	$class1 = 'd-flex w-100 justify-content-between';
	foreach ($LIST['data'] as $key => $val) {
		$wr_secret =  $val['wr_secret'] == '1';
		$wr_permit = !$wr_secret || $is_manager || $login_srl === $val['mb_srl'];
		$wr_title = !$wr_permit || $wr_secret ? '<svg class="bi me-1"><use href="'._AF_THEME_URL_.'bi-icons.svg#shield-lock"/></svg>' : '';
		$wr_title .= !$wr_permit ? getLang('error_permitted') : escapeHtml($val['wr_title'], true);
		$href = $wr_secret&&!$wr_permit ? '#' : getUrl('srl',$val['wr_srl'],'disp','','cpage','','rp','');
		echo '<a class="list-group-item list-group-item-action px-1'.($val['wr_srl']==$srl?' active" aria-current="true':'').'" href="'.$href.'">';
		echo '<div class="'.$class1.'"><span class="fs-5 mb-1">'.$wr_title.'</span></div>';
		echo '<div class="'.$class1.' text-body-secondary"><small>'.date('F j, Y', strtotime($val['wr_regdate'])).'</small><small>'.escapeHtml($val['mb_nick'], true).'</small></div></a>';
	}
?>
</div>