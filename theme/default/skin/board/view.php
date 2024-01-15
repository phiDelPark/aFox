<?php
if(!defined('__AFOX__')) exit();
@include_once dirname(__FILE__) . '/common.php';

$is_manager = isManager(__MID__);
$is_rp_grant = isGrant('reply', __MID__);

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
$wr_content = toHTML($wr_content, $DOC['wr_type']);
?>

<section id="documentView" aria-label="Contents of this post">
	<h3 class="pb-3 mb-1 fst-italic border-bottom"><?php echo $DOC['wr_title']?></h3>
	<p class="text-secondary"><?php echo date('F j, Y', strtotime($DOC[$is_col_update?'wr_update':'wr_regdate'])).' by <span>'.$DOC['mb_nick']?></span></p>
	<div class="h-md-250 mb-3">
		<?php echo (empty($_DATA['search']) ? $wr_content : highlightText($_DATA['search'], $wr_content)) ?>
	</div>
	<p class="d-flex w-100 justify-content-between p-1 border-bottom">
		<span><a href="<?php echo getUrl('disp','','srl','','cpage','','rp','') ?>" class="icon-link-hover"><svg class="bi"><use xlink:href="<?php echo _AF_THEME_URL_ ?>bi-icons.svg#list-square"/></svg></a></span>
		<span><a href="<?php echo getUrl('disp','deleteDocument', 'srl', $_DATA['srl']) ?>" class="icon-link-hover"><svg class="bi"><use xlink:href="<?php echo _AF_THEME_URL_ ?>bi-icons.svg#x-square"/></svg></a>
		<a href="<?php echo getUrl('disp','writeDocument', 'srl', $_DATA['srl']) ?>" class="icon-link-hover"><svg class="bi"><use xlink:href="<?php echo _AF_THEME_URL_ ?>bi-icons.svg#pencil-square"/></svg></a></span>
	</p>
</section>

<?php
	if(!__POPUP__) {
		include 'reply.php';
		include 'list.php';
	}

/* End of file view.php */
/* Location: ./theme/default/skin/board/view.php */
