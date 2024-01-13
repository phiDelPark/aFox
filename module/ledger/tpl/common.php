<?php
if(!defined('__AFOX__')) exit();

if (!__MODAL__) {
addJSLang(['new','edit','request_item_name']);
addCSS(_AF_URL_ . 'module/board/tpl/board'. (__DEBUG__ ? '.css?' . _AF_SERVER_TIME_ : '.min.css'));
addCSS(_AF_URL_ . 'module/ledger/tpl/ledger'. (__DEBUG__ ? '.css?' . _AF_SERVER_TIME_ : '.min.css'));
addCSS(_AF_URL_ . 'module/ledger/tpl/jquery-ui.min.css');
addJS(_AF_URL_ . 'module/ledger/tpl/jquery-ui.min.js');
addJS(_AF_URL_ . 'module/ledger/tpl/ledger'. (__DEBUG__ ? '.js?' . _AF_SERVER_TIME_ : '.min.js'));
}

// 구버전 sql 용 초기화
$_DATA['category'] = empty($_DATA['category']) ? null : $_DATA['category'];

$DOC = &$_{'ledger'};
$LIST = &$_{'ledger'}['_DOCUMENT_LIST_'];
$CATE = getCategorys();

/* End of file common.php */
/* Location: ./module/ledger/tpl/common.php */
/* 이 파일이 존재하면 해당 tpl 파일을 읽기전 자동으로 가장 먼저 읽어들임 */