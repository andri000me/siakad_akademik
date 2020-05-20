<?php
// Author: Irvandy Goutama
// Start Date: 31 Januari 2009

session_start();

include_once "../dwo.lib.php";
include_once "../db.mysql.php";
include_once "../connectdb.php";
include_once "../parameter.php";
include_once "../cekparam.php";
include_once "../util.lib.php";  
  
// *** Parameters ***
$JRID = $_REQUEST['JRID'];

include_once "../fpdf.php";

$lbr = 280;

$pdf = new FPDF('L', 'mm', 'A4');
$pdf->SetTitle("Jadwal Remedial");

if($JRID == 0)
{	$prodistring = (empty($_SESSION['_remedialProdiID']))? "" : "and jr.ProdiID='$_SESSION[_remedialProdiID]'";
	$tahunstring = (empty($_SESSION['_remedialTahunID']))? "" : "and jr.TahunID='$_SESSION[_remedialTahunID]'";
	
	$s = "select jr.* 
			from jadwalremedial jr
			where jr.KodeID='".KodeID."' $prodistring $tahunstring ";
	$r = _query($s);
}
else
{	$s = "select jr.*
			from jadwalremedial jr
			where jr.KodeID='".KodeID."' and JadwalRemedialID='$JRID'";
	$r = _query($s);
}	

$pdf->AddPage('L', 'A4');

$pdf->SetFont('Helvetica', 'B', 16);
$pdf->Cell($lbr, 9, "JADWAL REMEDIAL", 0, 0, 'C');
$pdf->Ln(9);

AmbilJadwal($r, $pdf);
$pdf->Ln(9);
AmbilFooter($pdf);

$pdf->Output();

// *** Functions ***

function AmbilFooter($p)
{	$p->SetFont('Helvetica', 'B', 12);
	$t = 7; 

	$identitas = GetFields('identitas', 'Kode', KodeID, '*');
	$p->Cell(270, $t, $identitas['Kota'].', '.AmbilBulan(date('m')).' '.date('Y'), 0, 1);
	$p->Cell(270, $t, '', 0, 1);
	$p->Cell(270, $t, 'BAAK', 0, 1);
}

function AmbilJadwal($query, $p) {
  // Buat headernya dulu
  $p->SetFont('Helvetica', 'B', 12);
  $t = 7; $count = 0;

  $p->Cell(15, $t, 'NO', 1, 0, 'C');
  $p->Cell(110, $t, 'MATA KULIAH', 1, 0, 'C');
  $p->Cell(75, $t, 'HARI/TANGGAL', 1, 0, 'C');
  $p->Cell(40, $t, 'WAKTU', 1, 0, 'C');
  $p->Cell(30, $t, 'RUANG', 1, 0, 'C');
  $p->Ln($t);

  while($w = _fetch_array($query))
  {		$count++;
		
		$s1 = "select * from presensiremedial where JadwalRemedialID='$w[JadwalRemedialID]' and KodeID='".KodeID."'";
		$r1 = _query($s1);
		$n1 = _num_rows($r1);
		
		$p->Cell(15, $n1*$t, $count, 1, 0, 'C');
		$p->Cell(110, $n1*$t, $w['Nama'], 1, 0, 'C');
		$nx = 0;
		while($w1 = _fetch_array($r1))
		{	if($nx > 0) $p->Cell(125);
			$p->Cell(75, $t, $w1['Tanggal'], 1, 0, 'C');
			$p->Cell(40, $t, substr($w1['JamMulai'], 0, 5).' - '.substr($w1['JamSelesai'], 0, 5), 1, 0, 'C');
			$p->Cell(30, $t, $w1['RuangID'], 1, 1, 'C');			
			$nx++;
		}
		$p->Ln($t+1);
  }
}

function HeaderLogo($p)
{	$pjg = 110;
	$logo = (file_exists("../img/logo.gif"))? "../img/logo.gif" : "img/logo.gif";
    $p->Image($logo, 12, 8, 18);
	$p->SetY(5);
    $p->SetFont("Helvetica", '', 8);
    $p->Cell($pjg, 6, $identitas['Yayasan'], 0, 1, 'C');
    $p->SetFont("Helvetica", 'B', 10);
    $p->Cell($pjg, 7, "AKADEMIK BINA INSANI", 0, 1, 'C');
    
    $p->SetFont("Helvetica", 'I', 6);
    $p->Cell($pjg, 3,
      "Jl. A. Yani Blok B2 no. 11 & 22, Bekasi 17148.", 0, 1, 'C');
    $p->Cell($pjg, 3,
      "Telp. 021-889 58130, Fax. 021-885 3574", 0, 1, 'C');
    $p->Ln(3);
    $p->Cell(190, 0, "", 1, 1);
    $p->Ln(2);
}

function AmbilBulan($integer)
{	$arrBulan = array('', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
						'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
	return $arrBulan[$integer+0];
}

function AmbilHari($string)
{	$arrHari['Mon'] = 'Senin';
	$arrHari['Tue'] = 'Selasa';
	$arrHari['Wed'] = 'Rabu';
	$arrHari['Thu'] = 'Kamis';
	$arrHari['Fri'] = 'Jumat';
	$arrHari['Sat'] = 'Sabtu';
	$arrHari['Sun'] = 'Minggu';
	
	return $arrHari[$string];
}

?>