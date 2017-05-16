<?php
if(!defined('__AFOX__')) exit();
$is_manager = isManager($_DATA['id']);

echo toHTML($_{'page'}['pg_type'], $_{'page'}['pg_content']);
?>

<?php if($is_manager) { ?>
<div class="clearfix">
	<div class="pull-right">
		<a href="<?php echo getUrl('disp','setupPage', 'id', $_DATA['id'])?>" role="button"><i class="glyphicon glyphicon-edit" aria-hidden="true"></i> <?php echo getLang('edit') ?></a>
	</div>
</div>
<?php } ?>

<?php
/* End of file default.php */
/* Location: ./theme/default/page/default.php */