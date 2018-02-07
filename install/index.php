<?php
define('__AFOX__',   TRUE);

// 서버 필요 조건
// * UTF-8
// * PHP version 5.4.0 이상
// * MYSQL version 5.1.0 이상

define('_AF_CONFIG_TABLE_', 'afox_config');
define('_AF_MEMBER_TABLE_', 'afox_members');
define('_AF_MODULE_TABLE_', 'afox_modules');
define('_AF_THEME_TABLE_', 'afox_themes');
define('_AF_MENU_TABLE_', 'afox_menus');
define('_AF_ADDON_TABLE_', 'afox_addons');
define('_AF_PAGE_TABLE_', 'afox_pages');
define('_AF_DOCUMENT_TABLE_', 'afox_documents');
define('_AF_COMMENT_TABLE_', 'afox_comments');
define('_AF_HISTORY_TABLE_', 'afox_histories');
define('_AF_VISITOR_TABLE_', 'afox_visitors');
define('_AF_NOTE_TABLE_', 'afox_notes');
define('_AF_FILE_TABLE_', 'afox_files');
define('_AF_TRIGGER_TABLE_', 'afox_triggers');

require_once dirname(__FILE__) . '/../lib/db/mysql.php';

if(!function_exists('password_hash')) {
	defined('PASSWORD_BCRYPT') or define('PASSWORD_BCRYPT', '');
	function password_hash($password, $algo) {
		$result = DB::query("SELECT password('$password') as pass", true);
		return $result[0]['pass'];
	}
}
if(!function_exists('password_verify')) {
	function password_verify($password, $hash) {
		$password = password_hash($password, 'PASSWORD_BCRYPT');
		return !empty($hash) && $password === $hash;
	}
}

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

	if(version_compare(PHP_VERSION, '5.4.0', '<')) {
		echo '<h3 style="color:red">PHP 버전이 낮습니다.<br>PHP 5.4.0 이상 버전을 사용해주세요. </h3>';
	}

	echo '<h3>에이폭스 CMS 설치</h3><form action="index.php" method="post" autocomplete="off">';
	echo '<strong style="display:inline-block;width:150px">DB 호스트*</strong> : <input type="text" name="db_host" value="localhost"><br>';
	echo '<strong style="display:inline-block;width:150px">DB 포트*</strong> : <input type="text" name="db_port" value="3306"><br>';
	echo '<strong style="display:inline-block;width:150px">DB 이름*</strong> : <input type="text" name="db_name" value=""><br>';
	echo '<strong style="display:inline-block;width:150px">DB 종류*</strong> : <select name="db_type"><option value="myisam">MyISAM</option><option value="innodb" '.(version_compare(PHP_VERSION, '5.5.0', '>=')?'selected':'').'>InnoDB (COMPACT)</option><option value="innodb8">InnoDB (KEY_BLOCK_8)</option><option value="innodb16">InnoDB (KEY_BLOCK_16)</option></select><br><br>';
	echo '<strong style="display:inline-block;width:150px">DB 아이디*</strong> : <input type="text" name="db_user" value=""><br>';
	echo '<strong style="display:inline-block;width:150px">DB 비밀번호*</strong> : <input type="text" name="db_pass" value=""><br><br>';
	echo '<h3>에이폭스 도메인 설정</h3>';
	echo '<span style="display:inline-block;width:150px">내 도메인</span> : <input type="text" name="domain" value=""><br>';
	echo '<span style="display:inline-block;padding-left:163px">현재 이 사이트의 도메인을 입력하세요.<br>도메인이 자주 바뀌면 비워두셔도 됩니다. 단, 문제 발생시 적어주세요.</span><br>';
	echo '<span style="display:inline-block;width:150px">쿠키 도메인</span> : <input type="text" name="cookie_domain" value=""><br>';
	echo '<span style="display:inline-block;padding-left:163px">쿠키 도메인 www.afox.kr 와 afox.kr 은 서로 다른 도메인으로 인식합니다.<br>쿠키를 공유하려면 .afox.kr 과 같이 입력하세요.</span><br><br>';
	echo '<span style="display:inline-block;width:150px">표준시간대</span> : <input type="text" name="time_zone" value="Asia/Seoul"><br><br>';
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
	exit("* 필수 값을 모두 채워 주세요.");
}

