<?php

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
$lbr = 190;

BuatIsinya($TahunID, $ProdiID, $pdf);

$pdf->Output();

// *** Functions ***
function BuatIsinya($TahunID, $ProdiID, $p) {
  $whr_prodi = ($ProdiID == '')? '' : "and h.ProdiID = '$ProdiID' ";
  $s = "select h.MhswID, m.Nama, h.ProdiID,
      h.Biaya, h.Potongan, h.Tarik, h.Bayar,
      format(h.Biaya, 0) as _Biaya,
      format(h.Potongan, 0) as _Potongan,
      format(h.Tarik, 0) as _Tarik,
      format(h.Bayar, 0) as _Bayar,
      (h.Biaya + h.Tarik - h.Potongan - h.Bayar) as TTL, 
      format((h.Biaya + h.Tarik - h.Potongan - h.Bayar), 0) as _TTL
    from khs h
      left outer join mhsw m on m.MhswID = h.MhswID and m.KodeID = '".KodeID."'
    where h.TahunID = '$TahunID'
      $whr_prodi
    order by h.ProdiID, h.MhswID";
  $r = _query($s); $n = 0;
  $t = 5; $ttl = 0; $_prd = ';alskdjfa;lsdhguairgsofjhjg9e8rgjpsofjg';
  
  while ($w = _fetch_array($r)) {
    if ($_prd != $w['ProdiID']) {
      if ($n > 0) {
        $_ttl = number_format($ttl+0);
        $p->SetFont('Helvetica', 'B', 10);
        $p->Cell($lbr, 1,  ' ', 1, 1);
        $p->Cell(158, $t, 'TOTAL :', 0, 0, 'R');
        $p->Cell(32, $t, $_ttl, 0, 0, 'R');
        $p->Ln($t+2);
      }
      $_prd = $w['ProdiID'];
      BuatHeaderTable($TahunID, $_prd, $p);
      $ttl = 0; $n = 0;
    }
    $n++;
    $ttl += $w['TTL'];
    $p->SetFont('Helvetica', '', 8);
    $p->Cell(8, $t, $n, 1, 0);
    $p->Cell(24, $t, $w['MhswID'], 1, 0);
    $p->Cell(58, $t, $w['Nama'], 1, 0);
    $p->Cell(20, $t, $w['_Biaya'], 1, 0, 'R');
    $p->Cell(20, $t, $w['_Potongan'], 1, 0, 'R');
	$p->Cell(20, $t, $w['_Bayar'], 1, 0, 'R');
    $p->Cell(20, $t, $w['_Tarik'], 1, 0, 'R');
    $p->Cell(20, $t, $w['_TTL'], 1, 0, 'R');
    $p->Ln($t); 
  }
  $_ttl = number_format($ttl+0);
  $p->SetFont('Helvetica', 'B', 10);
  $p->Cell($lbr, 1,  ' ', 1, 1);
  $p->Cell(158, $t, 'TOTAL :', 0, 0, 'R');
  $p->Cell(32, $t, $_ttl, 0, 0, 'R'); 
  $p->Ln($t+2);
}
function BuatHeadertable($TahunID, $ProdiID, $p) {
  global $lbr;
  $t = 5;
  $prd = GetaField('prodi', "ProdiID = '$ProdiID' and KodeID", KodeID, 'Nama');
  $p->AddPage();
  $p->SetFont('Helvetica', 'B', 14);
  $p->Cell($lbr, $t, "Rekapitulasi Biaya & Pembayaran Mahasiswa -- $TahunID", 0, 1, 'C');
  $p->Cell($lbr, $t, "Program Studi: $prd", 0, 1, 'C');
  $p->Ln(4);
  
  $p->SetFont('Helvetica', 'BI', 10);
  $p->Cell(8, $t, 'Nmr', 1, 0);
  $p->Cell(24, $t, 'N I M', 1, 0);
  $p->Cell(58, $t, 'Nama Mhsw', 1, 0);
  $p->Cell(20, $t, 'Biaya', 1, 0, 'R');
  $p->Cell(20, $t, 'Potongan', 1, 0, 'R');
  $p->Cell(20, $t, 'Bayar', 1, 0, 'R');
  $p->Cell(20, $t, 'Tarikan', 1, 0, 'R');
  $p->Cell(20, $t, 'Total', 1, 0, 'R');
  
  $p->Ln($t);
}
?>
