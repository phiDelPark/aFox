<?php if(!defined('__AFOX__')) exit();
@include_once dirname(__FILE__) . '/common.php';
$is_rp_grant = isGrant('reply', _MID_);

$wr_mb_srl = $DOC['mb_srl'];
if(!empty($wr_mb_srl)) $doc_mb = getMember($wr_mb_srl);

$login_srl = empty($_MEMBER['mb_srl']) ? false : $_MEMBER['mb_srl'];
$wr_secret = $DOC['wr_secret'] == '1';
$wr_grant_view = $DOC['grant_view'];
$wr_grant_write = $DOC['grant_write'];

$show_column = $CONFIGS[$use_style=='review'?'show_rv_column':'show_column'];
$is_btn_download = array_search('btn_download',$show_column)!==false;
$is_col_update = $use_style!='timeline'&&($use_style=='gallery'||array_search('wr_update',$show_column)!==false);

$wr_content = ($wr_grant_view || !$wr_secret) ? $DOC['wr_content'] : getLang('error_permitted');
$wr_content = preg_replace('/(<img)(((?!loading)[^>])+)>/is', '\1\2 loading="lazy">', toHTML($wr_content, $DOC['wr_type']));

$asc = isset($_GET['asc']);
?>

<section id="documentView" aria-label="Contents of this post">
	<h2 class="pb-3 mb-1 border-bottom"><?php echo $DOC['wr_title']?></h2>
	<p class="d-flex w-100 justify-content-between text-secondary">
		<small><svg class="bi sm" style="vertical-align:-2px"><use href="./theme/default/bi-icons.svg#<?php echo $wr_secret ? 'shield-lock' : 'clock'?>"/></svg>
		<?php echo date('l jS \of F Y', strtotime($DOC[$is_col_update?'wr_update':'wr_regdate']))?></small>
		<small><?php echo $DOC['mb_nick']?></small>
	</p>
	<?php
	$md_extra_keys = empty($_CFG['md_extra']['keys']) ? [] : $_CFG['md_extra']['keys'];
	if (!empty($md_extra_keys)) {
		echo '<div class="border-bottom mb-3">';
		foreach($md_extra_keys as $ex_key=>$ex_name) {
			$tmp = @$DOC['wr_extra']['values'][$ex_key];
			$_boxs = explode('|', $ex_name);
			if(!($is_radio=count($_boxs)>1))$_boxs = explode('&', $ex_name);
			$ex_name = $_boxs[0]; $is_required = substr($ex_name, 0, 1) == '*';
			if($is_required) $ex_name = substr($ex_name, 1);
			if(preg_match('/^https?:\/\/.+/', $tmp)) $tmp = '<a href="'.escapeHTML($tmp).'" target="_blank">'.$tmp.'</a>';
?>
		<div class="text-truncate mb-2">
			<strong class="col-md-1 d-inline-block" style="max-width:100px"><?php echo $ex_name?></strong>
			<span><?php echo count($_boxs)>2?str_replace(',',', ',$tmp):$tmp?></span>
		</div>
<?php } echo '</div>'; } ?>
	<div class="h-md-250 mb-3 p-1">
		<?php echo (empty($_GET['search']) ? $wr_content : highlightText($_GET['search'], $wr_content)) ?>
	</div>
	<div class="clearfix"></div>
<?php if(!empty($DOC['wr_tags'])) {
	echo '<div class="mb-1" aria-label="Tags in this post">';
	$hashtags = explode(',', $DOC['wr_tags']);
	foreach ($hashtags as $val) {
		echo '<a class="icon-link icon-link-hover gap-0 me-2" href="'.getUrl('','id',_MID_,'search','+'.$val).'"><svg class="bi"><use href="./theme/default/bi-icons.svg#hash"/></svg>'.$val.'</a>';
	}
	echo '</div>';
} ?>
	<p class="d-flex w-100 justify-content-between p-1 border-bottom">
		<span><a href="<?php echo getUrl('disp','','srl','','cpage','','rp','').($asc?'&asc':'') ?>" class="icon-link-hover"><svg class="bi"><use href="./theme/default/bi-icons.svg#list-square"><?php echo getLang('list') ?></use></svg></a></span>
		<span><a href="<?php echo getUrl('disp','delete', 'srl', $_GET['srl']) ?>" class="icon-link-hover"><svg class="bi"><use href="./theme/default/bi-icons.svg#x-square"><?php echo getLang('delete') ?></use></svg></a>
		<a href="<?php echo getUrl('disp','write', 'srl', $_GET['srl']) ?>" class="icon-link-hover"><svg class="bi"><use href="./theme/default/bi-icons.svg#pencil-square"><?php echo getLang('edit') ?></use></svg></a></span>
	</p>
</section>

<?php
	if(!_POPUP_) {
		include 'reply.php';
		include 'list.php';
	}

/* End of file view.php */
/* Location: ./theme/default/skin/board/view.php */
