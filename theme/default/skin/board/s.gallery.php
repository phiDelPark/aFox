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
	foreach ($LIST['data'] as $key => $val) {
		$_image = DB::gets(_AF_FILE_TABLE_, ['md_id'=>__MID__, 'mf_target'=>$val['wr_srl'], 'mf_type{LIKE}'=>'image%']);
		$wr_secret =  $val['wr_secret'] == '1';
		$wr_permit = !$wr_secret || $is_manager || $login_srl === $val['mb_srl'];
		$wr_title = !$wr_permit || $wr_secret ? '<svg class="bi me-1"><use href="'._AF_THEME_URL_.'bi-icons.svg#shield-lock"/></svg>' : '';
		$wr_title .= !$wr_permit ? getLang('error_permitted') : escapeHTML(strip_tags($val['wr_title']));
		$href = $wr_secret&&!$wr_permit ? '#' : getUrl('srl',$val['wr_srl'],'disp','','cpage','','rp','');
		echo '<div class="w-100 d-flex flex-wrap">';
		if(count($_image)==0) $_image = ['mf_srl'=>0];
		foreach ($_image as $v){
		echo '<a style="min-width:'.$_CFG['thumb_width'].'px;min-height:'.$_CFG['thumb_width'].'px" class="'.($val['wr_srl']==$srl?' active" aria-current="true':'').'" href="'.$href.'">';
		echo '<img src="./file='.$v['mf_srl'].'"><div class="details"><span class="title">'.$wr_title.'</span>';
		echo '<span class="info">'.date('F j, Y', strtotime($val['wr_regdate'])).' by '.$val['mb_nick'].'</span></div></a>';
		}
		echo '</div>';
	}
?>
</div>