<?php

session_start();
include_once "../db.mysql.php";
include_once "../connectdb.php";
include_once "../dwo.lib.php";
include_once "../parameter.php";
require_once 'Spreadsheet/Excel/Writer.php';

// *** Parameters ***
$_PMBPeriodID = GetSetVar('_PMBPeriodID');
$gels = GetFields('pmbperiod', 'PMBPeriodID', $_PMBPeriodID, '*');

// *** Buat file XL ***
$fn = 'tmp/'.$_SESSION['_Login'].'_pmbasalsekolah.xls';

$xls = new Spreadsheet_Excel_Writer();
$xls->send("$_SESSION[_Login]_pmbasalsekolah.xls");

$sh =& $xls->addWorksheet();
$sh->setPaper(9);
$sh->setLandscape();

// Format/Style
  $ttl =& $xls->addFormat();
  $ttl->setAlign('left');
  $ttl->setBold();
  $ttl->setSize(12);
  
  $bold =& $xls->addFormat();
  $bold->setAlign('left');
  $bold->setBold();
  $bold->setSize(10);
  $bold->setTop(1);
  $bold->setBottom(1);
  $bold->setRight(1);
  $bold->setLeft(1);
  
  $norm =& $xls->addFormat();
  $norm->setAlign('left');
  $norm->setSize(10);
  $norm->setTop(1);
  $norm->setBottom(1);
  $norm->setRight(1);
  $norm->setLeft(1);

  $hdr =& $xls->addFormat();
  $hdr->setBold();
  $hdr->setAlign('left');
  $hdr->setSize(10);
  $hdr->setTop(1);
  $hdr->setBottom(2);
  $hdr->setRight(1);
  $hdr->setLeft(1);

// Lebar kolom
$sh->setColumn(0, 0, 4); // Nomer
$sh->setColumn(1, 1, 26); // Nama
$sh->setColumn(2, 2, 10); // Status
$sh->setColumn(3, 3, 16); // Program
$sh->setColumn(4, 6, 20); // Pilihan 1-3

// Header
//$sh->insertBitmap(0, 0, "../img/logo.png", 0, 0, 1, 1);
$sh->write(0, 0, "Daftar Calon Mahasiswa Berdasarkan Asal Sekolah", $bold);
$sh->write(1, 0, $gels['Nama'], $bold);
$brs = 2; 
$sh->write($brs, 0, 'No', $bold);
$sh->write($brs, 1, 'Nama', $bold);
$sh->write($brs, 2, 'Status', $bold);
$sh->write($brs, 3, 'Program', $bold);
$sh->write($brs, 4, 'Pilihan1', $bold);
$sh->write($brs, 5, 'Pilihan2', $bold);
$sh->write($brs, 6, 'Pilihan3', $bold);
$sh->write($brs, 7, 'Nilai', $bold);

$brs++;

  // Ambil Data
  $s = "select p.PMBID, p.Nama, p.AsalSekolah, p.NilaiSekolah,
    sta.Nama as STAWAL, p.ProgramID,
    p1.Nama as Pil1, p2.Nama as Pil2, p3.Nama as Pil3
    from pmb p
      left outer join statusawal sta on p.StatusAwalID = sta.StatusAwalID
      left outer join prodi p1 on p1.ProdiID = p.Pilihan1
      left outer join prodi p2 on p2.ProdiID = p.Pilihan2
      left outer join prodi p3 on p3.ProdiID = p.Pilihan3
    where p.PMBPeriodID = '$_PMBPeriodID'
    order by p.AsalSekolah, p.Nama";
  $r = _query($s);
  $n = 0; $asal = 'woilkkdhfsdjhlasjhpirja';
  $cnt = 0;
  while ($w = _fetch_array($r)) {
    $cnt++;
    if ($asal != $w['AsalSekolah']) {
      $asal = $w['AsalSekolah'];
      $sh->write($brs, 0, $asal, $ttl);
      $brs++;
      $n = 0;
    }
    $n++;
    $sh->write($brs, 0, $n, $norm);
    $sh->write($brs, 1, $w['Nama'], $norm);
    $sh->write($brs, 2, $w['STAWAL'], $norm);
    $sh->write($brs, 3, $w['ProgramID'], $norm);
    $sh->write($brs, 4, $w['Pil1'], $norm);
    $sh->write($brs, 5, $w['Pil2'], $norm);
    $sh->write($brs, 6, $w['Pil3'], $norm);
    $sh->write($brs, 7, $w['NilaiSekolah'], $norm);
    $brs++;
  }

$brs++;
$sh->write($brs, 0, "Jumlah: ".$cnt);


$sh->hideScreenGridlines();
$xls->close();
?>
