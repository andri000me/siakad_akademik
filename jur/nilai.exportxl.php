<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 18 Sept 2008

session_start();

include_once "../dwo.lib.php";
include_once "../db.mysql.php";
include_once "../connectdb.php";
include_once "../parameter.php";
include_once "../cekparam.php";

// *** Parameters ***
$TahunID = sqling($_REQUEST['TahunID']);
$ProdiID = sqling($_REQUEST['ProdiID']);

// *** Main ***
ExportXL($TahunID, $ProdiID);

include_once "../disconnectdb.php";

// *** Functions ***
function ExportXL($TahunID, $ProdiID) {
  include_once "Spreadsheet/Excel/Writer.php";
  $xls =& new Spreadsheet_Excel_Writer();
  $xls->send("nilai-$TahunID-$ProdiID-$_SESSION[_Login].xls");
  $sheet =& $xls->addWorksheet("$TahunID-$ProdiID");
  $sheet->hideGridlines();
  $sheet->hideScreenGridlines();
  
  $bold =& $xls->addFormat();
  $bold->setAlign('center');
  $bold->setBold();
  $bold->setSize(11);
  $bold->setTop(1);
  $bold->setBottom(1);
  $bold->setRight(1);
  $bold->setLeft(1);
  
  $bold1 =& $xls->addFormat();
  $bold1->setAlign('left');
  $bold1->setBold();
  $bold1->setSize(10);
  
  $bold2 =& $xls->addFormat();
  $bold2->setAlign('left');
  $bold2->setSize(3);
  
  $mchs =& $xls->addFormat();
  $mchs->setAlign('left');
  $mchs->setSize(12);
  $mchs->setBold();
  
  $norm =& $xls->addFormat();
  $norm->setAlign('left');
  $norm->setSize(10);
  $norm->setTop(1);
  $norm->setBottom(1);
  
  // Lebar kolom
  $sheet->setColumn(0, 0, 5);
  $sheet->setColumn(1, 2, 8);
  $sheet->setColumn(3, 4, 15);
  $sheet->setColumn(5, 5, 5);

  // Buat header
  $i = 0;
  $sheet->writeString($i, 0, 'No.', $bold1);
  $sheet->writeString($i, 1, 'Tahun', $bold1);
  $sheet->writeString($i, 2, 'Prodi', $bold1);
  $sheet->writeString($i, 3, 'MK', $bold1);
  $sheet->writeString($i, 4, 'NIM', $bold1);
  $sheet->writeString($i, 5, 'Grade', $bold1);
  
  // Ambil data
  $s = "select k.TahunID, k.MhswID, k.MKKode, h.ProdiID, 
    k.NilaiAkhir, k.GradeNilai, k.BobotNilai
    from krs k
      left outer join khs h on h.KHSID = k.KHSID
    where k.TahunID = '$TahunID'
      and h.ProdiID = '$ProdiID'
    order by k.MKKode, k.MhswID";
  $r = _query($s); $n = 0; 
  while ($w = _fetch_array($r)) {
    $n++; $i++;
    $sheet->writeString($i, 0, $n, $norm);
    $sheet->writeString($i, 1, $w['TahunID'], $norm);
    $sheet->writeString($i, 2, $w['ProdiID'], $norm);
    $sheet->writeString($i, 3, $w['MKKode'], $norm);
    $sheet->writeString($i, 4, $w['MhswID'], $norm);
    $sheet->writeString($i, 5, $w['GradeNilai'], $norm);
  }
  
  $xls->close();
}
?>
