<?php
if(!defined('__AFOX__')) exit();

function proc($data) {

	$search = empty($data['searchex']) ? '' : $data['searchex'];
	$page = empty($data['page']) ? 1 : $data['page'];

	$_mids = [];
	$_count = 20;
	$_MOUDLE_CONFIG = [];

	$file = _AF_MODULE_DATA_ . 'searchex.php';
	if(file_exists($file)) @include $file;

	if(!empty($_MOUDLE_CONFIG)) {
		$_mids = empty($_MOUDLE_CONFIG['ids'])?[]:unserialize($_MOUDLE_CONFIG['ids']);
		$_count = empty($_MOUDLE_CONFIG['count'])?20:$_MOUDLE_CONFIG['count'];
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
/* Location: ./module/searchex/disp/default.php */
