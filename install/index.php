<?php
define('__AFOX__',   TRUE);
?>
<!doctype html><html lang="ko"><head><meta charset="utf-8"></head><body>

<?php
$datadir = dirname(__FILE__) . '/../data/';

if(file_exists($datadir.'config/_db_config.php')) {
	  exit("이미 설치되어있습니다.");
}

if(is_dir($datadir) || @mkdir($datadir, 0707)) {
	if(is_dir($datadir)) { chmod($datadir, 0707); }
} else {
	exit("${mydir} 디렉토리를 생성하지 못했습니다.");
}

if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
 if (!(is_readable($datadir) && is_writeable($datadir) && is_executable($datadir))){
	exit("${mydir} 디렉토리 퍼미션을 707로 변경하여 주세요.");
 }
}

if(empty($_POST['db_name'])) {

	echo '<form action="./" method="post" autocomplete="off">';
	echo '<span style="display:inline-block;width:150px">DB 호스트 : </span><input type="text" name="db_host" value="localhost"><br>';
	echo '<span style="display:inline-block;width:150px">DB 포트 : </span><input type="text" name="db_port" value="3306"><br>';
	echo '<span style="display:inline-block;width:150px">DB 이름 : </span><input type="text" name="db_name" value=""><br><br>';
	echo '<span style="display:inline-block;width:150px">DB 아이디 : </span><input type="text" name="db_user" value=""><br>';
	echo '<span style="display:inline-block;width:150px">DB 비밀번호 : </span><input type="text" name="db_pass" value=""><br><br>';
	echo '<button type="submit">설치 시작</button></form>';

	exit();
}

$dir_arr = array (
	$datadir.'cache',
	$datadir.'attach',
	$datadir.'member',
	$datadir.'config',
);

for ($i=0; $i<count($dir_arr); $i++) {
	if(!is_dir($dir_arr[$i]) && !@mkdir($dir_arr[$i], 0755)) exit("${$dir_arr[$i]} 디렉토리를 생성하지 못했습니다.");
	@chmod($dir_arr[$i], 0755);
}

if(empty($_POST['db_host'])||empty($_POST['db_port'])||empty($_POST['db_name'])||empty($_POST['db_user'])||empty($_POST['db_pass'])) {
	exit("값을 모두 채워 주세요.");
}

$db_host = $_POST['db_host'];
$db_port = $_POST['db_port'];
$db_name = $_POST['db_name'];
$db_user = $_POST['db_user'];
$db_pass = $_POST['db_pass'];

$charset = 'utf8';
$time_zone = 'Asia/Seoul';

require_once dirname(__FILE__) . '/../lib/db/mysql.php';
require_once dirname(__FILE__) . '/../lib/pbkdf2/PasswordStorage.php';

DB::init(array(
'host'=>$db_host,
'port'=>$db_port,
'name'=>$db_name,
'user'=>$db_user,
'pass'=>$db_pass,
'charset'=>$charset,
'time_zone'=>$time_zone
));

DB::transaction();

