<?php
session_start();

  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
  include_once "../header_pdf.php";

// *** Parameters ***
$PMBPeriodID = GetSetVar('PMBPeriodID');
$gels = GetFields('pmbperiod', "KodeID='".KodeID."' and PMBPeriodID", $PMBPeriodID, "*");

$lbr = 190;

// *** Cetak ***
$pdf = new PDF();
$pdf->SetTitle("Pengumuman PMB");
$pdf->AddPage('P');

CetakHeader($gels, $pdf);
CetakDataLulus($gels, $pdf);
CetakFooter($pdf);

$pdf->Output();

// *** Function ***
function CetakFooter($p) {
  global $arrID;
  $t = 6;
  $mrg = 120; $lbr = 80;
  
  $p->Ln(3);
  $p->Cell($mrg);
  $p->Cell($lbr, $t, $arrID['Kota'] . ', ' . date('d-m-Y'), 0, 1);

  $ketua = GetFields('pejabat', "KodeID = '".KodeID."' and KodeJabatan", 'KETUA', "*");
  $p->Cell($mrg);
  $p->Cell($lbr, $t, $ketua['Jabatan'], 0, 1);
  $p->Ln(10);
  $p->Cell($mrg);
  $p->Cell($lbr, $t, $ketua['Nama'], 0, 1);
  $p->Cell($mrg);
  $p->Cell($lbr, $t, "NIP: " . $ketua['NIP'], 0, 1);
}
function CetakDataLulus($gels, $p) {
  $s = "select p.PMBID, p.Nama, p.AsalSekolah, p.NilaiUjian, p.NilaiSekolah,
      p.ProdiID, p.ProgramID,
      prg.Nama as _PRG, prd.Nama as _PRD,
	  j.Nama as Jenjang
    from pmb p
      left outer join program prg on prg.ProgramID = p.ProgramID and prg.KodeID='".KodeID."'
      left outer join prodi prd on prd.ProdiID = p.ProdiID and prd.KodeID='".KodeID."'
	  left outer join jenjang j on j.JenjangID= prd.JenjangID 
    where p.KodeID = '".KodeID."'
      and p.PMBPeriodID = '$gels[PMBPeriodID]'
      and p.LulusUjian = 'Y'
    order by p.ProdiID, p.ProgramID, p.PMBID ";
  $r = _query($s);
  $n = 0;
  $t = 6;
  
  $pr = 'alskdjflaksjdf';
  while ($w = _fetch_array($r)) {
    $n++;
    if ($pr != $w['ProdiID'].$w['ProgramID']) {
      $pr = $w['ProdiID'].$w['ProgramID'];
      $p->Ln(1);
      $p->SetFont('Helvetica', 'B', 11);
      $p->Cell(190, 8, "Program Studi: $w[_PRD] $w[Jenjang] ~ $w[_PRG]", 0, 1);
      BuatHeaderTabel($p);
    }
	$sekolah = GetaField('asalsekolah',"SekolahID",$w[AsalSekolah],'Nama');
	if (empty($sekolah)) {
	$sekolah = GetaField('perguruantinggi',"PerguruanTinggiID",$w[AsalSekolah],'Nama');
	}
    $p->SetFont('Helvetica', '', 10);
    $p->Cell(16, $t, $n, 'LB', 0, 'C');
    $p->Cell(24, $t, $w['PMBID'], 'B', 0);
    $p->Cell(60, $t, $w['Nama'], 'B', 0);
	$p->SetFont('Helvetica', '', 7);
    $p->Cell(70, $t, $sekolah, 'B', 0);
	$p->SetFont('Helvetica', '', 10);
    $p->Cell(20, $t, $w['NilaiSekolah'], 'BR', 0, 'C');

    
    $p->Ln($t);
  }
}

function BuatHeaderTabel($p) {
  $t = 7;
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell(16, $t, 'No.', 1, 0, 'C');
  $p->Cell(24, $t, 'No. PMB', 1, 0, 'C');
  $p->Cell(60, $t, 'Nama Calon Mhsw', 1, 0);
  $p->Cell(70, $t, 'Asal Sekolah', 1, 0);
  $p->Cell(20, $t, 'Nilai', 1, 0,'C');

  
  $p->Ln($t);
}
function CetakHeader($gels, $pdf) {
  $pdf->SetFont('Helvetica', 'B', 14);
  $pdf->Cell($lbr, 9, "Pengumuman Kelulusan Ujian Saringan Masuk PMB", 0, 1, 'C');
  $pdf->SetFont('Helvetica', 'B', 12);
  $pdf->Cell($lbr, 8, $gels['Nama'], 0, 1, 'C');
}
?>
