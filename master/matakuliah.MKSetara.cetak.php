<?php
// Author : Irvandy Goutama
// Email  : irvandygoutama@gmail.com
// Start  : 04 Juni 2009

session_start();

  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
  include_once "../fpdf.php";

// *** Parameters ***
$kurid = $_REQUEST['kurid'];
if (empty($kurid)) {
  die(ErrorMsg("Kurikulum Belum Diset",
"Tidak dapat mencetak karena kurikulum belum ditentukan atau tidak ditemukan."));
}  
// *** Main
$pdf = new FPDF('P');
$pdf->SetTitle("Daftar Matakuliah Setara");
$pdf->SetAutoPageBreak(true, 5);
$pdf->SetFillColor(200, 200, 200);

$pdf->AddPage('P');
HeaderLogo("Daftar Matakuliah Setara", $pdf, 'P');
CetakMKSetara($kurid,$pdf);
$pdf->Output();

// *** Functions ***

function CetakMKSetara($kurid, $p) {
  $lbr = 190; $t = 6;
  
  $p->SetFont('Helvetica', 'B', 11);
  $p->Cell(10,  $t, 'No.', 1, 0, 'R');
  $p->Cell(30, $t, 'MKKode', 1, 0, 'L');
  $p->Cell(100, $t, 'Nama Mata Kuliah', 1, 0, 'L');
  $p->Cell(10, $t, 'SKS', 1, 0, 'C');
  $p->Cell(10, $t, 'Sesi', 1, 0, 'C');
  $p->Cell(30, $t, 'MK Setara', 1, 0, 'C');
  $p->Ln($t);
  // tuliskan
  $s = "select MKKode, Nama, SKS, Sesi, MKSetara
    from mk
    where KurikulumID='$kurid' and NA='N'
    order by Sesi, MKKode";
  $r = _query($s); 
  $n = 0; 
  while ($w = _fetch_array($r)) {
    $n++;
	$p->SetFont('Helvetica', 'B', 11);
	$p->Cell(10,  $t, $n, 1, 0, 'R');
	$p->Cell(30, $t, $w['MKKode'], 1, 0, 'L');
	$p->Cell(100, $t, $w['Nama'], 1, 0, 'L');
	$p->Cell(10, $t, $w['SKS'], 1, 0, 'C');
	$p->Cell(10, $t, $w['Sesi'], 1, 0, 'C');
	$p->Cell(30, $t, $w['MKSetara'], 1, 0, 'C');
	$p->Ln($t);
  }
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
