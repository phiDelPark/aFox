<?php
define('__AFOX__', TRUE);
define('_AF_EDITOR_PATH_', str_replace('\\', '/', realpath(dirname(__FILE__))) . '/');
define('_AF_EDITOR_NAME_', strtoupper($_GET['k']));

function _get_afox_url(){
  $result = preg_replace('/^\/\~[^\/]+(.*)$/', '$1', $_SERVER['SCRIPT_NAME']);
  $http = 'http' . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on') ? 's' : '') . '://';
  $port=($_SERVER['SERVER_PORT']==80||$_SERVER['SERVER_PORT']==443)?'':':'.$_SERVER['SERVER_PORT'];
  $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
  return $http.$host.$port.preg_replace('/module\/editor\/component.php$/', '/', $result);
}
define('_AF_URL_', _get_afox_url());

$_COMPONENT_INFO = [];
require_once _AF_EDITOR_PATH_ . 'components/' . $_GET['n'] . '/info.php';
?>

<!doctype html>
<html lang="ko">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="imagetoolbar" content="no">
<meta http-equiv="X-UA-Compatible" content="IE=10,chrome=1">
<title><?php echo $_COMPONENT_INFO['title'] ?></title>
<link href="<?php echo _AF_URL_ ?>common/css/bootstrap.min.css" rel="stylesheet">
<script src="<?php echo _AF_URL_ ?>common/js/jquery.min.js"></script>
<script src="<?php echo _AF_URL_ ?>common/js/bootstrap.min.js"></script>
<script src="<?php echo _AF_URL_ ?>common/js/common.min.js"></script>
</head>
<body>
<?php
require_once _AF_EDITOR_PATH_ . 'components/' . $_GET['n'] . '/index.php';
?>
</body>
</html>
<?php
/* End of file component.php */
/* Location: ./module/editor/component.php */
