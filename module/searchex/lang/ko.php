<?php
if(!defined('__AFOX__')) exit();

$_LANG['combine_search'] = '통합검색';
$_LANG['desc_combine_search'] = '검색을 통합할 모듈을 선택해주세요.';
$_LANG['desc_combine_search_finished'] = '통합 검색을 완료하여 %s개의 문서를 찾았습니다.';


// 모듈 설정이 없으므로 직접 기본정보 입력
$_CFG['md_title'] = getLang('combine_search');
$_CFG['md_description'] = getLang('desc_combine_search_finished');

/* End of file ko.php */
/* Location: ./module/search/lang/ko.php */
