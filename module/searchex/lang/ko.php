<?php
if(!defined('__AFOX__')) exit();

$_LANG['id']				= '아이디';
$_LANG['name']				= '이름';
$_LANG['title']				= '제목';

$_LANG['next']				= '다음';
$_LANG['previous']			= '이전';
$_LANG['search']			= '검색';

$_LANG['combine_search']    = '통합검색';
$_LANG['desc_combine_search'] = '검색을 통합할 모듈을 선택해주세요.';


// 모듈 설정이 없으므로 직접 기본정보 입력
$_CFG['md_title'] = getLang('combine_search');
$_CFG['md_about'] = '';

$_LANG['list_count']			= '목록 수';
$_LANG['desc_list_count']		= '게시판 한페이지에 출력할 목록 수를 설정할 수 있습니다.';

/* End of file ko.php */
/* Location: ./module/search/lang/ko.php */
