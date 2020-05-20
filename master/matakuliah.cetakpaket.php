<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 20 Oktober 2008

session_start();

  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
  include_once "../fpdf.php";

// *** Parameters ***
$MKPaketID = GetSetVar('MKPaketID');

$pdf = new FPDF();
$pdf->SetTitle("Matakuliah Paket");
$pdf->AddPage();
$pdf->SetFillColor(200, 200, 200);
$pdf->SetFont('Helvetica', 'B', 10);

CetakHeadernya($MKPaketID, $pdf);
CetakMatakuliahnya($MKPaketID, $pdf);

$pdf->Output();

// *** Functions ***
function CetakHeadernya($MKPaketID, $p) {
  $lbr = 140; $t = 6;
  if(empty($MKPaketID)) $_pid = "(SEMUA)";
  else $_pid = GetaField('mkpaket', "MKPaketID='$MKPaketID' and KodeID", KodeID, 'Nama');
  
  HeaderLogo("Daftar M.Kuliah Paket: $_pid", $p,'P');
}
function CetakMatakuliahnya($MKPaketID, $p) {
  $whr_pid = (empty($MKPaketID))? "" : "and mp.MKPaketID='$MKPaketID'";
  $s = "select mk.Sesi, mk.MKKode, mk.Nama, mk.Responsi, mk.PraktekKerja, mk.TugasAkhir, mk.SKS
    from mkpaketisi mp left outer join mk mk on mp.MKID=mk.MKID
    where mk.KodeID='".KodeID."'
		$whr_pid
	order by mk.Sesi, mk.Nama";
  $r = _query($s);
  
  $n = 0; $t = 6; $_sesi = '02q350mq3'; 
  
  BuatHeaderTabel($p);
  while ($w = _fetch_array($r)) {
    /*if ($_sesi != $w['Sesi']) { 
      $_sesi = $w['Sesi'];
      $p->SetFont('Helvetica', 'B', 10);
      $p->Ln(2);
      $p->Cell(170, $t+2, $w['Sesi'], 1, 1);
      BuatHeaderTabel($p);
    }*/
    $n++;
    
    $p->SetFont('Helvetica', '', 10);
    $p->Cell(10, $t, $n.'.', 1, 0, 'R');
    $p->Cell(30, $t, $w['MKKode'], 1, 0);
    $p->Cell(100, $t, $w['Nama'], 1, 0);
    $p->Cell(10, $t, $w['SKS'], 1, 0, 'C');
	$p->Cell(1, $t, '', 1, 0, '', true);
	$p->Cell(10, $t, $w['Sesi'], 1, 0, 'C');
	$p->Cell(1, $t, '', 1, 0, '', true);
	$p->Cell(10, $t, ($w['Responsi']=='Y')? 'Lab' : '', 1, 0, 'C');
    $p->Cell(10, $t, ($w['PraktekKerja']=='Y')? 'KP': '', 1, 0, 'C');
	$p->Cell(10, $t, ($w['TugasAkhir']=='Y')? 'TA' : '', 1, 0, 'C');
    $p->Ln($t);
  }
}
function BuatHeaderTabel($p) {
  $t = 5;
  $p->SetFont('Helvetica', 'BI', 10);
  $p->Cell(10, $t, 'No.', 1, 0);
  $p->Cell(30, $t, 'Kode MK', 1, 0);
  $p->Cell(100, $t, 'Nama', 1, 0);
  $p->Cell(10, $t, 'SKS', 1, 0, 'C');
  $p->Cell(1, $t, '', 1, 0, '', true);
  $p->Cell(10, $t, 'Sesi', 1, 0, 'C');
  $p->Cell(1, $t, '', 1, 0, '', true);
  $p->Cell(10, $t, 'Lab?', 1, 0, 'C');
  $p->Cell(10, $t, 'KP?', 1, 0, 'C');
  $p->Cell(10, $t, 'TA?', 1, 0, 'C');
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
