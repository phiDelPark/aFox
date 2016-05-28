<?php

if(!defined('__AFOX__')) exit();

function proc($data) {

	if(!isset($data['mu_type'])) return set_error(getLang('msg_invalid_request'),303);

	$mu_type = $data['mu_type'];

	DB::transaction();

	try {
		DB::delete(_AF_MENU_TABLE_,
			[
				'mu_type'=>$mu_type
			]
		);

		if(isset($data['item_key'])){
			$item_key = $data['item_key'];
			$parent_key = $data['parent_key'];
			$item_title = $data['item_title'];
			$item_link = $data['item_link'];
			$desc_key = $data['desc_key'];
			$collapse_key = $data['collapse_key'];
			$new_win_key = $data['new_win_key'];
			$ins_srl = [0];

			foreach ($item_key as $key => $value) {

				DB::insert(_AF_MENU_TABLE_,
					[
						'mu_srl'=>$key + 1,
						'mu_parent'=>$ins_srl[$parent_key[$key]],
						'mu_type'=>$mu_type,
						'mu_title'=>$item_title[$key],
						'mu_link'=>$item_link[$key],
						'mu_description'=>$desc_key[$key],
						'mu_collapse'=>(int)$collapse_key[$key],
						'mu_new_win'=>(int)$new_win_key[$key]
					]
				);
				$ins_srl[$value] = $key + 1;
			}
		}

	} catch (Exception $ex) {
		DB::rollback();
		return set_error($ex->getMessage(),$ex->getCode());
	}

	DB::commit();

	return ['error'=>0, 'message'=>getLang('success_saved')];
}

/* End of file updatemenu.php */
/* Location: ./module/admin/proc/updatemenu.php */