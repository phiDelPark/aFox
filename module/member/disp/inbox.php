<?php
if(!defined('__AFOX__')) exit();

function proc($data) {
	global $_MEMBER;
	if(empty($_MEMBER)) return set_error(getLang('error_request'),4303);

	$_item = $schs = [];
	if(!empty($data['srl'])) {
		$_item = DB::get(_AF_NOTE_TABLE_, ['mb_srl'=>$_MEMBER['mb_srl'],'nt_srl'=>$data['srl']]);
	}

	$count = 20;
	$page = empty($data["page"]) ? 1 : $data["page"];
	$search = empty($data['search']) ? '' : $data['search'];
	$_wheres = ['mb_srl'=>$_MEMBER['mb_srl'], "(_AND_)" => [], "(_OR_)" => []];

	if (!empty($search)) {
		$keys = [
			"@" => "nt_sender_nick",
			"?" => "nt_send_date",
		];
		$key = array_key_exists($key = substr($search, 0, 1) , $keys) ? $keys[$key] : '';
		empty($key) ? ($key = "nt_content") : ($search = substr($search, 1));
		$search = explode(" ", trim($search));

		if (!empty($search)) {
			$index = 0;
			foreach ($search as $value) {
				$value = explode("&", trim($value));
				$and_or = count($value) > 1 ? "(_AND_)" : "(_OR_)";
				foreach ($value as $v) {
					if ($key == "nt_send_date") {
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

	$_list = DB::gets(_AF_NOTE_TABLE_,'SQL_CALC_FOUND_ROWS *', $_wheres, 'nt_send_date', (($page-1)*$count).','.$count);
	if($error = DB::error()) return set_error($error->getMessage(),$error->getCode());

	$_list = ['data' => $_list];
	$_list['total_count'] = DB::foundRows();
	$_list['total_page'] = $_list['end_page'] = ceil($_list['total_count'] / $count);
	$_list['current_page'] = $page;

	$result = $_item;
	$result['tpl'] = 'inbox';
	$result['_DOCUMENT_LIST_'] =  $_list;

	return $result;
}

/* End of file inbox.php */
/* Location: ./module/member/disp/inbox.php */