$charset = 'utf8';
$db_host = $_POST['db_host'];
$db_port = $_POST['db_port'];
$db_name = $_POST['db_name'];
$db_user = $_POST['db_user'];
$db_pass = $_POST['db_pass'];
$time_zone = empty($_POST['time_zone']) ? 'Asia/Seoul' : $_POST['time_zone'];

$__tmp = trim($_POST['domain']);
$domain = empty($__tmp) ? '' : preg_replace('/https?\:\/\//i', '', str_replace('\\', '/',$_POST['domain']));
$__tmp = trim($_POST['cookie_domain']);
$cookie_domain = empty($__tmp) ? '' : preg_replace('/https?\:\/\//i', '', str_replace('\\', '/',$_POST['cookie_domain']));

$is_innodb = $_POST['db_type'] == 'innodb16' || $_POST['db_type'] == 'innodb8' || $_POST['db_type'] == 'innodb';
$innodb_option = !$is_innodb || $_POST['db_type'] == 'innodb' ? '' : ($_POST['db_type'] == 'innodb16' ? '16' : '8');

$o = array(
'host'=>$db_host,
'port'=>$db_port,
'name'=>$db_name,
'user'=>$db_user,
'pass'=>$db_pass,
'charset'=>$charset,
'time_zone'=>$time_zone
);

$o['host'] = isset($o['host'])   ? $o['host']   : 'localhost';
$o['user'] = isset($o['user'])   ? $o['user']   : 'root';
$o['pass'] = isset($o['pass'])   ? $o['pass']   : '';
$o['name'] = isset($o['name'])   ? $o['name'] : 'default';
$o['port'] = isset($o['port'])   ? $o['port']   : 3306;
$o['sock'] = isset($o['sock'])   ? $o['sock']   : FALSE;
$o['charset'] = isset($o['charset']) ? $o['charset'] : "utf8";
$o['time_zone'] = isset($o['time_zone']) ? $o['time_zone'] : "Asia/Seoul";

DB::init($o);

if($is_innodb){
	// 서버에서 Barracuda를 지원하면 Barracuda로 설치하지만 아니면 Antelope로 설치된다.
	// 단, 루트 사용자는 동적 설정이 가능하다.
	try {
		@DB::query("SET GLOBAL innodb_file_format=Barracuda");
		@DB::query("SET GLOBAL innodb_file_per_table=ON");
	} catch (Exception $e) {
	}
	if($innodb_option==='') {
	   $_engine = ' ENGINE=InnoDB ROW_FORMAT=COMPACT DEFAULT CHARSET='.$charset.';';
	} else {
	   $_engine = ' ENGINE=InnoDB ROW_FORMAT=COMPRESSED KEY_BLOCK_SIZE='.$innodb_option.' DEFAULT CHARSET='.$charset.';';
	}
} else {
	$_engine = ' ENGINE=MyISAM DEFAULT CHARSET='.$charset.';';
}

DB::transaction();

// 관리에 편하게 메인 설정들은 접두어 안 붙임

