<?php
if(!defined('__AFOX__')) exit();
?>

<article class="clearfix" role="list">
	<?php
		$current_page = $_{'board'}['current_page'];
		$total_page = $_{'board'}['total_page'];
		$start_page = $_{'board'}['start_page'];
		$end_page = $_{'board'}['end_page'];
		$srl = empty($_DATA['srl'])?0:$_DATA['srl'];
		$_tmp = '<i class="glyphicon glyphicon-lock" aria-hidden="true"></i> ';

		$is_manager = isManager(__MID__);
		$login_srl = empty($_MEMBER['mb_srl']) ? false : $_MEMBER['mb_srl'];

		$toggle = false;
		$toggle2 = false;
		$box_items = [false=>'',true=>''];
		$_tmp_image = _AF_URL_.'common/img/no_image.png';

		foreach ($_{'board'}['data'] as $key => $val) {
			$wr_secret =  $val['wr_secret'] == '1';
			$wr_permit = !$wr_secret || $is_manager || $login_srl === $value['mb_srl'];
			$_image = DB::get(_AF_FILE_TABLE_, ['md_id'=>__MID__, 'mf_target'=>$val['wr_srl'], 'mf_type{LIKE}'=>'image%']);
			echo $toggle?'':'<div class="item_area clearfix">';
	?>

			<div class="col-xs-<?php echo ($toggle2?'4':'8').(__MOBILE__?' mobile':'') ?>">
				<div class="title-container"><h3 class="text-ellipsis"><?php echo ($wr_secret?$_tmp:'').escapeHtml($val['wr_title'], true)?></h3></div>
				<a href="<?php echo (!$wr_permit&&$wr_secret?'#requirePassword" data-srl="'.$val['wr_srl'].'" data-param="srl,'.$val['wr_srl']:getUrl('srl',$val['wr_srl'],'disp','','cpage','','rp',''))?>">
					<div class="img-container" style="background-image: url('<?php echo (empty($_image['mf_srl'])?$_tmp_image:_AF_URL_.'?file='.$_image['mf_srl'].'&thumb')?>')"></div>
				</a>
			</div>

	<?php
			$toggle = !$toggle;
			$toggle2 = !$toggle?$toggle2:!$toggle2;

			echo $toggle?'':'</div>';
		}

		$toggle?'</div>':'';
	?>
</article>
