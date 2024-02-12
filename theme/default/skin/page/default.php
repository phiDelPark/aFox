<?php if(!defined('__AFOX__')) exit();
?>

<section id="pageView">
	<h2 class="pb-3 mb-3 border-bottom"><?php echo $_CFG['md_title']?></h2>
<?php if(isAdmin(_MID_)) { ?>
	<div class="position-relative" style="top:-1em">
		<a href="<?php echo getUrl('disp','setupPage', 'id', _MID_)?>" class="icon-link-hover text-decoration-none position-absolute top-0 end-0"><svg class="bi"><use href="<?php echo _AF_THEME_URL_ ?>bi-icons.svg#pencil-square"><?php echo getLang('edit') ?></use></svg></a>
	</div>
<?php }
echo toHTML(preg_replace('@\[_(/?)(STYLE|SCRIPT)/?_\]@is', '<\\1\\2>', $_DATA['pg_content']), $_DATA['pg_type']);
?>
</section>
<?php
/* End of file default.php */
/* Location: ./theme/default/page/default.php */
