<?php
// Author: Emanuel Setio Dewo
// 01-11-06

session_start();
include "../sisfokampus.php";
include "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";
include_once "parameter.php";
include_once "terbilang.php";

$BPMID = $_REQUEST['BPMID'];
$bpm = GetFields('bayarmhsw', "BayarMhswID", $BPMID, "*");
if (!empty($bpm)) {
  CetakBPM($bpm);
}
else echo ErrorMsg("BPM Tidak Ditemukan",
  "BPM dengan nomer <font size=+1>$BPMID</font> tidak ditemukan.
  <hr size=1 color=silver>
  Pilihan: <input type=button name='Tutup' Value='Tutup' onClick=\"window.close()\">");

function CetakBPM($bpm) {
  global $_lf;
  $NamaBank = GetaField('rekening', 'RekeningID', $bpm['RekeningID'], 'Nama');
  $NamaMhsw = GetaField('mhsw', 'MhswID', $bpm['MhswID'], 'Nama');
  // Buat cetakannya
  $mrg = str_pad(' ', 8, ' ');
  $isi = $_lf;
  // header
  $mrg = str_pad(' ', 8, ' ');
  $isi .= str_pad($bpm['BayarMhswID'], 50, ' ', STR_PAD_LEFT).$_lf.$_lf.$_lf.$_lf.$_lf;
  $isi .= $mrg.$mrg . $bpm['RekeningID'] . $_lf.$_lf;
  $isi .= $mrg.$mrg . $bpm['TahunID'] . $_lf;
  $isi .= $mrg.$mrg . $bpm['MhswID'] . ' / ' . $NamaMhsw . $_lf.$_lf;
  $isi .= $mrg.$mrg . $NamaBank . $_lf.$_lf.$_lf.$_lf;
  // cetak di posisi ke-10
  for ($i = 0; $i <= 9; $i++) $isi .= $_lf . $_lf;
  $ket = substr($bpm['Keterangan'], 0, 20);
  $jml = number_format($bpm['JumlahLain']);
  $isi .= $mrg. str_pad(' ', 10).
    str_pad($ket, 20).
    str_pad($jml, 15, ' ', STR_PAD_LEFT). $_lf.$_lf;
  $isi .= $mrg. str_pad(' ', 30). str_pad($jml, 15, ' ', STR_PAD_LEFT);
  // footer
  $isi .= $mrg . $isi_.$_lf;
  $isi .= $_lf.$_lf.$_lf;
  $tgl = date('d-m-Y');
  $isi .= $mrg. "Dicetak oleh: $_SESSION[_Login], $tgl".$_lf;
  $isi .= $_lf.$_lf.$_lf.$_lf.$_lf;
  
  // Buat Tujuan
  $nmf = HOME_FOLDER  .  DS . "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, chr(27).chr(18).chr(27).chr(67).chr(60));
  fwrite($f, $isi);
  fwrite($f, chr(12));
  fclose($f);
  TampilkanFileDWOPRN($nmf);
}

include_once "disconnectdb.php";
?>
