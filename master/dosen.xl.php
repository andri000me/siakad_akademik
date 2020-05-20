<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 08/12/2008

session_start();

include_once "../dwo.lib.php";
include_once "../db.mysql.php";
include_once "../connectdb.php";
include_once "../parameter.php";
include_once "../cekparam.php";

// *** Main ***
ExportXL();

// *** Functions ***
function ExportXL() {
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
  $sheet->setColumn(0, 0, 5);  // Nomer
  $sheet->setColumn(1, 1, 15); // NIDN
  $sheet->setColumn(2, 2, 50); // Nama
  $sheet->setColumn(3, 4, 15); // Gelar & telepon

  // Buat header
  $i = 0;
  $sheet->writeString($i, 0, 'No.', $bold1);
  $sheet->writeString($i, 1, 'NIDN', $bold1);
  $sheet->writeString($i, 2, 'Nama', $bold1);
  $sheet->writeString($i, 3, 'Gelar', $bold1);
  $sheet->writeString($i, 4, 'Telepon', $bold1);
  
  // Ambil data
  $s = "select d.NIDN, d.Nama, d.Gelar, d.Telephone, p.Nama as _Homebase, d.Homebase
    from dosen d
      left outer join prodi p on p.ProdiID = d.Homebase and p.KodeID = '".KodeID."'
    where d.NA = 'N'
    order by d.Homebase, d.Nama";
  $r = _query($s); $n = 0;
  $_prd = 'laksjdhfiasd809ohjlkf';
  
  while ($w = _fetch_array($r)) {
    if ($_prd != $w['Homebase']) {
      $_prd = $w['Homebase'];
      $i++; $n = 0;
      $sheet->writeString($i, 0, 'Homebase: ' . $w['_Homebase'], $bold1);
    }
    $i++; $n++;
    $sheet->writeString($i, 0, $n, $norm);
    $sheet->writeString($i, 1, $w['NIDN'], $norm);
    $sheet->writeString($i, 2, $w['Nama'], $norm);
    $sheet->writeString($i, 3, $w['Gelar'], $norm);
    $sheet->writeString($i, 4, $w['Telephone'], $norm);
  }
  
  $xls->close();
}
?>
