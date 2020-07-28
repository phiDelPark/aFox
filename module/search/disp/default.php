<?php
if(!defined('__AFOX__')) exit();

function proc($data) {
	// 권한 체크
	if(!isGrant('list', $data['id']) && !isManager($data['id'])) {
		return set_error(getLang('error_permitted'),4501);
	}

	$search = empty($data['combine']) ? '' : $data['combine'];
	$page = empty($data['page']) ? 1 : $data['page'];

	$_mids = [];
	$_count = 20;
	$_this = getModule('search');

	if(!empty($_this)) {
		$_mids = empty($_this['md_extra'])?[]:unserialize($_this['md_extra']);
		if(!empty($_mids)) $_mids = $_mids['md_ids'];
		$_count = empty($_this['md_list_count'])?20:$_this['md_list_count'];
	}

	$_wheres = ['md_id{IN}'=>implode(',', $_mids),'(_AND_)' =>[],'(_OR_)'=>[]];

	if(!empty($search)) {
		$schkeys = [''=>'wr_title','title'=>'wr_title','text'=>'wr_content','content'=>'wr_content','tag'=>'wr_tags','nick'=>'mb_nick','date'=>'wr_regdate'];
		$ss = explode(':', trim($search));
		$schkey = count($ss)>1 ? $schkeys[strtolower($ss[0])] : '';
		if($schkey != '') $search = implode(':', array_slice($ss,1));
		if(!empty($search)) {
			$search = trim($search);
			$and_or = strpos($search, '&') === 0 ? '(_AND_)' : '(_OR_)';
			if($and_or == '(_AND_)') $search = substr($search, 1);
			$search = explode(' ', $search);
			$index = 0;
			foreach($search as $v) {
				if(!empty($v)) {
					if($schkey == '') {
						$_wheres[$and_or]['wr_title{LIKE}['.$index.']'] = '%'.$v.'%';
						$_wheres[$and_or]['wr_content{LIKE}['.$index.']'] = '%'.$v.'%';
					} else {
						if($schkey=='wr_regdate') $v = str_replace('/', '-', $v);
						$v = ($schkey=='mb_nick'||$schkey=='wr_regdate'?'':'%').$v.'%';
						$_wheres[$and_or][$schkey.'{LIKE}['.$index.']'] = $v;
					}
					$index++;
				}
			}
		}
	}

	//if(count($wheres)) $_wheres = array_merge($_wheres, $wheres);

	$_list = DB::gets(_AF_DOCUMENT_TABLE_, 'SQL_CALC_FOUND_ROWS *', $_wheres, 'md_id,wr_regdate', (($page-1)*$_count).','.$_count);
	return setDataListInfo($_list, $page, $_count, DB::foundRows());
}

/* End of file default.php */
/* Location: ./module/search/disp/default.php */
