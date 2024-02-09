<?php
if(!defined('__AFOX__')) exit();

if($_CALLED['position'] == 'before_proc')
{
	// 비밀글이면 권한 체크
	if(DB::count(_AF_DOCUMENT_TABLE_,['wr_secret'=>1,'wr_srl'=>$_DATA['target']])>0) {
		$PERMIT_KEY = md5($_DATA['module'].'_'.$_DATA['target'].'_'.$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']);
		return get_session('_AF_SECRET_DOCUMENT_'.$PERMIT_KEY);
	} else {
		return true;
	}

}