<?php
if(!defined('__AFOX__')) exit();

$filter = explode(",", trim($_ADDON['filter']));

$title = strip_tags($_DATA['wr_title']);
$content = strip_tags($_DATA['wr_content']);
$pos = false;

for ($i=0,$n=count($filter); $i<$n; $i++) {
	$s = $filter[$i];
	$pos = stripos($title, $s);
	if ($pos !== false) break;
	$pos = stripos($content, $s);
	if ($pos !== false) break;
}

if($pos !== false) {
	return set_error('금지 단어인 '.$s.' 이(가) 있습니다.');
}
/* End of file procboardupdatedocument.php */
/* Location: ./addon/content_filter/before/procboardupdatedocument.php */