<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	if(empty($data['ao_id'])) return set_error(getLang('error_request'),4303);

	DB::transaction();

	try {

		$ao_id = $data['ao_id'];
		$use_pc = empty($data['use_pc'])?'0':$data['use_pc'];
		$use_mobile = empty($data['use_mobile'])?'0':$data['use_mobile'];
		$use_editor = empty($data['use_editor'])?'0':$data['use_editor'];
		$grant_access = empty($data['grant_access'])?'0':$data['grant_access'];

		$remove_array = ['ao_id', 'use_pc', 'use_mobile', 'grant_access', 'module', 'id', 'act', 'disp', 'success_url', 'error_url','response_tags'];
		foreach ($remove_array as $value) {
			if(isset($data[$value])) unset($data[$value]);
		}

		// 오류 방지를 위해서 확장 필드 최대 사이즈 체크
		$extra = serialize($data);
		if(strlen($extra) > 65535) {
			throw new Exception(getLang('overflow_max', ['extra_keys',65535]), 1401);
		}

		DB::delete(_AF_TRIGGER_TABLE_,['tg_key'=>'A','tg_id'=>$ao_id]);
		DB::delete(_AF_ADDON_TABLE_,['ao_id'=>$ao_id]);

		DB::insert(_AF_ADDON_TABLE_,
			[
				'ao_id'=>$ao_id,
				'use_editor'=>$use_editor,
				'ao_extra'=>$extra
			]
		);
		DB::insert(_AF_TRIGGER_TABLE_,
			[
				'tg_key'=>'A',
				'tg_id'=>$ao_id,
				'use_pc'=>$use_pc,
				'use_mobile'=>$use_mobile,
				'grant_access'=>$grant_access
			]
		);

		// 캐시 재생성
		set_cache('_AF_ADDON_'.$ao_id, $data);
		//에디터 컴포넌트 목록 캐시 생성
		$out = DB::gets(_AF_ADDON_TABLE_,
		['use_editor'=>'1'], [],
			function($r){
				$rset = [];
				$_ADDON_INFO = [];
				while ($row = DB::fetch($r)){
					$tmp = _AF_ADDONS_PATH_ . $row['ao_id'] . '/info.php';
					if(file_exists($tmp)){
						include $tmp;
						$rset[] = [0=>$row['ao_id'],1=>$_ADDON_INFO['title']];
					}
				}
				return $rset;
			}
		);
		set_cache('_AF_EDITOR_COMPONENTS', $out);

	} catch (Exception $ex) {
		DB::rollback();
		return set_error($ex->getMessage(),$ex->getCode());
	}

	DB::commit();

	return ['error'=>0, 'message'=>getLang('success_saved')];
}

/* End of file updateaddon.php */
/* Location: ./module/admin/proc/updateaddon.php */
