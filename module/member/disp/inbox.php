<?php
if(!defined('__AFOX__')) exit();

function proc($data) {
	global $_MEMBER;
	if(empty($_MEMBER)) return set_error(getLang('error_request'),4303);

	$_item = $schs = [];
	if(!empty($data['srl'])) {
		$_item = DB::get(_AF_NOTE_TABLE_, ['mb_srl'=>$_MEMBER['mb_srl'],'nt_srl'=>$data['srl']]);
	}

	$data['page'] = empty($data['page'])?1:$data['page'];
	$search = empty($data['search']) ? '' : $data['search'];
	if(!empty($search)) {
		$schkeys = ['date'=>'nt_send_date','read'=>'nt_read_date'];
		$ss = explode(':', $search);
		if(count($ss)>1 && !empty($schkeys[$ss[0]])) {
			$search = trim(implode(':', array_slice($ss,1)));
			if(!empty($search)) $schs = [$schkeys[$ss[0]].'{LIKE}'=>$search.'%'];
		} else {
			$schs = ['nt_sender_nick{LIKE}'=>'%'.$search.'%'];
		}
	}
	$_list = DB::gets(_AF_NOTE_TABLE_,'SQL_CALC_FOUND_ROWS *',['mb_srl'=>$_MEMBER['mb_srl'],'(_OR_)'=>$schs],'nt_send_date', (($data['page']-1)*20).',20');
	if($error = DB::error()) return set_error($error->getMessage(),$error->getCode());
	$_list = setDataListInfo($_list, $data['page'], 20, DB::foundRows());

	$result = $_item;
	$result['tpl'] = 'inbox';
	$result['_DOCUMENT_LIST_'] = $_list;

	return $result;
}

/* End of file inbox.php */
/* Location: ./module/member/disp/inbox.php */
