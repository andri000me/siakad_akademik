<?php

session_start();
include_once "../db.mysql.php";
include_once "../connectdb.php";
include_once "../dwo.lib.php";
include_once "../parameter.php";
require_once 'Spreadsheet/Excel/Writer.php';

// *** Parameters ***
$gel = sqling($_REQUEST['gel']);

// *** Buat file XL ***
$fn = 'tmp/'.$_SESSION['_Login'].'_pmb.xls';

$xls = new Spreadsheet_Excel_Writer();
$xls->send("$_SESSION[_Login]_pmb.xls");

$sh =& $xls->addWorksheet();
$sh->setPaper(9);
$sh->setLandscape();
$sh->freezePanes(array(3, 0));
$sh->freezePanes(array(0, 3));

// Format/Style
  $ttl =& $xls->addFormat();
  $ttl->setAlign('left');
  $ttl->setBold();
  $ttl->setSize(12);
  
  $bold =& $xls->addFormat();
  $bold->setAlign('center');
  $bold->setBold();
  $bold->setSize(11);
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
$sh->setColumn(1, 1, 10); // PMB
$sh->setColumn(2, 2, 28); // Nama
$sh->setColumn(3, 3, 4); // P/W
$sh->setColumn(4, 5, 20); // sekolah & telp
$sh->setColumn(6, 6, 12); // formulir
$sh->setColumn(7, 7, 10); // status
$sh->setColumn(8, 11, 24); // pilihan
$sh->setColumn(12, 12, 30); // Detail nilai
$sh->setColumn(13, 14, 8);
$sh->setColumn(15, 15, 24);

// Header
$sh->write(0, 0, "Penerimaan Mahasiswa Baru - $gel", $ttl);

$sh->write(2, 0, 'No.', $hdr);
$sh->write(2, 1, 'PMB ID', $hdr);
$sh->write(2, 2, 'Nama', $hdr);
$sh->write(2, 3, 'P/W', $hdr);
$sh->write(2, 4, 'Asal Sekolah', $hdr);
$sh->write(2, 5, 'Telp/Handphone', $hdr);
$sh->write(2, 6, 'Formulir', $hdr);
$sh->write(2, 7, 'Status', $hdr);
$sh->write(2, 8, 'Pilihan1', $hdr);
$sh->write(2, 9, 'Pilihan2', $hdr);
$sh->write(2, 10, 'Pilihan3', $hdr);
$sh->write(2, 11, 'Program', $hdr);
$sh->write(2, 12, 'Detail Nilai', $hdr);
$sh->write(2, 13, 'Nilai Akhir', $hdr);
$sh->write(2, 14, 'Lulus?', $hdr);
$sh->write(2, 15, 'Diterima di Prodi:', $hdr);

// Ambil data
  $s = "select pmb.*, replace(pmb.DetailNilai, '~', ', ') as DTL,
      _p1.Nama as P1, _p2.Nama as P2, _p3.Nama as P3, _prd.Nama as PRD
    from pmb
      left outer join prodi _p1 on pmb.Pilihan1 = _p1.ProdiID
      left outer join prodi _p2 on pmb.Pilihan2 = _p2.ProdiID
      left outer join prodi _p3 on pmb.Pilihan3 = _p3.ProdiID
      left outer join prodi _prd on pmb.ProdiID = _prd.ProdiID
    where pmb.KodeID = '".KodeID."' and pmb.PMBPeriodID = '$gel' 
    order by pmb.PMBID";
  $r = _query($s);
  $n = 0; $brs = 3;
  while ($w = _fetch_array($r)) {
    $n++;
    $sh->write($brs, 0, $n, $norm);
    $sh->write($brs, 1, $w['PMBID'], $norm);
    $sh->write($brs, 2, $w['Nama'], $norm);
    $sh->write($brs, 3, $w['Kelamin'], $norm);
    $sh->write($brs, 4, $w['AsalSekolah'], $norm);
    $sh->write($brs, 5, $w['Telepon'].'/'.$w['Handphone'], $norm);
    $sh->write($brs, 6, $w['FRM'], $norm);
    $sh->write($brs, 7, $w['STAWAL'], $norm);
    $sh->write($brs, 8, $w['P1'], $norm);
    $sh->write($brs, 9, $w['P2'], $norm);
    $sh->write($brs, 10, $w['P3'], $norm);
    $sh->write($brs, 11, $w['PRG'], $norm);
    $sh->write($brs, 12, $w['DTL'], $norm);
    $sh->write($brs, 13, $w['NilaiUjian'], $norm);
    $sh->write($brs, 14, $w['LulusUjian'], $norm);
    $sh->write($brs, 15, $w['PRD'], $norm);
    $brs++;
  }

$sh->hideScreenGridlines();
$xls->close();
?>
