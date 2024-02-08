<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	$wr_srl = (int) $data['wr_srl'];
	if(empty($wr_srl)) return set_error(getLang('error_request'),4303);

	global $_MEMBER;
	if(empty($_MEMBER) || empty($_MEMBER['mb_srl'])) return set_error(getLang('error_permitted'),4501);

	$doc = DB::get(_AF_DOCUMENT_TABLE_, 'md_id, mb_srl, wr_srl, wr_no', ['wr_srl'=>$wr_srl]);
	if($ex=DB::error()) return set_error($ex->getMessage(), $ex->getCode());
	if(empty($doc['wr_srl'])) return set_error(getLang('error_request'),4303);

	$d_mb_srl = $doc['mb_srl'];

	if($_MEMBER['mb_srl'] == $d_mb_srl) return set_error(getLang('warn_not_allowable', ['author']),3505);

	$_out = getHistory('wr_yes::'.$wr_srl);
	if(!empty($_out)) return set_error(getLang('warn_actioned', ['yes']), 3303);

	DB::transaction();

	try {

		$_out = setHistory('wr_no::'.$wr_srl, 1, false, function($v)use($wr_srl,$d_mb_srl){
			// 처음에만 포인트 사용
			if(!empty($v)) return set_error(getLang('warn_actioned', ['no']), 3303);

			DB::update(_AF_DOCUMENT_TABLE_,
				[
					'^wr_no'=>'wr_no+1'
				], [
					'wr_srl'=>$wr_srl
				]
			);

			if(!empty($d_mb_srl)) setPoint(-1, $d_mb_srl);
		});
		if(!empty($_out['error'])) throw new Exception($_out['message'], $_out['error']);

		$wr_no = $doc['wr_no'] + 1;

	} catch (Exception $ex) {
		DB::rollback();
		return set_error($ex->getMessage(),$ex->getCode());
	}

	DB::commit();

	return ['error'=>0, 'message'=>getLang('success_finished'), 'wr_srl'=>$wr_srl, 'mb_srl'=>$_MEMBER['mb_srl'], 'wr_no'=>$wr_no];
}

/* End of file updateno.php */
/* Location: ./module/board/proc/updateno.php */