try {
$_err_keys = _AF_CONFIG_TABLE_;
$create_sql = '
	  CREATE TABLE IF NOT EXISTS '._AF_CONFIG_TABLE_.' (
	   lang           CHAR(5)      NOT NULL,
	   start          CHAR(11)     NOT NULL,
	   theme          VARCHAR(255) NOT NULL,
	   title          VARCHAR(255) NOT NULL,
	   use_signup     CHAR(1)      NOT NULL DEFAULT 0,
	   use_visit      CHAR(1)      NOT NULL DEFAULT 0,
	   use_captcha    CHAR(1)      NOT NULL DEFAULT 0,
	   use_protect    CHAR(1)      NOT NULL DEFAULT 0,
	   use_full_login CHAR(1)      NOT NULL DEFAULT 0,
	   point_login    INT(11)      NOT NULL DEFAULT 0)'.$_engine;

DB::query($create_sql);
if($error = DB::error()) throw new Exception($error->getMessage(),$error->getCode());


$_err_keys = _AF_THEME_TABLE_;
$create_sql = '
	  CREATE TABLE IF NOT EXISTS '._AF_THEME_TABLE_.' (
	   th_id          VARCHAR(255) NOT NULL,
	   th_extra       TEXT         NOT NULL DEFAULT \'\',

	  UNIQUE KEY ID_UK (th_id))'.$_engine;

DB::query($create_sql);
if($error = DB::error()) throw new Exception($error->getMessage(),$error->getCode());

$_err_keys = _AF_MENU_TABLE_;
$create_sql = '
	  CREATE TABLE IF NOT EXISTS '._AF_MENU_TABLE_.' (
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
	  INDEX TYPE_IX (mu_type))'.$_engine;

DB::query($create_sql);
if($error = DB::error()) throw new Exception($error->getMessage(),$error->getCode());

$_err_keys = _AF_MEMBER_TABLE_;
$create_sql = '
	  CREATE TABLE IF NOT EXISTS '._AF_MEMBER_TABLE_.' (
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
	   mb_block_id     TEXT         NOT NULL DEFAULT \'\',
	   mb_extra        TEXT         NOT NULL DEFAULT \'\',

	  CONSTRAINT SRL_PK PRIMARY KEY (mb_srl),
	  UNIQUE KEY ID_UK (mb_id),
	  INDEX RANK_IX (mb_rank))'.$_engine;

DB::query($create_sql);
if($error = DB::error()) throw new Exception($error->getMessage(),$error->getCode());

$_err_keys = _AF_ADDON_TABLE_;
$create_sql = '
	  CREATE TABLE IF NOT EXISTS '._AF_ADDON_TABLE_.' (
	   ao_id          VARCHAR(255) NOT NULL,
	   ao_extra       TEXT         NOT NULL DEFAULT \'\',

	  UNIQUE KEY ID_UK (ao_id))'.$_engine;

DB::query($create_sql);
if($error = DB::error()) throw new Exception($error->getMessage(),$error->getCode());

$_err_keys = _AF_MODULE_TABLE_;
$create_sql = '
	  CREATE TABLE IF NOT EXISTS '._AF_MODULE_TABLE_.' (
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
	   thumb_width     INT(11)      NOT NULL DEFAULT 0,
	   thumb_height    INT(11)      NOT NULL DEFAULT 0,
	   thumb_option    CHAR(1)      NOT NULL DEFAULT 0,
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
	   md_extra        TEXT         NOT NULL DEFAULT \'\',

	  UNIQUE KEY ID_UK (md_id),
	  INDEX KEY_IX (md_key))'.$_engine;

DB::query($create_sql);
if($error = DB::error()) throw new Exception($error->getMessage(),$error->getCode());

$_err_keys = _AF_DOCUMENT_TABLE_;
$create_sql = '
	  CREATE TABLE IF NOT EXISTS '._AF_DOCUMENT_TABLE_.' (
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
	   wr_good         INT(11)      NOT NULL DEFAULT 0,
	   wr_hate         INT(11)      NOT NULL DEFAULT 0,
	   wr_file         INT(11)      NOT NULL DEFAULT 0,
	   wr_reply        INT(11)      NOT NULL DEFAULT 0,
	   mb_srl          INT(11)      NOT NULL DEFAULT 0,
	   mb_rank         CHAR(1)      NOT NULL DEFAULT 0,
	   mb_nick         VARCHAR(20)  NOT NULL,
	   mb_password     VARCHAR(100),
	   mb_ipaddress    VARCHAR(128) NOT NULL DEFAULT \'\',
	   wr_regdate      datetime     NOT NULL DEFAULT \'0000-00-00 00:00:00\',
	   wr_update       datetime     NOT NULL DEFAULT \'0000-00-00 00:00:00\',
	   wr_updater      VARCHAR(20)  ,
	   wr_extra        TEXT         NOT NULL DEFAULT \'\',

	  CONSTRAINT SRL_PK PRIMARY KEY (wr_srl),
	  INDEX REGDATE_IX (md_id, wr_regdate),
	  INDEX UPDATE_IX (md_id, wr_update),
	  INDEX CATEGORY_RDIX (md_id, wr_category, wr_regdate),
	  INDEX CATEGORY_UDIX (md_id, wr_category, wr_update),
	  INDEX MEMBER_IX (mb_srl),
	  INDEX IP_IX (mb_ipaddress))'.$_engine;

DB::query($create_sql);
if($error = DB::error()) throw new Exception($error->getMessage(),$error->getCode());

$_err_keys = _AF_COMMENT_TABLE_;
$create_sql = '
	  CREATE TABLE IF NOT EXISTS '._AF_COMMENT_TABLE_.' (
	   rp_srl          INT(11)      NOT NULL AUTO_INCREMENT,
	   wr_srl          INT(11)      NOT NULL,
	   rp_parent       INT(11)      NOT NULL DEFAULT 0,
	   rp_status       CHAR(1)      NOT NULL DEFAULT 0,
	   rp_secret       CHAR(1)      NOT NULL DEFAULT 0,
	   rp_type         CHAR(1)      NOT NULL DEFAULT 0,
	   rp_content      TEXT         NOT NULL,
	   rp_good         INT(11)      NOT NULL DEFAULT 0,
	   rp_hate         INT(11)      NOT NULL DEFAULT 0,
	   rp_file         INT(11)      NOT NULL DEFAULT 0,
	   mb_srl          INT(11)      NOT NULL DEFAULT 0,
	   mb_rank         CHAR(1)      NOT NULL DEFAULT 0,
	   mb_nick         VARCHAR(20)  NOT NULL,
	   mb_password     VARCHAR(100),
	   mb_ipaddress    VARCHAR(128) NOT NULL DEFAULT \'\',
	   rp_regdate      datetime     NOT NULL DEFAULT \'0000-00-00 00:00:00\',
	   rp_update       datetime     NOT NULL DEFAULT \'0000-00-00 00:00:00\',
	   rp_depth        CHAR(5)      NOT NULL DEFAULT \'\',

	  CONSTRAINT SRL_PK PRIMARY KEY (rp_srl),
	  INDEX SRL_IX (wr_srl),
	  INDEX PARENT_IX (rp_parent),
	  INDEX MEMBER_IX (mb_srl),
	  INDEX IP_IX (mb_ipaddress))'.$_engine;

DB::query($create_sql);
if($error = DB::error()) throw new Exception($error->getMessage(),$error->getCode());

$_err_keys = _AF_PAGE_TABLE_;
$create_sql = '
	  CREATE TABLE IF NOT EXISTS '._AF_PAGE_TABLE_.' (
	   md_id           CHAR(11)     NOT NULL,
	   pg_type         CHAR(1)      NOT NULL DEFAULT 0,
	   pg_content      LONGTEXT     NOT NULL DEFAULT \'\',
	   pg_file         INT(11)      NOT NULL DEFAULT 0,
	   pg_reply        INT(11)      NOT NULL DEFAULT 0,
	   pg_regdate      datetime     NOT NULL DEFAULT \'0000-00-00 00:00:00\',
	   pg_update       datetime     NOT NULL DEFAULT \'0000-00-00 00:00:00\',
	   pg_extra        TEXT         NOT NULL DEFAULT \'\',

	  UNIQUE KEY ID_UK (md_id))'.$_engine;

DB::query($create_sql);
if($error = DB::error()) throw new Exception($error->getMessage(),$error->getCode());

$_err_keys = _AF_FILE_TABLE_;
$create_sql = '
	  CREATE TABLE IF NOT EXISTS '._AF_FILE_TABLE_.' (
	   mf_srl          INT(11)      NOT NULL AUTO_INCREMENT,
	   md_id           CHAR(11)     NOT NULL,
	   mf_target       INT(11)      NOT NULL,
	   mf_name         VARCHAR(255) NOT NULL,
	   mf_upload_name  VARCHAR(255) NOT NULL,
	   mf_type         VARCHAR(255) NOT NULL,
	   mf_size         INT(11)      NOT NULL,
	   mf_link         CHAR(1)      NOT NULL DEFAULT 0,
	   mf_download     INT(11)      NOT NULL DEFAULT 0,
	   mf_description  VARCHAR(255) NOT NULL DEFAULT \'\',
	   mb_srl          INT(11)      NOT NULL DEFAULT 0,
	   mb_ipaddress    VARCHAR(128) NOT NULL DEFAULT \'\',
	   mf_regdate      datetime     NOT NULL DEFAULT \'0000-00-00 00:00:00\',

	  CONSTRAINT SRL_PK PRIMARY KEY (mf_srl),
	  INDEX TARGET_IX (md_id, mf_target),
	  INDEX MEMBER_IX (mb_srl),
	  INDEX IP_IX (mb_ipaddress))'.$_engine;

DB::query($create_sql);
if($error = DB::error()) throw new Exception($error->getMessage(),$error->getCode());

$_err_keys = _AF_HISTORY_TABLE_;
$create_sql = '
	  CREATE TABLE IF NOT EXISTS '._AF_HISTORY_TABLE_.' (
	   mb_srl          INT(11)      NOT NULL DEFAULT 0,
	   mb_ipaddress    VARCHAR(128) NOT NULL,
	   hs_action       VARCHAR(255) NOT NULL,
	   hs_regdate      datetime     NOT NULL DEFAULT \'0000-00-00 00:00:00\',

	  INDEX MEMBER_IX (mb_srl),
	  INDEX IP_IX (mb_ipaddress),
	  INDEX ACTION_IX (hs_action),
	  INDEX REGDATE_IX (hs_regdate))'.$_engine;

DB::query($create_sql);
if($error = DB::error()) throw new Exception($error->getMessage(),$error->getCode());

$_err_keys = _AF_NOTE_TABLE_;
$create_sql = '
	  CREATE TABLE IF NOT EXISTS '._AF_NOTE_TABLE_.' (
	   nt_srl          INT(11)      NOT NULL AUTO_INCREMENT,
	   mb_srl          INT(11)      NOT NULL DEFAULT 0,
	   nt_sender       INT(11)      NOT NULL DEFAULT 0,
	   nt_sender_nick  VARCHAR(20)  NOT NULL DEFAULT \'\',
	   nt_send_date    datetime     NOT NULL DEFAULT \'0000-00-00 00:00:00\',
	   nt_read_date    datetime     NOT NULL DEFAULT \'0000-00-00 00:00:00\',
	   nt_content      TEXT         NOT NULL DEFAULT \'\',

	  CONSTRAINT SRL_PK PRIMARY KEY (nt_srl),
	  INDEX MEMBER_IX (mb_srl),
	  INDEX SENDER_IX (nt_sender))'.$_engine;

DB::query($create_sql);
if($error = DB::error()) throw new Exception($error->getMessage(),$error->getCode());

$_err_keys = _AF_VISITOR_TABLE_;
$create_sql = '
	  CREATE TABLE IF NOT EXISTS '._AF_VISITOR_TABLE_.' (
	   mb_ipaddress    VARCHAR(128) NOT NULL,
	   vs_agent        VARCHAR(255) NOT NULL,
	   vs_referer      VARCHAR(255) NOT NULL,
	   vs_regdate      datetime     NOT NULL DEFAULT \'0000-00-00 00:00:00\',

	  INDEX AGENT_IX (vs_agent),
	  INDEX REGDATE_IX (vs_regdate))'.$_engine;

DB::query($create_sql);
if($error = DB::error()) throw new Exception($error->getMessage(),$error->getCode());

$_err_keys = _AF_TRIGGER_TABLE_;
$create_sql = '
	  CREATE TABLE IF NOT EXISTS '._AF_TRIGGER_TABLE_.' (
	   tg_key         CHAR(1)      NOT NULL,
	   tg_id          VARCHAR(255) NOT NULL,
	   use_pc         CHAR(1)      NOT NULL DEFAULT 0,
	   use_mobile     CHAR(1)      NOT NULL DEFAULT 0,

	  INDEX PC_IX (use_pc),
	  INDEX MOBILE_IX (use_mobile))'.$_engine;

DB::query($create_sql);
if($error = DB::error()) throw new Exception($error->getMessage(),$error->getCode());

$_err_keys = 'insert_members';
$row = DB::get(_AF_MEMBER_TABLE_, 'mb_id', ['mb_id'=>'admin']);
if($error = DB::error()) throw new Exception($error->getMessage(),$error->getCode());
if (empty($row['mb_id'])) {
	$sql = 'INSERT INTO '._AF_MEMBER_TABLE_.' (`mb_rank`, `mb_id`, `mb_password`, `mb_nick`, `mb_regdate`) VALUES ("%s", "%s", "%s", "%s", NOW())';
	DB::query(sprintf($sql, 's', 'admin', password_hash('0000', PASSWORD_BCRYPT), '관리자'));
}

$_err_keys = 'insert_themes';
$row = DB::get(_AF_THEME_TABLE_, 'th_id', ['th_id'=>'default']);
if($error = DB::error()) throw new Exception($error->getMessage(),$error->getCode());
if (empty($row['th_id'])) {
	$tmp = [];
	$tmp['carousel_item_1'] = '<h1>헤드라인 예제</h1><p>이것은 헤드라인 예제입니다.<br>이 헤드라인은 (테마 설정)에서 사용자가 원하는 대로 작성하시면 됩니다.<br>에이폭스는 누구나 쉽고 편하고 자유롭게 콘텐츠를 발행을 할 수 있도록 하기 위한 CMS(Content Management System)입니다.</p><a class="btn btn-primary" href="#">오늘 가입</a>';
	$tmp['carousel_item_2'] = '<h1>두번째 헤드라인 예제</h1><p>에이폭스는 누구나 쉽고 편하고 자유롭게 콘텐츠를 발행을 할 수 있도록 하기 위한 CMS(Content Management System)입니다.<br>afox에 의해 디자인되고 만들어 졌으며 코드 기여자의 도움과 코어 팀에 의해 유지보수 됩니다.</p><a class="btn btn-primary" href="#">자세히 알아보기</a>';
	$tmp['carousel_item_3'] = '<h1>마지막으로 하나 더</h1><p>에이폭스는 각각의 기능과 디자인이 구조적으로 연결되는 모듈형 구조로 개발 및 유지보수를 쉽게 하도록 도와주며 관리자는 손쉽게 설정과 디자인을 변경할 수 있으며 여러분만의 개성을 가진 웹 사이트를 만들 수 있습니다.</p><a class="btn btn-primary" href="#">갤러리 검색</a>';
	$tmp['footer_html'] = '에이폭스는 <a href="http://afox.kr" target="_blank">@afox</a>에 의해 디자인되고 만들어 졌으며 <a href="https://github.com/phiDelPark/aFox/graphs/contributors">코드 기여자</a>의 도움과 <a href="https://github.com/phiDelPark?tab=people">코어 팀</a>에 의해 유지보수 됩니다.<br>코드는 <a rel="license" href="https://github.com/phiDelPark/aFox/blob/master/LICENSE" target="_blank">MIT</a>, 문서는 <a rel="license" href="https://creativecommons.org/licenses/by/3.0/" target="_blank">CC BY 3.0</a>에 의거하여 허가합니다.';
	$tmp = "'".str_replace(['\\',"\0","\n","\r","'",'"',"\x1a"],['\\\\','\\0','\\n','\\r',"\\'",'\\"','\\Z'],serialize($tmp))."'";
	$sql = 'INSERT INTO '._AF_THEME_TABLE_.' (`th_id`, `th_extra`) VALUES ("default", '.$tmp.')';
	DB::query($sql);
}

$_err_keys = 'insert_config';
$row = DB::get(_AF_CONFIG_TABLE_, 'theme', []);
if($error = DB::error()) throw new Exception($error->getMessage(),$error->getCode());
if (empty($row['theme'])) {
	$sql = 'INSERT INTO '._AF_CONFIG_TABLE_.' (`theme`, `start`, `title`, `use_signup`) VALUES ("default", "welcome", "에이폭스", "1")';
	DB::query($sql);
}

$_err_keys = 'insert_modules';
$row = DB::get(_AF_MODULE_TABLE_, 'md_id', ['md_id'=>'welcome']);
if($error = DB::error()) throw new Exception($error->getMessage(),$error->getCode());
if (empty($row['md_id'])) {
	$sql = 'INSERT INTO '._AF_MODULE_TABLE_.' (`md_id`, `md_key`, `md_title`, `md_regdate`) VALUES ("%s", "%s", "%s", NOW())';
	DB::query(sprintf($sql, 'welcome', 'page', ''));
}

$_err_keys = 'insert_pages';
$row = DB::get(_AF_PAGE_TABLE_, 'md_id', ['md_id'=>'welcome']);
if($error = DB::error()) throw new Exception($error->getMessage(),$error->getCode());
if (empty($row['md_id'])) {
	$doc_data = '';
	$fp = fopen(dirname(__FILE__) . '/../README.md',"r");
	while( !feof($fp) ) $doc_data .= fgets($fp);
	fclose($fp);
	$sql = 'INSERT INTO '._AF_PAGE_TABLE_.' (`md_id`, `pg_type`, `pg_content`, `pg_update`, `pg_regdate`) VALUES ("%s", "1", %s, NOW(), NOW())';
	DB::query(sprintf($sql, 'welcome', "'".str_replace(['\\',"\0","\n","\r","'",'"',"\x1a"],['\\\\','\\0','\\n','\\r',"\\'",'\\"','\\Z'],$doc_data)."'"));
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
fwrite($f, '<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">'."\n");
fwrite($f, '<script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.3/jquery.min.js"></script>'."\n");
fwrite($f, '<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>'."\n");
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
fwrite($f, "'domain'=>'{$domain}',\n");
fwrite($f, "'cookie_domain'=>'{$cookie_domain}'\n");
fwrite($f, ");");
fclose($f);
chmod($file, 0644);

$success_msg = "<br>설치 성공<br><br>관리자 아이디 : admin<br>관리자 비밀번호 : 0000<br><br>주의 : 관리자 로그인 후에 관리자 페이지에 접속 후 관리자 비밀번호를 바꿔주세요.<br><br>";

// 새로 설치가 아닐때를 대비 업데이트 체크
/*
$upbuild = 1;
function __AFOX__delete_updatefiles($dir) {
	@chmod($dir . 'update/1.php', 0777);
	@unlink($dir . 'update/1.php');
	@chmod($dir . 'update', 0777);
	@rmdir($dir . 'update');
	@chmod($dir . 'update.php', 0777);
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

$dir = dirname(__FILE__) . '/';
if(file_exists($dir . 'update.php')) {
	__AFOX__flush_msg("<br>설치를 마치고 업데이트를 체크하는중...<br>");
	mysqli_begin_transaction($link, MYSQLI_TRANS_START_READ_WRITE);
	try{
		for ($i=1; $i <= $upbuild; $i++) {
			include $dir . 'update/'.$i.'.php';
		}
		mysqli_commit($link);
		__AFOX__delete_updatefiles($dir);
	} catch (Exception $ex) {
		mysqli_rollback($link);
		echo $error_msg.'</body></html>';
		exit;
	} // finally {}
}

$file = $datadir.'config/_update.php';
@chmod($file, 0777);
@unlink($file);
$f = @fopen($file, 'w');
fwrite($f, "<?php\nif(!defined('__AFOX__')) exit();\n");
fwrite($f, "\$_UPBUILD={$upbuild};");
fclose($f);
chmod($file, 0644);
*/

echo $success_msg;

?>
</body></html>

<?php
/* End of file index.php */
/* Location: ./install/index.php */
