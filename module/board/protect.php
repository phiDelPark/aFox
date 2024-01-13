<?php
if(!defined('__AFOX__')) exit();

$_PROTECT['proc.deleteboard']		= ['grant' => 's'];
$_PROTECT['proc.updateboard']		= ['grant' => 's'];

$_PROTECT['disp.documentlist']		= ['grant' => '0'];
$_PROTECT['disp.viewdocument']		= ['grant' => '0'];
$_PROTECT['disp.writedocument']		= ['grant' => '0'];
$_PROTECT['disp.deletedocument']	= ['grant' => '0'];

$_PROTECT['proc.checkpassword']		= ['grant' => '0'];
$_PROTECT['proc.deletecomment']		= ['grant' => '0'];
$_PROTECT['proc.deletedocument']	= ['grant' => '0'];
$_PROTECT['proc.updatecomment']		= ['grant' => '0'];
$_PROTECT['proc.updatedocument']	= ['grant' => '0'];
$_PROTECT['proc.updateconfig']		= ['grant' => 'm'];
$_PROTECT['proc.restoredocument']	= ['grant' => '1'];

$_PROTECT['proc.getcomment']		= ['grant' => '0'];
$_PROTECT['proc.getdocument']		= ['grant' => '0'];

$_PROTECT['proc.updategood']		= ['grant' => '1'];
$_PROTECT['proc.updatehate']		= ['grant' => '1'];

/* End of file protect.php */
/* Location: ./module/board/protect.php */
