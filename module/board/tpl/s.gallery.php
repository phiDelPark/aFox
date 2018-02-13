<?php
if(!defined('__AFOX__')) exit();
?>

<article class="clearfix" role="list">
	<?php
		$current_page = $LIST['current_page'];
		$total_page = $LIST['total_page'];
		$start_page = $LIST['start_page'];
		$end_page = $LIST['end_page'];
		$srl = empty($_DATA['srl'])?0:$_DATA['srl'];
		$_tmp = '<i class="glyphicon glyphicon-lock" aria-hidden="true"></i> ';

		$is_manager = isManager(__MID__);
		$login_srl = empty($_MEMBER['mb_srl']) ? false : $_MEMBER['mb_srl'];

		$toggle = false;
		$toggle2 = false;
		$box_items = [false=>'',true=>''];
		$_tmp_image = _AF_URL_.'common/img/no_image.png';

		foreach ($LIST['data'] as $key => $val) {
			$wr_secret =  $val['wr_secret'] == '1';
			$wr_permit = !$wr_secret || $is_manager || $login_srl === $val['mb_srl'];
			$_image = DB::gets(_AF_FILE_TABLE_, ['md_id'=>__MID__, 'mf_target'=>$val['wr_srl'], 'mf_type{LIKE}'=>'image%']);
			if(!$wr_permit&&$wr_secret){
				$href = '#requirePassword" data-srl="'.$val['wr_srl'].'" data-param="srl,'.$val['wr_srl'];
			} else {
				$edit = '';
				$link = getUrl('srl',$val['wr_srl'],'disp','','cpage','','rp','');
				if(empty($CONFIGS['modal_image_view'])) {
					$href = $link;
				} else {
					$mf_srls = [];
					foreach ($_image as $v) $mf_srls[] = $v['mf_srl'];
					asort($mf_srls);
					$href = '#" data-toggle="modal" data-target="#af_gallery_modal" data-mf-srls="'.implode(',', $mf_srls);
					if(empty($val['mb_srl']) || $is_manager || $login_srl === $val['mb_srl']){
						$edit = '<a class="edit-button" href="'.$link.'&disp=writeDocument"><i class="glyphicon glyphicon-edit" aria-hidden="true"></i></a>';
					}
				}
			}
			echo $toggle?'':'<div class="item_area clearfix">';
	?>

			<div class="col-xs-<?php echo ($toggle2?'4':'8').(__MOBILE__?' mobile':'') ?>">
				<div class="title-container">
					<div>
						<div class="text-ellipsis"><?php echo ($wr_secret?$_tmp:'').escapeHtml($val['wr_title'], true)?></div>
						<?php if(!empty($CONFIGS['show_gl_column'])) {
						echo '<div><span class="mb_nick pull-left" data-srl="'.$val['mb_srl'].'" data-rank="'.(ord($val['mb_rank']) - 48).'">'.$val['mb_nick'].'</span><span class="pull-right">'.date('Y/m/d', strtotime($val['wr_update'])).'</span></div>';
						} ?>
					</div>
				</div>
				<a href="<?php echo $href ?>">
					<div class="img-container" style="background-image: url('<?php echo (empty($_image[0])?$_tmp_image:_AF_URL_.'?file='.$_image[0]['mf_srl'].'&thumb')?>')"></div>
				</a>
				<?php echo $edit ?>
			</div>

	<?php
			$toggle = !$toggle;
			$toggle2 = !$toggle?$toggle2:!$toggle2;

			echo $toggle?'':'</div>';
		}

		$toggle?'</div>':'';
	if(!empty($CONFIGS['modal_image_view'])) { ?>
	<div class="modal fade" id="af_gallery_modal" tabindex="-1" role="dialog">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
		<button type="button" class="close prev" aria-label="Prev"><span aria-hidden="true">&lt;</span></button>
		<button type="button" class="close next" aria-label="Next"><span aria-hidden="true">&gt;</span></button>
		<div class="modal-dialog" role="document">
			<div class="modal-content">
			<div class="modal-body">
			</div>
			</div>
		</div>
	</div>
	<?php } ?>
</article>
