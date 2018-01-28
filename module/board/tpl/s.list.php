<?php
if(!defined('__AFOX__')) exit();
?>

<article class="clearfix">
	<table class="table table-hover list-table" role="list">
	<thead>
		<tr>
			<?php if(__MOBILE__) { ?>
			<th><?php echo getLang('title')?></th>
			<?php } else { ?>
			<th class="col-xs-1 hidden-xs"><?php echo getLang('number')?></th>
			<th><?php echo getLang('title')?></th>
			<th class="col-xs-2"><?php echo getLang('name')?></th>
			<th class="col-xs-1 hidden-xs"><?php echo getLang('view')?></th>
			<th class="col-xs-1"><?php echo getLang('date')?></th>
			<?php } ?>
		</tr>
	</thead>
	<tbody>

	<?php
		$current_page = $_{'board'}['current_page'];
		$total_page = $_{'board'}['total_page'];
		$start_page = $_{'board'}['start_page'];
		$end_page = $_{'board'}['end_page'];
		$srl = empty($_DATA['srl'])?0:$_DATA['srl'];
		$_tmp = '<i class="glyphicon glyphicon-lock" aria-hidden="true"></i> ';

		$is_manager = isManager(__MID__);
		$login_srl = empty($_MEMBER['mb_srl']) ? false : $_MEMBER['mb_srl'];

		if(__MOBILE__) {
			foreach ($_{'board'}['data'] as $key => $val) {
				$wr_secret =  $val['wr_secret'] == '1';
				$wr_permit = !$wr_secret || $is_manager || $login_srl === $val['mb_srl'];
				echo '<tr data-hot-track'.($val['wr_srl']==$srl?' class="active"':'').'><td class="wr_title"><a href="'.(!$wr_permit&&$wr_secret?'#requirePassword" data-srl="'.$val['wr_srl'].'" data-param="srl,'.$val['wr_srl']:getUrl('srl',$val['wr_srl'],'disp','','cpage','','rp','')).'" onclick="return false">'.($wr_secret?$_tmp:'').escapeHtml($val['wr_title'], true).'</a>'.($val['wr_reply']>0?' <small>(+'.$val['wr_reply'].')</small>':'');
				echo '<div class="clearfix"><span class="mb_nick" data-srl="'.$val['mb_srl'].'" data-rank="'.(ord($val['mb_rank']) - 48).'">'.escapeHtml($val['mb_nick'], true).'</span>';
				echo '<span class="pull-right">'.date('m/d', strtotime($val['wr_update'])).'</span></div></td></tr>';
			}
		} else {
			foreach ($_{'board'}['data'] as $key => $val) {
				$wr_secret =  $val['wr_secret'] == '1';
				$wr_permit = !$wr_secret || $is_manager || $login_srl === $val['mb_srl'];
				echo '<tr data-hot-track'.($val['wr_srl']==$srl?' class="active"':'').'><th class="hidden-xs" scope="row">'.$val['wr_srl'].'</th>';
				echo '<td class="wr_title"><a href="'.(!$wr_permit&&$wr_secret?'#requirePassword" data-srl="'.$val['wr_srl'].'" data-param="srl,'.$val['wr_srl']:getUrl('srl',$val['wr_srl'],'disp','','cpage','','rp','')).'" onclick="return false">'.($wr_secret?$_tmp:'').escapeHtml($val['wr_title'], true).'</a>'.($val['wr_reply']>0?' <small>(+'.$val['wr_reply'].')</small>':'').'</td>';
				echo '<td nowrap><span class="mb_nick" data-srl="'.$val['mb_srl'].'" data-rank="'.(ord($val['mb_rank']) - 48).'">'.escapeHtml($val['mb_nick'], true).'</span></td>';
				echo '<td class="hidden-xs">'.$val['wr_hit'].'</td>';
				echo '<td>'.date('Y/m/d', strtotime($val['wr_update'])).'</td></tr>';
			}
		}
	?>

	</tbody>
	</table>
</article>
