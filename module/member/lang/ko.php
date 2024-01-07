<?php
if(!defined('__AFOX__')) exit();
// 에러가 발생할 가능성이 있으니 언어에 ' (홑따옴표)는 쓰지마세요.
// 필요하면 ` (악센트) 이걸 사용하세요.

$_LANG['agree_signup'] = '가입을 동의합니다';
$_LANG['not_use_signup'] = '지금은 회원 가입을 하실 수 없습니다.';

$_LANG['terms_of_use'] = '이용약관';
$_LANG['verify_password'] = '비밀번호 확인';

$_LANG['desc_mb_id'] = '아이디 첫 글자는 영문이여야 하며 영문, 숫자, 언더바(_)만 사용 가능합니다.';
$_LANG['desc_mb_password'] = '비밀번호와 비밀번호 확인을 입력하세요.';
$_LANG['desc_change_password'] = '비밀번호를 변경하려면 비밀번호와 확인을 입력하세요.';
$_LANG['desc_member_icon'] = '회원 아이콘은 가로,세로 100x100 크기의 png 파일로 등록해주세요.';
$_LANG['desc_member_memo'] = '회원 프로필에 표시할 메모를 입력할 수 있습니다. (Markdown 지원)';
$_LANG['desc_terms_of_use'] = '가입전에 이용약관을 꼭 읽고 가입에 동의해주세요.';

$_LANG['msg_diff_password'] = '새 비밀번호와 비밀번호 확인이 서로 다릅니다.';
$_LANG['msg_email_exists'] = '같은 메일이 이미 등록 되어있습니다.';
$_LANG['msg_nick_exists'] = '같은 별명이 이미 등록 되어있습니다.';
$_LANG['msg_prohibit_id'] = '이 아이디나 별명은 사용하실 수 없습니다.';
$_LANG['msg_wrong_password'] = '가입된 회원아이디가 아니거나 비밀번호가 틀립니다.'."\n".'비밀번호는 대소문자를 구분합니다.';
$_LANG['msg_login_overtry'] = '로그인 시도를 3회 실패하였습니다.'."\n".'이제 보안코드를 출력하니 같이 적어주십시요.';

$_LANG['md_title_inbox'] = '편지함';
$_LANG['md_description_inbox'] = '';
$_LANG['md_title_trash'] = '휴지통';
$_LANG['md_description_trash'] = '';
$_LANG['md_title_signup'] = getLang(empty($_MEMBER)?'member_signup':'member');
$_LANG['md_description_signup'] = '';


// 모듈 설정이 없으므로 직접 기본정보 입력
$_CFG['md_title'] = empty($_DATA['disp'])?'':getLang('md_title_'.strtolower($_DATA['disp']));
$_CFG['md_description'] = empty($_DATA['disp'])?'':getLang('md_description_'.strtolower($_DATA['disp']));

/* End of file ko.php */
/* Location: ./module/member/lang/ko.php */
