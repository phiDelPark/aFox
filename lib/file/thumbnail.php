<?php
if(!defined('__AFOX__')) exit();

function thumbnail($source, $destination, $width, $height, $fit = false) {
	$ret = $source;
	$iifo = getImageSize($source);
	if(($iifo[2] === 1 && function_exists('imageCreateFromGif'))
		|| ($iifo[2] === 2 && function_exists('imageCreateFromJpeg'))
		|| ($iifo[2] === 3 && function_exists('imageCreateFromPng'))) {
		$_x = $_y = 0;
		if($fit) {
			if(($iifo[0]/$width) == ($iifo[1]/$height)) {
			} else if(($iifo[0]/$width) < ($iifo[1]/$height)) {
				$width=$height*($iifo[0]/$iifo[1]);
			} else {
				$height=$width*($iifo[1]/$iifo[0]);
			}
		} else {
			if($iifo[0] >= $iifo[1]){
				$rt = $iifo[0] / $width;
				$_y = round(($height - floor($iifo[1] / $rt)) / 2);
			} else {
				$rt = $iifo[1] / $height;
				$_x = round(($width - floor($iifo[0] / $rt)) / 2);
			}
		}
		$dir = dirname($destination);
		if(is_dir($dir) || mkdir($dir, _AF_DIR_PERMIT_, true)) {
			if($iifo[2] === 1) {$isrc = imageCreateFromGif($source);}
			else if($iifo[2] === 3) {$isrc = imageCreateFromPng($source);}
			else {$isrc = imageCreateFromJpeg($source);}
			$idst = imageCreateTrueColor($width, $height);
			imageFill($idst, 0, 0, imageColorAllocate($idst, 255,255,255));
			ImageCopyResampled($idst, $isrc, $_x, $_y, 0, 0, $width, $height, $iifo[0], $iifo[1]);
			imageInterlace($idst);
			imagePNG($idst, $destination, 9);
			@chmod($destination, _AF_FILE_PERMIT_);
			imageDestroy($idst);
			imageDestroy($isrc);
			$ret = $destination;
		}
	}
	return $ret;
}

/* End of file thumbnail.php */
/* Location: ./lib/file/thumbnail.php */
