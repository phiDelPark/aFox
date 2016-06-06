<?php
if(!defined('__AFOX__')) exit();
echo "<br>";

__AFOX__flush_msg("ao_use_pc > use_pc<br>");
$r = mysqli_query($link, 'SHOW COLUMNS FROM '._AF_ADDON_TABLE_.' LIKE \'use_pc\'');
if(mysqli_errno($link)) throw new Exception(mysqli_error($link), mysqli_errno($link));
if(empty(mysqli_num_rows($r))) {
	mysqli_query($link, 'ALTER TABLE '._AF_ADDON_TABLE_.' CHANGE ao_use_pc use_pc CHAR(1) NOT NULL DEFAULT 0');
	if(mysqli_errno($link)) throw new Exception(mysqli_error($link), mysqli_errno($link));
}

__AFOX__flush_msg("ao_use_mobile > use_mobile<br>");
$r = mysqli_query($link, 'SHOW COLUMNS FROM '._AF_ADDON_TABLE_.' LIKE \'use_mobile\'');
if(mysqli_errno($link)) throw new Exception(mysqli_error($link), mysqli_errno($link));
if(empty(mysqli_num_rows($r))) {
	mysqli_query($link, 'ALTER TABLE '._AF_ADDON_TABLE_.' CHANGE ao_use_mobile use_mobile CHAR(1) NOT NULL DEFAULT 0');
	if(mysqli_errno($link)) throw new Exception(mysqli_error($link), mysqli_errno($link));
}