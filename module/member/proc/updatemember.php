<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	global $_MEMBER;

	if(isset($data['new_mb_id'])) $data['mb_id'] = $data['new_mb_id'];
	$data['mb_id'] = trim($data['mb_id']);

	if(empty($data['mb_id'])||empty(trim($data['mb_nick']))) return set_error(getLang('error_request'),4303);

	$data['mb_nick'] = trim(strip_tags($data['mb_nick']));
	$data['mb_email'] = trim(strip_tags($data['mb_email']));
	$data['mb_homepage'] = trim(strip_tags($data['mb_homepage']));

	if(!preg_match('/^[a-zA-Z]+\w{2,}$/', $data['mb_id'])) {
		return set_error(getLang('invalid_value', ['id']),701);
	}

	if(!preg_match('/^[\w-]+((?:\.|\+|\~)[\w-]+)*@[\w-]+(\.[\w-]+)+$/', $data['mb_email'])) {
		return set_error(getLang('invalid_value', ['email']),701);
	}

	if(!empty($data['mb_homepage'])&&!preg_match('/^(https?|ftp):\/\/[\w-]+(\.[\w-]+)+(:\d+)?/', $data['mb_homepage'])) {
		return set_error(getLang('invalid_value', ['homepage']),701);
	}

	if(!empty($data['new_mb_password'])) {
		if($data['new_mb_password'] !== $data['verify_mb_password']) {
			return set_error(getLang('msg_diff_password'),503);
		}
		$new_password = encryptString($data['new_mb_password']);
	} else {
		$new_password = false;
	}

	$is_admin = !empty($_MEMBER) && $_MEMBER['mb_rank'] == 's';

	// 금지 아이디 체크
	$file = _AF_CONFIG_DATA_ . 'prohibit_id.php';
	if(file_exists($file) && !$is_admin) {
		include $file;
		for ($i=0, $n=count($_PROHIBIT_IDS); $i<$n; $i++) {
			if($_PROHIBIT_IDS[$i] === $data['mb_id'] || $_PROHIBIT_IDS[$i] === $data['mb_nick']) {
				throw new Exception(getLang('msg_prohibit_id'), 3);
				break;
			}
		}
	}

	$member = getMember($data['mb_id']);
	if(!empty($member['error'])) return set_error($member['message'],$member['error']);
	//이메일 체크
	if(empty($member['mb_id']) || ($data['mb_email'] != $member['mb_email'])) {
		$out = getDBItem(_AF_MEMBER_TABLE_, ['mb_email'=>$data['mb_email']], 'mb_email');
		if(!empty($out['error'])) return set_error($out['message'],$out['error']);
		if(!empty($out['mb_email'])) return set_error(getLang('msg_email_exists'),802);
	}
	//닉네임 체크
	if(empty($member['mb_id']) || ($data['mb_nick'] != $member['mb_nick'])) {
		$out = getDBItem(_AF_MEMBER_TABLE_, ['mb_nick'=>$data['mb_nick']], 'mb_nick');
		if(!empty($out['error'])) return set_error($out['message'],$out['error']);
		if(!empty($out['mb_nick'])) return set_error(getLang('msg_nick_exists'),802);
	}

	// 아이콘 삭제 값이 넘어오면
	$remove_mb_icon = !empty($data['remove_files'][0]) && $data['remove_files'][0] == 'mb_icon';

	$destination = '';
	$mb_icon_tmp = '';
	if(!empty($_FILES['mb_icon']['tmp_name'])) {
		// 파일이 여러개 넘어오면 에러
		if(is_array($_FILES['mb_icon']['tmp_name'])) {
			return set_error(getLang('error_request'),4303);
		}

		if(!preg_match('/\.(png)$/i', $_FILES['mb_icon']['name'])) {
			return set_error(getLang('warning_permit', ['png']),2501);
		}

		$mb_icon_tmp = $_FILES['mb_icon']['tmp_name'];

		$size = getimagesize($mb_icon_tmp);
		if($size[0] > 100 || $size[1] > 100 || $size[0] < 50 || $size[1] < 50) {
			return set_error(getLang('invalid_value',['size']),701);
		}

		$destination = '/profile_image.png';
		// 디비에 등록된 후 옮기기 위해 임시폴더에 유지
		$ret = moveUpFile($_FILES['mb_icon'], '', 500000);
		if(!empty($ret['error'])) return set_error($ret['message'],$ret['error']);
		$remove_mb_icon = 1;
	}

	DB::transaction();

	try {

		$in_data = [
			'mb_nick'=>$data['mb_nick'],
			'mb_email'=>$data['mb_email'],
			'mb_homepage'=>$data['mb_homepage'],
			'mb_memo'=>xssClean($data['mb_memo'])
		];

		if($new_password) $in_data['mb_password'] = $new_password;

		// 등급과 포인트는 관리자만 지정 가능
		if($is_admin){
			if(isset($data['mb_point'])) $in_data['mb_point'] = $data['mb_point'];

			// 더 낮은 등급이 못 바꿈
			if(!isset($in_data['mb_point']) || (!empty($member['mb_rank']) && ord($member['mb_rank']) >= ord($_MEMBER['mb_rank']))) {
				unset($in_data['mb_rank']);
			} else {
				if(empty($data['new_mb_rank'])){
					//일반 회원, 포인트에 따라 계급 조정
					//최대 50 레벨 //주의, 50레벨 이상은 일반 회원이 아님
					$_sum_point = $in_data['mb_point'];
					$_rank = ($_sum_point > 250000) ? 50 : floor(sqrt(floor($_sum_point / 10) / 10));
					$in_data['mb_rank'] = chr($_rank + 48);
				} else {
					$in_data['mb_rank'] = ($data['new_mb_rank'] == 2 ? 's' : 'm');
				}
			}
		} else {
			unset($in_data['mb_rank']);
			unset($in_data['mb_point']);
		}

		if (empty($member['mb_id'])) {

			if(empty($data['new_mb_id']) || empty($new_password)) {
				throw new Exception(getLang('request_input',[empty($data['new_mb_id'])?'id':'password']), 3);
			}

			$in_data['mb_id'] = $data['mb_id'];
			$in_data['(mb_regdate)'] = 'NOW()';

			DB::insert(_AF_MEMBER_TABLE_, $in_data);
			$mb_srl = DB::insertId();
		} else {

			if(isset($data['new_mb_id'])) {
				throw new Exception(getLang('error_exists'), 4251);
			}

			if($remove_mb_icon) {
				$_icon = $member['mb_srl'].'/profile_image.png';
				if(file_exists(_AF_MEMBER_DATA_.$_icon)) {
					unlinkFile(_AF_MEMBER_DATA_.$_icon);
				}
			}

			DB::update(_AF_MEMBER_TABLE_,
				$in_data,
				['mb_id'=>$member['mb_id']]
			);
			$mb_srl = $member['mb_srl'];
		}

		// 정상적으로 디비에 입력되면 아이콘도 이동
		if($mb_icon_tmp) {
			$destination = _AF_MEMBER_DATA_.$mb_srl.$destination;
			$dir = dirname($destination);
			if(!is_dir($dir) && !mkdir($dir, _AF_DIR_PERMIT_, true)) {
				throw new Exception(getLang('upload_err_code(7)'), 10407);
			}
			@chmod($destination, 0707);
			if (@move_uploaded_file($mb_icon_tmp, $destination)) {
				@chmod($destination, _AF_FILE_PERMIT_);
			} else {
				@chmod($destination, _AF_FILE_PERMIT_);
			}
		}

		// TODO 나중에 닉네임 바꿀때 시간 제한 둘때 사용하기 위해서 기록
		setHistoryAction('mb_nick', $mb_srl);

	} catch (Exception $ex) {
		DB::rollback();
		return set_error($ex->getMessage(),$ex->getCode());
	}

	DB::commit();

	return ['error'=>0, 'message'=>getLang('success_saved')];
}

/* End of file updatemember.php */
/* Location: ./module/member/proc/updatemember.php */