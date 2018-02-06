<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	unlinkAll(_AF_CACHE_DATA_);
	return ['error'=>0, 'message'=>getLang('success_saved')];
}

/* End of file clearcache.php */
/* Location: ./module/admin/proc/clearcache.php */
