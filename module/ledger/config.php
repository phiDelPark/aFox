<?php
if(!defined('__AFOX__')) exit();
@include_once dirname(__FILE__) . '/funcs.php';

define('_AF_LEDGER_DATA_TABLE_', 'afc_ledger_data');
define('_AF_LEDGER_CATEGORY_TABLE_', 'afc_ledger_category');
define('_AF_LEDGER_HISTORY_TABLE_', 'afc_ledger_history');

// 모듈 설정이 없으므로 직접 기본정보 입력
$_CFG['md_title'] = getLang('md_title_ledger');
$_CFG['md_description'] = getLang('md_description_ledger');

/* End of file config.php */
/* Location: ./module/ledger/config.php */