<?php if(!defined('__AFOX__')) exit();

setLang('srl', getLang('number'));
setLang('hit', getLang('view'));
setLang('nick', getLang('name'));
$show_column = $CONFIGS['show_column'];

$srl = @$_GET['srl']?$_GET['srl']:0;
$login_srl = empty($_MEMBER['mb_srl']) ? false : $_MEMBER['mb_srl'];
?>

<div class="list-group list-group-flush mb-4" aria-label="List of post">
<?php
	foreach ($LIST as $key => $val) {
		$wr_secret =  $val['wr_secret'] == '1';
		$wr_permit = !$wr_secret || $is_manager || $login_srl === $val['mb_srl'];
		$wr_title = !$wr_permit || $wr_secret ? '<svg class="bi me-1"><use href="'._AF_THEME_URL_.'bi-icons.svg#shield-lock"/></svg>' : '';
		$wr_title .= !$wr_permit ? getLang('error_permitted') : escapeHTML($val['wr_title']);
		$href = $wr_secret&&!$wr_permit ? '#' : getUrl('srl',$val['wr_srl'],'cpage','','rp','').($asc?'&asc':'');
		echo '<div class="w-100 d-flex justify-content-between">';
		$_image = DB::gets(_AF_FILE_TABLE_, ['md_id'=>_MID_, 'mf_target'=>$val['wr_srl'], 'mf_type{LIKE}'=>'image%']);
		if(count($_image)===0) $_image = ['mf_srl'=>0];
		foreach ($_image as $v){
		echo '<a class="'.($val['wr_srl']==$srl?' active" aria-current="true':'').'" href="'.$href.'">';
		echo '<img src="./?file='.$v['mf_srl'].'&thumb=x"><div class="details"><span class="title">'.$wr_title.'</span>';
		echo '<span class="info">'.date('F j, Y', strtotime($val['wr_regdate'])).' by '.$val['mb_nick'].'</span></div></a>';
		}
		echo '</div>';
	}
?>
</div>