try {
$_err_keys = 'afox_config';
$create_sql = '
	  CREATE TABLE IF NOT EXISTS afox_config (
	   lang           CHAR(5)      NOT NULL,
	   start          CHAR(11)     NOT NULL,
	   theme          VARCHAR(255) NOT NULL,
	   title          VARCHAR(255) NOT NULL,
	   point_login    INT(11)      NOT NULL DEFAULT 0,
	   use_level      CHAR(1)      NOT NULL DEFAULT 0,
	   use_captcha    CHAR(1)      NOT NULL DEFAULT 0,
	   use_visit      CHAR(1)      NOT NULL DEFAULT 0,
	   use_signup     CHAR(1)      NOT NULL DEFAULT 0,
	   extra          TEXT) ENGINE=INNODB DEFAULT CHARSET='.$charset.';';

DB::query($create_sql);

$_err_keys = 'afox_themes';
$create_sql = '
	  CREATE TABLE IF NOT EXISTS afox_themes (
	   th_id          VARCHAR(255) NOT NULL,
	   extra          TEXT         NOT NULL DEFAULT \'\',

	  UNIQUE KEY ID_UK (th_id)) ENGINE=INNODB DEFAULT CHARSET='.$charset.';';

DB::query($create_sql);

$_err_keys = 'afox_menus';
$create_sql = '
	  CREATE TABLE IF NOT EXISTS afox_menus (
	   mu_srl          INT(11)      NOT NULL,
	   mu_parent       INT(11)      NOT NULL DEFAULT 0,
	   mu_status       CHAR(1)      NOT NULL DEFAULT 0,
	   mu_type         CHAR(1)      NOT NULL,
	   mu_title        VARCHAR(255) NOT NULL,
	   mu_link         VARCHAR(255) NOT NULL DEFAULT \'\',
	   mu_description  VARCHAR(255) NOT NULL DEFAULT \'\',
	   mu_collapse     CHAR(1)      NOT NULL DEFAULT 0,
	   mu_new_win      CHAR(1)      NOT NULL DEFAULT 0,

	  INDEX SRL_IX (mu_srl),
	  INDEX TYPE_IX (mu_type)) ENGINE=INNODB DEFAULT CHARSET='.$charset.';';

DB::query($create_sql);

$_err_keys = 'afox_members';
$create_sql = '
	  CREATE TABLE IF NOT EXISTS afox_members (
	   mb_srl          INT(11)      NOT NULL AUTO_INCREMENT,
	   mb_id           CHAR(11)     NOT NULL,
	   mb_rank         CHAR(1)      NOT NULL DEFAULT 0,
	   mb_status       CHAR(1)      NOT NULL DEFAULT 0,
	   mb_point        BIGINT(14)   NOT NULL DEFAULT 0,
	   mb_nick         VARCHAR(20)  NOT NULL,
	   mb_password     VARCHAR(100) NOT NULL,
	   mb_email        VARCHAR(255) NOT NULL DEFAULT \'\',
	   mb_homepage     VARCHAR(255) NOT NULL DEFAULT \'\',
	   mb_memo         TEXT         NOT NULL DEFAULT \'\',
	   mb_regdate      datetime     NOT NULL DEFAULT \'0000-00-00 00:00:00\',
	   mb_login        datetime     NOT NULL DEFAULT \'0000-00-00 00:00:00\',
	   extra           TEXT         NOT NULL DEFAULT \'\',

	  CONSTRAINT SRL_PK PRIMARY KEY (mb_srl),
	  UNIQUE KEY ID_UK (mb_id),
	  INDEX RANK_IX (mb_rank)) ENGINE=INNODB DEFAULT CHARSET='.$charset.';';

DB::query($create_sql);

$_err_keys = 'afox_addons';
$create_sql = '
	  CREATE TABLE IF NOT EXISTS afox_addons (
	   ao_id          VARCHAR(255) NOT NULL,
	   ao_use_pc      CHAR(1)      NOT NULL DEFAULT 0,
	   ao_use_mobile  CHAR(1)      NOT NULL DEFAULT 0,
	   extra          TEXT         NOT NULL DEFAULT \'\',

	  UNIQUE KEY ID_UK (ao_id),
	  INDEX PC_IX (ao_use_pc),
	  INDEX MOBILE_IX (ao_use_mobile)) ENGINE=INNODB DEFAULT CHARSET='.$charset.';';

DB::query($create_sql);

$_err_keys = 'afox_modules';
$create_sql = '
	  CREATE TABLE IF NOT EXISTS afox_modules (
	   md_id           CHAR(11)     NOT NULL,
	   md_key          VARCHAR(100) NOT NULL,
	   md_status       CHAR(1)      NOT NULL DEFAULT 0,
	   md_category     VARCHAR(255) NOT NULL DEFAULT \'\',
	   md_title        VARCHAR(255) NOT NULL,
	   md_description  VARCHAR(255) NOT NULL DEFAULT \'\',
	   md_file_max     INT(11)      NOT NULL DEFAULT 0,
	   md_file_size    INT(11)      NOT NULL DEFAULT 0,
	   md_file_ext     VARCHAR(255) NOT NULL DEFAULT \'\',
	   md_list_count   INT(11)      NOT NULL DEFAULT 20,
	   md_regdate      datetime     NOT NULL DEFAULT \'0000-00-00 00:00:00\',
	   md_manager      INT(11)      NOT NULL DEFAULT 0,
	   use_style       CHAR(1)      NOT NULL DEFAULT 0,
	   use_type        CHAR(1)      NOT NULL DEFAULT 0,
	   use_secret      CHAR(1)      NOT NULL DEFAULT 0,
	   point_view      INT(11)      NOT NULL DEFAULT 0,
	   point_write     INT(11)      NOT NULL DEFAULT 0,
	   point_reply     INT(11)      NOT NULL DEFAULT 0,
	   point_download  INT(11)      NOT NULL DEFAULT 0,
	   grant_list      CHAR(1)      NOT NULL DEFAULT 0,
	   grant_view      CHAR(1)      NOT NULL DEFAULT 0,
	   grant_write     CHAR(1)      NOT NULL DEFAULT 0,
	   grant_reply     CHAR(1)      NOT NULL DEFAULT 0,
	   grant_upload    CHAR(1)      NOT NULL DEFAULT 0,
	   grant_download  CHAR(1)      NOT NULL DEFAULT 0,
	   extra           TEXT         NOT NULL DEFAULT \'\',

	  UNIQUE KEY ID_UK (md_id),
	  INDEX KEY_IX (md_key)) ENGINE=INNODB DEFAULT CHARSET='.$charset.';';

DB::query($create_sql);

$_err_keys = 'afox_documents';
$create_sql = '
	  CREATE TABLE IF NOT EXISTS afox_documents (
	   wr_srl          INT(11)      NOT NULL AUTO_INCREMENT,
	   md_id           CHAR(11)     NOT NULL,
	   wr_parent       INT(11)      NOT NULL DEFAULT 0,
	   wr_status       CHAR(1)      NOT NULL DEFAULT 0,
	   wr_secret       CHAR(1)      NOT NULL DEFAULT 0,
	   wr_type         CHAR(1)      NOT NULL DEFAULT 0,
	   wr_category     VARCHAR(20)  NOT NULL DEFAULT \'\',
	   wr_title        VARCHAR(255) NOT NULL,
	   wr_content      LONGTEXT     NOT NULL DEFAULT \'\',
	   wr_tags         TEXT,
	   wr_hit          INT(11)      NOT NULL DEFAULT 0,
	   wr_hate         INT(11)      NOT NULL DEFAULT 0,
	   wr_good         INT(11)      NOT NULL DEFAULT 0,
	   wr_file         INT(11)      NOT NULL DEFAULT 0,
	   wr_reply        INT(11)      NOT NULL DEFAULT 0,
	   mb_srl          INT(11)      NOT NULL DEFAULT 0,
	   mb_nick         VARCHAR(20)  NOT NULL,
	   mb_password     VARCHAR(100),
	   wr_ipaddress    VARCHAR(128) NOT NULL DEFAULT \'\',
	   wr_regdate      datetime     NOT NULL DEFAULT \'0000-00-00 00:00:00\',
	   wr_update       datetime     NOT NULL DEFAULT \'0000-00-00 00:00:00\',
	   wr_updater      VARCHAR(20)  ,
	   extra           TEXT         NOT NULL DEFAULT \'\',

	  CONSTRAINT SRL_PK PRIMARY KEY (wr_srl),
	  INDEX REGDATE_IX (md_id, wr_regdate),
	  INDEX UPDATE_IX (md_id, wr_update),
	  INDEX CATEGORY_RDIX (md_id, wr_category, wr_regdate),
	  INDEX CATEGORY_UDIX (md_id, wr_category, wr_update),
	  INDEX MEMBER_IX (mb_srl),
	  INDEX IP_IX (wr_ipaddress)) ENGINE=INNODB DEFAULT CHARSET='.$charset.';';

DB::query($create_sql);

$_err_keys = 'afox_comments';
$create_sql = '
	  CREATE TABLE IF NOT EXISTS afox_comments (
	   rp_srl          INT(11)      NOT NULL AUTO_INCREMENT,
	   wr_srl          INT(11)      NOT NULL,
	   rp_parent       INT(11)      NOT NULL DEFAULT 0,
	   rp_status       CHAR(1)      NOT NULL DEFAULT 0,
	   rp_secret       CHAR(1)      NOT NULL DEFAULT 0,
	   rp_type         CHAR(1)      NOT NULL DEFAULT 0,
	   rp_content      TEXT         NOT NULL,
	   rp_hate         INT(11)      NOT NULL DEFAULT 0,
	   rp_good         INT(11)      NOT NULL DEFAULT 0,
	   rp_file         INT(11)      NOT NULL DEFAULT 0,
	   mb_srl          INT(11)      NOT NULL DEFAULT 0,
	   mb_nick         VARCHAR(20)  NOT NULL,
	   mb_password     VARCHAR(100),
	   rp_ipaddress    VARCHAR(128) NOT NULL DEFAULT \'\',
	   rp_regdate      datetime     NOT NULL DEFAULT \'0000-00-00 00:00:00\',
	   rp_update       datetime     NOT NULL DEFAULT \'0000-00-00 00:00:00\',
	   rp_depth        CHAR(5)      NOT NULL DEFAULT \'\',

	  CONSTRAINT SRL_PK PRIMARY KEY (rp_srl),
	  INDEX SRL_IX (wr_srl),
	  INDEX PARENT_IX (rp_parent),
	  INDEX MEMBER_IX (mb_srl),
	  INDEX IP_IX (rp_ipaddress)) ENGINE=INNODB DEFAULT CHARSET='.$charset.';';

DB::query($create_sql);

$_err_keys = 'afox_pages';
$create_sql = '
	  CREATE TABLE IF NOT EXISTS afox_pages (
	   md_id           CHAR(11)     NOT NULL,
	   pg_type         CHAR(1)      NOT NULL DEFAULT 0,
	   pg_content      LONGTEXT     NOT NULL DEFAULT \'\',
	   pg_file         INT(11)      NOT NULL DEFAULT 0,
	   pg_regdate      datetime     NOT NULL DEFAULT \'0000-00-00 00:00:00\',
	   pg_update       datetime     NOT NULL DEFAULT \'0000-00-00 00:00:00\',
	   extra           TEXT         NOT NULL DEFAULT \'\',

	  UNIQUE KEY ID_UK (md_id)) ENGINE=INNODB DEFAULT CHARSET='.$charset.';';

DB::query($create_sql);

$_err_keys = 'afox_files';
$create_sql = '
	  CREATE TABLE IF NOT EXISTS afox_files (
	   mf_srl          INT(11)      NOT NULL AUTO_INCREMENT,
	   md_id           CHAR(11)     NOT NULL,
	   mf_target       INT(11)      NOT NULL,
	   mf_name         VARCHAR(255) NOT NULL,
	   mf_upload_name  VARCHAR(255) NOT NULL,
	   mf_description  VARCHAR(255) NOT NULL DEFAULT \'\',
	   mf_size         INT(11)      NOT NULL,
	   mf_type         VARCHAR(255) NOT NULL,
	   mf_download     INT(11)      NOT NULL DEFAULT 0,
	   mb_srl          INT(11)      NOT NULL DEFAULT 0,
	   mf_ipaddress    VARCHAR(128) NOT NULL DEFAULT \'\',
	   mf_regdate      datetime     NOT NULL DEFAULT \'0000-00-00 00:00:00\',

	  CONSTRAINT SRL_PK PRIMARY KEY (mf_srl),
	  INDEX TARGET_IX (md_id, mf_target),
	  INDEX MEMBER_IX (mb_srl),
	  INDEX IP_IX (mf_ipaddress)) ENGINE=INNODB DEFAULT CHARSET='.$charset.';';

DB::query($create_sql);

$_err_keys = 'afox_histories';
$create_sql = '
	  CREATE TABLE IF NOT EXISTS afox_histories (
	   mb_srl          INT(11)      NOT NULL DEFAULT 0,
	   hs_ipaddress    VARCHAR(128) NOT NULL,
	   hs_action       VARCHAR(255) NOT NULL,
	   hs_regdate      datetime     NOT NULL DEFAULT \'0000-00-00 00:00:00\',

	  INDEX MEMBER_IX (mb_srl),
	  INDEX IP_IX (hs_ipaddress),
	  INDEX ACTION_IX (hs_action),
	  INDEX REGDATE_IX (hs_regdate)) ENGINE=INNODB DEFAULT CHARSET='.$charset.';';

DB::query($create_sql);

$_err_keys = 'afox_notes';
$create_sql = '
	  CREATE TABLE IF NOT EXISTS afox_notes (
	   nt_srl          INT(11)      NOT NULL AUTO_INCREMENT,
	   mb_srl          INT(11)      NOT NULL DEFAULT 0,
	   nt_sender       INT(11)      NOT NULL DEFAULT 0,
	   nt_send_date    datetime     NOT NULL DEFAULT \'0000-00-00 00:00:00\',
	   nt_read_date    datetime     NOT NULL DEFAULT \'0000-00-00 00:00:00\',
	   nt_note         TEXT         NOT NULL DEFAULT \'\',

	  CONSTRAINT SRL_PK PRIMARY KEY (nt_srl),
	  INDEX MEMBER_IX (mb_srl),
	  INDEX SENDER_IX (nt_sender)) ENGINE=INNODB DEFAULT CHARSET='.$charset.';';

DB::query($create_sql);

$_err_keys = 'afox_visitors';
$create_sql = '
	  CREATE TABLE IF NOT EXISTS afox_visitors (
	   vs_ipaddress    VARCHAR(128) NOT NULL,
	   vs_agent        VARCHAR(255) NOT NULL,
	   vs_referer      VARCHAR(255) NOT NULL,
	   vs_regdate      datetime     NOT NULL DEFAULT \'0000-00-00 00:00:00\',

	  INDEX AGENT_IX (vs_agent),
	  INDEX REGDATE_IX (vs_regdate)) ENGINE=INNODB DEFAULT CHARSET='.$charset.';';

DB::query($create_sql);

$_err_keys = 'insert_members';
$sql = 'SELECT mb_id FROM afox_members WHERE mb_id = \'admin\'';
$mb = DB::get($sql);
if (!$mb['mb_id']) {
	$sql = 'INSERT INTO afox_members (`mb_rank`, `mb_id`, `mb_password`, `mb_nick`, `mb_regdate`) VALUES ("%s", "%s", "%s", "%s", NOW())';
	DB::query(sprintf($sql, 's', 'admin', PasswordStorage::create_hash('0000'), '관리자'));
}

$_err_keys = 'insert_themes';
$sql = 'SELECT th_id FROM afox_themes WHERE th_id = \'default\'';
$cf = DB::get($sql);
if (!$cf['th_id']) {
	$tmp = [];
	$tmp['carousel_item_1'] = '<h1>헤드라인 예제</h1><p>이것은 헤드라인 예제입니다.<br>이 헤드라인은 (테마 설정)에서 사용자가 원하는 대로 작성하시면 됩니다.<br>에이폭스는 누구나 쉽고 편하고 자유롭게 콘텐츠를 발행을 할 수 있도록 하기 위한 CMS(Content Management System)입니다.</p><a class="btn btn-primary" href="#">오늘 가입</a>';
	$tmp['carousel_item_2'] = '<h1>두번째 헤드라인 예제</h1><p>에이폭스는 누구나 쉽고 편하고 자유롭게 콘텐츠를 발행을 할 수 있도록 하기 위한 CMS(Content Management System)입니다.<br>afox에 의해 디자인되고 만들어 졌으며 코드 기여자의 도움과 코어 팀에 의해 유지보수 됩니다.</p><a class="btn btn-primary" href="#">자세히 알아보기</a>';
	$tmp['carousel_item_3'] = '<h1>마지막으로 하나 더</h1><p><p>에이폭스는 각각의 기능과 디자인이 구조적으로 연결되는 모듈형 구조로 개발 및 유지보수를 쉽게 하도록 도와주며 관리자는 손쉽게 설정과 디자인을 변경할 수 있으며 여러분만의 개성을 가진 웹 사이트를 만들 수 있습니다.</p></p><a class="btn btn-primary" href="#">갤러리 검색</a>';
	$tmp['footer_html'] = '에이폭스는 <a href="http://afox.kr" target="_blank">@afox</a>에 의해 디자인되고 만들어 졌으며 <a href="https://github.com/phiDelPark/aFox/graphs/contributors">코드 기여자</a>의 도움과 <a href="https://github.com/phiDelPark?tab=people">코어 팀</a>에 의해 유지보수 됩니다.<br>코드는 <a rel="license" href="https://github.com/phiDelPark/aFox/blob/master/LICENSE" target="_blank">MIT</a>, 문서는 <a rel="license" href="https://creativecommons.org/licenses/by/3.0/" target="_blank">CC BY 3.0</a>에 의거하여 허가합니다.';
	$tmp = DB::quotes(serialize($tmp));
	$sql = 'INSERT INTO afox_themes (`th_id`, `extra`) VALUES ("default", '.$tmp.')';
	DB::query($sql);
}

$_err_keys = 'insert_config';
$sql = 'SELECT theme FROM afox_config WHERE 1';
$cf = DB::get($sql);
if (!$cf['theme']) {
	$sql = 'INSERT INTO afox_config (`theme`, `start`, `title`, `use_signup`) VALUES ("default", "welcome", "에이폭스", "1")';
	DB::query($sql);
}

$_err_keys = 'insert_modules';
$sql = 'SELECT md_id FROM afox_modules WHERE md_id = \'welcome\'';
$pg = DB::get($sql);
if (!$pg['md_id']) {
	$sql = 'INSERT INTO afox_modules (`md_id`, `md_key`, `md_title`, `md_regdate`) VALUES ("%s", "%s", "%s", NOW())';
	DB::query(sprintf($sql, 'welcome', 'page', ''));
}

$_err_keys = 'insert_pages';
$sql = 'SELECT md_id FROM afox_pages WHERE md_id = \'welcome\'';
$pg = DB::get($sql);
if (!$pg['md_id']) {
	$doc_data = '';
	$fp = fopen(dirname(__FILE__) . '/../README.md',"r");
	while( !feof($fp) ) $doc_data .= fgets($fp);
	fclose($fp);
	$sql = 'INSERT INTO afox_pages (`md_id`, `pg_type`, `pg_content`, `pg_update`, `pg_regdate`) VALUES ("%s", "1", %s, NOW(), NOW())';
	DB::query(sprintf($sql, 'welcome', DB::quotes($doc_data)));
}

} catch (Exception $ex) {
	DB::rollback();
	exit('{"STATUS":' . $ex->getCode() . ',"MESSAGE":"'.$_err_keys.': ' . $ex->getMessage() .'"}');
}

