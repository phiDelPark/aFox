<?php
if(!defined('__AFOX__')) exit();
// 에러가 발생할 가능성이 있으니 언어에 ' (홑따옴표)는 쓰지마세요.

$_LANG['all']				= '모두';
$_LANG['none']				= '없음';

$_LANG['time']				= '시간';
$_LANG['date']				= '날짜';
$_LANG['year']				= '년';
$_LANG['month']				= '월';
$_LANG['day']				= '일';
$_LANG['today']				= '오늘';
$_LANG['tomorrow']			= '내일';
$_LANG['yesterday']			= '어제';

$_LANG['alert']				= '알림';
$_LANG['confirm']			= '확인';
$_LANG['danger']			= '위험';
$_LANG['error']				= '에러';
$_LANG['success']			= '성공';
$_LANG['warning']			= '경고';

$_LANG['ok']				= '확인';
$_LANG['cancel']			= '취소';
$_LANG['yes']				= '예';
$_LANG['no']				= '아니오';
$_LANG['open']				= '열기';
$_LANG['close']				= '닫기';

$_LANG['save']				= '저장';
$_LANG['edit']				= '편집';
$_LANG['copy']				= '복사';
$_LANG['move']				= '이동';
$_LANG['delete']			= '삭제';

$_LANG['use']				= '사용';
$_LANG['using']				= '사용중';
$_LANG['notuse']			= '미사용';
$_LANG['agree']				= '동의';
$_LANG['disagree']			= '동의안함';

$_LANG['login']				= '로그인';
$_LANG['logout']			= '로그아웃';

$_LANG['calling_server']	= '서버에 요청 중입니다.';

$_LANG['request_input']		= '%s을(를) 입력하세요.';
$_LANG['request_select']	= '%s을(를) 선택하세요.';

$_LANG['confirm_empty']		= '%s을(를) 비우시겠습니까?';
$_LANG['confirm_copy']		= '%s을(를) 복사하시겠습니까?';
$_LANG['confirm_delete']	= '%s을(를) 삭제하시겠습니까?';
$_LANG['confirm_move']		= '%s을(를) 이동하시겠습니까?';
$_LANG['confirm_register']	= '%s을(를) 등록하시겠습니까?';
$_LANG['confirm_restore']	= '%s을(를) 복구하시겠습니까?';
$_LANG['confirm_save']		= '%s을(를) 저장하시겠습니까?';
$_LANG['confirm_send']		= '%s을(를) 발송하시겠습니까?';

$_LANG['success_moved']		= '이동했습니다.';
$_LANG['success_copied']	= '복사했습니다.';
$_LANG['success_deleted']	= '삭제했습니다.';
$_LANG['success_saved']		= '저장했습니다.';
$_LANG['success_sended']	= '발송했습니다.';
$_LANG['success_connected']	= '연결했습니다.';
$_LANG['success_login']		= '로그인했습니다.';
$_LANG['success_logout']	= '로그아웃했습니다.';
$_LANG['success_finished']	= '작업을 마쳤습니다.';

// invalid_... 올바르지 않은 값 오류번호 2001~2999
$_LANG['invalid_value']		= '%s의 값이 올바르지 않습니다.'; //2001

// warn_... 경고 오류번호 3001~3999
$_LANG['warn_exists']		= '%s이(가) 존재합니다.'; // 3103
$_LANG['warn_not_exists']	= '%s이(가) 존재하지 않습니다.'; // 3105
$_LANG['warn_selected']		= '선택된 %s이(가) 없습니다.'; // 3203
$_LANG['warn_not_select']	= '%s은(는) 선택할 수 없습니다.'; // 3205
$_LANG['warn_actioned']		= '이미 %s을(를) 하였습니다.'; // 3303
$_LANG['warn_not_action']	= '%s을(를) 할 수 없습니다.'; // 3305
$_LANG['warn_allowable']	= '%s만 허용됩니다.'; // 3503
$_LANG['warn_not_allowable']= '%s는(은) 허용되지 않습니다.'; // 3505
$_LANG['warn_shortage']		= '%s이(가) 부족합니다.'; // 3701

// error_... 에러시 오류번호 4001~4999
$_LANG['error_occured']		= '오류가 발생했습니다.'; // 4001
$_LANG['error_founded']		= '대상을 찾을 수 없습니다.'; // 4201
$_LANG['error_request']		= '잘못된 요청입니다.'; // 4303
$_LANG['error_permitted']	= '권한이 없습니다.'; // 4501
$_LANG['error_password']	= '비밀번호가 다릅니다.'; // 4801

// upload_err_... 업로드 관련
$_LANG['error_upload(0)']	= '파일 업로드가 성공했습니다.'; // 10400
$_LANG['error_upload(1)']	= '업로드한 파일이 PHP upload_max_filesize 보다 큽니다.'; // 10401
$_LANG['error_upload(2)']	= '업로드한 파일이 설정된 최대 파일 크기보다 큽니다.'; // 10402
$_LANG['error_upload(3)']	= '파일이 일부분만 전송되었습니다.'; // 10403
$_LANG['error_upload(4)']	= '파일이 전송되지 않았습니다.'; // 10404
$_LANG['error_upload(6)']	= '임시 폴더가 없습니다.'; // 10406
$_LANG['error_upload(7)']	= '디스크에 파일 쓰기를 실패했습니다.'; // 10407
$_LANG['error_upload(8)']	= '확장에 의해 파일 업로드가 중지되었습니다.'; // 10408
$_LANG['error_upload(-1)']	= '업로드한 파일이 HTTP post로 전송된 것이 아닙니다.'; // 10489
$_LANG['error_upload(-3)']	= '업로드한 파일이 설정된 최대 수 보다 큽니다.'; // 10487

/* End of file default_ko.php */
/* Location: ./common/lang/default_ko.php */
