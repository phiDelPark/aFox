<?php
if(!defined('__AFOX__')) exit();
@include_once dirname(__FILE__) . '/lang/' . _AF_LANG_ . '.php';

define('_CUSTOM_MOUDLE_GUID_', '354EBFD5-7265-40D7-9C61-3A900991500C');

// 모듈 설정이 없으므로 직접 기본정보 입력
$_CFG['md_title'] = getLang('combine_search');
$_CFG['md_description'] = getLang('desc_combine_search_finished');


/* End of file config.php */
/* Location: ./module/searchex/config.php */