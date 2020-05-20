<?php

session_start();
include_once "../sisfokampus1.php";
include_once "../$_SESSION[mnux].lib.php";

HeaderSisfoKampus("KRS Paket", 1);

// *** Parameters ***
$mhswid = $_SESSION['_Login'];
$khsid = GetSetVar('khsid');
if ($_SESSION['_LevelID']==120 && !empty($mhswid)) {
	$update = _query("UPDATE khs set KonfirmasiKRS='Y', SetujuPA='' where MhswID='".$mhswid."' and KHSID='".$khsid."'");
}
TutupScript($mhswid, $khsid);

function TutupScript($mhswid, $khsid) {
echo <<<SCR
<SCRIPT>
  function ttutup() {
    opener.location='../index.php?mnux=$_SESSION[mnux]&gos=&mhswid=$mhswid&khsid=$khsid';
    self.close();
    return false;
  }
  ttutup();
</SCRIPT>
SCR;
}
?>
