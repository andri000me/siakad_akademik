<?php

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Presensi Mahasiswa", 1);

// *** Parameters ***
$pid = $_REQUEST['pid']+0;

Tutup();

function Tutup() {
echo <<<SCR
<SCRIPT>
  function ttutup() {
    window.location='..$_GET[bck]';
    return false;
  }
  ttutup();
</SCRIPT>
SCR;
}
?>