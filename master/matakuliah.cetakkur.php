<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 20 Oktober 2008

session_start();

  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
  include_once "../header_pdf.php";

// *** Parameters ***
$ProdiID = GetSetVar('p');

$pdf = new PDF();
$pdf->SetTitle("Matakuliah");
$pdf->AddPage();
$pdf->SetFont('Helvetica', 'B', 14);

CetakHeadernya($ProdiID, $pdf);
CetakMatakuliahnya($ProdiID, $pdf);

$pdf->Output();

// *** Functions ***
function CetakHeadernya($ProdiID, $p) {
  $lbr = 190; $t = 6;
  if(empty($ProdiID)) $_prd = "(SEMUA)";
  else $_prd = GetaField('prodi', "ProdiID='$kur[ProdiID]' and KodeID", KodeID, 'Nama');
  $p->SetFont('Helvetica', 'B', 14);
  $p->Cell($lbr, $t, "Daftar Kurikulum Program Studi: $_prd", 0, 1, 'C');
  $p->Ln(3);
}
function CetakMatakuliahnya($ProdiID, $p) {
  $whr_prodi = (empty($ProdiID))? "" : "and ProdiID='$ProdiID'";
  $s = "select k.*
    from kurikulum k
    where k.KodeID='".KodeID."'
		$whr_prodi
	order by k.ProdiID, k.Nama";
  $r = _query($s);
  
  $n = 0; $t = 6; $ss = -25;
  
  while ($w = _fetch_array($r)) {
    if ($ss != $w['ProdiID']) { 
      $ss = $w['ProdiID'];
      $p->SetFont('Helvetica', 'B', 10);
      $p->Ln(2);
      $p->Cell(190, $t+2, $w['ProdiID'], 1, 1);
      BuatHeaderTabel($p);
    }
    $n++;
    
    $p->SetFont('Helvetica', '', 10);
    $p->Cell(10, $t, $n, 1, 0);
    $p->Cell(30, $t, $w['KurikulumKode'], 1, 0);
    $p->Cell(120, $t, $w['Nama'], 1, 0);
    $p->Cell(30, $t, $w['Sesi'], 1, 0, 'C');
    
    $p->Ln($t);
  }
}
function BuatHeaderTabel($p) {
  $t = 5;
  $p->SetFont('Helvetica', 'BI', 10);
  $p->Cell(10, $t, 'Nmr.', 1, 0);
  $p->Cell(30, $t, 'Kode Kurikulum', 1, 0);
  $p->Cell(120, $t, 'Nama Kurikulum', 1, 0);
  $p->Cell(30, $t, 'Jenis Sesi', 1, 0, 'C');
  $p->Ln($t);
}
?>
