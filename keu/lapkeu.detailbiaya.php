<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 17 Oktober 2008

session_start();

include_once "../dwo.lib.php";
include_once "../db.mysql.php";
include_once "../connectdb.php";
include_once "../parameter.php";
include_once "../cekparam.php";
include_once "../header_pdf.php";

// *** Parameters ***
$TahunID = GetSetVar('TahunID');
$ProdiID = GetSetVar('ProdiID');

// *** Init PDF
$pdf = new PDF();
$pdf->SetTitle("Rekap Biaya Mahasiswa");
$pdf->AddPage();
$lbr = 190;

BuatIsinya($TahunID, $ProdiID, $pdf);

$pdf->Output();

// *** Functions ***
function BuatIsinya($TahunID, $ProdiID, $p) {
  $whr_prodi = ($ProdiID == '')? '' : "m.ProdiID = '$ProdiID' ";
  $s = "select bm.MhswID, bm.MhswID, bm.TahunID,
    bm.BIPOT2ID, bm.BIPOTNamaID, bm.Nama, bm.TrxID,
    bm.Jumlah, bm.Besar, (bm.Jumlah * bm.Besar) as AMT,
    format(bm.Jumlah, 0) as _JML,
    format(bm.Besar, 0) as _BSR,
    format(bm.Jumlah * bm.Besar, 0) as _AMT,
    m.Nama as NamaMhsw
    
    from bipotmhsw bm
      left outer join mhsw m on m.MhswID = bm.MhswID and m.KodeID = '".KodeID."'
    where bm.NA = 'N'
      and bm.PMBMhswID = 1
      and bm.TahunID = '$TahunID'
      $whr_prodi
    order by m.ProdiID, bm.MhswID";
  $r = _query($s); $n = 0;
  $t = 5;
  
  $p->SetFont('Helvetica', '', 10);
  while ($w = _fetch_array($r)) {
    $n++;
    $p->Cell(10, $t, $n, 'B', 0);
    $p->Cell(40, $t, $w['Nama'], 'B', 0);
    $p->Cell(20, $t, $w['_JML'], 'B', 0, 'R');
    $p->Cell(20, $t, $w['_BSR'], 'B', 0, 'R');
    $p->Cell(25, $t, $w['_AMT'], 'B', 0, 'R');
    
    $p->Ln($t); 
  }
  
}
?>
