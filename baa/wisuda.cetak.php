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

// *** Init PDF
$pdf = new PDF();
$pdf->SetTitle("Daftar Wisudawan - $TahunID");
$pdf->AddPage();
$lbr = 190;

BuatHeadernya($pdf);
BuatIsinya($pdf);

$pdf->Output();

// *** Functions ***
function BuatHeadernya($p) {
  $gel = GetFields('wisuda', "KodeID='".KodeID."' and TahunID", $_SESSION['TahunID'], 
    "*, date_format(TglWisuda, '%d-%m-%Y') as _TglWisuda");
  $t = 6;
  $p->SetFont('Helvetica', 'B', 14);
  $p->Cell($lbr, $t, "Daftar Wisudawan " . $gel['Nama'], 0, 1, 'C');
  $p->SetFont('Helvetica', '', 12);
  $p->Cell($lbr, $t, "Tanggal Wisuda: " . $gel['_TglWisuda'], 0, 1, 'C');
  $p->Ln(4);
}
function BuatIsinya($p) {
  global $lbr;
  $s = "select w.MhswID, w.Judul, w.Predikat, w.Prasyarat, 
    m.IPK, m.ProdiID, m.Nama as NamaMhsw
    from wisudawan w
      left outer join mhsw m on w.MhswID = m.MhswID and m.KodeID = '".KodeID."'
    where w.KodeID = '".KodeID."'
      and w.TahunID = '$_SESSION[TahunID]'
    order by m.ProdiID, w.MhswID";
  $r = _query($s); $prd = ';alskdjf;asd';
  $t = 6;
  while ($w = _fetch_array($r)) {
    if ($prd != $w['ProdiID']) {
      $prd = $w['ProdiID'];
      $NamaProdi = GetaField('prodi', "KodeID='".KodeID."' and ProdiID", $prd, 'Nama');
      $p->SetFont('Helvetica', 'B', 10);
      $p->Cell($lbr, $t, $NamaProdi, 'B', 1);
      BikinHeadernya($p);
    }
    $p->SetFont('Helvetica', '', 9);
    $p->Cell(25, $t, $w['MhswID'], 'B', 0);
    $p->Cell(55, $t, $w['NamaMhsw'], 'B', 0);
    //$p->Cell(12, $t, $w['IPK'], 'B', 0, 'R');
    $p->Cell(30, $t, $w['Predikat'], 'B', 0);
    $p->Cell(68, $t, $w['Prasyarat'], 'B', 0);
    $p->Ln($t);
  }
}
function BikinHeadernya($p) {
  $t = 6;
  $p->SetFont('Helvetica', 'BI', 8);
  $p->Cell(25, $t, 'NPM', 'B', 0);
  $p->Cell(55, $t, 'Nama Mahasiswa', 'B', 0);
  //$p->Cell(12, $t, 'IPK', 'B', 0, 'R');
  $p->Cell(30, $t, 'Predikat', 'B', 0);
  $p->Cell(68, $t, 'Prasyarat', 'B', 0);
  $p->Ln($t);
}
?>
