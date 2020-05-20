<?php
// Author : Irvandy Goutama
// Email  : irvandygoutama@gmail.com
// Start  : 2 Juni 2009

session_start();

include_once "../dwo.lib.php";
include_once "../db.mysql.php";
include_once "../connectdb.php";
include_once "../parameter.php";
include_once "../cekparam.php";
include_once "../fpdf.php";

// *** Parameters ***
$ProsesStatusMhswID = $_REQUEST['_psmid'];

if(empty($ProsesStatusMhswID))
	die(ErrorMsg("Gagal", "Tidak ditemukan copy dari transaksi perubahan status mahasiswa yang dicari.</br>
							Harap menghubungi Kepala Bagian Administrasi untuk mengurus hal ini."));

// *** Init PDF
$pdf = new FPDF();
$pdf->SetTitle("Surat Bukti Perubahan Status Mahasiswa");
$pdf->SetAutoPageBreak(true, 5);
$lbr = 190;

$pdf->AddPage();
HeaderLogo("Surat Bukti Perubahan Status Mahasiswa", $pdf, 'P');	
BuatIsinya($ProsesStatusMhswID, $pdf);
BuatFooter($ProsesStatusMhswID, $pdf);

$pdf->Output();

// *** FUnctions ***
function BuatIsinya($ProsesStatusMhswID, $p) {

  $s = "select *
		from prosesstatusmhsw where ProsesStatusMhswID='$ProsesStatusMhswID' and KodeID='".KodeID."'";
  $r = _query($s);
  $n = 0; $t = 5; 
  
  while ($w = _fetch_array($r)) {
    $n++;
	$p->SetFont('Helvetica', 'BI', 10);
    $p->Cell(100, $t, "SK: $w[SK]", 0, 1);
	$p->Ln($t);
	$p->SetFont('Helvetica', '', 10);
	$p->Cell(100, $t, "Nama Mahasiswa yang tertera di bawah ini:", 0, 1); 
    
	$p->SetFont('Helvetica', '', 10);
    $p->Cell(30, $t, 'NIM', 0, 0);
	$p->Cell(4, $t, ':', 0, 0);
	$p->SetFont('Helvetica', 'B', 10);
	$p->Cell(30, $t, $w['MhswID'], 0, 1);
    
	$p->SetFont('Helvetica', '', 10);
	$p->Cell(30, $t, 'Nama', 0, 0);
	$p->Cell(4, $t, ':', 0, 0);
	$p->SetFont('Helvetica', 'B', 10);
	$p->Cell(30, $t, GetaField('mhsw', "MhswID='$w[MhswID]' and KodeID", KodeID, 'Nama'), 0, 1);
	$p->Ln($t);
	
	$p->SetFont('Helvetica', '', 10);
	$p->Cell(80, $t, "akan mengalami perubahan status mahasiswa:", 0, 0);
    
	$p->SetFont('Helvetica', 'B', 12);
	$p->Cell(40, $t, GetaField('statusmhsw', "StatusMhswID", $w['StatusMhswLama'], 'Nama'), 1, 0, 'C');
    $p->SetFont('Helvetica', '', 10);
	$p->Cell(20, $t, 'menjadi', 0, 0, 'C');
	
	$p->SetFont('Helvetica', 'B', 12);
	$p->Cell(40, $t, GetaField('statusmhsw', "StatusMhswID", $w['StatusMhswID'], 'Nama'), 1, 0, 'C');
    $p->Ln($t);
  }
  $p->Ln($t);
  
}

function BuatFooter($ProsesStatusMhswID, $p)
{	
  $t = 5;
  $p->Ln($t);
  
  $identitas = GetFields('identitas', 'Kode', KodeID, '*');
  $MhswID = GetaField('prosesstatusmhsw', "ProsesStatusMhswID='$ProsesStatusMhswID' and KodeID", KodeID, 'MhswID');
  
  $arr = array();
  $arr[] = array('', $identitas['Kota'].', '.date('d M Y'));
  $arr[] = array('Mengetahui,', 'Mahasiswa,');
  $arr[] = array('', '');
  $arr[] = array('', '');
  $arr[] = array('', '');
  $arr[] = array('', '');
  $arr[] = array('', '');
  $arr[] = array(GetaField('pejabat', "KodeJabatan='PUKET4' and KodeID", KodeID, 'Nama'), GetaField('mhsw', "MhswID='$MhswID' and KodeID", KodeID, 'Nama'));
  $arr[] = array('Biro Akademik', '');
  
  // Tampilkan
  $p->SetFont('Helvetica', '', 9);
  foreach ($arr as $a) {
    $p->Cell(10, $t, '', 0, 0);
	$p->Cell(50, $t, $a[0], 0, 0, 'C');
	$p->Cell(60, $t, '', 0, 0);
    $p->Cell(50, $t, $a[1], 0, 0, 'C');
	
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
