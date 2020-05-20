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
$pdf->SetTitle("Rekapitulasi Jumlah Pendaftar Per Periode");
$pdf->AddPage('P');

BuatHeaderLap($gel, $gels, $pdf);
TampilkanIsinya($gel, $gels, $pdf);

$pdf->Output();

// *** Functions ***
function BuatHeaderLap($gel, $gels, $p) {
  global $lbr;
  $t = 6;
  $p->SetFont('Helvetica', 'B', 14);
  $p->Cell($lbr, $t, "Rekapitulasi Jumlah Pendaftar Per Periode", 0, 1, 'C');
  $p->SetFont('Helvetica', 'B', 12);
  $p->Cell($lbr, $t, "Sampai Dengan Periode $gel Reguler", 0, 1, 'C');
  $p->Ln(4);
}
function TampilkanIsinya($gel, $gels, $p) {
  $t = 6; $lebar = 20;
  $arrJumlah = array();
  // Ambil Prodinya
  GetArrayProdi($arrProdiID, $arrProdi);
  GetArrayPeriode($arrPeriode, $gel);
  BuatHeaderTabel($arrPeriode, $lebar, $p);
  
  for ($i = 0; $i < sizeof($arrProdiID); $i++) {
    $p->SetFont('Helvetica', '', 10);
    $p->Cell(30, $t, $arrProdiID[$i], 'B', 0);
    $p->Cell(80, $t, $arrProdi[$i], 'B', 0);
    AmbilJumlah($arrProdiID[$i], $arrPeriode, $t, $lebar, $p, $arrJumlah);
    $p->Ln($t);
  }
  BuatTotal($arrPeriode, $arrJumlah, $lebar, $p);
}
function BuatTotal($arrPeriode, $arrJumlah, $lebar, $p) {
  $t = 6;
  $p->SetFont('Helvetica', 'BI', 11);
  $p->Cell(130, $t, 'Total :', '', 0, 'R');
  foreach ($arrPeriode as $per) {
    $p->Cell($lebar, $t, $arrJumlah[$per], '', 0, 'R');
  }
}
function AmbilJumlah($ProdiID, $arrPeriode, $t, $lebar, $p, &$arrJumlah) {
  foreach ($arrPeriode as $per) {
    $jml = GetaField('aplikan',
      "ProdiID='$ProdiID' and PMBPeriodID='$per' AND ProgramID='R' and PMBFormulirID !='' and KodeID", KodeID,
      "count(AplikanID)")+0;
    $arrJumlah[$per] += $jml;
    $p->Cell($lebar, $t, $jml, 'B', 0, 'R');
  }
}
function BuatHeaderTabel($arrPeriode, $lebar, $p) {
  $t = 7;
  $p->SetFont('Helvetica', 'BI', 10);
  $p->Cell(120, $t, "Program Studi", 'BT', 0);
  foreach ($arrPeriode as $per) {
    $p->Cell($lebar, $t, $per, 'BT', 0, 'R');
  }
  $p->Ln($t);
}
function GetArrayProdi(&$arrProdiID, &$arrProdi) {
  $s = "select p.ProdiID, p.Nama
    from prodi p
    where p.KodeID = '".KodeID."'
    and p.NA = 'N'
    order by p.ProdiID";
  $r = _query($s);
  $arrProdiID = array();
  $arrProdi = array();
  while ($w = _fetch_array($r)) {
    $arrProdiID[] = $w['ProdiID'];
    $arrProdi[] = $w['Nama'];
  }
}
function GetArrayPeriode(&$arrPeriode, $gel) {
  $max = $_SESSION['maxperiode']+0;
  $s = "select PMBPeriodID
    from pmbperiod
    where KodeID = '".KodeID."'
      and PMBPeriodID <= '$gel'
    order by PMBPeriodID desc
    limit $max";
  $r = _query($s);
  while ($w = _fetch_array($r)) {
    $arrPeriode[] = $w['PMBPeriodID'];
  }
}
?>
