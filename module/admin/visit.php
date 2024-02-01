<?php
	if(!defined('__AFOX__')) exit();

	$_POST['page'] = empty($_POST['page'])?1:$_POST['page'];
	$search = empty($_POST['search']) ? '' : trim($_POST['search']);
	$_wheres = [
		"(_AND_)" => [], "(_OR_)" => []
	];

	if (!empty($search)) {
		$keys = [
			":" => "vs_referer", //:referer
			"@" => "mb_ipaddress", //@ip
			"d" => "vs_regdate", //d202010
		];
		$key = array_key_exists($key = substr($search, 0, 1) , $keys) ? $keys[$key] : '';
		empty($key) ? ($key = "vs_agent") : ($search = substr($search, 1));
		if ($search = explode(" ", $search)) {
			$index = 0;
			foreach ($search as $value) {
				$value = explode("&", trim($value));
				$and_or = count($value) > 1 ? "(_AND_)" : "(_OR_)";
				foreach ($value as $v) {
					if ($key == "vs_regdate") {
						$v = str_split($v, 4);
						$v = $v[0] . (empty($v[1]) ? "" : "-" . implode("-", str_split($v[1], 2)));
					} else {
						$v = "%" . $v;
					}
					$_wheres[$and_or][$key . "{LIKE}[" . $index++ . "]"] = DB::escape($v . "%");
				}
			}
		}
	}

	$vs_list = DB::gets(_AF_VISITOR_TABLE_, 'SQL_CALC_FOUND_ROWS *', $_wheres,'vs_regdate', (($_POST['page']-1)*20).',20');
	if($error = DB::error()) $error = set_error($error->getMessage(),$error->getCode());
	$vs_list = setDataListInfo($vs_list, $_POST['page'], 20, DB::foundRows());
?>

<table class="table">
<thead>
	<tr>
		<th scope="col">@<?php echo getLang('ip')?></th>
		<th scope="col" class="text-wrap"><?php echo getLang('agent')?></th>
		<th scope="col" class="text-end"><?php echo getLang('date')?></th>
	</tr>
</thead>
<tbody>

<?php
	$end_page = $total_page = 0;
	$start_page = $current_page = 1;

	if($error) {
		messageBox($error['message'], $error['error'], false);
	} else {
		$current_page = $vs_list['current_page'];
		$total_page = $vs_list['total_page'];
		$start_page = $vs_list['start_page'];
		$end_page = $vs_list['end_page'];

		foreach ($vs_list['data'] as $key => $value) {
			echo '<tr><th scope="row">'.$value['mb_ipaddress'].'</th>';
			echo '<td class="text-wrap">'.escapeHTML($value['vs_agent']).(empty($value['vs_referer'])?'':'<br><a href="'.$value['vs_referer'].'" target="_blank">'.escapeHTML($value['vs_referer']).'</a>').'</td>';
			echo '<td>'.date('y/m/d h:i', strtotime($value['vs_regdate'])).'</td></tr>';
		}
	}
?>

</tbody>
</table>

<div class="d-flex w-100 justify-content-between mt-4">
	<form action="<?php echo getUrl('') ?>" method="get">
		<input type="hidden" name="admin" value="<?php echo $_POST['disp'] ?>">
		<div class="input-group mb-3">
			<label class="input-group-text bg-transparent" for="search"<?php echo empty($_POST['search'])?'':' onclick="location.replace(\''.getUrl('search','').'\')"'?>><svg class="bi" aria-hidden="true"><use href="<?php echo _AF_URL_?>module/admin/bi-icons.svg#<?php echo empty($_POST['search'])?'search':'x-lg'?>"/></svg></label>
			<input type="text" name="search" id="search" value="<?php echo empty($_POST['search'])?'':$_POST['search'] ?>" class="form-control" style="max-width:140px;border-left:0" required>
			<button class="btn btn-default btn-outline-control" style="border-color:var(--bs-border-color)" type="submit"><?php echo getLang('search') ?></button>
		</div>
	</form>
	<nav aria-label="Page navigation of the list">
	<ul class="pagination">
		<?php if($start_page>10) echo '<li class="page-item"><a class="page-link" href="'.getUrl('page',$start_page-10).'">&laquo;</a></li>' ?>
		<li class="page-item text-nowrap"><a class="page-link <?php echo $current_page <= 1 ? ' disabled" aria-disabled="true' : ''?>" href="<?php echo  $current_page <= 1 ? '#' : getUrl('page',$current_page-1)?>" aria-label="Previous"><span aria-hidden="true">&lsaquo;</span> <?php echo getLang('previous') ?></a></li>
		<li class="page-item d-lg-none"><a class="page-link disabled" aria-disabled="true"><?php echo $current_page.' / '.$total_page?></a></li>
		<?php for ($i=$start_page; $i <= $end_page; $i++) echo '<li class="page-item d-none d-lg-inline-block"><a class="page-link'.($current_page == $i ? ' active" aria-current="page' : '').'" href="'.getUrl('page',$i).'">'.$i.'</a></li>' ?>
		<li class="page-item text-nowrap"><a class="page-link<?php echo $current_page >= $total_page ? ' disabled" aria-disabled="true' : ''?>" href="<?php echo $current_page >= $total_page ? '#' : getUrl('page',$current_page+1)?>" aria-label="Next"><?php echo getLang('next') ?> <span aria-hidden="true">&rsaquo;</span></a></li>
		<?php if(($total_page-$end_page)>0) echo '<li class="page-item"><a class="page-link" href="'.getUrl('page',$end_page+1).'">&raquo;</a></li>' ?>
	</ul>
	</nav>
</div>

<?php
/* End of file visit.php */
/* Location: ./module/admin/visit.php */
