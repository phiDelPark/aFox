<?php
if(!defined('__AFOX__')) exit();

addJSLang(['close','error','document','comment','password','request_input','confirm_select_delete']);
addCSS(_AF_URL_ . 'module/board/tpl/board'. (__DEBUG__ ? '.css?' . _AF_SERVER_TIME_ : '.min.css'));
addJS(_AF_URL_ . 'module/board/tpl/board'. (__DEBUG__ ? '.js?' . _AF_SERVER_TIME_ : '.min.js'));

// 구버전 sql 용 초기화
$_DATA['category'] = empty($_DATA['category']) ? null : $_DATA['category'];

// 개별 설정 초기화
if(!isset($_CFG['md_extra']['configs'])) {
	$_CFG['md_extra']['configs'] = [
		'show_column'=>['wr_srl','wr_title','mb_nick','wr_hit','wr_regdate'],
		'show_rv_column'=>['mb_nick','extra_vars','wr_update']
	];
}

$CONFIGS = &$_CFG['md_extra']['configs'];

$DOC = &$_{'board'};
$LIST = &$_{'board'}['_DOCUMENT_LIST_'];
$REPLYS = &$_{'board'}['_COMMENT_LIST_'];

$use_style = ['list','review','gallery','timeline'];
$use_style = $use_style[abs($_CFG['use_style'])];

/* End of file common.php */
/* Location: ./module/board/tpl/common.php */
/* 이 파일이 존재하면 해당 tpl 파일을 읽기전 자동으로 가장 먼저 읽어들임   */
