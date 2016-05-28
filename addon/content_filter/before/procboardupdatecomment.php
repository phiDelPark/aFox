<?php
if(!defined('__AFOX__')) exit();

$filter = explode(",", trim($_ADDON['filter']));

$content = strip_tags($_DATA['rp_content']);
$pos = false;

for ($i=0,$n=count($filter); $i<$n; $i++) {
	$s = $filter[$i];
	$pos = stripos($content, $s);
	if ($pos !== false) break;
}

if($pos !== false) {
	return set_error('금지 단어인 '.$s.' 이(가) 있습니다.');
}
/* End of file procboardupdatecomment.php */
/* Location: ./addon/content_filter/before/procboardupdatecomment.php */