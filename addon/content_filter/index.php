<?php
if(!defined('__AFOX__')) exit();

if($called_position == 'before_proc' && ($called_trigger == 'updatecomment' || $called_trigger == 'updatedocument')) {

	$filter = explode(",", trim($_ADDON['filter']));
	$pos = false;

	if($called_trigger == 'updatecomment') {

		$content = strip_tags($_DATA['rp_content']);

		for ($i=0,$n=count($filter); $i<$n; $i++) {
			$s = $filter[$i];
			$pos = stripos($content, $s);
			if ($pos !== false) break;
		}

	} else if($called_trigger == 'updatedocument') {

		$title = strip_tags($_DATA['wr_title']);
		$content = strip_tags($_DATA['wr_content']);

		for ($i=0,$n=count($filter); $i<$n; $i++) {
			$s = $filter[$i];
			$pos = stripos($title, $s);
			if ($pos !== false) break;
			$pos = stripos($content, $s);
			if ($pos !== false) break;
		}
	}

	if($pos !== false) {
		return set_error('금지 단어인 '.$s.' 이(가) 있습니다.');
	}
}

/* End of file index.php */
/* Location: ./addon/content_filter/index.php */