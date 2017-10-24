<?php
define('__AFOX__', TRUE);
define('_AF_EDITOR_PATH_', str_replace('\\', '/', realpath(dirname(__FILE__))) . '/');
define('_AF_EDITOR_NAME_', strtoupper($_GET['k']));
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
<link href="../../../common/css/bootstrap.min.css" rel="stylesheet">
<script src="../../../common/js/jquery.min.js" id="def-jQuery-JS"></script>
<script src="../../../common/js/bootstrap.min.js" id="def-Bootstrap-JS"></script>
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
