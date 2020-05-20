<?php
// Author : Irvandy Gouttama
// Email  : irvandygoutama@gmail.com
// Start  : 02 Juni 2008

session_start();

  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
  include_once "../fpdf.php";

// *** Parameters ***

// *** Main
$pdf = new FPDF('P');
$pdf->SetTitle("Daftar Rekening Dosen");
$pdf->AddPage('P');
HeaderLogo("Daftar Rekening Dosen", $pdf, 'P');
$pdf->SetFont('Helvetica', 'B', 14);

Isinya($pdf);

$pdf->Output();

// *** Functions ***
function Isinya($p) {
  $lbr = 190; $t = 5;
  
  JudulKolomnya($p);
  $p->SetFont('Helvetica', '', 7);
  $s = "select d.Nama, d.Login, d.NamaBank, d.NamaAkun, d.NomerAkun  
    from dosen d
    where d.NA='N'
    order by d.Nama";
  $r = _query($s);
  $n = 0;
  
  while($w = _fetch_array($r))
  { $n++;
	$p->Cell(8, $t, $n, 1, 0);
    $p->Cell(25, $t, $w['Login'], 1, 0, 'C');
	$p->Cell(50, $t, $w['Nama'], 1, 0);
	$p->Cell(2, $t, '', 1, 0);
	$p->Cell(30, $t, $w['NamaBank'], 1, 0);
	$p->Cell(50, $t, $w['NamaAkun'], 1, 0);
	$p->Cell(25, $t, $w['NomerAkun'], 1, 0, 'R');
	$p->Ln($t);
  }
}
function JudulKolomnya($p) {
  $t = 6;
  $p->SetFont('Helvetica', 'B', 7);
  $p->Cell(8, $t, 'No.', 1, 0);
  $p->Cell(25, $t, 'Nomor Dosen', 1, 0);
  $p->Cell(50, $t, 'Nama Dosen', 1, 0);
  $p->Cell(2, $t, '', 1, 0);
  $p->Cell(30, $t, 'Nama Bank', 1, 0);
  $p->Cell(50, $t, 'Nama Pada Rekening', 1, 0);
  $p->Cell(25, $t, 'Nomor Rekening', 1, 0, 'R');
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
