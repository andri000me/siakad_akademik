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
$prodi = $_REQUEST['prodi'];
$program = $_REQUEST['program'];
$srcmhswkey = $_REQUEST['srcmhswkey'];
$srcmhswval = $_REQUEST['srcmhswval'];

// *** Main
$pdf = new FPDF('P');
$pdf->SetTitle("List Seluruh Mahasiswa - $thn");
$pdf->SetAutoPageBreak(true, 5);
$pdf->SetFillColor(200, 200, 200);

$pdf->AddPage('P');
HeaderLogo("Data Detail Mahasiswa", $pdf, 'P');
Isinya($prodi, $program, $srcmhswkey, $srcmhswval,$pdf);

$pdf->Output();

// *** Functions ***

function Isinya($prodi, $program, $srcmhswkey, $srcmhswval, $p) {
  $lbr = 190; $t = 6;
  
  // BuatHeader
  $p->SetFont('Helvetica', 'B', 11);
  $p->Cell(10,  $t, 'No.', 1, 0, 'R');
  $p->Cell(30, $t, 'NPM', 1, 0, 'L');
  $p->Cell(75, $t, 'Nama Mahasiswa', 1, 0, 'L');
  $p->Cell(15, $t, 'Prodi', 1, 0, 'C');
  $p->Cell(15, $t, 'Program', 1, 0, 'C');
  $p->Cell(15, $t, 'Status', 1, 0, 'C');
  $p->Cell(30, $t, 'Telepon', 1, 0, 'C');
  $p->Ln($t);
  
  $whr = array(); $ord = ''; $strwhr = '';
  if(!empty($prodi)) $whr[] = " m.ProdiID='$prodi' ";
  if(!empty($program)) $whr[] = " m.ProgramID='$program' ";
  if(!empty($srcmhswkey) && !empty($srcmhswval)) {
    $whr[] = "m.$srcmhswkey like '%$srcmhswval%' ";
    $ord = "order by m.$srcmhswkey";
  }
  if (!empty($whr)) $strwhr = "and " .implode(' and ', $whr);
  $strwhr = str_replace('NPM', "MhswID", $strwhr);
  $ord = str_replace('NPM', "MhswID", $ord);
  
  $s = "select m.MhswID, m.Nama, m.ProdiID, m.ProgramID, m.StatusMhswID, m.Telepon, m.Handphone, sm.Keluar
			from mhsw m left outer join statusmhsw sm on m.StatusMhswID=sm.StatusMhswID
			where m.KodeID='".KodeID."' $strwhr $ord";
  $r = _query($s);
  $n = 0;
  while($w = _fetch_array($r))
  {	  $n++;
	  $arrTelepon = array();
	  if(!empty($w['Telepon'])) $arrTelepon[] = $w['Telepon'];
	  if(!empty($w['Handphone'])) $arrTelepon[] = $w['Handphone'];
	  $_Telepon = implode('/', $arrTelepon);
	  
	  $p->SetFont('Helvetica', '', 10);
	  $p->Cell(10,  $t, $n, 1, 0, 'R');
	  $p->Cell(30, $t, $w['MhswID'], 1, 0, 'L');
	  $p->Cell(75, $t, $w['Nama'], 1, 0, 'L');
	  $p->Cell(15, $t, $w['ProdiID'], 1, 0, 'C');
	  $p->Cell(15, $t, $w['ProgramID'], 1, 0, 'C');
	  $p->Cell(15, $t, $w['StatusMhswID'], 1, 0, 'C', ($w['Keluar'] == 'N')?false:true);
	  $p->Cell(30, $t, $_Telepon, 1, 0, 'C');
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
