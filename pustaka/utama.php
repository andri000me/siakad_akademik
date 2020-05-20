<?php

// *** Parameters ***
$arrPustaka = array('Anggota~anggota',
  'Bibliografi~bibliografi',
  'Cetak Katalog~katalog',
  'Barcode Buku~barcode',
  'Barcode Anggota~barcode.anggota',
  'Sirkulasi~sirkulasi',
  'Laporan~laporan',
  'Setup~setup'
);
$idxPustaka = GetSetVar('idxPustaka', 0);

// *** Main ***
TampilkanJudul("Perpustakaan");
TampilkanMenuPustaka($arrPustaka, $idxPustaka);
if (empty($_REQUEST['gos'])) {
  $_gos = $arrPustaka[$idxPustaka];
  $_gos1 = explode('~', $_gos);
  $gos = $_gos1[1];
}
else $gos = $_REQUEST['gos'];
include_once $_SESSION['mnux'].'.'.$gos.'.php';

// *** Functions ***
function TampilkanMenuPustaka($arr, $idx) {
  $i = 0;
  echo "<table class=bsc cellspacing=1 align=center>";
  foreach ($arr as $a) {
    $_a = explode('~', $a);
    $sel = ($idx == $i)? 'class=menuaktif' : 'class=menuitem';
    echo "<td $sel><a href='?mnux=$_SESSION[mnux]&idxPustaka=$i&gos=".$_a[1]."'>".$_a[0]."</a></td>";
    $i++;
  }
  echo "</table>";
}
?>
