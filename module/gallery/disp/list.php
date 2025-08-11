<?php if (!defined("__AFOX__")) exit();

function proc($data)
{
	global $_CFG;

	$count = empty($_CFG["md_list_count"]) ? 100 : $_CFG["md_list_count"];
	$search = empty($data["search"]) ? "" : $data["search"];
	$page = empty($data["page"]) ? 1 : $data["page"];

	$_wheres = ["md_id" => $data['id'], "_AND_" => [], "_OR_" => []];
	if($search){
		$_wheres['_AND_']['mf_about{REGEXP}'] = "('(^|,)".DB::escape($search)."($|,)')";
	}
	$asc = isset($data['asc']);

	//$_list = DB::query('SELECT * FROM '._AF_FILE_TABLE_.' WHERE mf_about REGEXP (\'분류\')');
	$_list = DB::gets(_AF_FILE_TABLE_, "SQL_CALC_FOUND_ROWS *", $_wheres, "mf_srl".($asc?' ASC':''), (($page - 1) * $count) . "," . $count);

	$result = ['tpl' => 'list', 'list' => $_list];
	$result['total_count'] = DB::foundRows();
	$result['total_page'] = $result['end_page'] = ceil($result['total_count'] / $count);
	$result['start_page'] = ($page - 1 - (($page - 1) % 10)) + 1;
	if ($result['end_page'] > ($result['start_page'] + 10)) $result['end_page'] = $result['start_page'] + 10;
	$result['current_page'] = $page;

	return $result;
}

/* End of file default.php */
/* Location: ./module/gallery/disp/default.php */