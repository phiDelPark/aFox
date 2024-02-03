<?php if (!defined("__AFOX__")) exit();

// TODO 나중에 필요하면 캐시 처리 하자
function getDocument($srl, $field = "*", $inc_hit = false)
{
	global $_MEMBER;

	$field = $field . "," . implode(",", ["md_id", "mb_srl"]);
	$result = DB::get(_AF_DOCUMENT_TABLE_, $field, ["wr_srl" => $srl]);
	if ($ex = DB::error()) return set_error($ex->getMessage() , $ex->getCode());
	if (empty($result["md_id"])) return set_error(getLang("error_request") , 4303);
	elseif ($result["md_id"] == "_AFOXtRASH_" && !isManager($result["wr_updater"])) {
		return set_error(getLang("error_permitted") , 4501); // only manager
	}

	$wr_mb = $result["mb_srl"];
	if ($inc_hit && $_MEMBER && $wr_mb != $_MEMBER["mb_srl"])
	{ // exclude yourself
		if (($point = (int)getModule($result["md_id"], "point_view")) !== 0) {
			$_out = setHistory("wr_hit::" . $srl, $point, false,
				function ($v) use ($srl, $wr_mb, $point) {
					// 처음에만 포인트 사용
					if (!empty($v)) return;
					// 자신은 포인트 사용 안함
					if (empty($wr_mb) || $wr_mb !== $v["mb_srl"]) {
						$_r = setPoint($point);
						if (!empty($_r["error"])) return set_error($_r["message"], $_r["error"]);
					}
				}
			);
			if (!empty($_out["error"])) return set_error($_out["message"], $_out["error"]);
		}

		$hit_key = "afox_wr_hit::" . $srl;
		$uinfo = ["mb_srl" => $_MEMBER ? $_MEMBER["mb_srl"] : 0, "ipaddress" => $_SERVER["REMOTE_ADDR"], ];

		if (!get_session($hit_key) && !get_cookie($hit_key)) {
			$ukey = ($uinfo["mb_srl"] > 0 ? "mb_srl" : "mb_ipaddress") . "{<>}";
			$uval = $uinfo["mb_srl"] > 0 ? $uinfo["mb_srl"] : $uinfo["ipaddress"];
			DB::update(_AF_DOCUMENT_TABLE_, ["^wr_hit" => "wr_hit+1"], ["wr_srl" => $srl, $ukey => $uval]);
		}
		set_session($hit_key, true);
		set_cookie($hit_key, true, 86400 * 31);
	}

	return $result;
}

function getDocumentList($id, $count, $page, $search = "", $category = "", $order = "wr_regdate", $callback = null)
{
	$_wheres = ["md_id" => $id, "(_AND_)" => empty($category) ? [] : ["wr_category" => $category], "(_OR_)" => []];

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

	if (empty($callback)) {
		$callback = function ($r) {
			$rset = [];
			while ($row = DB::fetch($r)) {
				// 확장 변수가 있으면 unserialize
				if (!empty($row["wr_extra"]) && !is_array($row["wr_extra"])) {
					$row["wr_extra"] = unserialize($row["wr_extra"]);
				}
				$rset[] = $row;
			}
			return $rset;
		};
	}

	$page = empty($page) ? 1 : $page;
	$_list = DB::gets(_AF_DOCUMENT_TABLE_, "SQL_CALC_FOUND_ROWS *", $_wheres, $order, (($page - 1) * $count) . "," . $count, $callback);

	$result = [];
	$result['data'] = $_list;
	$result['total_count'] = DB::foundRows();
	$result['total_page'] = ceil($result['total_count'] / $count);
	$result['current_page'] = $page;

	return $result;
}

function getComment($srl, $field = "*")
{
	return DB::get(_AF_COMMENT_TABLE_, $field, ["rp_srl" => $srl]);
}

function getCommentList($srl, $callback = null)
{
	return DB::gets(_AF_COMMENT_TABLE_, ["wr_srl" => $srl], ["rp_parent" => "asc", "rp_depth" => "asc"], $callback);
}

function getHashtags($content)
{
	$tags = ["pre", "code", "xml", "textarea", "input", "select", "option", "script", "style", "iframe", "button", "img", "embed", "object", "ins"];
	$pattern = "/<(" . implode("|", $tags) . ')[^>]*>.*?<\/\1>/si';
	$content = preg_replace('/(<br\s?\/?>|\r|\n)/i', " ", preg_replace($pattern, " ", $content));
	$content = htmlspecialchars_decode(strip_tags($content) , ENT_QUOTES);
	$tags = [];
	$pattern = "/\s#([\w]{3,})/u";
	preg_replace_callback($pattern, function ($matches) use (&$tags)
		{
			$tags[md5(strtoupper($matches[1])) ] = $matches[1];
			return "";
		}
	, $content);
	return implode(",", $tags);
}

function highlightText($key, $html)
{
	return preg_replace_callback('#((?:(?!<[/a-z]).)*)([^>]*>|$)#si', function ($mc) use ($key)
		{
			return str_ireplace($key, "<mark>" . $key . "</mark>", $mc[1]) . $mc[2];
		}
	, $html);
}

/* End of file funcs.php */
/* Location: ./module/board/funcs.php */