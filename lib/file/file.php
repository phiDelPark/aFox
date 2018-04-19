<?php
if(!defined('__AFOX__')) exit();
function setHttpError($e,$b=false){
header('HTTP/1.1 '.$e); header("Connection: close");
if($b){set_error('HTTP/1.1 '.$e, 3); header('Location: '.$_SERVER['HTTP_REFERER']);}
exit;
}
function file_triggerModuleCall($f,$_DATA) {
include $f; $r=null; $t='before_proc';
if(function_exists($t)){$r=call_user_func($t, [$_DATA]);}
return $r===true ? $_DATA : false;
}
function file_triggerCall($rank, &$data) {
$s=DB::gets(_AF_TRIGGER_TABLE_,'tg_id',['tg_key'=>'M','^'=>'ASCII(grant_access)<='.$rank]);
foreach ($s as $v) {$inf=_AF_MODULES_PATH_.'/'.$v['tg_id'].'/trigger/filedownload.php';
if(file_exists($inf)) {$r=file_triggerModuleCall($inf,$data);if($r===false) return false;$data=$r;}
} return true;
}
$mb_srl = 0; $mb_rank = '0';
$tmp=isset($_SESSION['AF_LOGIN_ID'])?$_SESSION['AF_LOGIN_ID']:get_cookie('AF_LOGIN_ID');
if(!empty($tmp)&&preg_match('/^[a-zA-Z]+\w{2,}$/',$tmp)){$out=DB::get(_AF_MEMBER_TABLE_,['mb_id'=>$tmp]);
if(!DB::error()&&!empty($out['mb_srl'])){$mb_srl=$out['mb_srl']; $mb_rank=$out['mb_rank'];}}
if($_CFG['use_full_login']==1&&empty($mb_srl)) setHttpError('401 Unauthorized');
if($_CFG['use_protect']==1&&!empty($_SERVER['HTTP_REFERER'])&&!preg_match('/^https?:[\/]+[a-z0-9\-\.]*'.$_SERVER['SERVER_NAME'].'.+/i',$_SERVER['HTTP_REFERER'])) setHttpError('401 Unauthorized');
static $_f = [];
$srl = (int)$_GET['file'];
$thumb = isset($_GET['thumb']);
$key = $srl.($thumb?'_thumb'.$_GET['thumb']:'');
if(!isset($_f[$key])){
	$out = DB::get(_AF_FILE_TABLE_,['mf_srl'=>$srl]);
	if(DB::error()) setHttpError('400 Bad Request');
	$ori_mdid = $out['md_id'];
	$ori_target = $out['mf_target'];
	if($out['mf_link']=='1'){
		if(!is_numeric($out['mf_upload_name'])||(int)$out['mf_upload_name']<1) setHttpError('400 Bad Request');
		$out = DB::get(_AF_FILE_TABLE_,['mf_srl'=>$out['mf_upload_name']]);
		if(DB::error()) setHttpError('400 Bad Request');
	}
	$tmp=explode('/',$out['mf_type']);
	$ft=strtolower(array_shift($tmp));
	$fts=array('binary'=>0,'image'=>1,'video'=>2,'audio'=>3);
	$_f[$key]=['module'=>$ori_mdid,'srl'=>$srl,'target'=>$ori_target,'member'=>$out['mb_srl'],'permission'=>true,'point'=>0,'mime'=>$out['mf_type'],'type'=>empty($fts[$ft])?'binary':$ft,'name'=>$out['mf_name']];
	$_f[$key]['path']=_AF_ATTACH_DATA_.$_f[$key]['type'].'/'.$out['md_id'].'/'.$out['mf_target'].'/'.$out['mf_upload_name'];
	if($_f[$key]['type']=='binary'){ //binary면 권한 체크 //isGrant() 함수 작성
		$module = DB::get(_AF_MODULE_TABLE_, 'md_id,point_download,grant_download',['md_id'=>$out['md_id']]);
		if(empty($module['md_id'])) setHttpError('400 Bad Request', true);
		$_f[$key]['point'] = (int)$module['point_download'];
		$_f[$key]['permission'] = !($_f[$key]['point'] < 0 && empty($mb_srl)); //포인트가 -면 비회원 다운 불가
		$grant = $module['grant_download'];
		if($_f[$key]['permission']&&!empty($grant)){
			$tmp = ord($mb_rank);
			$_f[$key]['permission'] = $tmp < 116 && ord($grant) <= $tmp; //0 = 48, z = 122 //s = 115 초과면 에러
			if(!$_f[$key]['permission']) setHttpError('401 Unauthorized', true);
		}
	} else if($_f[$key]['type']=='image'){
		if(!file_exists($_f[$key]['path'])){
			$_f[$key]['path']=_AF_PATH_.'common/img/no_image.png';
		} elseif($thumb) {
			$size = $_GET['thumb'];
			if(empty($size)){ //썸네일 사이즈 빈값이면 모듈설정 사용
				$module = DB::get(_AF_MODULE_TABLE_, 'md_id,thumb_width,thumb_height,thumb_option', ['md_id'=>$out['md_id']]);
				if(empty($module['md_id'])) setHttpError('400 Bad Request');
				$tw = (int)$module['thumb_width'];
				$th = (int)$module['thumb_height'];
				$fit = $module['thumb_option']==='1';
			} else { //사용자 썸네일 2가지 크기만
				$size = explode('x', $size);
				$tw = $size[0]>100?200:($size[0]>0?100:0);
				$th = $size[1]>100?200:($size[1]>0?100:0);
				$fit = $size[2]==='1';
			}
			if(!empty($tw)&&!empty($th)){
				if(file_exists($tmp=_AF_ATTACH_DATA_.'thumbnail/'.$out['md_id'].'/'.$out['mf_target'].'/'.$out['mf_srl'].'_'.$tw.'x'.$th.'x'.($fit?'1':'0').'.png')){
					$_f[$key]['path']=$tmp;
				} else {
					require_once dirname(__FILE__) . '/thumbnail.php';
					$_f[$key]['path']=thumbnail($_f[$key]['path'],$tmp,$tw,$th,$fit);
				}
			}
		}
	}
}
if(!$_f[$key]['permission']) setHttpError('401 Unauthorized', $_f[$key]['type']=='binary');
if($_f[$key]['type']=='binary') { //fileDownload 트리거 호출
	if(!file_triggerCall(ord($mb_rank),$_f[$key])) setHttpError('401 Unauthorized', true);
}
if(!$fp = @fopen($_f[$key]['path'], 'rb')) setHttpError('404 Not Found');
$fstat=fstat($fp);
if(!empty($_SERVER['HTTP_IF_MODIFIED_SINCE'])){
	$tmp=strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']);
	if($tmp&&($tmp>=$fstat['mtime'])){fclose($fp); setHttpError('304 Not Modified');}
}
if($_f[$key]['type']=='binary'){ //다운로드 조회 기록
	$act='mf_download'; $point=$_f[$key]['point'];
	if(!empty($point)){
		$uinfo = ['mb_srl'=>$mb_srl,'ipaddress'=>$_SERVER['REMOTE_ADDR']];
		$pkey = ($uinfo['mb_srl'] > 0 ? 'mb_srl':'mb_ipaddress');
		$pval = ($uinfo['mb_srl'] > 0 ? $uinfo['mb_srl']:$uinfo['ipaddress']);
		$uinfo['data'] = DB::get(_AF_HISTORY_TABLE_,['hs_action'=>$act.'('.$srl.')',$pkey=>$pval]);
		if(!empty($mb_srl) && !DB::error()){
			if(empty($uinfo['data'])){ //처음 한번만 //자신은 포인트 사용안함
				if($mb_srl !== $_f[$key]['member']){
					$mb = DB::get(_AF_MEMBER_TABLE_,'mb_point',['mb_srl'=>$mb_srl]);
					if(DB::error() || ($mb['mb_point']+$point) < 0) setHttpError('401 Unauthorized', true); //포인트 모자르면 에러
					DB::update(_AF_MEMBER_TABLE_,['^mb_point'=>'mb_point'.($point>0?'+':'').$point],['mb_srl'=>$mb_srl]);
				}
				DB::insert(_AF_HISTORY_TABLE_,['mb_srl'=>$uinfo['mb_srl'],'mb_ipaddress'=>$uinfo['ipaddress'],'hs_action'=>$act.'('.$srl.')','^hs_regdate'=>'NOW()']);
			}
		} else setHttpError('500 Internal Server Error', true);
	} DB::update(_AF_FILE_TABLE_, ['^mf_download'=>'mf_download+1'], ['mf_srl'=>$srl]);
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
