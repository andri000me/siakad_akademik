<?php 
session_start();
	/* 	Author	: Arisal Yanuarafi
		Start	: 17 Agustus 2014  */
if (isset($_REQUEST['BukuID'])) {
$_SESSION['_antrianCetakID'] = ($_SESSION['_antrianCetakID'] == '' ? $_REQUEST['BukuID'] : $_SESSION['_antrianCetakID'].'~'.$_REQUEST['BukuID']);
}
if (isset($_REQUEST['AnggotaID'])) {
$_SESSION['_antrianAnggotaCetakID'] = ($_SESSION['_antrianAnggotaCetakID'] == '' ? $_REQUEST['AnggotaID'] : $_SESSION['_antrianAnggotaCetakID'].'~'.$_REQUEST['AnggotaID']);
}

?>