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
$pdf->SetTitle("Cama Per Asal Kota");
$pdf->AddPage('P');

BuatHeaderLap($gel, $gels, $pdf);
TampilkanIsinya($gel, $gels, $pdf);

$pdf->Output();

// *** Functions ***
function TampilkanHeader($p) {
  $t = 6;
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell(120, $t, 'Jumlah', 1, 1,'R');
  
}
function TampilkanIsinya($gel, $gels, $p) {
  $s = "select count(p.PMBID) as _PMBID, p.PMBID, p.Nama, p.AsalSekolah, p.NilaiUjian, NilaiSekolah,
	if(a.Nama like '_%', concat(p.AsalSekolah, ' - ', a.Nama), 
		if(pt.Nama like '_%', concat(p.AsalSekolah, ' - ', pt.Nama), p.AsalSekolah)) as _NamaSekolah, 
    p.KotaOrtu, prd.Nama as _PRD, prg.Nama as _PRG
    from pmb p
      left outer join prodi prd on prd.ProdiID = p.ProdiID and prd.KodeID = '".KodeID."'
      left outer join program prg on prg.ProgramID = p.ProgramID and prg.KodeID = '".KodeID."'
	  left outer join asalsekolah a on a.SekolahID = p.AsalSekolah
	  left outer join perguruantinggi pt on pt.PerguruanTinggiID = p.AsalSekolah
    where p.KodeID = '".KodeID."'
      and p.PMBPeriodID = '$gel'
      group by p.AsalSekolah
    order by p.AsalSekolah, p.Nama ";
  $r = _query($s);
  $n = 0; $t = 6;

  $AS = ';laskdjf;laskdjf';
  while ($w = _fetch_array($r)) {
    if ($AS != $w['AsalSekolah']) {
      $AS = $w['AsalSekolah'];
      $p->Ln(2);
      $p->SetFont('Helvetica', '', 11);
      $p->Cell(185, $t, $w['_NamaSekolah'], B, 1);
      TampilkanHeader($p);
    }
    $n++;
    $p->SetFont('Helvetica', '', 9);
    $p->Cell(185, $t, $w['_PMBID'], 'BR', 0, 'R');
    $p->Ln($t);
  }
}
function BuatHeaderLap($gel, $gels, $p) {
  global $lbr;
  $p->SetFont('Helvetica', 'B', 14);
  $p->Cell($lbr, 8, "Daftar Calon Mahasiswa Per Asal Sekolah", 0, 1, 'C');
  $p->SetFont('Helvetica', 'B', 12);
  $p->Cell($lbr, 6, $gels['Nama'], 0, 1, 'C');
}

?>
