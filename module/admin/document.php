<?php if(!defined('__AFOX__')) exit();

	$_POST['page'] = empty($_POST['page'])?1:$_POST['page'];
	$search = empty($_POST['search']) ? '' : trim($_POST['search']);
	$category = empty($_POST['category'])?null:$_POST['category'];
	$_wheres = [
		'md_id'.(empty($category)?'{<>}':'')=>empty($category)?'_AFOXtRASH_':$category,
		"(_AND_)" => [], "(_OR_)" => []
	];

	if (!empty($search)) {
		$keys = [
			":" => "wr_title", //:title
			"@" => "mb_nick", //@nick
			"#" => "wr_tags", //#tag
			"d" => "wr_regdate", //d202010
		];
		$key = array_key_exists($key = substr($search, 0, 1) , $keys) ? $keys[$key] : '';
		empty($key) ? ($key = "wr_content") : ($search = substr($search, 1));
		if ($search = explode(" ", $search)) {
			$index = 0;
			foreach ($search as $value) {
				$value = explode("&", trim($value));
				$and_or = count($value) > 1 ? "(_AND_)" : "(_OR_)";
				foreach ($value as $v) {
					if ($key == "wr_regdate") {
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

	$doc_list = DB::gets(_AF_DOCUMENT_TABLE_, 'SQL_CALC_FOUND_ROWS *', $_wheres,'wr_regdate', (($_POST['page']-1)*20).',20');
	if($error = DB::error()) $error = set_error($error->getMessage(),$error->getCode());
	$doc_list = setDataListInfo($doc_list, $_POST['page'], 20, DB::foundRows());
?>

<table class="table">
<thead>
	<tr>
		<th scope="col"><a href="#DataManageAction"><?php echo getLang('data_manage')?></a></th>
		<th scope="col" class="text-wrap"><?php echo getLang('title')?></th>
		<th scope="col"><?php echo getLang('author')?></th>
		<th scope="col"><?php echo getLang('status')?></th>
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
		$current_page = $doc_list['current_page'];
		$total_page = $doc_list['total_page'];
		$start_page = $doc_list['start_page'];
		$end_page = $doc_list['end_page'];

		foreach ($doc_list['data'] as $key => $value) {
			echo '<tr><td scope="row"><a class="text-light" href="'.getUrl('category',$value['md_id']).'">'.$value['md_id'].'</a></td>';
			echo '<td class="text-wrap"><a href="./?srl='.$value['wr_srl'].'" target="_blank">'.escapeHTML(cutstr(strip_tags($value['wr_title']),50)).'</a>'.(empty($value['wr_reply'])?'':' <small>('.$value['wr_reply'].')</small>').'</td>';
			echo '<td>'.$value['mb_nick'].'</td>';
			echo '<td>'.($value['wr_secret']?'S/':'--/').($value['wr_status']?$value['wr_status']:'--').'</td>';
			echo '<td>'.date('Y/m/d', strtotime($value['wr_regdate'])).'</td></tr>';
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
/* End of file document.php */
/* Location: ./module/admin/document.php */
