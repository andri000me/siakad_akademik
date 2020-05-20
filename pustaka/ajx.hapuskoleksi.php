<?php 
session_start();
	/* 	Author	: Arisal Yanuarafi
		Start	: 17 Agustus 2014  */
	include_once "../dwo.lib.php";
  	include_once "../db.mysql.php";
  	include_once "../connectdb.php";
  	include_once "../parameter.php";
  	include_once "../cekparam.php";
$item_id = $_REQUEST['item_id'];
$biblio_id = $_REQUEST['biblio_id'];
if ($item_id > 0) {
	$s = "DELETE from app_pustaka1.item where item_id='$item_id'";
	$r = _query($s);
}
if ($biblio_id > 0) {
	$s = "DELETE from app_pustaka1.item where biblio_id='$biblio_id'";
	$r = _query($s);
}
exit();