<?php

session_start();
include_once "../db.mysql.php";
include_once "../connectdb.php";
include_once "../dwo.lib.php";
include_once "../parameter.php";
require_once 'Spreadsheet/Excel/Writer.php';

// *** Parameters ***
$fn = $_REQUEST['fn'];
$fn_text = "../$fn.txt";
if (file_exists($fn_text) === false) die("File $fn tidak ditemukan");

$f = fopen($fn_text, 'r');
$isi = fread($f, filesize($fn_text));
fclose($f);
$_isi = explode("\n", $isi);

// *** Buat file XL ***
$xls = new Spreadsheet_Excel_Writer();
$xls->send("$fn.xls");

$sh =& $xls->addWorksheet();
$sh->setPaper(9);

$sh->setColumn(0, 0, 14); // MhswID
$sh->setColumn(1, 1, 30); // Nama
$sh->setColumn(2, 2, 60); // Pesan

  $hdr =& $xls->addFormat();
  $hdr->setBold();
  $hdr->setAlign('left');
  $hdr->setSize(10);
  $hdr->setTop(1);
  $hdr->setBottom(2);
  $hdr->setRight(1);
  $hdr->setLeft(1);

$i = 0;
foreach($_isi as $brs) {
  $i++;
  $brs = TRIM($brs);
  $_b = explode('|', $brs);
  $sh->writeString($i, 0, $_b[0]);
  $sh->writeString($i, 1, $_b[1]);
  $sh->writeString($i, 2, $_b[2]);
  if ($i == 2) {
    $i++;
    $sh->writeString($i, 0, 'NIM', $hdr);
    $sh->write($i, 1, 'Mahasiswa', $hdr);
    $sh->write($i, 2, 'Catatan', $hdr);
  }
}


$sh->hideScreenGridlines();
$xls->close();
?>
