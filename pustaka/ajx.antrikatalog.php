<?php 
session_start();
	/* 	Author	: Arisal Yanuarafi
		Start	: 17 Agustus 2014  */
if (isset($_REQUEST['BukuID'])) {
$_SESSION['_antrianKatalogID'] = ($_SESSION['_antrianKatalogID'] == '' ? $_REQUEST['BukuID'] : $_SESSION['_antrianKatalogID'].'~'.$_REQUEST['BukuID']);
}

?>