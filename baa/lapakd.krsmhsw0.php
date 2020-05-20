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
$pdf->SetTitle("Daftar Mahasiswa Yang Mengambil KRS");
$lbr = 190;

//BuatHeadernya($TahunID, $ProdiID, $sta, $pdf);
BuatIsinya($TahunID, $ProdiID, $pdf);

$pdf->Output();

// *** FUnctions ***
function BuatIsinya($TahunID, $ProdiID, $p) {
  $whr_prodi = (empty($ProdiID))? '' : "and h.ProdiID = '$ProdiID' ";
  $s = "select h.*,
      m.Nama as NamaMhsw,
      d.Nama as NamaPA, d.Gelar
    from khs h
      left outer join mhsw m on m.MhswID = h.MhswID and m.KodeID = '".KodeID."'
      left outer join dosen d on d.Login = m.PenasehatAkademik and d.KodeID = '".KodeID."'
    where h.KodeID = '".KodeID."'
      and h.TahunID = '$TahunID'
      and h.SKS = 0
      $whr_prodi
    order by h.ProdiID, h.MhswID";
  $r = _query($s);
  
  $n = 0; $t = 5; $_prd = 'laksdjfalksdfh';
  while ($w = _fetch_array($r)) {
    if ($_prd != $w['ProdiID']) {
      $_prd = $w['ProdiID'];
      $NamaProdi = GetaField('prodi', "KodeID='".KodeID."' and ProdiID", $_prd, 'Nama');
      $p->AddPage();
      BuatHeader($TahunID, $NamaProdi, $p);
    }
    $n++;
    $NamaPA = (empty($w['NamaPA']))? '(Belum diset)' : $w['NamaPA'];
    $p->SetFont('Helvetica', '', 10);
    $p->Cell(11, $t, $n, 'LB', 0); 
    $p->Cell(27, $t, $w['MhswID'], 'B', 0);
    $p->Cell(60, $t, $w['NamaMhsw'], 'B', 0);
    $p->Cell(10, $t, $w['Sesi'], 'B', 0, 'R');
    $p->Cell(10, $t, $w['SKS'], 'B', 0, 'R');
    $p->Cell(10, $t, $w['MaxSKS'], 'B', 0, 'R');
    $p->Cell(60, $t, $NamaPA, 'BR', 0);
    $p->Ln($t);
  }
}
function BuatHeader($TahunID, $NamaProdi, $p) {
  global $lbr;
  $t = 6;
  $p->SetFont('Helvetica', 'B', 14);
  $p->Cell($lbr, $t, "Daftar Mahasiswa Yang Belum Mengambil KRS - $TahunID", 0, 1, 'C');
  $p->Cell($lbr, $t, "Program Studi: $NamaProdi", 0, 1, 'C');
  $p->Ln($t+2);
  // Header tabel
  $p->SetFont('Helvetica', 'B', 10);
  $p->Cell(11, $t, 'Nmr', 1, 0);
  $p->Cell(27, $t, 'N P M', 1, 0);
  $p->Cell(60, $t, 'Nama Mahasiswa', 1, 0);
  $p->Cell(10, $t, 'Smtr', 1, 0);
  $p->Cell(10, $t, 'SKS', 1, 0);
  $p->Cell(10, $t, 'Max', 1, 0);
  $p->Cell(60, $t, 'Penasehat Akd.', 1, 0);
  $p->Ln($t);
}
?>
