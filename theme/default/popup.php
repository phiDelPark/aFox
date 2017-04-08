<?php
	if(!defined('__AFOX__')) exit();

?>

<?php if($err = get_error()) { ?>
	<div class="auto-hide" data-timer="5">
		<h3 class="clearfix"><span class="timer-progress pull-left" data-repeat-char="&bull;"></span> <i class="fa fa-<?php echo $err['error']!=0?'warning':'exclamation-circle'?>" aria-hidden="true"></i> <?php echo $err['message']?></h3>
	</div>
<?php } ?>

<div class="container">

	<div class="bs-popup-body">
		<section>
			<article>
			<?php echo dispModuleContent()?>
			</article>
		</section>
	</div>

</div>
