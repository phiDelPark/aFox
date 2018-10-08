<?php

if(!defined('__AFOX__')) exit();

function proc($data) {

	DB::transaction();

	try {
		$module = getModule('search');
		$md_extra = [];
		$md_extra['md_ids'] = empty($data['md_ids']) ? [] : $data['md_ids'];

		if (empty($module['md_id'])) {
			DB::insert(_AF_MODULE_TABLE_,
				[
					'md_id'=>'search',
					'md_key'=>'search',
					'md_title'=>getLang('combine_search'),
					'md_list_count'=>empty($data['md_list_count']) ? 20 : abs($data['md_list_count']),
					'md_extra'=>empty($md_extra)?'':serialize($md_extra),
					'^md_regdate'=>'NOW()'
				]
			);
		} else {
			DB::update(_AF_MODULE_TABLE_,
				[
					'md_key'=>'search',
					'md_list_count'=>empty($data['md_list_count']) ? 20 : abs($data['md_list_count']),
					'md_extra'=>empty($md_extra)?'':serialize($md_extra)
				], [
					'md_id'=>'search'
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

/* End of file setupsearch.php */
/* Location: ./module/search/proc/setupsearch.php */
