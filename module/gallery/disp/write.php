<?php

if(!defined('__AFOX__')) exit();

function proc($data) {

	if(!isGrant('write', __MID__)) {
		return set_error(getLang('error_permitted'),4501);
	}

	$result['tpl'] = 'write';
	return $result;
}

/* End of file write.php */
/* Location: ./module/gallery/disp/write.php */
