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

	}

	exit();
}

?>

<iframe src="./?admin=clearcache&flush=1&danger=1" style="width:100%;height:calc(100vh - 10rem - var(--topNavbarHeight));">
Your browser does not support iframes.
</iframe>

<?php
/* End of file clearcache.php */
/* Location: ./module/admin/clearcache.php */
