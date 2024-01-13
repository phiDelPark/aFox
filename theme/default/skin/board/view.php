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
$wr_content = preg_replace('/(<img[^>]*\s+)(src)(\s*=[^>]*>)/is', '\\1data-scroll-src\\3', $wr_content);
?>

<svg xmlns="http://www.w3.org/2000/svg" class="d-none">
	<symbol id="bi-pencil-square" viewBox="1 1 14 14">
		<path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
		<path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
	</symbol>
	<symbol id="bi-x-square" viewBox="0 0 16 16">
		<path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
		<path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
	</symbol>
	<symbol id="bi-list" viewBox="0 0 16 12">
	<path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5"/>
	</symbol>
</svg>

<section id="documentView" aria-label="Contents of this post">
	<h3 class="pb-3 mb-1 fst-italic border-bottom"><?php echo $DOC['wr_title']?></h3>
	<p class="text-secondary"><?php echo date('F j, Y', strtotime($DOC[$is_col_update?'wr_update':'wr_regdate'])).' by <span>'.$DOC['mb_nick']?></span></p>
	<div class="h-md-250 mb-3">
		<?php echo (empty($_DATA['search']) ? $wr_content : highlightText($_DATA['search'], $wr_content)) ?>
	</div>
	<p class="d-flex w-100 justify-content-between p-1 border-bottom">
		<span><a href="<?php echo getUrl('disp','','srl','','cpage','','rp','') ?>" class="icon-link-hover"><svg class="bi" width="1.4em" height="1.4em"><use xlink:href="#bi-list"/></svg></a></span>
		<span><a href="<?php echo getUrl('disp','deleteDocument', 'srl', $_DATA['srl']) ?>" class="icon-link-hover"><svg class="bi"><use xlink:href="#bi-x-square"/></svg></a>
		<a href="<?php echo getUrl('disp','writeDocument', 'srl', $_DATA['srl']) ?>" class="icon-link-hover"><svg class="bi"><use xlink:href="#bi-pencil-square"/></svg></a></span>
	</p>
</section>

<?php
	if(!__POPUP__) {
		include 'reply.php';
		include 'list.php';
	}

/* End of file view.php */
/* Location: ./theme/default/skin/board/view.php */
