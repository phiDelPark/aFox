<?php
if(!defined('__AFOX__')) exit();

// 정규식 패턴
define('_AF_PATTERN_ID_', '^[a-zA-Z]+\w{2,}$');
define('_AF_PATTERN_CATEGORY_', '^[^\x{00}-\x{19}\x{21}-\x{2b}\x{2d}\x{2f}\x{3a}-\x{40}\x{5b}-\x{60}\x{7b}-\x{a0}]+$');
define('_AF_PATTERN_EXTRAKEY_', '^[^\x{00}-\x{19}\x{21}-\x{25}\x{27}-\x{29}\x{2b}\x{2d}\x{2f}\x{3a}-\x{40}\x{5b}-\x{60}\x{7b}\x{7d}-\x{a0}]+$');

/* End of file patterns.php */
/* Location: ./module/board/patterns.php */