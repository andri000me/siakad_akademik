<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 24 Agustus 2008

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
$pdf->SetTitle("Cama Per Nilai Sekolah");
$pdf->AddPage('P');

BuatHeaderLap($gel, $gels, $pdf);
$pdf->Ln(2);
TampilkanIsinya($gel, $gels, $pdf);

$pdf->Output();

// *** Functions ***
function TampilkanHeader($p) {
  $t = 6;
  $p->SetFont('Helvetica', 'B', 9, 'C');
  $p->Cell(12, $t, 'No.', 1, 0, 'C');
  $p->Cell(17, $t, 'Nilai UAN', 1, 0, 'C');
  $p->Cell(20, $t, 'No. PMB', 1, 0, 'C');
  $p->Cell(50, $t, 'Nama Cama', 1, 0, 'C');
  $p->Cell(50, $t, 'Program Studi', 1, 0, 'C');
  $p->Cell(30, $t, 'Prg Pendidikan', 1, 1, 'C');
  //$p->Cell(10, $t, 'USM', 1, 1, 'R');
}
function TampilkanIsinya($gel, $gels, $p) {
  $s = "select p.PMBID, p.Nama, p.AsalSekolah, NilaiSekolah,
    p.KotaOrtu, prd.Nama as _PRD, prg.Nama as _PRG
    from pmb p
      left outer join prodi prd on prd.ProdiID = p.Pilihan1 and prd.KodeID = '".KodeID."'
      left outer join program prg on prg.ProgramID = p.ProgramID and prg.KodeID = '".KodeID."'
    where p.KodeID = '".KodeID."'
      and p.PMBPeriodID = '$gel'
    order by p.NilaiSekolah desc, p.Nama ";
  $r = _query($s);
  $n = 0; $t = 6;

  TampilkanHeader($p);
  while ($w = _fetch_array($r)) {
    $n++;
    $p->SetFont('Helvetica', '', 9);
    $p->Cell(12, $t, $n, 'LB', 0, 'C');
    $p->Cell(17, $t, $w['NilaiSekolah'], 'B', 0, 'C');
    $p->Cell(20, $t, $w['PMBID'], 'B', 0, 'C');
    $p->Cell(50, $t, $w['Nama'], 'B', 0, 'L');
    $p->Cell(50, $t, $w['_PRD'], 'B', 0, 'C');
    $p->Cell(30, $t, $w['_PRG'], 'BR', 0, 'C');
  //  $p->Cell(10, $t, $w['NilaiUjian'], 'BR', 0, 'R');
    $p->Ln($t);
  }
}
function BuatHeaderLap($gel, $gels, $p) {
  global $lbr;
  $p->SetFont('Helvetica', 'B', 14);
  $p->Cell($lbr, 8, "Daftar Calon Mahasiswa Berdasarkan Nilai UAN", 0, 1, 'C');
  $p->SetFont('Helvetica', 'B', 12);
  $p->Cell($lbr, 6, $gels['Nama'], 0, 1, 'C');
}

?>
