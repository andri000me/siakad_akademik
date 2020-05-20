<?php
// *** Parameters ***
$arrMatri = array(
	'Setup Mata Uji~setupmatauji',
	'Kelompok Orientasi~entry',
	'Presensi Orientasi~presensi',
	'Nilai Orientasi~nilai'
);
$idxMatri = GetSetVar('idxMatri', 0);

// Tampilkan
TampilkanJudul("Orientasi Pengenalan Kampus");
TampilkanMenuMatri($arrMatri, $idxMatri);
if (empty($_REQUEST['gos'])) {
  $_gos = $arrMatri[$idxMatri];
  $_gos1 = explode('~', $_gos);
  $gos = $_gos1[1];
}
else $gos = $_REQUEST['gos'];
include_once $_SESSION['mnux'].'.'.$gos.'.php';

// *** Functions ***
function TampilkanMenuMatri($arr, $idx) {
  $i = 0;
  echo "<table class=bsc cellspacing=1 align=center>";
  foreach ($arr as $a) {
    $_a = explode('~', $a);
    $sel = ($idx == $i)? 'class=menuaktif' : 'class=menuitem';
    echo "<td $sel><a href='?mnux=$_SESSION[mnux]&idxMatri=$i&gos=".$_a[1]."'>".$_a[0]."</a></td>";
    $i++;
  }
  echo "</table>";
}
?>
