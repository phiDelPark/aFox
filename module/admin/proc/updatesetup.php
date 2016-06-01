<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	if(empty($data['start'])) return set_error(getLang('msg_invalid_request'),303);
	if(empty($data['theme'])) $data['theme'] = 'default';

	if(!empty($_FILES)) {
		$_lst = ['logo','favicon'];
		$_lsext = ['png','ico'];

		foreach ($_lst as $key => $val) {
			if (empty($_FILES[$val]['name'])) continue;

			if(!preg_match('/\.('.$_lsext[$key].')$/i', $_FILES[$val]['name'])) {
				return set_error(getLang('warn_permit',[$_lsext[$key]]),303);
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
				'title'=>$data['title'],
				'point_login'=>(int)$data['point_login'],
				'use_captcha'=>$data['use_captcha'],
				'use_visit'=>$data['use_visit'],
				'use_signup'=>$data['use_signup'],
				'protect_file'=>$data['protect_file']
			]
		);


		$_lst = ['prohibit_id','possible_ip','intercept_ip'];
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
				fwrite($f, '$_'.strtoupper($val).'S=array(');
				foreach ($prohibit_id as $v) {
					fwrite($f, $_comma.'\''.escapeHtml($v, true).'\'');
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