<?php
if(!defined('__AFOX__')) exit();
require_once dirname(__FILE__) . '/../../initial/config.php';
function setHttpError($e,$b=false){
	header('HTTP/1.1 '.$e);
	header("Connection: close");
	if($b){
		$_SESSION['AF_VALIDATOR_ERROR']=['error'=>3,'message'=>'HTTP/1.1 '.$e];
		header('Location: '.$_SERVER['HTTP_REFERER']);
	}
	exit;
}
$mb_srl = 0;
$mb_rank = '0';
if(!empty($tmp=isset($_SESSION['AF_LOGIN_ID'])?$_SESSION['AF_LOGIN_ID']:get_cookie('AF_LOGIN_ID'))&&preg_match('/^[a-zA-Z]+\w{2,}$/',$tmp)){
	$out=DB::get("SELECT * FROM "._AF_MEMBER_TABLE_." WHERE mb_id = '{$tmp}'");
	if(!DB::error()&&!empty($out['mb_srl'])){
		$mb_srl = $out['mb_srl'];
		$mb_rank = $out['mb_rank'];
	}
}
if($_CFG['use_full_login']==1&&empty($mb_srl)) setHttpError('401 Unauthorized');
if($_CFG['use_protect']==1&&!empty($_SERVER['HTTP_REFERER'])&&!preg_match('/^https?:[\/]+[a-z0-9\-\.]*'.$_SERVER['SERVER_NAME'].'.+/i',$_SERVER['HTTP_REFERER'])) setHttpError('401 Unauthorized');
static $_f = [];
$srl = (int)$_GET['file'];
$thumb = isset($_GET['thumb']);
$key = $srl.($thumb?'_thumb'.$_GET['thumb']:'');
if(!isset($_f[$key])) {
	$out = DB::assoc(DB::select(_AF_FILE_TABLE_,['mf_srl'=>$srl]));
	if(!empty($out['error'])) setHttpError('400 Bad Request');
	$ft=strtolower(array_shift(explode('/',$out['mf_type'])));
	$fts=array('binary'=>0,'image'=>1,'video'=>2,'audio'=>3);
	$_f[$key]=['mb_srl'=>$out['mb_srl'], 'permission'=>true, 'point'=>0, 'mime'=>$out['mf_type'], 'type'=>empty($fts[$ft])?'binary':$ft, 'name'=>$out['mf_name']];
	$_f[$key]['path']=_AF_ATTACH_DATA_.$_f[$key]['type'].'/'.$out['md_id'].'/'.$out['mf_target'].'/'.$out['mf_upload_name'];
	if($_f[$key]['type']=='binary') { // binary면 권한 체크 // isGrant() 함수 안불러서 작성
		$module = DB::assoc(DB::select(_AF_MODULE_TABLE_, ['md_id'=>$out['md_id']], ['md_id','point_download','grant_download']));
		if(empty($module['md_id'])) setHttpError('400 Bad Request', true);
		$_f[$key]['point'] = (int)$module['point_download'];
		$_f[$key]['permission'] = !($_f[$key]['point'] < 0 && empty($mb_srl)); // 포인트가 -면 비회원 다운 불가
		if($_f[$key]['permission']&&!empty($grant = $module['grant_download'])) {
			$tmp = ord($mb_rank);
			$_f[$key]['permission'] = $tmp < 116 && ord($grant) <= $tmp; // 0 = 48, z = 122 // s = 115 초과면 에러
			if(!$_f[$key]['permission']) setHttpError('401 Unauthorized', true);
		}
	} else if($thumb&&$_f[$key]['type']=='image') {
		if(!file_exists($_f[$key]['path'])) {
			$_f[$key]['path']=_AF_PATH_.'common/img/no_image.png';
		} else {
			if(empty($size = $_GET['thumb'])) { // 썸네일 사이즈가 빈값이면 모듈설정 사용
				$module = DB::assoc(DB::select(_AF_MODULE_TABLE_, ['md_id'=>$out['md_id']], ['md_id','thumb_width','thumb_height','thumb_option']));
				if(empty($module['md_id'])) setHttpError('400 Bad Request', true);
				$tw = (int)$module['thumb_width'];
				$th = (int)$module['thumb_height'];
				$fit = $module['thumb_option']==='1';
			} else { // 사용자 썸네일 사이즈는 2가지 크기만
				$size = explode('x', $size);
				$tw = $size[0]>100?200:($size[0]>0?100:0);
				$th = $size[1]>100?200:($size[1]>0?100:0);
				$fit = $size[2]==='1';
			}
			if(!empty($tw)&&!empty($th)) {
				if(file_exists($tmp=_AF_ATTACH_DATA_.'thumbnail/'.$out['md_id'].'/'.$out['mf_target'].'/'.$out['mf_srl'].'_'.$tw.'x'.$th.'x'.($fit?'1':'0').'.png')) {
					$_f[$key]['path']=$tmp;
				} else {
					require_once dirname(__FILE__) . '/thumbnail.php';
					$_f[$key]['path']=thumbnail($_f[$key]['path'],$tmp,$tw,$th,$fit);
				}
			}
		}
	}
}
if(!$_f[$key]['permission']) setHttpError('401 Unauthorized', true); // binary 다운로드 불가면 에러
//if($_f[$key]['type']=='binary' && !triggerCall('before_proc', 'fileDownload', $_f[$key])) setHttpError('401 Unauthorized', true); // binary 트리거 호출
if(!$fp = @fopen($_f[$key]['path'], 'rb')) setHttpError('404 Not Found');
$fstat=fstat($fp);
if(!empty($_SERVER['HTTP_IF_MODIFIED_SINCE'])){
	$tmp=strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']);
	if($tmp&&($tmp>=$fstat['mtime'])){
		fclose($fp);
		setHttpError('304 Not Modified');
	}
}
if($_f[$key]['type']=='binary'){ // 다운로드 조회를 위해 기록 setHistoryAction() 함수 안불러서 작성
	$act = 'mf_download';
	$uinfo = ['mb_srl'=>$mb_srl,'ipaddress'=>$_SERVER['REMOTE_ADDR']];
	$pkey = ($uinfo['mb_srl'] > 0 ? 'mb_srl':'mb_ipaddress');
	$pval = ($uinfo['mb_srl'] > 0 ? $uinfo['mb_srl']:$uinfo['ipaddress']);
	$out = DB::select(_AF_HISTORY_TABLE_,['hs_action'=>$act.'('.$srl.')',$pkey=>$pval]);
	if(!DB::error()) {
		if(is_null($uinfo['data'] = DB::assoc($out))) { // 처음 한번만 포인트 사용 // 자신은 포인트 사용 안함
			if(!empty($point = $_f[$key]['point']) && !empty($mb_srl) && ($mb_srl !== $_f[$key]['mb_srl'])) {
				$mb = DB::get('SELECT mb_point FROM '._AF_MEMBER_TABLE_.' WHERE mb_srl='.$mb_srl);
				if(DB::error() || ($mb['mb_point']+$point) < 0) setHttpError('401 Unauthorized', true); // 포인트 모자르면 에러
				DB::update(_AF_MEMBER_TABLE_,['(mb_point)'=>'mb_point'.($point>0?'+':'').$point],['mb_srl'=>$mb_srl]);
			}
			DB::insert(_AF_HISTORY_TABLE_,['mb_srl'=>$uinfo['mb_srl'],'mb_ipaddress'=>$uinfo['ipaddress'],'hs_action'=>$act.'('.$srl.')','(hs_regdate)'=>'NOW()']);
			DB::update(_AF_FILE_TABLE_, ['(mf_download)'=>'mf_download+1'], ['mf_srl'=>$srl]);
		}
	} else setHttpError('500 Internal Server Error', true);
}
header('Last-Modified: '.$fstat['mtime']);
header("Content-Disposition: attachment; filename=".$_f[$key]['name']);
header('Cache-Control:');
header('Content-Type: text/plain');
header("Connection: close");
fpassthru($fp);
fclose($fp);

/* End of file file.php */
/* Location: ./lib/file/file.php */
