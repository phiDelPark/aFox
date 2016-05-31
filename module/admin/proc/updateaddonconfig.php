<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	if(empty($data['ao_id'])) return set_error(getLang('msg_invalid_request'),303);

	DB::transaction();

	try {

		$ao_id = $data['ao_id'];
		$ao_use_pc = empty($data['ao_use_pc'])?'0':$data['ao_use_pc'];
		$ao_use_mobile = empty($data['ao_use_mobile'])?'0':$data['ao_use_mobile'];

		$out = getDBItem(_AF_ADDON_TABLE_, ['ao_id'=>$ao_id], 'ao_id');
		if(!empty($out['error'])) throw new Exception($out['message'], $out['error']);

		$addon_exists = !empty($out['ao_id']);

		$remove_array = ['ao_id', 'ao_use_pc', 'ao_use_mobile', 'module', 'id', 'act', 'disp', 'success_return_url', 'error_return_url'];
		foreach ($remove_array as $value) {
			if(isset($data[$value])) unset($data[$value]);
		}

		// 오류 방지를 위해서 확장 필드 최대 사이즈 체크
		$extra = serialize($data);
		if(strlen($extra) > 65535) {
			throw new Exception(getLang('msg_max_overflow', [65535]), 1401);
		}

		if($addon_exists) {
			DB::update(_AF_ADDON_TABLE_,
				[
					'ao_use_pc'=>$ao_use_pc,
					'ao_use_mobile'=>$ao_use_mobile,
					'extra'=>$extra
				], [
					'ao_id'=>$ao_id
				]
			);
		} else {
			DB::insert(_AF_ADDON_TABLE_,
				[
					'ao_id'=>$ao_id,
					'ao_use_pc'=>$ao_use_pc,
					'ao_use_mobile'=>$ao_use_mobile,
					'extra'=>$extra
				]
			);
		}

	} catch (Exception $ex) {
		DB::rollback();
		return set_error($ex->getMessage(),$ex->getCode());
	}

	DB::commit();

	return ['error'=>0, 'message'=>getLang('success_saved')];
}

/* End of file updateaddonconfig.php */
/* Location: ./module/admin/proc/updateaddonconfig.php */