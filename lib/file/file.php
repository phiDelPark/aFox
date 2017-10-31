<?php
if(!defined('__AFOX__')) exit();
require_once dirname(__FILE__) . '/../../initial/config.php';
function setHttpError($err, $back = false) {
	header('HTTP/1.1 '.$err);
	header("Connection: close");
	if($back) {
		$_SESSION['AF_VALIDATOR_ERROR'] = ['error'=>3,'message'=>'HTTP/1.1 '.$err];
		header('Location: ' . $_SERVER['HTTP_REFERER']); // binary 는 뒤로 가기
	}
	exit;
}
function getLogin($f='mb_srl') {
	$mbid = isset($_SESSION['AF_LOGIN_ID']) ? $_SESSION['AF_LOGIN_ID'] : get_cookie('AF_LOGIN_ID');
	if(!empty($mbid) && preg_match('/^[a-zA-Z]+\w{2,}$/', $mbid)) {
		$mb = DB::get("SELECT * FROM "._AF_MEMBER_TABLE_." WHERE mb_id = '{$mbid}'");
		if(!DB::error() && !empty($mb['mb_srl'])) return $mb[$f];
	}
	return '0';
}
if($_CFG['use_full_login']==1&&empty((int)getLogin())) setHttpError('401 Unauthorized');
if($_CFG['use_protect']==1&&!empty($_SERVER['HTTP_REFERER'])&&!preg_match('/^https?:[\/]+[a-z0-9\-\.]*'.$_SERVER['SERVER_NAME'].'.+/i',$_SERVER['HTTP_REFERER'])) setHttpError('401 Unauthorized');
static $_file = [];
$srl = (int)$_GET['file'];
$thumb = isset($_GET['thumb']);
$key = $srl.($thumb?'_thumb'.$_GET['thumb']:'');
if(!isset($_file[$key])) {
	$file = DB::assoc(DB::select(_AF_FILE_TABLE_,['mf_srl'=>$srl]));
	if(!empty($file['error'])) setHttpError('400 Bad Request');
	$filetype=strtolower(array_shift(explode('/',$file['mf_type'])));
	$_tmp=array('binary'=>0,'image'=>1,'video'=>2,'audio'=>3);
	$_file[$key]=['mb_srl'=>$file['mb_srl'], 'is_download'=>true, 'point_download'=>0, 'type'=>empty($_tmp[$filetype])?'binary':$filetype, 'name'=>$file['mf_name']];
	$_file[$key]['path']=_AF_ATTACH_DATA_.$_file[$key]['type'].'/'.$file['md_id'].'/'.$file['mf_target'].'/'.$file['mf_upload_name'];
	// binary면 권한 체크 // isGrant() 함수 안불러서 작성
	if($_file[$key]['type']=='binary') {
		$out = DB::select(_AF_MODULE_TABLE_, ['md_id'=>$file['md_id']], ['md_id','point_download','grant_download']);
		$module = DB::assoc($out);
		if(empty($module['md_id'])) setHttpError('400 Bad Request', true);
		$_file[$key]['point_download'] = (int)$module['point_download'];
		$grant = $module['grant_download'];
		if(!empty($grant)) {
			$rank = ord(getLogin('mb_rank'));
			$_file[$key]['is_download'] = ord($grant) <= $rank; // 0 = 48, z = 122 // s = 115 초과면 블럭 대상임
			if($rank > 115 || !$_file[$key]['is_download']) setHttpError('401 Unauthorized', true);
		}
	} else if($thumb&&$_file[$key]['type']=='image') {
		if(!file_exists($_file[$key]['path'])) {
			$_file[$key]['path']=_AF_PATH_.'common/img/no_image.png';
		} else {
			$size = $_GET['thumb'];
			if(empty($size)) {
				// 썸네일 사이즈가 빈값이면 모듈설정 사용
				$out = DB::select(_AF_MODULE_TABLE_, ['md_id'=>$file['md_id']], ['md_id','thumb_width','thumb_height','thumb_option']);
				$module = DB::assoc($out);
				if(empty($module['md_id'])) setHttpError('400 Bad Request', true);
				$tw = (int)$module['thumb_width'];
				$th = (int)$module['thumb_height'];
				$fit = $module['thumb_option']==='1';
			} else {
				// 사용자 썸네일 사이즈는 2가지 크기만
				$size = explode('x', $size);
				$tw = (int)$size[0];
				$th = (int)$size[1];
				$fit = (int)$size[2] === 1;
				$tw = $tw>100?200:($tw>0?100:0);
				$th = $th>100?200:($th>0?100:0);
			}
			if(!empty($tw)&&!empty($th)) {
				$thumb_file=_AF_ATTACH_DATA_.'thumbnail/'.$file['md_id'].'/'.$file['mf_target'].'/'.$file['mf_srl'].'_'.$tw.'x'.$th.'x'.($fit?'1':'0').'.png';
				if(file_exists($thumb_file)) {
					$_file[$key]['path']=$thumb_file;
				} else {
					require_once dirname(__FILE__) . '/thumbnail.php';
					$_file[$key]['path']=thumbnail($_file[$key]['path'],$thumb_file,$tw,$th,$fit);
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
		fclose($fp);
		setHttpError('304 Not Modified');
	}
}
// 다운로드 조회를 위해 기록 // setHistoryAction() 함수 안불러서 작성
if($_file[$key]['type']=='binary'){
	$point = $_file[$key]['point_download'];
	$mb_srl = (int)getLogin();
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
/* Location: ./lib/file/file.php */
