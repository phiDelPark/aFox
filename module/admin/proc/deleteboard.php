<?php

if(!defined('__AFOX__')) exit();

function proc($data) {
	if(empty($data['md_id'])) return set_error(getLang('msg_invalid_request'),303);

	global $_MEMBER;
	$is_admin = !empty($_MEMBER) && $_MEMBER['mb_rank'] == 's';

	DB::transaction();

	try {
		$module = getDBItem(_AF_MODULE_TABLE_, ['md_key'=>'board','md_id'=>$data['md_id']]);
		if(!empty($module['error'])) throw new Exception($module['message'], $module['error']);
		if(empty($module['md_id'])) throw new Exception(getLang('msg_invalid_request'), 303);

		// 권한 체크 // 관리자만
		if(!$is_admin) throw new Exception(getLang('msg_not_permitted'), 901);

		$md_id = $module['md_id'];

		$sql = 'SELECT md_id,wr_srl,wr_updater FROM '._AF_DOCUMENT_TABLE_.' WHERE md_id=:1 OR (md_id=\'_AFOXtRASH_\' AND wr_updater=:2)';
		DB::getList($sql, [$md_id,$md_id], function($r){
			while ($row = DB::assoc($r)) {
				$_md_id = ($row['md_id'] === '_AFOXtRASH_') ? $row['wr_updater'] : $row['md_id'];
				$_wr_srl = $row['wr_srl'];
				// 파일 삭제
				$variable = ['binary','image','video','audio'];
				foreach ($variable as $val) {
					$directory = _AF_ATTACH_DATA_ . $val . '/' . $_md_id . '/' . $_wr_srl . '/';
					if(is_dir($directory)){
						$handle = @opendir($directory); // 절대경로
						while ($file = readdir($handle)) {
							unlinkFile($directory.$file);
						}
						closedir($handle);
						unlinkDir($directory);
					}
				}
			}
		});

		// 파일 , 댓글 , 문서 삭제
		DB::query('DELETE d, c, f FROM '._AF_DOCUMENT_TABLE_.' AS d LEFT JOIN '._AF_COMMENT_TABLE_.' AS c ON c.wr_srl = d.wr_srl LEFT JOIN '._AF_FILE_TABLE_.' AS f ON f.md_id = d.md_id WHERE d.md_id = \'' . $md_id . '\' OR (d.md_id = \'_AFOXtRASH_\' AND d.wr_updater = \'' . $md_id . '\')');
		// 모듈 삭제
		DB::delete(_AF_MODULE_TABLE_,['md_key'=>'board','md_id'=>$md_id]);

	} catch (Exception $ex) {
		DB::rollback();
		return set_error($ex->getMessage(),$ex->getCode());
	}

	DB::commit();

	return ['error'=>0, 'message'=>getLang('success_deleted')];
}

/* End of file deleteboard.php */
/* Location: ./module/admin/proc/deleteboard.php */