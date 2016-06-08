<?php
if(!defined('__AFOX__')) exit();
require_once dirname(__FILE__) . '/initial/config.php';
function setHttpError($err, $back = false) {
	header('HTTP/1.1 '.$err);
	header("Connection: close");
	if($back) {
		set_error('HTTP/1.1 '.$err, 3);
		header('Location: ' . $_SERVER['HTTP_REFERER']); // binary 는 뒤로 가기
	}
	exit;
}
if($_CFG['protect_file']=='1' && !preg_match('/https?:[\/]+[a-z0-9\-\.]*'.$_SERVER['SERVER_NAME'].'.+/i',$_SERVER['HTTP_REFERER'])) setHttpError('401 Unauthorized');
static $_file = [];
$srl = (int)$_GET['file'];
$thumb = empty($_GET['thumb']) ? '' : $_GET['thumb'];
$thumbfit = isset($_GET['fit']);
$key = $srl.$thumb;
if(!isset($_file[$key])) {
	$file = DB::assoc(DB::select(_AF_FILE_TABLE_,['mf_srl'=>$srl]));
	if(!empty($file['error'])) setHttpError('400 Bad Request');
	$filetype=strtolower(array_shift(explode('/',$file['mf_type'])));
	$_tmp=array('binary'=>0,'image'=>1,'video'=>2,'audio'=>3);
	$_file[$key]=['mb_srl'=>$file['mb_srl'], 'is_download'=>true, 'point_download'=>0, 'type'=>empty($_tmp[$filetype])?'binary':$filetype, 'name'=>$file['mf_name']];
	$_file[$key]['path']=_AF_ATTACH_DATA_.$_file[$key]['type'].'/'.$file['md_id'].'/'.$file['mf_target'].'/'.$file['mf_upload_name'];
	// binary면 권한 체크 // isGrant() 함수 안불러서 작성
	if($_file[$key]['type']==='binary') {
		$out = DB::select(_AF_MODULE_TABLE_, ['md_id'=>$file['md_id']]);
		$module = DB::assoc($out);
		if(empty($module['md_id'])) setHttpError('400 Bad Request', true);
		$_file[$key]['point_download'] = (int)$module['point_download'];
		$grant = $module['grant_download'];
		if(!empty($grant)) {
			$rank = ord(empty($_MEMBER['mb_rank']) ? '0' : $_MEMBER['mb_rank']);
			if($rank > 115) return false; // s = 115 초과면 블럭 대상임
			$_file[$key]['is_download'] = ord($grant) <= $rank; // 0 = 48, z = 122
			if(!$_file[$key]['is_download']) setHttpError('401 Unauthorized', true);
		}
	} else if($_file[$key]['type']==='image' && !empty($thumb)) {
		$size = explode('x', $thumb);
		$tw = (int)$size[0];
		$th = (int)$size[1];
		if(!empty($tw)&&!empty($th)) {
			// 50, 100, 200 3가지 크기외 불필요
			$tw = $tw<100?50:($tw<200?100:200);
			$th = $th<100?50:($th<200?100:200);
			$thumb_file=_AF_ATTACH_DATA_.'thumbnail/'.$file['md_id'].'/'.$file['mf_target'].'/'.$file['mf_srl'].'_'.$tw.'x'.$th.'.png';
			if(file_exists($thumb_file)) {
				$_file[$key]['path']=$thumb_file;
			} else if(file_exists($_file[$key]['path'])) {
				$iifo = getImageSize($_file[$key]['path']);
				if(($iifo[2] === 1 && function_exists('imageCreateFromGif'))
					|| ($iifo[2] === 2 && function_exists('imageCreateFromJpeg'))
					|| ($iifo[2] === 3 && function_exists('imageCreateFromPng'))) {
					$_x = $_y = 0;
					if($thumbfit) {
						if(($iifo[0]/$tw) == ($iifo[1]/$th)) {
						} else if(($iifo[0]/$tw) < ($iifo[1]/$th)) {
							$tw=$th*($iifo[0]/$iifo[1]);
						} else {
							$th=$tw*($iifo[1]/$iifo[0]);
						}
					} else {
						if($iifo[0] >= $iifo[1]){
							$rt = $iifo[0] / $tw;
							$_y = round(($th - floor($iifo[1] / $rt)) / 2);
						} else {
							$rt = $iifo[1] / $th;
							$_x = round(($tw - floor($iifo[0] / $rt)) / 2);
						}
					}
					$dir = dirname($thumb_file);
					if(is_dir($dir) || mkdir($dir, _AF_DIR_PERMIT_, true)) {
						if($iifo[2] === 1) {$isrc = imageCreateFromGif($_file[$key]['path']);}
						else if($iifo[2] === 3) {$isrc = imageCreateFromPng($_file[$key]['path']);}
						else {$isrc = imageCreateFromJpeg($_file[$key]['path']);}
						$idst = imageCreateTrueColor($tw, $th);
						imageFill($idst, 0, 0, imageColorAllocate($idst, 255,255,255));
						ImageCopyResampled($idst, $isrc, $_x, $_y, 0, 0, $tw, $th, $iifo[0], $iifo[1]);
						imageInterlace($idst);
						imagePNG($idst, $thumb_file);
						@chmod($thumb_file, _AF_FILE_PERMIT_);
						imageDestroy($idst);
						imageDestroy($isrc);
						$_file[$key]['path']=$thumb_file;
					}
				}
			}
		}
	}
}
if(!$_file[$key]['is_download']) setHttpError('401 Unauthorized', true); // binary 다운로드 불가면 에러
if(!$fp = @fopen($_file[$key]['path'], 'rb')) setHttpError('404 Not Found');
$fstat=fstat($fp);
if(!empty($_SERVER['HTTP_IF_MODIFIED_SINCE'])){
	$modifiedSince=strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']);
	if($modifiedSince&&($modifiedSince>=$fstat['mtime'])){
		fclose($fp); setHttpError('304 Not Modified');
	}
}
// 다운로드 조회를 위해 기록 // setHistoryAction() 함수 안불러서 작성
if($_file[$key]['type']==='binary'){
	$point = $_file[$key]['point_download'];
	$mb_srl = empty($_MEMBER)?0:(int)$_MEMBER['mb_srl'];
	if(empty($mb_srl) && $point < 0) setHttpError('401 Unauthorized', true); // -값은 비회원 불가
	$act = 'mf_download';
	$uinfo = ['mb_srl'=>$mb_srl,'ipaddress'=>$_SERVER['REMOTE_ADDR']];
	$pkey = ($uinfo['mb_srl'] > 0 ? 'mb_srl':'mb_ipaddress');
	$pval = ($uinfo['mb_srl'] > 0 ? $uinfo['mb_srl']:$uinfo['ipaddress']);
	$_tmp = DB::select(_AF_HISTORY_TABLE_,['hs_action'=>$act.'('.$srl.')',$pkey=>$pval]);
	if(!DB::error()) {
		if(is_null($uinfo['data'] = DB::assoc($_tmp))) {
			// 처음 한번만 포인트 사용 // 자신은 포인트 사용 안함
			if(!empty($point) && !empty($mb_srl) && ($mb_srl !== $_file[$key]['mb_srl'])) {
				$mb = DB::get('SELECT mb_point FROM '._AF_MEMBER_TABLE_.' WHERE mb_srl='.$mb_srl);
				if(DB::error() || ($mb['mb_point']+$point) < 0) setHttpError('401 Unauthorized', true); // 포인트 모자르면 에러
				DB::update(_AF_MEMBER_TABLE_,['(mb_point)'=>'mb_point'.($point>0?'+':'').$point],['mb_srl'=>$mb_srl]);
			}
			if(true === DB::insert(_AF_HISTORY_TABLE_,['mb_srl'=>$uinfo['mb_srl'],'mb_ipaddress'=>$uinfo['ipaddress'],'hs_action'=>$act.'('.$srl.')','(hs_regdate)'=>'NOW()'])){
				DB::update(_AF_FILE_TABLE_, ['(mf_download)'=>'mf_download+1'], ['mf_srl'=>$srl]);
			}
		}
	} else setHttpError('500 Internal Server Error', true);
}
header('Last-Modified: '.$fstat['mtime']);
header("Content-Disposition: attachment; filename=".$_file[$key]['name']);
header('Cache-Control:');
header('Content-Type: text/plain');
header("Connection: close");
fpassthru($fp);
fclose($fp);

/* End of file file.php */
/* Location: ./file.php */