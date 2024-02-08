<?php if (!defined("__AFOX__")) exit();

function proc($data)
{
	$mids = empty($data["md_extra"]) ? [] : unserialize($data["md_extra"]);
	$count = empty($data["md_list_count"]) ? 20 : $data["md_list_count"];
	$search = empty($data["search"]) ? "" : $data["search"];
	$page = empty($data["page"]) ? 1 : $data["page"];

	if (empty($mids)) $mids[] = "@@@@@@@@@@"; // 검색할 모듈이 없으면 임시 값
	$_wheres = ["md_id{IN}" => implode(",", $mids) , "(_AND_)" => [], "(_OR_)" => []];

	if (!empty($search)) {
		$keys = [
			":" => "wr_title", //:title
			"@" => "mb_nick", //@nick
			"#" => "wr_tags", //#tag
			"?" => "wr_regdate", //?202010
		];

		$key = array_key_exists($key = substr($search, 0, 1) , $keys) ? $keys[$key] : '';
		empty($key) ? ($key = "wr_content") : ($search = substr($search, 1));
		$search = explode(" ", trim($search));

		if (!empty($search)) {

			$index = 0;
			foreach ($search as $value) {
				$value = explode("&", trim($value));
				$and_or = count($value) > 1 ? "(_AND_)" : "(_OR_)";

				foreach ($value as $v) {
					$cmd = '{LIKE}';
					if ($key == "wr_regdate") {
						$v = str_split($v, 4);
						$v = $v[0].(empty($v[1])?"":"-".implode("-",str_split($v[1],2)))."%";
					} else if ($key == "wr_tags") {
						$cmd = '{REGEXP}'; $key = '^'.$key;
						$v = "('(^|,)".DB::escape($v)."($|,)')";
					} else {
						$v = DB::escape("%" . $v. "%");
					}
					$_wheres[$and_or][$key . $cmd . '[' . $index++ . ']'] = $v;
				}
			}
		}
	}

	$_list = DB::gets(_AF_DOCUMENT_TABLE_, "SQL_CALC_FOUND_ROWS *", $_wheres, "md_id,wr_regdate", (($page - 1) * $count) . "," . $count);

	$result = [];
	$result['data'] = $_list;
	$result['total_count'] = DB::foundRows();
	$result['total_page'] = $result['end_page'] = ceil($result['total_count'] / $count);
	$result['start_page'] = ($page - 1 - (($page - 1) % 10)) + 1;
	if ($result['end_page'] > ($result['start_page'] + 10)) $result['end_page'] = $result['start_page'] + 10;
	$result['current_page'] = $page;

	return $result;
}

/* End of file default.php */
/* Location: ./module/searchex/disp/default.php */