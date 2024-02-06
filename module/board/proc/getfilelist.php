<?php if(!defined('__AFOX__')) exit();

function proc($data) {
	if(empty($data['md_id']) || empty($data['mf_target'])) return set_error(getLang('error_request'),4303);
	$exif = !empty($data['exif']);
	$files = DB::gets(_AF_FILE_TABLE_, ['md_id'=>$data['md_id'],'mf_target'=>$data['mf_target']], 'mf_type',
	!$exif?null:function ($r) {
		$rset = [];
		$types = array('binary'=>0, 'image' => 1, 'video' => 2, 'audio' => 3);
		while($row = DB::fetch($r)){
			$row['EXIF'] = [];
			$type = explode('/', strtolower($row['mf_type']));
			$type = empty($types[$type[0]]) ? 'binary' : $type[0];
			$file = _AF_ATTACH_DATA_.$type.'/'.$row['md_id'].'/'.$row['mf_target'].'/'.$row['mf_upload_name'];
			if($type == 'image' && function_exists('exif_read_data') && file_exists($file)){
				if($exif = @exif_read_data($file, 'EXIF')) $row['EXIF'] = $exif;
			}
			$rset[] = $row;
		}
		return $rset;
	});
	return $files;
}

/* End of file getfilelist.php */
/* Location: ./module/board/proc/getfilelist.php */
