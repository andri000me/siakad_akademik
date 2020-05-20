<?php
// Author: Emanuel Setio Dewo
// 20 June 2006
// www.sisfokampus.net
include "../sisfokampus.php";
include_once "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";
include_once "parameter.php";

$nmf = HOME_FOLDER . DS . "tmp/$_SESSION[_Login].fak.dwoprn";
$f = fopen($nmf, 'w');

$MaxCol = 80;
$grs = str_pad('-', $MaxCol, '-')."\r\n";
fwrite($f, str_pad($arrID['Nama'], $MaxCol, ' ', STR_PAD_BOTH).$_lf); 
fwrite($f, str_pad("Daftar Fakultas & Program Studi", $MaxCol, ' ', STR_PAD_BOTH).$_lf);
fwrite($f, $grs);
// Ambil data
$s = "select p.*, f.Nama as NamaFak
  from prodi p
    left outer join fakultas f on p.FakultasID=f.FakultasID
  order by p.FakultasID";
$r = _query($s); $fak = '';
while ($w = _fetch_array($r)) {
  if ($fak != $w['NamaFak']) {
    $fak = $w['NamaFak'];
    fwrite($f, $_lf);
    fwrite($f, str_pad($w['NamaFak'], 50).$_lf);
    fwrite($f, $grs);
  }
  fwrite($f, '     ' . 
    str_pad($w['ProdiID'], 5) .
    str_pad($w['Nama'], 30).
    str_pad($w['NoSKDikti'], 20).
    $_lf);
}
fwrite($f, $grs); 
fclose($f);
include_once "dwoprn.php";
DownloadDWOPRN($nmf, 'fak');

include_once "disconnectdb.php";
?>
