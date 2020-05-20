<?php
session_start();

  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
  include_once "../header_pdf.php";

// *** Parameters ***
$gel = $_REQUEST['gel'];
$gels = GetFields('pmbperiod', "KodeID='".KodeID."' and PMBPeriodID", $gel, "*");

$lbr = 190;

// *** Cetak ***
$pdf = new PDF('P', 'mm', 'A4');
$pdf->SetTitle("Cama Per Program Pendidikan");
$pdf->AddPage('P');

BuatHeaderLap($gel, $gels, $pdf);
TampilkanIsinya($gel, $gels, $pdf);

$pdf->Output();

// *** Functions ***
function TampilkanHeader($p) {
  $t = 6;
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell(10, $t, 'No.', 1, 0);
  $p->Cell(20, $t, 'No. PMB', 1, 0);
  $p->Cell(55, $t, 'Nama Cama', 1, 0);
  $p->Cell(105, $t, 'Pilihan Program Studi I, II, dan III', 1, 1);
}
function TampilkanIsinya($gel, $gels, $p) {
  $s = "select p.PMBID, p.Nama, p.ProgramID,
		p1.ProdiID as _Pilihan1, p2.ProdiID as _Pilihan2, p3.Nama as _Pilihan3
    from pmb p
		left outer join prodi p1 on p1.ProdiID=p.Pilihan1
		left outer join prodi p2 on p2.ProdiID=p.Pilihan2
		left outer join prodi p3 on p3.ProdiID=p.Pilihan3
    where p.KodeID = '".KodeID."'
      and p.PMBPeriodID = '$gel'
    order by p.ProgramID, p.Nama";
  $r = _query($s);
  $n = 0; $t = 6;

  $ProgramID = ';laskdjf;laskdjf';
  while ($w = _fetch_array($r)) {
    if ($ProgramID != $w['ProgramID']) {
      $ProgramID = $w['ProgramID'];
      $p->Ln(2);
      $p->SetFont('Helvetica', 'B', 10);
      $p->Cell(185, $t, $w['ProgramID'], 0, 1);
      TampilkanHeader($p);
    }
    $n++;
    $p->SetFont('Helvetica', '', 9);
    $p->Cell(10, $t, $n, 'LB', 0);
    $p->Cell(20, $t, $w['PMBID'], 'B', 0);
    $p->Cell(55, $t, $w['Nama'], 'B', 0);
    $p->Cell(35, $t, $w['_Pilihan1'], 1, 0);
	$p->Cell(35, $t, $w['_Pilihan2'], 1, 0);
	$p->Cell(35, $t, $w['_Pilihan3'], 1, 0);
    $p->Ln($t);
  }
}
function BuatHeaderLap($gel, $gels, $p) {
  global $lbr;
  $p->SetFont('Helvetica', 'B', 14);
  $p->Cell($lbr, 8, "Daftar Calon Mahasiswa Per Asal Program Pendidikan", 0, 1, 'C');
  $p->SetFont('Helvetica', 'B', 12);
  $p->Cell($lbr, 6, $gels['Nama'], 0, 1, 'C');
}

?>
