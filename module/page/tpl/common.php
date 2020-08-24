<?php
if(!defined('__AFOX__')) exit();

if (!__MODAL__) {
addCSS(_AF_URL_ . 'module/page/tpl/page'. (__DEBUG__ ? '.css?' . _AF_SERVER_TIME_ : '.min.css'));
addJS(_AF_URL_ . 'module/page/tpl/page'. (__DEBUG__ ? '.js?' . _AF_SERVER_TIME_ : '.min.js'));
}

$PAGE = &$_{'page'};

/* End of file common.php */
/* Location: ./module/page/tpl/common.php */
/* 이 파일이 존재하면 해당 tpl 파일을 읽기전 자동으로 가장 먼저 읽어들임   */
