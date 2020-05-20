<?php

session_start();
include_once "../sisfokampus1.php";
include_once "../$_SESSION[mnux].lib.php";

HeaderSisfoKampus("KRS Paket", 1);

// *** Parameters ***
$mhswid = ($_SESSION['_LevelID']==120)? $_SESSION['_Login'] : GetSetVar('mhswid');
$khsid = GetSetVar('khsid');
if ($_SESSION['_LevelID']==1 || $_SESSION['_LevelID']==40 || $_SESSION['_LevelID']==42 || $_SESSION['_LevelID']==20 || $_SESSION['_LevelID']==56) {
	$update = _query("UPDATE khs set SetujuPA='', KonfirmasiKRS='N' where MhswID='".$mhswid."' and KHSID='".$khsid."'");
}
TutupScript($mhswid, $khsid);

function TutupScript($mhswid, $khsid) {
echo <<<SCR
<SCRIPT>
  function ttutup() {
    opener.location='../index.php?mnux=$_SESSION[mnux]&gos=&mhswid=$mhswid&khsid=$khsid&tolak=1';
    self.close();
    return false;
  }
  ttutup();
</SCRIPT>
SCR;
}
?>
