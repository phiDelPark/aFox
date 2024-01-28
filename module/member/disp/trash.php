<?php
if(!defined('__AFOX__')) exit();

function proc($data) {
	global $_MEMBER;
	if(empty($_MEMBER)) return set_error(getLang('error_request'),4303);

	$_item = $schs = [];
	if(!empty($data['srl'])){
		$_item = DB::get(_AF_DOCUMENT_TABLE_, ['md_id'=>'_AFOXtRASH_','mb_srl'=>$_MEMBER['mb_srl'],'wr_srl'=>$data['srl']]);
	}

	$data['page'] = empty($data['page'])?1:$data['page'];
	$search = empty($data['search']) ? '' : $data['search'];
	if(!empty($search)) {
		$schkeys = ['tag'=>'wr_tags','nick'=>'mb_nick','date'=>'wr_regdate'];
		$ss = explode(':', $search);
		if(count($ss)>1 && !empty($schkeys[$ss[0]])) {
			$search = trim(implode(':', array_slice($ss,1)));
			if(!empty($search)) $schs = [$schkeys[$ss[0]].'{LIKE}'=>$search.'%'];
		} else {
			$schs = ['wr_title{LIKE}'=>'%'.$search.'%', 'wr_content{LIKE}'=>'%'.$search.'%'];
		}
	}

	$count = 20;
	$page = empty($data["page"]) ? 1 : $data["page"];

	$_list = DB::gets(_AF_DOCUMENT_TABLE_,'SQL_CALC_FOUND_ROWS *',['md_id'=>'_AFOXtRASH_','mb_srl'=>$_MEMBER['mb_srl'],'(_OR_)'=>$schs],'wr_regdate', (($page-1)*$count).','.$count);
	if($error = DB::error()) return set_error($error->getMessage(),$error->getCode());

	$_list = ['data' => $_list];
	$_list['total_count'] = DB::foundRows();
	$_list['total_page'] = $_list['end_page'] = ceil($_list['total_count'] / $count);
	$_list['current_page'] = $page;

	$result = $_item;
	$result['tpl'] = 'trash';
	$result['_DOCUMENT_LIST_'] =  $_list;

	return $result;
}

/* End of file trash.php */
/* Location: ./module/member/disp/trash.php */
