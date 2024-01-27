<?php if (!defined("__AFOX__")) exit();

function proc($data)
{
	$_mids = empty($data["md_extra"]) ? [] : unserialize($data["md_extra"]);
	$_count = empty($data["md_list_count"]) ? 20 : $data["md_list_count"];
	$search = empty($data["search"]) ? "" : $data["search"];
	$page = empty($data["page"]) ? 1 : $data["page"];

	if (empty($_mids)) $_mids[] = "@@@@@@@@@@"; // 검색할 모듈이 없으면 임시 값
	$_wheres = ["md_id{IN}" => implode(",", $_mids) , "(_AND_)" => [], "(_OR_)" => []];

	if (!empty($search))
	{
		$keys = [":" => "wr_title", //:title
			"@" => "mb_nick", //@nick
			"#" => "wr_tags", //#tag
			"d" => "wr_regdate", //d202010
		];

		$key = array_key_exists($key = substr($search, 0, 1) , $keys) ? $keys[$key] : '';
		empty($key) ? ($key = "wr_content") : ($search = substr($search, 1));
		$search = explode(" ", trim($search));

		if (!empty($search))
		{
			$index = 0;
			foreach ($search as $value)
			{
				$value = explode("&", trim($value));
				$and_or = count($value) > 1 ? "(_AND_)" : "(_OR_)";
				foreach ($value as $v)
				{
					if ($key == "wr_regdate")
					{
						$v = str_split($v, 4);
						$v = $v[0] . (empty($v[1]) ? "" : "-" . implode("-", str_split($v[1], 2)));
					}
					$_wheres[$and_or][$key . "{LIKE}[" . $index++ . "]"] = "%" . $v . "%";
				}
			}
		}
	}
	//if(count($wheres)) $_wheres = array_merge($_wheres, $wheres);
	$_list = DB::gets(_AF_DOCUMENT_TABLE_, "SQL_CALC_FOUND_ROWS *", $_wheres, "md_id,wr_regdate", ($page - 1) * $_count . "," . $_count);

	return setDataListInfo($_list, $page, $_count, DB::foundRows());
}

/* End of file default.php */
/* Location: ./module/searchex/disp/default.php */