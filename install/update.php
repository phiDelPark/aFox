<?php
if(!defined('__AFOX__')) exit();

$upbuild = 1;

function __AFOX__delete_updatefiles($dir) {
	chmod($dir . 'update/1.php', 0777);
	@unlink($dir . 'update/1.php');
	chmod($dir . 'update', 0777);
	@rmdir($dir . 'update');
	chmod($dir . 'update.php', 0777);
	if(!@unlink($dir . 'update.php')){
		echo "업데이트 파일 삭제에 실패했습니다.<br>'./install/update*' 파일, 폴더를 직접 지워주세요.<br><br>";
	}
}
function __AFOX__flush_msg($msg) {
	echo $msg;
	echo str_repeat(' ', 4096) . "\n";
	ob_flush();
	flush();
	sleep(1);
}
?>

<!doctype html><html lang="ko"><head><meta charset="utf-8"></head><body>

<?php

if(file_exists(_AF_PATH_ . 'data/config/_update.php')) {
	include _AF_PATH_ . 'data/config/_update.php';
	if(isset($_UPBUILD) && $_UPBUILD >= $upbuild) {
		__AFOX__delete_updatefiles(dirname(__FILE__) . '/');
		echo '업데이트 체크를 완료했습니다. <a href="'._AF_URL_.'">뒤로가기...</a>';
		exit();
	}
}

if(!empty($_POST['start']) && $_POST['start']=='true') {

$o = $_DBINFO;
mysqli_report(MYSQLI_REPORT_OFF);
$link = new mysqli(isset($o['host'])   ? $o['host']   : 'localhost',
					 isset($o['user'])   ? $o['user']   : 'root',
					 isset($o['pass'])   ? $o['pass']   : '',
					 isset($o['name'])   ? $o['name'] : 'default',
					 isset($o['port'])   ? $o['port']   : 3306,
					 isset($o['sock'])   ? $o['sock']   : FALSE );
if( mysqli_connect_errno() ) {
	die(mysqli_connect_error() . ' (' . mysqli_connect_errno() . ')');
}
mysqli_query($link, "SET NAMES ".(isset($o['charset']) ? $o['charset'] : "utf8"));
mysqli_query($link, "SET time_zone = '".(isset($o['time_zone']) ? $o['time_zone'] : "Asia/Seoul")."'");

if(empty($_POST['id']) || empty($_POST['pass'])) {
	echo '관리자 id, password 를 입력하세요. <a href="'._AF_URL_.'">뒤로가기...</a>';
	exit();
}

require_once _AF_PATH_ . 'lib/pbkdf2/PasswordStorage.php';

$r = mysqli_query($link, 'SELECT mb_rank,mb_password FROM '._AF_MEMBER_TABLE_.' WHERE mb_id = \''.$_POST['id'].'\'');
if(mysqli_errno($link)) throw new Exception(mysqli_error($link), mysqli_errno($link));
$row = mysqli_fetch_assoc($r);
if (empty($row['mb_password']) || !PasswordStorage::verify_password($_POST['pass'], $row['mb_password']) || $row['mb_rank'] !== 's') {
	echo '관리자가 아닙니다. <a href="'._AF_URL_.'">뒤로가기...</a>';
	exit();
}

mysqli_begin_transaction($link,MYSQLI_TRANS_START_READ_WRITE);
try{

	$dir = dirname(__FILE__) . '/';

	for ($i=1; $i <= $upbuild; $i++) {
		include $dir . 'update/'.$i.'.php';
	}

	mysqli_commit($link);

	echo '<br>업데이트 성공 <a href="'._AF_URL_.'">뒤로가기...</a><br><br>';

	$file = _AF_PATH_ . 'data/config/_update.php';
	@chmod($file, 0777);
	@unlink($file);
	$f = @fopen($file, 'w');
	fwrite($f, "<?php\nif(!defined('__AFOX__')) exit();\n");
	fwrite($f, "\$_UPBUILD={$upbuild};");
	fclose($f);
	chmod($file, 0644);

	__AFOX__delete_updatefiles($dir);

} catch (Exception $ex) {
	mysqli_rollback($link);
	exit($ex->getMessage());
}

echo '</body></html>';
exit();
}
?>

업데이트 할 사항이 있습니다.<br>
안전한 업데이트를 위해 DB를 백업해 두세요.<br><br>계속하시겠습니까?<br><br>

<form action="<?php _AF_URL_ . 'index.php' ?>" method="post" autocomplete="off">
<input type="hidden" name="start" value="true">
<span style="display:inline-block;width:150px">관리자 ID: </span><input type="text" name="id"><br>
<span style="display:inline-block;width:150px">PASSWORD : </span><input type="password" name="pass"><br>
<button type="submit">업데이트 시작</button></form>

</body></html>

<?php

/* End of file update.php */
/* Location: ./install/update.php */