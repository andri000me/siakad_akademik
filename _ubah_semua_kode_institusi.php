<?php
// Start  : 08/01/2009

session_start();
include_once "sisfokampus.php";
HeaderSisfoKampus("Ubah Semua ID Institusi");

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'fnKonfirmasi' : $_REQUEST['gos'];
$gos();

// *** Functions ***
function fnKonfirmasi() {
  echo Konfirmasi("Konfirmasi",
    "Anda akan mengubah semua id data institusi
    <hr size=1 color=silver />
    <input type=button name='btnProses' value='Proses'
    onClick=\"location='?gos=fnProses'\" />");
}
function fnProses() {
  $tablesArray = array('alumni', 'bayarmhsw', 'bipot', 'bipotmhsw', 'dosen', 'fakultas', 'gradeipk', 'honordosen',
						'jadwal', 'jenismk', 'kampus', 'karyawan', 'khs', 'konsentrasi', 'krs', 'maxsks', 'mhsw', 'mk', 'mkpaket',
						'nilai', 'pejabat', 'pmb', 'pmbformjual', 'pmbformulir', 'pmbformsyarat', 'pmbgrade', 'pmbperiod', 'pmbsyarat', 
						'pmbweb', 'praktekkerja',
						'presenter', 'prodi', 'prodiusm', 'program', 'rekening', 'ruang', 'statusmhsw', 'sumberinfo',
						'ta', 'tabimbingan', 'tahun', 'wawancara', 'wawancarausm', 'wisuda', 'wisudawan');
  foreach($tablesArray as $table)
  {
    $s1 = "update $table set KodeID='".KodeID."'";
    $r1 = _query($s1);
    $jml = _affected_rows($r1);
    echo "<li>Table $table, diproses: $jml</li>";
  }
  echo "</ol>";
  echo "<font size=+1>Selesai.</font>";
}

?>
