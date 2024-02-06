<?php
if(!defined('__AFOX__')) exit();

$_PROTECT['proc.deleteboard']		= ['grant' => 's'];
$_PROTECT['proc.updateboard']		= ['grant' => 's'];
$_PROTECT['proc.updateconfig']		= ['grant' => 'm'];

$_PROTECT['proc.updateyes']		    = ['grant' => '1'];
$_PROTECT['proc.updateno']		    = ['grant' => '1'];
$_PROTECT['proc.restoredocument']	= ['grant' => '1'];

$_PROTECT['proc.checkpassword']		= ['grant' => '0'];
$_PROTECT['proc.deletecomment']		= ['grant' => '0'];
$_PROTECT['proc.deletedocument']	= ['grant' => '0'];
$_PROTECT['proc.updatecomment']		= ['grant' => '0'];
$_PROTECT['proc.updatedocument']	= ['grant' => '0'];

$_PROTECT['proc.getcomment']		= ['grant' => '0'];
$_PROTECT['proc.getdocument']		= ['grant' => '0'];
$_PROTECT['proc.getfilelist']		= ['grant' => '0'];

$_PROTECT['disp.list']		        = ['grant' => '0'];
$_PROTECT['disp.view']		        = ['grant' => '0'];
$_PROTECT['disp.write']		        = ['grant' => '0'];
$_PROTECT['disp.delete']	        = ['grant' => '0'];

/* End of file protect.php */
/* Location: ./module/board/protect.php */
