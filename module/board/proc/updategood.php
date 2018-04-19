<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	$wr_srl = (int) $data['wr_srl'];
	if(empty($wr_srl)) return set_error(getLang('error_request'),4303);

	global $_MEMBER;
	if(empty($_MEMBER) || empty($_MEMBER['mb_srl'])) return set_error(getLang('error_permitted'),4501);

	$doc = DB::get(_AF_DOCUMENT_TABLE_, 'md_id, mb_srl, wr_srl, wr_good', ['wr_srl'=>$wr_srl]);
	if($ex=DB::error()) return set_error($ex->getMessage(), $ex->getCode());
	if(empty($doc['wr_srl'])) return set_error(getLang('error_request'),4303);

	$d_mb_srl = $doc['mb_srl'];

	if($_MEMBER['mb_srl'] == $d_mb_srl) return set_error(getLang('warning_not_allowable', ['author']),3505);

	DB::transaction();

	try {

		$_out = setHistoryAction('wr_good::'.$wr_srl, 1, false, function($v)use($wr_srl,$d_mb_srl){
			// 처음에만 포인트 사용
			if(!empty($v['data'])) {
				return set_error(getLang('warning_actioned', ['good']), 3303);
			}

			DB::update(_AF_DOCUMENT_TABLE_,
				[
					'^wr_good'=>'wr_good+1'
				], [
					'wr_srl'=>$wr_srl
				]
			);

			if(!empty($d_mb_srl)) setPoint(2, $d_mb_srl);
		});
		if(!empty($_out['error'])) throw new Exception($_out['message'], $_out['error']);

		$wr_good = $doc['wr_good'] + 1;

	} catch (Exception $ex) {
		DB::rollback();
		return set_error($ex->getMessage(),$ex->getCode());
	}

	DB::commit();

	return ['error'=>0, 'message'=>getLang('success_finished'), 'wr_srl'=>$wr_srl, 'mb_srl'=>$_MEMBER['mb_srl'], 'wr_good'=>$wr_good];
}

/* End of file updategood.php */
/* Location: ./module/board/proc/updategood.php */
