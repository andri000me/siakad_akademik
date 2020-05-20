<?php

// *** Parameters ***
$arrSetupPMB = array('Gelombang PMB~gelombang',
  'Daftar Presenter~presenter',
  'Sumber Informasi~sumberinfo',
  'Prasyarat Formulir~prasyarat',
  'Formulir PMB~formulir',
  'Komponen USM~usm',
  'Prodi-USM~prodiusm',
  'Prodi-Wawancara~wawancarausm',
  'Status Awal~stawal',
  'PMB Grade~pmbgrade',
  'PMB Target~target');
$idxPMB = GetSetVar('idxPMB', 0);

// *** Main ***
TampilkanJudul("Setup PMB");
TampilkanMenuPMB($arrSetupPMB, $idxPMB);
if (empty($_REQUEST['gos'])) {
  $_gos = $arrSetupPMB[$idxPMB];
  $_gos1 = explode('~', $_gos);
  $gos = $_gos1[1];
}
else $gos = $_REQUEST['gos'];
include_once $_SESSION['mnux'].'.'.$gos.'.php';

// *** Functions ***
function TampilkanMenuPMB($arr, $idx) {
  $i = 0;
  echo "<table class=bsc cellspacing=1 align=center>";
  foreach ($arr as $a) {
    $_a = explode('~', $a);
    $sel = ($idx == $i)? 'class=menuaktif' : 'class=menuitem';
    echo "<td $sel><a href='?mnux=$_SESSION[mnux]&idxPMB=$i&gos=".$_a[1]."'>".$_a[0]."</a></td>";
    $i++;
  }
  echo "</table>";
}
?>
