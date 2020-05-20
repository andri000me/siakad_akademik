<?php

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Presensi Mahasiswa", 1);

// *** Parameters ***
$id = $_REQUEST['id']+0;
$st = $_REQUEST['st'];
$nilai = GetaField('jenispresensi', 'JenisPresensiID', $st, 'Nilai')+0;

$pm = GetFields('presensimhsw', 'PresensiMhswID', $id, '*');
// Update presensinya
$s = "update presensimhsw set JenisPresensiID = '$st', Nilai = $nilai
  where PresensiMhswID = '$id' ";
$r = _query($s);
// Hitung & update ke KRS
$jml = GetaField('presensimhsw', 'KRSID', $pm['KRSID'], "sum(Nilai)")+0;
// Update KRS
$s = "update krs
  set _Presensi = $jml
  where KRSID = $pm[KRSID]";
$r = _query($s);
?>
