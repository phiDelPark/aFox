<?php
if(!defined('__AFOX__')) exit();

$_PROTECT['setup']				= ['grant' => 's'];
$_PROTECT['proc.updatesetup']	= ['grant' => 's'];
$_PROTECT['proc.deletefiles']	= ['grant' => 's'];
$_PROTECT['proc.modifyfiles']	= ['grant' => 's'];

$_PROTECT['proc.getfiles']	    = ['grant' => '0'];
$_PROTECT['proc.updategallery']	= ['grant' => '0'];

$_PROTECT['disp.list']			= ['grant' => '0'];
$_PROTECT['disp.view']			= ['grant' => '0'];
$_PROTECT['disp.write']			= ['grant' => '0'];
$_PROTECT['disp.delete']		= ['grant' => '0'];

/* End of file protect.php */
/* Location: ./module/gallery/protect.php */
