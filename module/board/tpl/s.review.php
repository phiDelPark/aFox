<?php
if(!defined('__AFOX__')) exit();
$show_rv_column = array_flip($CONFIGS['show_rv_column']);
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

		$box_items = [false=>'',true=>''];
		$_tmp_image = _AF_URL_.'common/img/no_image.png';
		$_required_extras = [];

		// 확장변수가 있으면 필수만 골라냄
		if(isset($show_rv_column['extra_vars'])&&!empty($_CFG['md_extra']['keys'])) {
			foreach($_CFG['md_extra']['keys'] as $ex_key=>$ex_caption){
				if(substr($ex_caption,-1,1) === '*') $_required_extras[] = $ex_key;
			}
		}

		foreach($LIST['data'] as $key => $val) {
			$wr_secret =  $val['wr_secret'] == '1';
			$wr_permit = !$wr_secret || $is_manager || $login_srl === $val['mb_srl'];

			$_image = DB::get(_AF_FILE_TABLE_, ['md_id'=>__MID__, 'mf_target'=>$val['wr_srl'], 'mf_type{LIKE}'=>'image%']);
			$wr_extra_vars = '';

			if(count($_required_extras) > 0) {
				foreach($_required_extras as $ex_key){
					$wr_extra_vars .= '<div class="wr_extra_area"><strong>'.substr($_CFG['md_extra']['keys'][$ex_key],0,-1).':</strong> <span>'.(@$val['wr_extra']['vars'][$ex_key]).'</span></div>';
				}
			}

			if(isset($show_rv_column['btn_download'])) {
				$_file = DB::get(_AF_FILE_TABLE_, ['md_id'=>__MID__,'mf_target'=>$val['wr_srl'],'mf_type{LIKE}'=>'application%','(_OR_)'=>['^'=>'LOWER(`mf_name`)LIKE\'%.zip\'OR LOWER(`mf_name`)LIKE\'%.7z\'']]);
				if(!empty($_file['mf_srl'])){
					$wr_extra_vars .= '<div class="wr_extra_area btn_download"><strong>'.getLang('download').':</strong> <a href="./?file='.$_file['mf_srl'].'"><code>'.$_file['mf_name'].'</code></a></div>';
				}
			}

			$box_items[true] = '<a href="'.(!$wr_permit&&$wr_secret?'#requirePassword" data-srl="'.$val['wr_srl'].'" data-param="srl,'.$val['wr_srl']:getUrl('srl',$val['wr_srl'],'disp','','cpage','','rp','')).'"><div class="img-container" style="background-image: url(\''.(empty($_image['mf_srl'])?$_tmp_image:_AF_URL_.'?file='.$_image['mf_srl'].'&thumb').'\')"></div></a>';
			$box_items[false] = '<h3 class="text-ellipsis"><a href="'.(!$wr_permit&&$wr_secret?'#requirePassword" data-srl="'.$val['wr_srl'].'" data-param="srl,'.$val['wr_srl']:getUrl('srl',$val['wr_srl'],'disp','','cpage','','rp','')).'"'.($val['wr_srl']==$srl?' class="active"':'').'>'.($wr_secret?$_tmp:'').escapeHtml($val['wr_title'], true).'</a>'.($val['wr_reply']>0?' <small>(+'.$val['wr_reply'].')</small>':'').'</h3>';
			$box_items[false] .= '<div class="author clearfix">'.(isset($show_rv_column['mb_nick'])?'<span class="mb_nick pull-left" data-srl="'.$val['mb_srl'].'" data-rank="'.(ord($val['mb_rank']) - 48).'">'.$val['mb_nick'].'</span>':'').'<span class="pull-right">'.date('Y/m/d', strtotime($val[(isset($show_rv_column['wr_update'])?'wr_update':'wr_regdate')])).'</span></div>'.$wr_extra_vars.'<div class="wr_content">'.cutstr(!$wr_permit&&$wr_secret?getLang('msg_is_secret'):escapeHtml($val['wr_content'], true, ENT_QUOTES, false), __MOBILE__?100:300).'</div>';
			?>

			<div class="item_area<?php echo (__MOBILE__?' mobile':'').($val['wr_srl']==$srl?' active':'') ?> clearfix">
				<div class="pull-left">
					<?php echo $box_items[true] ?>
				</div>
				<div class="pull-right">
					<?php echo $box_items[false] ?>
				</div>
			</div>

			<?php
		}
	?>

</article>
