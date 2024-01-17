<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	if(empty($data['md_id'])) return set_error(getLang('error_request'),4303);

	$ids = empty($data['md_ids'])?'':serialize($data['md_ids']);
	$count = empty($data['md_list_count']) ? 20 : abs($data['md_list_count']);

	DB::transaction();

	try {

		$md_id = getModule('@searchex', 'md_id');

		if (empty($md_id)) {

			DB::insert(_AF_MODULE_TABLE_,
				[
					'md_id'=>'@searchex',
					'md_key'=>'searchex',
					'md_title'=>getLang('combine_search'),
					'md_list_count'=>(int)$count,
					'md_extra'=>$ids,
					'^md_regdate'=>'NOW()'
				]
			);
		} else {

			DB::update(_AF_MODULE_TABLE_,
				[
					'md_key'=>'searchex',
					'md_list_count'=>(int)$count,
					'md_extra'=>$ids
				], [
					'md_id'=>'@searchex'
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

/* End of file updatesetup.php */
/* Location: ./module/searchex/proc/updatesetup.php */
