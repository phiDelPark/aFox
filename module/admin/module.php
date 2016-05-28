<?php
	if(!defined('__AFOX__')) exit();

	if(empty($_DATA['mid']))
		include_once dirname(__FILE__) . '/module.ls.php';
	else
		include_once _AF_MODULES_PATH_ . $_DATA['mid'] . '/setup.php';

/* End of file module.php */
/* Location: ./module/admin/module.php */