<?php
// Author: Emanuel Setio Dewo
// www.sisfokampus.net
// 20 June 2006
include "../sisfokampus.php";
include_once "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";
include_once "parameter.php";

$KampusID = $_REQUEST['KampusID'];
$kamp = GetFields('kampus', 'KampusID', $KampusID, '*');

$nmf = "tmp\$_SESSION[_Login].ruang.dwoprn";
$f = fopen($nmf, 'w');

$MaxCol = 90;
$grs = str_pad('-', $MaxCol, '-')."\r\n";
fwrite($f, str_pad($arrID['Nama'], $MaxCol, ' ', STR_PAD_BOTH).$_lf); 
fwrite($f, str_pad("Daftar Ruangan di Kampus:", $MaxCol, ' ', STR_PAD_BOTH).$_lf);
fwrite($f, str_pad($kamp['Nama'], $MaxCol, ' ', STR_PAD_BOTH).$_lf);
fwrite($f, $grs);
// header
$hdr = "Nomer ".
  str_pad('Kode', 10).
  str_pad('Nama', 30).
  str_pad('Prodi', 30).
  "Kul? Kaps \r\n". $grs;
fwrite($f, $hdr);
// Ambil data
$s = "select r.*
  from ruang r
  where KampusID=$KampusID";
$r = _query($s); $brs = 0;
while ($w = _fetch_array($r)) {
  $brs++;
  $prd = TRIM($w['ProdiID'], '.');
  $prd = str_replace('.', ',', $prd);
  fwrite($f, str_pad($brs, 6).
    str_pad($w['RuangID'], 10).
    str_pad($w['Nama'], 30).
    str_pad($prd, 30).
    str_pad($w['RuangKuliah'], 4, ' ', STR_PAD_BOTH). ' '.
    str_pad($w['Kapasitas'], 4, ' ', STR_PAD_RIGHT) . $_lf);
}
fwrite($f, $grs); 
fclose($f);
include_once "dwoprn.php";
DownloadDWOPRN($nmf, 'fak');

include_once "disconnectdb.php";

?>
