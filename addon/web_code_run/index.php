<?php
if(!defined('__AFOX__')) exit();

if($called_position == 'after_disp' && $called_trigger == 'default' && !empty($_DATA['wr_content'])) {
	addJS(_AF_URL_.'addon/web_code_run/web_code_run.js'.(__DEBUG__ ? '?' . _AF_SERVER_TIME_ : ''));
}

/* End of file index.php */
/* Location: ./addon/web_code_run/index.php */
