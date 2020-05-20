<?php

function Def() {
  global $mnux, $pref, $token;
  include_once "$mnux.$_SESSION[$pref].php";
  $sub = (!empty($_REQUEST['sub']))? $_REQUEST['sub'] : "Def$_SESSION[$pref]";
  $sub();
}

// *** Parameters ***
$pref = 'mk';
$arrMK = array(
  'Kurikulum->Kur', 
  'Konsentrasi->Kons',
  'Jenis Mata Kuliah->Jen', 
  'Pilihan Wajib->Pil',
  'Jenis Kurikulum->JenKur',
  'Mata Kuliah->MK',
  'Mata Kuliah Setara->MKSet',
  'Nilai->Nil',
  'MaxSKS->MaxSKS',
  'Kehadiran SKS->HadirSKS', 
  'Paket Matakuliah->Pkt',
  "Predikat->pred");
$tokendef = 'MK';
$mnux = $_SESSION['mnux'];
$token = GetSetVar($pref, $tokendef);
$prodi = GetSetVar('prodi');
$kurid = GetSetVar("kurid_$prodi");
$mkkode = GetSetVar("mkkode_$prodi");
if (empty($kurid) && !empty($prodi)) {
  $_kurid = GetaField("kurikulum", "NA='N' and ProdiID", $prodi, "KurikulumID");
  $_SESSION["kurid_$prodi"] = $_kurid;
  $kurid = $_kurid;
}

// *** Main ***
TampilkanJudul("Administrasi Mata Kuliah");
if (empty($_SESSION['_ProdiID'])) echo ErrorMsg('Tidak Ada Hak Akses',
  "Anda tidak memiliki hak akses terhadap modul ini.<br>
  Hubungi Superuser/Administrator untuk memberikan hak akses terhadap program studi.");
else {
  TampilkanSubMenu($_SESSION['mnux'], $arrMK, $pref, $token);
  if (!empty($token)) Def();
}
?>
