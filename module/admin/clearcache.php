<?php if(!defined('__AFOX__')) exit();

if(!empty($_POST['flush'])) {
	ob_end_clean();

	$dir = _AF_CACHE_DATA_;
	$subdir = true;

	if(is_dir($dir)){
		$handle = @opendir($dir); // 절대경로
		while ($file = readdir($handle)){
			if($file != '.' && $file != '..'){ // 하위 폴더면
				if($subdir && is_dir($dir.$file.'/'))
					unlinkAll($dir.$file.'/', $subdir);
				else if(unlinkFile($dir.$file)){
					/*
					echo '<b style="color:green">SUCCESS:</b> '.$dir.$file.'<br />';
					ob_flush();
					flush();
					*/
				} else {
					echo '<b style="color:red">ERROR:</b> '.$dir.$file.'<br />';
					ob_flush();
					flush();
				}
			}
		}
		@closedir($handle);
		if(unlinkDir($dir)){
			echo '<b style="color:blue">SUCCESS:</b> '.$dir.'<br />';
			ob_flush();
			flush();
		} else {
			echo '<b style="color:red">ERROR:</b> '.$dir.'<br />';
			ob_flush();
			flush();
		}
	}

	if(!empty($_POST['danger'])) {
/*//
		$out = DB::gets(_AF_FILE_TABLE_);
		$_file_types = array('binary'=>0, 'image' => 1, 'video' => 2, 'audio' => 3);

		foreach ($out as $key => $value) {
			$type = explode('/', strtolower($value['mf_type']));
			$type = empty($_file_types[$type[0]]) ? 'binary' : $type[0];
			$md_id = $value['md_id'];
			$wr_srl = $value['mf_target'];
			$upload_name = $value['mf_upload_name'];

			$dest_filename = md5($key.$value['mf_name'].time());
			$dest_name = _AF_PATH_ . 'data/attach_move/' . $type . '/' . $md_id . '/' . $wr_srl . '/' . $dest_filename;
			$full_name = _AF_PATH_ . 'data/attach/' . $type . '/' . $md_id . '/' . $wr_srl . '/' . $upload_name;

			if(file_exists($full_name)){
				if(!is_dir($dir=dirname($dest_name)) && !mkdir($dir, _AF_DIR_PERMIT_, true)) {
					echo '<b style="color:red">ERROR mkdir:</b> '.$dir.'<br />';
				} else {
					chmod($full_name, 0777);
					if(rename($full_name, $dest_name)) {
						//$iinfo = getimagesize($full_name);
						chmod($dest_name, _AF_ATTACH_PERMIT_);

						$mf_srl = $value['mf_srl'];
						DB::update(_AF_FILE_TABLE_, ['mf_upload_name'=>$dest_filename], ['mf_srl'=>$mf_srl]);

						echo '<b style="color:blue">FILE:</b> '.$full_name.'<br />';
					} else {
						chmod($full_name, _AF_ATTACH_PERMIT_);
						echo '<b style="color:red">ERROR:</b> '.$full_name.'<br />';
					}
				}
				ob_flush();
				flush();
			}
		}
//*/
	}

	exit();
}

?>

<iframe src="./?admin=clearcache&flush=1" style="width:100%;height:calc(100vh - 10rem - var(--topNavbarHeight));">
Your browser does not support iframes.
</iframe>

<?php
/* End of file clearcache.php */
/* Location: ./module/admin/clearcache.php */
