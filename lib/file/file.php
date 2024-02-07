<?php if(!defined("__AFOX__"))exit();function setHttpError($e,$b=false){header("HTTP/1.1 ".$e);header("Connection: close");if($b){set_error("HTTP/1.1 ".$e,3);header("Location: ".$_SERVER["HTTP_REFERER"]);}exit();}function file_triggerModuleCall($f,$d){include $f;$r=0;if(function_exists($t="before_proc")){$r=call_user_func($t,[$d]);}return $r===true?$d:false;}function file_triggerCall($a,&$d){$s=DB::gets(_AF_TRIGGER_TABLE_,"tg_id",["tg_key"=>"M","^"=>"ASCII(grant_access)<=".$a]);foreach($s as $v){$f=_AF_MODULES_PATH_."/".$v["tg_id"]."/trigger/filedownload.php";if(file_exists($f)){$r=file_triggerModuleCall($f,$d);if($r===false)return false;$d=$r;}}return true;}$mb_srl=0;$mb_rank="0";$t=isset($_SESSION["AF_LOGIN_ID"])?$_SESSION["AF_LOGIN_ID"]:get_cookie("AF_LOGIN_ID");if(($t)&&preg_match('/^[a-zA-Z]+\w{2,}$/',$t)){$o=DB::get(_AF_MEMBER_TABLE_,["mb_id"=>$t]);if(!DB::error()&&!empty($o["mb_srl"])){$mb_srl=$o["mb_srl"];$mb_rank=$o["mb_rank"];}}if($_CFG["use_protect"]=='1'&&!empty($_SERVER["HTTP_REFERER"])&&!preg_match("/^https?:[\/]+[a-z0-9\-\.]*".$_SERVER["SERVER_NAME"].".+/i",$_SERVER["HTTP_REFERER"]))setHttpError("401 Unauthorized");static $_f=[];$srl=(int)$_GET["file"];$thumb=isset($_GET["thumb"])?$_GET["thumb"]:"";$key=$srl.($thumb?"_thumb".$thumb:"");if(!isset($_f[$key])){$o=DB::get(_AF_FILE_TABLE_,["mf_srl"=>$srl]);if(DB::error())setHttpError("400 Bad Request");$o_d=$o["md_id"];$o_t=$o["mf_target"];if($o["mf_link"]=="1"){if(!is_numeric($o["mf_upload_name"])||(int)$o["mf_upload_name"]<1)setHttpError("400 Bad Request");$o=DB::get(_AF_FILE_TABLE_,["mf_srl"=>$o["mf_upload_name"]]);if(DB::error())setHttpError("400 Bad Request");}$ts=["image"=>1,"video"=>2,"audio"=>3];$t=explode("/",strtolower($o["mf_type"]));$type=empty($ts[$t[0]])?"binary":$t[0];$_f[$key]=["srl"=>$srl,"module"=>$o_d,"target"=>$o_t,"point"=>0,"permission"=>true,"type"=>$type,"member"=>$o["mb_srl"],"mime"=>$o["mf_type"],"name"=>$o["mf_name"]];$_f[$key]["path"]=_AF_ATTACH_DATA_.$type."/".$o["md_id"]."/".$o["mf_target"]."/".$o["mf_upload_name"];if($type=="binary"){$m=DB::get(_AF_MODULE_TABLE_,"md_id,point_download,grant_download",["md_id"=>$o["md_id"]]);if(empty($m["md_id"]))setHttpError("400 Bad Request",true);$_f[$key]["point"]=(int)$m["point_download"];$_f[$key]["permission"]=!($_f[$key]["point"]<0&&empty($mb_srl));if($_f[$key]["permission"]&&($g=$m["grant_download"])){$_f[$key]["permission"]=($t=ord($mb_rank))<116&&ord($g)<=$t;if(!$_f[$key]["permission"])setHttpError("401 Unauthorized",true);}
}elseif($type=="image"){if(!file_exists($_f[$key]["path"])){$_f[$key]["path"]=_AF_PATH_."common/img/no_image.png";}elseif($thumb){$sz=explode("x",$thumb);$sz[2]=empty($sz[2])?"0":"1";if(empty($sz[0])||empty($sz[1])){$m=DB::get(_AF_MODULE_TABLE_,"md_id,thumb_width,thumb_height,thumb_option",["md_id"=>$o["md_id"]]);if(empty($m["md_id"]))setHttpError("400 Bad Request");$sz=[$m["thumb_width"],$m["thumb_height"],empty($m["thumb_option"])?"0":"1"];}else{$sz[0]=ceil(($sz[0]>300?300:$sz[0])/50)*50;$sz[1]=ceil(($sz[1]>300?300:$sz[1])/50)*50;}if((int)$sz[0]&&(int)$sz[1]){if(file_exists($t=_AF_ATTACH_DATA_."thumbnail/".$o["md_id"]."/".$o["mf_target"]."/".$o["mf_srl"]."/".$sz[0]."x".$sz[1]."x".$sz[2].".png")){$_f[$key]["path"]=$t;}else{require_once dirname(__FILE__)."/thumbnail.php";$_f[$key]["path"]=thumbnail($_f[$key]["path"],$t,$sz[0],$sz[1],$sz[2]);}}}}}if(!$_f[$key]["permission"])setHttpError("401 Unauthorized",$type=="binary");if($type=="binary"){if(!file_triggerCall(ord($mb_rank),$_f[$key]))setHttpError("401 Unauthorized", true);}if(!($fp=@fopen($_f[$key]["path"],"rb")))setHttpError("404 Not Found");$fa=fstat($fp);if(!empty($_SERVER["HTTP_IF_MODIFIED_SINCE"])&&($t=strtotime($_SERVER["HTTP_IF_MODIFIED_SINCE"]))&&$t>=$fa["mtime"]){fclose($fp);setHttpError("304 Not Modified");}if($type=="binary"){if(($pt=$_f[$key]["point"])){$at="mf_download";if(!empty($mb_srl)){$uf=DB::get(_AF_HISTORY_TABLE_,["hs_action"=>"::".$at."::".$srl."::","mb_srl"=>$mb_srl]);if(empty($uf["mb_srl"])){if($mb_srl!==$_f[$key]["member"]){$mb=DB::get(_AF_MEMBER_TABLE_,"mb_point",["mb_srl"=>$mb_srl]);if(DB::error()||$mb["mb_point"]+$pt<0)setHttpError("401 Unauthorized",true);DB::update(_AF_MEMBER_TABLE_,["^mb_point"=>"mb_point".($pt>0?"+":"").$pt],["mb_srl"=>$mb_srl]);}DB::insert(_AF_HISTORY_TABLE_,["mb_srl"=>$mb_srl,"hs_action"=>"::".$at."::".$srl."::","hs_value"=>1,"^hs_regdate"=>"NOW()"]);}}else setHttpError("500 Internal Server Error",true);}DB::update(_AF_FILE_TABLE_,["^mf_download"=>"mf_download+1"],["mf_srl"=>$srl]);}header("Last-Modified: ".$fa["mtime"]);header("Content-Disposition: attachment; filename=\"".$_f[$key]["name"]."\"");header("Cache-Control:");header("Content-Type: ".$_f[$key]["mime"]);header("Connection: close");fpassthru($fp);fclose($fp);
/* End of file file.php, Location: ./lib/file/file.php */