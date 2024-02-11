<?php
if(!defined('__AFOX__')) exit();

function proc($data) {
	global $_MEMBER;
	if(empty($_MEMBER)) return set_error(getLang('error_request'),4303);

	$_item = [];
	if(!empty($data['srl'])){
		$_item = DB::get(_AF_DOCUMENT_TABLE_, ['md_id'=>'_AFOXtRASH_','mb_srl'=>$_MEMBER['mb_srl'],'wr_srl'=>$data['srl']]);
	}

	$count = 20;
	$page = empty($data["page"]) ? 1 : $data["page"];
	$search = empty($data['search']) ? '' : $data['search'];
	$_wheres = ['md_id'=>'_AFOXtRASH_','mb_srl'=>$_MEMBER['mb_srl'], "(_AND_)" => [], "(_OR_)" => []];

	if (!empty($search)) {
		$keys = [
			":" => "wr_title", //:title
			//"@" => "mb_nick", //@nick
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

	$_list = DB::gets(_AF_DOCUMENT_TABLE_,'SQL_CALC_FOUND_ROWS *', $_wheres, 'wr_regdate', (($page-1)*$count).','.$count);
	if($error = DB::error()) return set_error($error->getMessage(),$error->getCode());

	$result = $_item;
	$result['tpl'] = 'trash';
	$result['list'] =  $_list;
	$result['total_count'] = DB::foundRows();
	$result['total_page'] = $result['end_page'] = ceil($result['total_count'] / $count);
	$result['current_page'] = $page;

	return $result;
}

/* End of file trash.php */
/* Location: ./module/member/disp/trash.php */
