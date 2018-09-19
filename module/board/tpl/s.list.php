<?php
if(!defined('__AFOX__')) exit();

setLang('srl', getLang('number'));
setLang('hit', getLang('view'));
setLang('nick', getLang('name'));
$hidexs = ['wr_srl'=>1,'wr_hit'=>1,'wr_good'=>1,'wr_hate'=>1];
$show_column = $CONFIGS['show_column'];
?>

<article class="clearfix">
	<table class="table table-hover list-table" role="list">
	<?php if(!__MOBILE__) { ?>
	<thead>
		<tr>
			<?php
				foreach ($show_column as $col) {
					echo '<th class="'.$col.(empty($hidexs[$col])?'':' col-xs-1 hidden-xs').'">'.getLang(substr($col,3)).'</th>';
				}
			?>
		</tr>
	</thead>
	<?php } ?>
	<tbody>
	<?php
		$current_page = $LIST['current_page'];
		$total_page = $LIST['total_page'];
		$start_page = $LIST['start_page'];
		$end_page = $LIST['end_page'];
		$srl = empty($_DATA['srl'])?0:$_DATA['srl'];
		$_tmp = '<i class="glyphicon glyphicon-lock" aria-hidden="true"></i> ';

		$is_manager = isManager(__MID__);
		$login_srl = empty($_MEMBER['mb_srl']) ? false : $_MEMBER['mb_srl'];

		if(__MOBILE__) {
			$is_col_update = array_search('wr_update', $show_column)!==false;
			foreach ($LIST['data'] as $key => $val) {
				$wr_secret =  $val['wr_secret'] == '1';
				$wr_permit = !$wr_secret || $is_manager || $login_srl === $val['mb_srl'];
				echo '<tr data-hot-track'.($val['wr_srl']==$srl?' class="active"':'').'><td class="wr_title"><a href="'.(!$wr_permit&&$wr_secret?'#requirePassword" data-srl="'.$val['wr_srl'].'" data-param="srl,'.$val['wr_srl']:getUrl('srl',$val['wr_srl'],'disp','','cpage','','rp','')).'" onclick="return false">'.($wr_secret?$_tmp:'').escapeHtml($val['wr_title'], true).'</a>'.($val['wr_reply']>0?' <small>(+'.$val['wr_reply'].')</small>':'');
				echo '<div class="clearfix"><span class="mb_nick" data-srl="'.$val['mb_srl'].'" data-rank="'.(ord($val['mb_rank']) - 48).'">'.escapeHtml($val['mb_nick'], true).'</span>';
				echo '<span class="pull-right">'.date('m/d', strtotime($val[$is_col_update?'wr_update':'wr_regdate'])).'</span></div></td></tr>';
			}
		} else {
			foreach ($LIST['data'] as $key => $val) {
				$wr_secret =  $val['wr_secret'] == '1';
				$wr_permit = !$wr_secret || $is_manager || $login_srl === $val['mb_srl'];
				echo '<tr data-hot-track'.($val['wr_srl']==$srl?' class="active"':'').'>';
				foreach ($show_column as $col) {
					switch ($col) {
						case 'wr_title':
							echo '<td class="wr_title">'.(empty($val['wr_category'])?'':'<span>'.$val['wr_category'].' | </span>').'<a href="'.(!$wr_permit&&$wr_secret?'#requirePassword" data-srl="'.$val['wr_srl'].'" data-param="srl,'.$val['wr_srl']:getUrl('srl',$val['wr_srl'],'disp','','cpage','','rp','')).'" onclick="return false">'.($wr_secret?$_tmp:'').escapeHtml($val['wr_title'], true).'</a>'.($val['wr_reply']>0?' <small>(+'.$val['wr_reply'].')</small>':'').'</td>';
							break;
						case 'mb_nick':
							echo '<td class="col-xs-2" nowrap><span class="mb_nick" data-srl="'.$val['mb_srl'].'" data-rank="'.(ord($val['mb_rank']) - 48).'">'.escapeHtml($val['mb_nick'], true).'</span></td>';
							break;
						case 'wr_regdate':
						case 'wr_update':
							echo '<td class="col-xs-1">'.date('Y/m/d', strtotime($val[$col])).'</td>';
							break;
						default:
							echo '<td class="'.$col.(empty($hidexs[$col])?'':' col-xs-1 hidden-xs').'">'.($val[$col]).'</td>';
					}
				}
				echo '</tr>';
			}
		}
	?>

	</tbody>
	</table>
</article>
