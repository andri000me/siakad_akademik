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
$pdf->SetTitle("Cama Per Asal Propinsi");
$pdf->AddPage('P');

BuatHeaderLap($gel, $gels, $pdf);
TampilkanIsinya($gel, $gels, $pdf);

$pdf->Output();

// *** Functions ***
function TampilkanHeader($p) {
  $t = 6;
  $p->SetFont('Helvetica', 'B', 8);
  $p->Cell(100, $t, 'Jumlah Mhsw', 1, 1);
}
function TampilkanIsinya($gel, $gels, $p) {
  $s = "select p.PMBID, p.Nama,
    UPPER(a.NamaPropinsi) as _Kota, p.AsalSekolah, a.PropinsiID,
	if(a.Nama like '_%', a.Nama, 
		if(pt.Nama like '_%', pt.Nama, p.AsalSekolah)) as _NamaSekolah 
    from pmb p
	  left outer join asalsekolah a on a.SekolahID = p.AsalSekolah
	  left outer join perguruantinggi pt on pt.PerguruanTinggiID = p.AsalSekolah
    where p.KodeID = '".KodeID."'
      and p.PMBPeriodID = '$gel'
      and a.SekolahID is not null
      and p.MhswID is not Null
      group by a.PropinsiID
    order by UPPER(p.AsalSekolah), p.Nama ";
  $r = _query($s);
  $n = 0; $t = 6;

  $Kota = ';laskdjf;laskdjf';
  while ($w = _fetch_array($r)) {
    if ($Kota != $w['_Kota']) {
      $Kota = $w['_Kota'];
      $p->Ln(2);
      $p->SetFont('Helvetica', 'B', 9);
      $p->Cell(185, $t, $w['_Kota'], 0, 1);
      $n = 0;
      TampilkanHeader($p);
    }
    $n++;
    $p->SetFont('Helvetica', '', 8);
    $p->Cell(100, $t, GetaField('pmb p left outer join asalsekolah a on a.SekolahID=p.AsalSekolah', "a.PropinsiID='$w[PropinsiID]' and p.PMBPeriodID = '$gel' and p.MhswID is not Null and p.KodeID",KodeID,"COUNT(p.PMBID)") , 'BR', 0);
    $p->Ln($t);
    //
  }
}
function BuatHeaderLap($gel, $gels, $p) {
  global $lbr;
  $p->SetFont('Helvetica', 'B', 10);
  $p->Cell($lbr, 8, "Daftar Calon Mahasiswa Per Asal Propinsi", 0, 1, 'C');
  $p->SetFont('Helvetica', 'B', 11);
  $p->Cell($lbr, 6, $gels['Nama'], 0, 1, 'C');
}

?>
