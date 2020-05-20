<?php
// Author : Irvandy Goutama
// Email  : irvandygoutama@gmail.com
// Start  : 05 Juni 2008

session_start();

include_once "../dwo.lib.php";
include_once "../db.mysql.php";
include_once "../connectdb.php";
include_once "../parameter.php";
include_once "../cekparam.php";
include_once "../fpdf.php";

// *** Parameters ***
$TahunID = GetSetVar('TahunID');
$ProdiID = GetSetVar('ProdiID');

// *** Init PDF
$pdf = new FPDF();
$pdf->SetTitle("Rekap Beasiswa Mahasiswa");
$pdf->SetAutoPageBreak(true, 5);
$pdf->AddPage();
$tahunstring = (empty($TahunID))? '' : "Tahun $TahunID";
HeaderLogo("Mahasiswa Yang Mendapatkan Beasiswa $tahunstring", $pdf, 'P');
BuatHeaderTable($TahunID, $ProdiID, $pdf);
$lbr = 190;

BuatIsinya($TahunID, $ProdiID, $pdf);

$pdf->Output();

// *** Functions ***
function BuatIsinya($TahunID, $ProdiID, $p) {
  $whr_prodi = ($ProdiID == '')? '' : "and h.ProdiID = '$ProdiID' ";
  $whr_tahun = (empty($TahunID))? '' : "and h.TahunID = '$TahunID'";
  $s = "select h.MhswID, m.Nama, h.ProdiID, h.IP, h.SKS, h.TotalSKS, 
      h.Potongan,
      format(h.Potongan, 0) as _Potongan
    from khs h
      left outer join mhsw m on m.MhswID = h.MhswID and m.KodeID = '".KodeID."'
    where h.KodeID='".KodeID."'
	  $whr_tahun
      $whr_prodi
	  and h.Potongan > 0
    order by h.MhswID, h.Sesi";
  $r = _query($s); $n = 0;
  $t = 5; $ttl = 0; $_mhsw = ';alskdjfa;lsdhguairgsofjhjg9e8rgjpsofjg';
  
  while ($w = _fetch_array($r)) {
    //$TandaBeasiswa = GetaField('bipotmhsw', '
	
	$n++;
    $ttl += $w['Potongan'];
	$ttlsks += $w['SKS'];
	$ttlttlsks += $w['TotalSKS'];
	$ttlips += $w['IP'];
    $p->SetFont('Helvetica', '', 10);
    $p->Cell(10, $t, $n, 'LB', 0);
    $p->Cell(25, $t, $w['MhswID'], 'B', 0);
    $p->Cell(70, $t, $w['Nama'], 'B', 0);
	$p->Cell(15, $t, $w['IP'], 'B', 0, 'R');
	$p->Cell(15, $t, $w['SKS'], 'B', 0, 'R');
	$p->Cell(15, $t, $w['TotalSKS'], 'B', 0, 'R');
    $p->Cell(22, $t, $w['_Potongan'], 'B', 0, 'R');
    $p->Ln($t); 
  }
  $_ttl = number_format($ttl+0);
  $p->SetFont('Helvetica', 'B', 11);
  $p->Cell($lbr, 1,  ' ', 1, 1);
  $p->Cell(105, $t, 'TOTAL :', 0, 0, 'R');
  $TotalIPS = ($n > 0) ? number_format($ttlips/$n, 2) : 0;
  $p->Cell(15, $t, $TotalIPS, 0, 0, 'R');
  $TotalSKS = ($n > 0) ? number_format($ttlsks/$n, 2) : 0;
  $p->Cell(15, $t, $TotalSKS, 0, 0, 'R');
  $TotalTotalSKS = ($n > 0) ? number_format($ttlttlsks/$n, 2) : 0;
  $p->Cell(15, $t, $TotalTotalKS, 0, 0, 'R');
  $p->Cell(22, $t, $_ttl, 0, 0, 'R'); 
  $p->Ln($t+2);
}
function BuatHeadertable($TahunID, $ProdiID, $p) {
  global $lbr;
  $t = 5;
  $prd = GetaField('prodi', "ProdiID = '$ProdiID' and KodeID", KodeID, 'Nama');
  $p->SetFont('Helvetica', 'B', 14);
  $tahunstring = (empty($TahunID))? '' : "pada $TahunID";
  $p->Cell($lbr, $t, "Mahasiswa Yang Mendapatkan Beasiswa $tahunstring", 0, 1, 'C');
  $p->Ln(4);
  
  $p->SetFont('Helvetica', 'BI', 10);
  $p->Cell(10, $t, 'Nmr', 1, 0);
  $p->Cell(25, $t, 'N I M', 1, 0);
  $p->Cell(70, $t, 'Nama Mhsw', 1, 0);
  $p->Cell(15, $t, 'IPK', 1, 0, 'R');
  $p->Cell(15, $t, 'SKS', 1, 0, 'R');
  $p->Cell(15, $t, 'Ttl SKS', 1, 0, 'R');
  $p->Cell(22, $t, 'Potongan', 1, 0, 'R');
  $p->Ln($t);
}

function HeaderLogo($jdl, $p, $orientation='P')
{	$pjg = 110;
	$logo = (file_exists("../img/logo.jpg"))? "../img/logo.jpg" : "img/logo.jpg";
    $identitas = GetFields('identitas', 'Kode', KodeID, 'Nama, Alamat1, Telepon, Fax');
	$p->Image($logo, 12, 8, 18);
	$p->SetY(5);
    $p->SetFont("Helvetica", '', 8);
    $p->Cell($pjg, 5, $identitas['Yayasan'], 0, 1, 'C');
    $p->SetFont("Helvetica", 'B', 10);
    $p->Cell($pjg, 7, $identitas['Nama'], 0, 0, 'C');
    
	//Judul
	if($orientation == 'L')
	{
		$p->SetFont("Helvetica", 'B', 16);
		$p->Cell(20, 7, '', 0, 0);
		$p->Cell($pjg, 7, $jdl, 0, 1, 'C');
	}
	else
	{	$p->SetFont("Helvetica", 'B', 12);
		$p->Cell(80, 7, $jdl, 0, 1, 'R');
	}
	
    $p->SetFont("Helvetica", 'I', 6);
	$p->Cell($pjg, 3,
      $identitas['Alamat1'], 0, 1, 'C');
    $p->Cell($pjg, 3,
      "Telp. ".$identitas['Telepon'].", Fax. ".$identitas['Fax'], 0, 1, 'C');
    $p->Ln(3);
	if($orientation == 'L') $length = 275;
	else $length = 190;
    $p->Cell($length, 0, '', 1, 1);
    $p->Ln(2);
}

?>
