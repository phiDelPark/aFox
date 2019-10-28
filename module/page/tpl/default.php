<?php
if(!defined('__AFOX__')) exit();
$pg_content = toHTML(preg_replace('@\[_(/?)(STYLE|SCRIPT)/?_\]@is', '<\\1\\2>', $PAGE['pg_content']), $PAGE['pg_type']);
echo preg_replace('/(<img[^>]*\s+)(src)(\s*=[^>]*>)/is', '\\1data-scroll-src\\3', $pg_content);
?>

<?php if(isAdmin(__MID__)) { ?>
<div class="clearfix">
	<div class="pull-right">
		<a href="<?php echo getUrl('disp','setupPage', 'id', __MID__)?>" role="button"><i class="glyphicon glyphicon-edit" aria-hidden="true"></i> <?php echo getLang('edit') ?></a>
	</div>
</div>
<?php } ?>

<?php
/* End of file default.php */
/* Location: ./theme/default/page/default.php */
