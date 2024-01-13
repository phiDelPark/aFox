<?php
if(!defined('__AFOX__')) exit();
@include_once dirname(__FILE__) . '/config.php';
$_MODULE = getModule('@ledger');
$isLedgerDB = DB::status(_AF_LEDGER_DATA_TABLE_);
?>
<?php if($isLedgerDB){ ?>
	<div class="form-group">
		<label for="id_md_category"><?php echo getLang('category')?></label>
		<input type="text" name="md_category" class="form-control" id="id_md_category" maxlength="255" pattern="^[^\x21-\x2b\x2d\x2f\x3a-\x40\x5b-\x60]+" required placeholder="<?php echo getLang('desc_ledger_category')?>">
	</div>
<?php } else { ?>
	<p>
	<div class="panel panel-info" role="alert">
		<div class="panel-body">
		<input type="hidden" name="create_database" value="1">
		<?php echo getLang('desc_create_module_db')?>
		</div>
	</div>
	</p>
<?php } ?>
	<div class="form-group">
<?php if(!$isLedgerDB){ ?>
		<button type="submit" class="btn btn-primary mw-20"><i class="glyphicon glyphicon-ok" aria-hidden="true"></i> <?php echo getLang('btn_create_module_db')?></button>
<?php } ?>
	</div>
	<div class="form-group">
		<label><?php echo getLang('category')?></label>
	</div>
	<div class="form-group">
		<ul>
		<?php
		if($isLedgerDB){
			$categorys = getCategorys();
			foreach($categorys as $val){
				echo '<li data-srl="' . $val['ca_srl'] . '">' . $val['ca_name'] . '</li>';
			}
		}
		?>
		</ul>
	</div>
<?php
/* End of file setup.php */
/* Location: ./module/ledger/setup.php */
