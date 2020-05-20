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
$TAID = GetSetVar('TAID');
if (empty($TAID))
  die(ErrorMsg("Error",
    "Tentukan Tugas Akhir-nya dulu.
    <hr size=1 color=silver />
    <input type=button name='Tutup' value='Tutup'
      onClick='window.close()' />"));

// *** Main
$pdf = new FPDF('P');
$pdf->SetTitle("Daftar Bimbingan TA Mahasisawa");
$pdf->AddPage('P');
HeaderLogo("Daftar Bimbingan TA Mahasiswa", $pdf, 'P');
$pdf->SetFont('Helvetica', 'B', 14);

Isinya($TAID, $pdf);

$pdf->Output();

// *** Functions ***
function Isinya($TAID, $p) {
  $lbr = 190; $t = 5;
  BuatHeadernya($TAID, $p);
  JudulKolomnya($p);
  $p->SetFont('Helvetica', '', 10);
  $s = "select tb.Catatan, 
	  date_format(tb.TglBimbingan, '%d-%m-%y') as _TglBimbingan
    from tabimbingan tb
    where tb.TAID = '$TAID'
    order by tb.TglBimbingan";
  $r = _query($s);
  $n = 0;
  
  while ($w = _fetch_array($r)) {
    $n++;
	$length = 80;
    $Judul = $w['Catatan'];
	$JudulArr = array();
	if(strlen($Judul) < $length) $JudulArr[] = $Judul;
	else
	{	$_Judul = $Judul;
		while(strlen($_Judul) > 0)
		{	
			if(strlen($_Judul) > $length)
			{
				$partialstring = substr($_Judul, 0, $length);	
				$temppos = strrpos($partialstring, ' ');
				$partialstring = substr($_Judul, 0, $temppos);	
				$JudulArr[] = $partialstring;
				$_Judul = substr($_Judul, $temppos+1);
			}
			else
			{	$JudulArr[] = $_Judul;
				$_Judul = '';
			}
		}
	}
	
	$partcount = 0;
	foreach($JudulArr as $partial)
	{   $p->Cell(8, $t, '', 'L', 0);
		$p->Cell(30, $t, '', 'L', 0);
		$p->Cell(160, $t, $partial, 'L', 0);
		$p->Cell(0, $t, '', 'R', 0);
		$p->Ln($t);
		$partcount++;
	}
	
	$p->SetY($p->GetY() - ($partcount+1)*($t)/2);
	$p->Cell(8, $t, $n, 0, 0);
    $p->Cell(30, $t, $w['_TglBimbingan'], 0, 0, 'C');
	$p->Cell(120, $t, '', 0, 0);
    $p->SetY($p->GetY() + ($partcount-1)*($t)/2);
	$p->Cell(0, $t, '', 'B', 0);
	$p->Ln($t);
  }
  
  while($n < 16)
  { $n++;
	$p->Cell(8, $t, '', 'LT', 0);
    $p->Cell(30, $t, '', 'LT', 0);
	$p->Cell(0, $t, '', 'LTR', 0);
	$p->Ln($t/2);
	$p->Cell(5, $t, $n, 0, 0);
	$p->Ln($t/2);
	$p->Cell(8, $t, '', 'LB', 0);
    $p->Cell(30, $t, '', 'LB', 0, 'C');
	$p->Cell(0, $t, '', 'LBR', 0);
	$p->Ln($t);
  }
}
function JudulKolomnya($p) {
  $t = 6;
  $p->SetFont('Helvetica', 'B', 10);
  $p->Cell(8, $t, 'No.', 1, 0);
  $p->Cell(30, $t, 'Tgl Bimbingan', 1, 0);
  $p->Cell(0, $t, 'Catatan Bimbingan', 1, 0);
  $p->Ln($t);
}
function BuatHeadernya($TAID, $p)
{ $ta = GetFields("ta t
      left outer join mhsw m on m.MhswID = t.MhswID and m.KodeID = '".KodeID."'
      left outer join dosen d on d.Login = t.Pembimbing and d.KodeID = '".KodeID."'",
	  "t.TAID", $TAID, "t.*,
		  m.Nama as NamaMhsw,
		  m.TahunID, 
		  date_format(t.TglMulai, '%d-%m-%y') as _TglMulai,
		  date_format(t.TglSelesai, '%d-%m-%y') as _TglSelesai,
		  left(d.Nama, 28) as DSN, d.Gelar");
  
  $arr = array();
  $arr[] = array('NIM', ':', $ta['MhswID']);
  $arr[] = array('Nama', ':', $ta['NamaMhsw'],
    'Tanggal Mulai', ':', $ta['_TglMulai']);
  $arr[] = array('Dosen Pembimbing', ':', $ta['DSN'].', '.$ta['Gelar'], 'Tanggal Selesai', ':', $ta['_TglSelesai']);

  // Tampilkan
  $p->SetFont('Helvetica', '', 9);
  $t = 5;
  foreach ($arr as $a) {
    // Kolom 1
    $p->SetFont('Helvetica', 'I', 9);
    $p->Cell(30, $t, $a[0], 0, 0);
    $p->Cell(4, $t, $a[1], 0, 0, 'C');
    $p->SetFont('Helvetica', 'B', 9);
    $p->Cell(85, $t, $a[2], 0, 0);
    // Kolom 2
    $p->SetFont('Helvetica', 'I', 9);
    $p->Cell(30, $t, $a[3], 0, 0);
    $p->Cell(4, $t, $a[4], 0, 0, 'C');
    $p->SetFont('Helvetica', 'B', 9);
    $p->Cell(25, $t, $a[5], 0, 0);
    $p->Ln($t);
  }
  $p->SetFont('Helvetica', 'I', 9);
  $p->Cell(30, $t, 'Judul Skripsi', 'LT', 0);
  $p->Cell(4, $t, ':', 'T', 0, 'C');
  $p->Cell(0, $t, '', 'TR', 0);
  
  $p->SetFont('Helvetica', 'B', 9);
  $p->SetX(10);
  
  $length = 90;
  $Judul = $ta['Judul'];
	$JudulArr = array();
	if(strlen($Judul) < $length) $JudulArr[] = $Judul;
	else
	{	$_Judul = $Judul;
		while(strlen($_Judul) > 0)
		{	
			if(strlen($_Judul) > $length)
			{
				$partialstring = substr($_Judul, 0, $length);	
				$temppos = strrpos($partialstring, ' ');
				$partialstring = substr($_Judul, 0, $temppos);	
				$JudulArr[] = $partialstring;
				$_Judul = substr($_Judul, $temppos+1);
			}
			else
			{	$JudulArr[] = $_Judul;
				$_Judul = '';
			}
		}
	}
	
	$partcount = 0;
	foreach($JudulArr as $partial)
	{   $p->Cell(34, $t, '', 'L', 0);
		$p->Cell(0, $t, $partial, 'R', 0);
		$p->Ln($t);
		$partcount++;
	}
	$p->Cell(0, $t, '', 'T', 0);
	$p->Ln($t);
}

function HeaderLogo($jdl, $p, $orientation='P')
{	$pjg = 110;
	$logo = (file_exists("../img/logo.jpg"))? "../img/logo.jpg" : "img/logo.jpg";
    $identitas = GetFields('identitas', 'Kode', KodeID, 'Nama, Alamat1, Telepon, Fax');
	$p->Image($logo, 12, 8, 18);
	$p->SetY(5);
    $p->SetFont("Helvetica", '', 8);
    $p->Cell($pjg, 5, "YAYASAN KESEJAHTERAAN ANAK BANGSA", 0, 1, 'C');
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
