<?php
if(!defined('__AFOX__')) exit();

$_PROTECT['proc.deletefile']			= ['grant' => 's'];
$_PROTECT['proc.deletefiles']			= ['grant' => 's'];
$_PROTECT['proc.deletecomments']		= ['grant' => 's'];
$_PROTECT['proc.deletedocuments']		= ['grant' => 's'];

$_PROTECT['proc.deletethemeconfig']		= ['grant' => 's'];
$_PROTECT['proc.deleteaddonconfig']		= ['grant' => 's'];

$_PROTECT['proc.updatefile']			= ['grant' => 's'];
$_PROTECT['proc.updateaddon']		    = ['grant' => 's'];
$_PROTECT['proc.updatemenu']			= ['grant' => 's'];
$_PROTECT['proc.updatesetup']			= ['grant' => 's'];
$_PROTECT['proc.updatetheme']		    = ['grant' => 's'];
$_PROTECT['proc.selecttheme']	    	= ['grant' => 's'];

$_PROTECT['proc.combinefiles']			= ['grant' => 's'];
$_PROTECT['proc.movedocuments']			= ['grant' => 's'];
$_PROTECT['proc.emptytrashbin']		= ['grant' => 's'];

$_PROTECT['proc.getfile']				= ['grant' => 'm'];
$_PROTECT['proc.getfilelist']			= ['grant' => 'm'];
$_PROTECT['proc.clearcache']			= ['grant' => 'm'];


/* End of file protect.php */
/* Location: ./module/admin/protect.php */
