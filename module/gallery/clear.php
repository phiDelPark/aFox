<?php if(!defined('__AFOX__')) exit();

if(@$_GET['clear']) {
	ob_end_clean();

	function __unlinkThumbnail($dir)
	{
		$handle = @opendir($dir); // 절대경로
		while ($file = readdir($handle)){
			if($file != '.' && $file != '..'){ // 하위 폴더면
				if(is_dir($dir.$file.'/'))
					__unlinkThumbnail($dir.$file.'/');
				else if(unlinkFile($dir.$file)){
					/*
					// 파일 까지 출력하면 브라우저 다운 될꺼 같아 주석
					//echo '<b style="color:blue">SUCCESS:</b> '.$file.'<br />';
					echo '○';
					ob_flush();
					flush();
					*/
				} else {
					echo '<b style="color:red">ERROR:</b> '.$file.'<br />';
					ob_flush();
					flush();
				}
			}
		}
		@closedir($handle);
		if(unlinkDir($dir)){
			//echo '<b style="color:blue">SUCCESS:</b> '.$dir.'<br />';
			echo '●';
			ob_flush();
			flush();
		} else {
			echo '<b style="color:red">ERROR:</b> '.$dir.'<br />';
			ob_flush();
			flush();
		}
	}

	$thumbnail_dir = _AF_ATTACH_DATA_.'thumbnail/'.$_GET['clear'].'/';
	if(is_dir($thumbnail_dir)){
		__unlinkThumbnail($thumbnail_dir);
	}
}

if(!is_dir($thumbnail_dir)){
?>

<script>
	parent.document.querySelector('#thumbWidth').removeAttribute('readonly');
	parent.document.querySelector('#thumbHeight').removeAttribute('readonly');
	parent.document.querySelector('#openClearThumbnail').style.display = 'none';
</script>

<?php
}
exit();
/* End of file clear.php */
/* Location: ./module/gallery/clear.php */
