<?php
if(!defined('__AFOX__')) exit();

if (!__MODAL__) {
addJSLang([]);
}

// 구버전 sql 용 초기화
$_POST['category'] = empty($_POST['category']) ? null : $_POST['category'];

// 개별 설정 초기화
if(!isset($_CFG['md_extra']['configs'])) {
	$_CFG['md_extra']['configs'] = [
		'show_column'=>['wr_srl','wr_title','mb_nick','wr_hit','wr_regdate'],
		'show_rv_column'=>['mb_nick','extra_values','wr_update']
	];
}
$CONFIGS = &$_CFG['md_extra']['configs'];

$DOC = &$_DATA;
$LIST = &$_DATA['list'];
$REPLYS = &$_DATA['replys'];

$use_style = ['list','review','gallery','timeline'];
$use_style = $use_style[abs($_CFG['use_style'])];

/* End of file common.php */
/* Location: ./theme/default/skin/board/common.php */
