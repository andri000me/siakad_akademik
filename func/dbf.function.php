<?php

include "../class/dbf.class.php";

function DBFCreate($NamaDBF, $Header){
	$dbf = new DBFConnection($NamaDBF);
	$ret = $dbf->DBFCreate($Header);
	return $ret;
}

function InsertDataDBF($NamaDBF, $Data){
	$dbf = new DBFConnection($NamaDBF);
	$dbf->DBFOpen();
	$dbf->DBFAddRecord($Data);
	$dbf->DBFClose();
}

?>
