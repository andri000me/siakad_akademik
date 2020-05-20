<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 28 Agustus 2008

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Presensi Remedial Mahasiswa", 1);

// *** Parameters ***
$id = $_REQUEST['id']+0;
$st = $_REQUEST['st'];
$nilai = GetaField('jenispresensi', 'JenisPresensiID', $st, 'Nilai')+0;

$pm = GetFields('presensiremedialmhsw', 'PresensiRemedialMhswID', $id, '*');
// Update presensinya
$s = "update presensiremedialmhsw set JenisPresensiID = '$st', Nilai = $nilai
  where PresensiRemedialMhswID = '$id' ";
$r = _query($s);
// Hitung & update ke KRS
$jml = GetaField('presensiremedialmhsw', 'KRSRemedialID', $pm['KRSRemedialID'], "sum(Nilai)")+0;
// Update KRS
$s = "update krsremedial
  set _Presensi = $jml
  where KRSRemedialID = $pm[KRSRemedialID]";
$r = _query($s);
?>
<script>window.close()</script>
