<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	if(empty($data['start'])) return set_error(getLang('error_request'),4303);
	if(empty($data['theme'])) $data['theme'] = 'default';

	if(!empty($_FILES)) {
		$_lst = ['logo','favicon'];
		$_lsext = ['png','ico'];

		foreach ($_lst as $key => $val) {
			if (empty($_FILES[$val]['name'])) continue;

			if(!preg_match('/\.('.$_lsext[$key].')$/i', $_FILES[$val]['name'])) {
				return set_error(getLang('warning_allowable',[$_lsext[$key]]),3503);
			}

			$destination = _AF_CONFIG_DATA_.$val.'.'.$_lsext[$key];
			$ret = moveUpFile($_FILES[$val], $destination);
			if(!empty($ret['error'])) return $ret;
		}
	}

	if(!empty($data['remove_files'])) {
		foreach ($data['remove_files'] as $val) {
			unlinkFile(_AF_CONFIG_DATA_.$val.'.'.$_lsext[(int)($val=='favicon')]);
		}
	}

	DB::transaction();

	try {
		DB::query('DELETE FROM '._AF_CONFIG_TABLE_.' WHERE 1');

		DB::insert(_AF_CONFIG_TABLE_,
			[
				'theme'=>$data['theme'],
				'start'=>$data['start'],
				'title'=>trim($data['title']),
				'use_captcha'=>empty($data['use_captcha'])?'0':'1',
				'use_visit'=>empty($data['use_visit'])?'0':'1',
				'use_signup'=>empty($data['use_signup'])?'0':'1',
				'use_protect'=>empty($data['use_protect'])?'0':'1',
				'use_full_login'=>empty($data['use_full_login'])?'0':'1',
				'point_login'=>(int)$data['point_login']
			]
		);

		$_lst = ['prohibit_id','access_ip'];
		foreach ($_lst as $val) {
			$data[$val] = trim($data[$val]);
			$file = _AF_CONFIG_DATA_.$val.'.php';
			if(empty($data[$val])) {
				unlinkFile($file);
			} else {
				$_comma = '';
				$prohibit_id = explode(($val=='prohibit_id'?',':"\n"), $data[$val]);
				$f = @fopen($file, 'w');
				fwrite($f, "<?php if(!defined('__AFOX__')) exit();\n");
				if($val=='access_ip') {
					fwrite($f, '$_ACCESS_IP_MODE="'.($data['access_ip_mode']=='possible'?'possible':'intercept').'";');
				}
				fwrite($f, '$_'.strtoupper($val).'S=array(');
				foreach ($prohibit_id as $v) {
					$v = trim(escapeHtml($v, true));
					if(empty($v)) continue;
					if($val=='access_ip'){
						$v = str_replace(".", "\.", $v);
						$v = str_replace("+", "[0-9\.]+", $v);
					}
					fwrite($f, $_comma.'"'.$v.'"');
					$_comma = ',';
				}
				fwrite($f, ");");
				fclose($f);
				chmod($file, _AF_FILE_PERMIT_);
			}
		}

		$_lst = ['base_cdn_list','terms_of_use'];
		foreach ($_lst as $val) {
			$data[$val] = trim($data[$val]);
			$file = _AF_CONFIG_DATA_.$val.'.php';
			if(empty($data[$val])) {
				unlinkFile($file);
			} else {
				$f = @fopen($file, 'w');
				fwrite($f, "<?php if(!defined('__AFOX__')) exit();?>\n");
				fwrite($f, $data[$val]);
				fclose($f);
				chmod($file, _AF_FILE_PERMIT_);
			}
		}

	} catch (Exception $ex) {
		DB::rollback();
		return set_error($ex->getMessage(),$ex->getCode());
	}

	DB::commit();

	return ['error'=>0, 'message'=>getLang('success_saved')];
}

/* End of file updatesetup.php */
/* Location: ./module/admin/proc/updatesetup.php */
