<?php define('__AFOX__', TRUE);
define('_AF_EDITOR_PATH_', str_replace('\\', '/', realpath(dirname(__FILE__))) . '/');
define('_AF_EDITOR_NAME_', empty($_GET['k']) ? NULL : strtoupper($_GET['k']));
define('_AF_PATH_', str_replace('module/editor/','', _AF_EDITOR_PATH_));
function _get_afox_url(){
  $result = preg_replace('/^\/\~[^\/]+(.*)$/', '$1', $_SERVER['SCRIPT_NAME']);
  $http = 'http' . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on') ? 's' : '') . '://';
  $port=($_SERVER['SERVER_PORT']==80||$_SERVER['SERVER_PORT']==443)?'':':'.$_SERVER['SERVER_PORT'];
  $host = str_replace($port,'',isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME']);
  return $http.$host.$port.preg_replace('/module\/editor\/component.php$/', '', $result);
}
define('_AF_URL_', _get_afox_url());
define('_AF_ADDONS_PATH_', _AF_PATH_ . 'addon/');
$_ADDON_INFO = [];
require_once _AF_ADDONS_PATH_ . $_GET['n'] . '/info.php';
?>
<!doctype html>
<html lang="ko"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta http-equiv="imagetoolbar" content="no"><meta http-equiv="X-UA-Compatible" content="IE=10,chrome=1">
<title><?php echo $_ADDON_INFO['title'] ?></title>
<style>body{background-color:lightgray}</style>
<script>const bootstrap=[];/* Avoiding errors caused by bootstrap deactivation */</script>
<script src="<?php echo _AF_URL_?>common/js/common.min.js"></script>
</head><body>
<?php require_once _AF_ADDONS_PATH_ . $_GET['n'] . '/editor.php';?>
</body></html>
<?php
/* End of file component.php */
/* Location: ./module/editor/component.php */
