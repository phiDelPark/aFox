<?php
if(!defined('__AFOX__')) exit();

if(empty($_WIDGET['vid'])) return;

$rel = isset($_WIDGET['rel']) ? $_WIDGET['rel'] : '1';
$showinfo = isset($_WIDGET['showinfo']) ? $_WIDGET['showinfo'] : '1';
$controls = isset($_WIDGET['controls']) ? $_WIDGET['controls'] : '1';
$time = $_WIDGET['time'];

if(!empty($time)) {
	$s = 0;
	if(preg_match_all('/([0-9]+)[h|m|s]/is', $time, $m)) {
		if(count($m[1]) === 3) {
			$s = ($m[1][0]*60)+($m[1][1]*60)+$m[1][2];
		} else if(count($m[1]) === 2) {
			$s = ($m[1][0]*60)+$m[1][1];
		} else $s = $m[1][0];
	}
	$_WIDGET['start'] = $s ? $s : (int)($time);
}

$opts = '';
$opts .= empty($_WIDGET['start'])?'':'start='.((int) $_WIDGET['start']).'&';
$opts .= $_WIDGET['rel']=='0'?'rel=0&':'';
$opts .= $_WIDGET['showinfo']=='0'?'showinfo=0&':'';
$opts .= $_WIDGET['controls']=='0'?'controls=0&':'';

$width = (int) empty($_WIDGET['width'])?'560':$_WIDGET['width'];
$height = (int) empty($_WIDGET['height'])?'315':$_WIDGET['height'];
?>

<iframe src="https://www.youtube.com/embed/<?php echo $_WIDGET['vid'].(empty($opts)?'':'?'.$opts) ?>" width="<?php echo $width ?>" height="<?php echo $height ?>" frameborder="0" allowfullscreen></iframe>

<?php

/* End of file index.php */
/* Location: ./widget/youtube/index.php */
