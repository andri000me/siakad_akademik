<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 05 Agustus 2008

session_start();
include_once "../sisfokampus1.php";
include_once "../header_surat.php";

HeaderSisfoKampus("Kartu Peserta Test");

// *** Parameters ***
$id = sqling($_REQUEST['id']);
$w = GetFields('pmb', "KodeID='".KodeID."' and PMBID", $id, '*');
$arrID = GetFields('identitas', 'Kode', KodeID, '*');

CetakKartu($arrID, $w);

// *** Functions ***
function CetakKartu($arrID, $w) {
  BuatHeader($arrID);
}

?>
