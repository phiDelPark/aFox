<?php

if(!defined('__AFOX__')) exit();

function proc($data) {

	if(!is_dir(_AF_MODULE_DATA_) && !mkdir(_AF_MODULE_DATA_, _AF_DIR_PERMIT_, true)){
		return set_error(getLang('upload_err_code(7)', 10407));
	}

	$file = _AF_MODULE_DATA_ . 'searchex.php';
	if(file_exists($file)){
		@chmod($file, 0707);
		@unlink($file);
	}

	$md_ids = empty($data['md_ids'])?'':serialize($data['md_ids']);
	$list_count = empty($data['md_list_count']) ? 20 : abs($data['md_list_count']);

	$f = @fopen($file, 'w');
	fwrite($f, "<?php\nif(!defined('__AFOX__')) exit();\n");
	fwrite($f, "\$_MOUDLE_CONFIG=array (\n");
	fwrite($f, "'ids'=>'{$md_ids}',\n");
	fwrite($f, "'count'=>'{$list_count}',\n");
	fwrite($f, ");");
	fclose($f);
	chmod($file, 0644);

	return set_error(getLang('success_saved', 0));
}

/* End of file setupsearch.php */
/* Location: ./module/search/proc/setupsearch.php */
