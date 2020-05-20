<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 23 September 2008

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
    k.IP, m.ProdiID, m.Nama as NamaMhsw, m.Kota
    from wisudawan w
      left outer join mhsw m on w.MhswID = m.MhswID and m.KodeID = '".KodeID."'
	  left outer join khs k on k.MhswID = m.MhswID
    where w.KodeID = '".KodeID."'
      and w.TahunID = '$_SESSION[TahunID]'
	  and k.TahunID = '$_SESSION[TahunID]'
    order by m.Kota, m.ProdiID, w.MhswID";
  $r = _query($s); $prd = ';alskdjf;asd'; $kota = 'al;skjdfalsdhfa';
  $t = 6;
  $p->setFillColor(230, 230, 0);
  while ($w = _fetch_array($r)) {
    if ($kota != $w['Kota']) {
      $kota = $w['Kota'];
      $p->Ln($t);
      $p->SetFont('Helvetica', 'BI', 12);
      $p->Cell($lbr, $t, "Kota/Kabupaten: $kota", 'B', 1, 'C');
      $prd = 'a;lsdkjfa;sdflkj';
    }
    if ($prd != $w['ProdiID']) {
      $prd = $w['ProdiID'];
      $NamaProdi = GetaField('prodi', "KodeID='".KodeID."' and ProdiID", $prd, 'Nama');
      $p->SetFont('Helvetica', 'B', 10);
      $p->Cell($lbr, $t, $NamaProdi, 'B', 1, '', true);
      BikinHeadernya($p);
    }
    $p->SetFont('Helvetica', '', 9);
    $p->Cell(20, $t, $w['MhswID'], 'B', 0);
    $p->Cell(60, $t, $w['NamaMhsw'], 'B', 0);
    $p->Cell(12, $t, $w['IP'], 'B', 0, 'R');
    $p->Cell(30, $t, $w['Predikat'], 'B', 0);
    $p->Cell(68, $t, $w['Prasyarat'], 'B', 0);
    $p->Ln($t);
  }
}
function BikinHeadernya($p) {
  $t = 6;
  $p->SetFont('Helvetica', 'BI', 8);
  $p->Cell(20, $t, 'NIM', 'B', 0);
  $p->Cell(60, $t, 'Nama Mahasiswa', 'B', 0);
  $p->Cell(12, $t, 'IPK', 'B', 0, 'R');
  $p->Cell(30, $t, 'Predikat', 'B', 0);
  $p->Cell(68, $t, 'Prasyarat', 'B', 0);
  $p->Ln($t);
}
?>