DB::commit();

$file = $datadir.'config/prohibit_id.php';
$f = @fopen($file, 'w');
fwrite($f, "<?php if(!defined('__AFOX__')) exit();\n");
fwrite($f, "\$_PROHIBIT_IDS=array('system','시스템','admin','administrator','관리자','운영자','주인장','어드민','webmaster','웹마스터','sysop','시삽','시샵','manager','매니저','메니저','root','루트','support','서포트','guest','방문객');");
fclose($f);
chmod($file, 0644);

$file = $datadir.'config/base_cdn_list.php';
$f = @fopen($file, 'w');
fwrite($f, "<?php if(!defined('__AFOX__')) exit();?>\n");
fwrite($f, '<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">'."\n");
fwrite($f, '<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.6.2/css/font-awesome.min.css" rel="stylesheet">'."\n");
fwrite($f, '<script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>'."\n");
fwrite($f, '<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>'."\n");
fclose($f);
chmod($file, 0644);

$file = $datadir.'config/_db_config.php';
$f = @fopen($file, 'w');
fwrite($f, "<?php\nif(!defined('__AFOX__')) exit();\n");
fwrite($f, "\$_DBINFO=array (\n");
fwrite($f, "'host'=>'{$db_host}',\n");
fwrite($f, "'port'=>'{$db_port}',\n");
fwrite($f, "'name'=>'{$db_name}',\n");
fwrite($f, "'user'=>'{$db_user}',\n");
fwrite($f, "'pass'=>'{$db_pass}',\n");
fwrite($f, "'charset'=>'{$charset}',\n");
fwrite($f, "'time_zone'=>'{$time_zone}',\n");
fwrite($f, "'http_port'=>'',\n");
fwrite($f, "'https_port'=>'',\n");
fwrite($f, "'use_ssl'=>'none', //'none','always','optional'\n");
fwrite($f, "'cookie_domain'=>'' //도메인쿠키공유(.도메인.com)\n");
fwrite($f, ");");
fclose($f);
chmod($file, 0644);

echo "설치 성공<br><br>";
echo "관리자 아이디 : admin<br>";
echo "관리자 비밀번호 : 0000<br><br>";
echo "주의 : 관리자 로그인 후에 관리자 페이지에 접속 후 관리자 비밀번호를 바꿔주세요.<br><br>";

?>

</body></html>

<?php

/* End of file __install.php */
/* Location: ./__install.php */