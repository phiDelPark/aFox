<?php if(!defined('__AFOX__')) exit();
@include_once dirname(__FILE__) . '/common.php';
?>
<svg xmlns="http://www.w3.org/2000/svg" class="d-none">
	<symbol id="bi-pencil-square" viewBox="1 1 14 14">
		<path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
		<path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
	</symbol>
</svg>

<section id="pageView">
	<h3 class="pb-3 mb-3 fst-italic border-bottom"><?php echo $_CFG['md_title']?></h3>
<?php if(isAdmin(__MID__)) { ?>
	<div class="position-relative">
		<a href="<?php echo getUrl('disp','setupPage', 'id', __MID__)?>" class="icon-link-hover text-decoration-none position-absolute top-0 end-0"><svg class="bi"><use xlink:href="#bi-pencil-square"/></svg></a>
	</div>
<?php }
$pg_content = toHTML(preg_replace('@\[_(/?)(STYLE|SCRIPT)/?_\]@is', '<\\1\\2>', $PAGE['pg_content']), $PAGE['pg_type']);
echo preg_replace('/(<img[^>]*\s+)(src)(\s*=[^>]*>)/is', '\\1data-scroll-src\\3', $pg_content);
?>
</section>
<?php
/* End of file default.php */
/* Location: ./theme/default/page/default.php */
