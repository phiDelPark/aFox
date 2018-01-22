<?php
if(!defined('__AFOX__')) exit();

function before_filedownload(&$files) {
	$file = $files[0];
	// 비밀글이면 권한 체크
	if(DB::count(_AF_DOCUMENT_TABLE_,['wr_secret'=>1,'wr_srl'=>$file['target']])>0){
		$PERMIT_KEY = md5($file['module'].'_'.$file['target'].'_'.$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']);
		return get_session('_AF_SECRET_DOCUMENT_'.$PERMIT_KEY);
	} else {
		return true;
	}
}

/* End of file trigger.php */
/* Location: ./module/board/trigger.php */
