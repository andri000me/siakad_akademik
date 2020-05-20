<?php
session_start();
	/* 	Author	: Arisal Yanuarafi
		Start	: 8 Sept 2013  */
	
	include_once "../../dwo.lib.php";
  	include_once "../../db.mysql.php";
  	include_once "../../connectdb.php";
  	include_once "../../parameter.php";
  	include_once "../../cekparam.php";


$UnsurID = sqling($_GET['kat']);
$URL = $_SERVER['REQUEST_URI'];$URL = substr($URL, 0, 11);  
if (!empty($UnsurID) && $URL != '/keskul/ajx') {
	$optunsur = GetOption2('penelitian_subkategori', "concat(Nama)", 'Subkat_ID', "$UnsurID",
    "NA='N' and Telkat_ID='$UnsurID'", "Subkat_ID");	
	
	echo $optunsur;
         

}
?>