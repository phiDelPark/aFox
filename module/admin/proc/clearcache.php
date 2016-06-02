<?php

if(!defined('__AFOX__')) exit();

function __clear_cache($dir, $rmd = true) {
	$dirs = dir($dir);
	while(false !== ($entry = $dirs->read())) {
		if(($entry != '.') && ($entry != '..')) {
			if(is_dir($dir.'/'.$entry)) {
				__clear_cache($dir.'/'.$entry, true);
			} else {
				unlinkFile($dir.'/'.$entry);
			}
		}
	}
	$dirs->close();
	if($rmd) unlinkDir($dir);
}

function proc($data) {
	__clear_cache(_AF_CACHE_DATA_, false);
	return ['error'=>0, 'message'=>getLang('success_saved')];
}

/* End of file clearcache.php */
/* Location: ./module/admin/proc/clearcache.php */