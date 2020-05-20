<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 08 Sept 2008

session_start();

  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
  include_once "../fpdf.php";
  include_once "../util.lib.php";
  
// *** Parameters ***	 
$ProdiID = $_REQUEST['ProdiID'];
if(empty($ProdiID)) $ProdiID = GetaField('prodi', "KodeID", KodeID, 'ProdiID');

// *** Main
$thn = NamaTahun($TahunID);
$pdf = new FPDF('P','mm','A4');
$pdf->SetTitle("Formulir Komprehensif");

$pdf->AddPage('P');
$pdf->SetAutoPageBreak(true, 5);
HeaderLogo("EVALUASI UJIAN KOMPREHENSIF", $pdf, 'P');
BuatHeadernya($pdf);
Isinya($pdf);
BuatFooternya($pdf);
$pdf->AddPage('P');
BuatKeterangannya($ProdiID, $pdf);
$pdf->Output();

// *** Functions ***

function BuatHeadernya($p) 
{	$t = 5; $kolom1 = 90;
	$p->SetFont('Helvetica', '', 10);
	$p->Cell(0, $t, "Semester: ............................................ Tahun Akademik 20..... /20..... ", 0, 0, 'C');
	$p->Ln($t);
	$p->Ln($t);
	
	$p->Cell($kolom1, $t, "Nama Peserta Ujian", 1, 0);
	$p->Cell(0, $t, '', 1, 0);
	$p->Ln($t);
	
	$p->Cell($kolom1, $t, "Nomor Induk Mahasiswa", 1, 0);
	$p->Cell(0, $t, '', 1, 0);
	$p->Ln($t);
	
	$p->Cell($kolom1, $t, "Ujian Ke", 1, 0);
	$p->Cell(0, $t, '', 1, 0);
	$p->Ln($t);
	
	$p->Cell($kolom1, $t, "Tanggal Ujian Komprehensif", 1, 0);
	$p->Cell(0, $t, '', 1, 0);
	$p->Ln($t);
	$p->Ln($t);
}

function Isinya($p) {
  $lbr = 190; $t = 6;
  
  $p->SetFont('Helvetica', 'BU', 10);  
  $p->Cell(0, $t, 'Penilaian Ujian Komprehensif', 0, 0);
  $p->Ln($t);
  
  $p->SetFont('Helvetica', 'BI', 10);  
  $p->MultiCell(0, $t, '(Berilah tanda X pada kotak yang tersedia sesuai dengan penilaian yang diberikan, tuliskan nilainya di kotak).', 0, 0);
  $p->Ln($t);
  
  KriteriaPenilaian('1. PEMAHAMAN MATERI', $p, 'NILAI*');
  KriteriaPenilaian('2. SISTEMATIKA / LOGIKA MENJAWAB', $p);
  KriteriaPenilaian('3. PERILAKU / SOPAN SANTUN DALAM UJIAN', $p);
  KriteriaPenilaian2('4. NILAI RATA-RATA', $p, 3);
  KriteriaPenilaian2('5. NILAI AKHIR (Dalam Huruf)', $p, 3);
  KriteriaPenilaian2('6. KETERANGAN LAINNYA', $p, 4);
}

function KriteriaPenilaian($jdl, $p, $jdl2='')
{	$t = 6;
	$lbr1 = 160;
    $lbrkotak = 6;
	$lbrkosong = (($lbr1/5)-$lbrkotak)/2;
	$arrKet = array('Tidak Memuaskan', 'Kurang Memuaskan', 'Cukup Memuaskan', 'Memuaskan', 'Sangat Memuaskan');
	
	$p->SetFont('Helvetica', 'B', 10);  
	$p->Cell($lbr1, $t, $jdl, 0, 0);
    $p->Cell($lbrkosong/2, $t, '', 'L', 0, 'C');
	$p->Cell(0, $t, $jdl2, 0, 0);
	$p->Ln($t);
	
	$p->SetFont('Helvetica', '', 10);
	for($i = 0; $i < count($arrKet); $i++)
	{	$p->Cell($lbrkosong, $t, '', 0, 0);
		$p->Cell($lbrkotak, $t, $i, 1, 0, 'C');
		$p->Cell($lbrkosong, $t, '', 0, 0);
	}
	$p->Cell($lbrkosong/2, $t, '', 'L', 0, 'C');
	$p->Cell($lbrkotak, $t, '', 1, 0);
	$p->Ln($t);
	
	$p->SetFont('Helvetica', '', 8);
	foreach($arrKet as $ket)
	{	$p->Cell($lbrkotak+(2*$lbrkosong), $t, $ket, 0, 0, 'C');
	}
	$p->Cell(0, $t, '', 'L', 0, 'C');
	$p->Ln($t);
	
	$p->Cell($lbr1, $t, '', 0, 0);
	$p->Cell(0, $t, '', 'L', 0, 'C');
	$p->Ln($t);
}

function KriteriaPenilaian2($jdl, $p, $numlines)
{	$t = 6;
	$lbr1 = 160;
    $lbrkotak = 6;
	$lbrkosong = (($lbr1/5)-$lbrkotak)/2;
	
	$p->SetFont('Helvetica', 'B', 10);  
	$p->Cell($lbr1, $t, $jdl, 0, 0);
    $p->Cell($lbrkosong/2, $t, '', 'L', 0, 'C');
	$p->Ln($t);
	
	$p->SetFont('Helvetica', '', 10);
	for($i = 0; $i < $numlines; $i++)
	{	$p->Cell($lbr1-3, $t, '', 'B', 0);
		$p->Cell(3, $t, '', 0, 0);
		$p->Cell($lbrkosong/2, $t, '', 'L', 0, 'C');
		if($i == 1) $p->Cell($lbrkotak, $t, '', 1, 0, 'C');
		$p->Ln($t);
	}
	$p->Cell($lbr1, $t, '', 0, 0);
	$p->Cell(0, $t, '', 'L', 0, 'C');
	$p->Ln($t);
}

function BuatFooternya($p){
  $t = 6;
  $identitas = GetFields('identitas', 'Kode', KodeID, '*');
  
  $p->SetFont('Helvetica', '', 10);
  $p->Cell(0, $t, $identitas['Kota'].', ..................................... 20.....', 0, 0);
  $p->Ln($t);
  
  $p->Cell(0, $t, 'Tim Penguji :', 0, 0);
  $p->Ln($t);
  
  for($i = 1 ; $i <= 3; $i++)
  {	  $p->Cell(0, $t, $i.'.    ...................................................................................', 0, 0);
	  $p->Ln($t);
  }
}

function BuatKeterangannya($prd, $p)
{	$t= 4;
	$lbr = 190;
	$lbrket = 40;
	$p->SetFont('Helvetica', '', 7);
	$p->Cell(0, $t, 'Keterangan', 0, 1);
	$p->Ln($t);
	
	$p->Cell(10, $t, '*)', 0, 0);
	$p->Cell(0, $t, 'Pindahkan angka pilihan Anda yang berada di dalam kotak kecil, ke dalam kotak NILAI', 0, 1);
	
	$p->Cell(10, $t, '**)', 0, 0);
	$p->Cell(0, $t, 'Jumlahkan seluruh NILAI yang ada di dalam kotak besar di samping kanan, dan dibagi dengan angka 4', 0, 1);
	
	$p->Cell(10, $t, '***)', 0, 0);
	$p->Cell(0, $t, 'Gunakan pedoman di bawah ii untuk menentukan NILAI AKHIR ( dalam Huruf )', 0, 1);
	$p->Ln($t*2);
	
	$p->SetFont('Helvetica', 'B', 7);
	$p->Cell(($lbr-(2*$lbrket))/2);
	$p->Cell($lbrket, $t, 'Jika, Range Rata - Rata', 1, 0, 'C');
	$p->Cell($lbrket, $t, 'Maka, Nilai Akhir', 1, 0, 'C');
	$p->Ln($t);
	
	$s = "select * from nilaikompre where KodeID='".KodeID."' and ProdiID='$prd' order by Nama DESC";
	$r = _query($s);
	while($w = _fetch_array($r))
	{	$p->Cell(($lbr-(2*$lbrket))/2);
		$p->Cell($lbrket, $t, $w['NilaiMin'].' - '.$w['NilaiMax'], 'LR', 0, 'C');
		$p->Cell($lbrket, $t, $w['Nama'], 'LR', 0, 'C');
		$p->Ln($t);
	}
	$p->Cell(($lbr-(2*$lbrket))/2);
	$p->Cell($lbrket*2, $t, '', 'T', 0);
	
}	

function HeaderLogo($jdl, $p, $orientation='P', $jdl2='')
{	$pjg = 110;
	$logo = (file_exists("../img/logo.jpg"))? "../img/logo.jpg" : "img/logo.jpg";
    $identitas = GetFields('identitas', 'Kode', KodeID, 'Nama, Alamat1, Telepon, Fax');
	$p->Image($logo, 12, 8, 18);
	$p->SetY(5);
    $p->SetFont("Helvetica", '', 8);
    $p->Cell($pjg, 5, $identitas['Yayasan'], 0, 1, 'C');
    $p->SetFont("Helvetica", 'B', 9);
    $p->Cell($pjg, 7, $identitas['Nama'], 0, 0, 'C');
    
	//Judul
	$p->SetFont("Helvetica", 'B', 14);
	$p->Cell(20, 7, '', 0, 0);
    $p->Cell(60, 7, $jdl, 0, 0, 'R');
	
    $p->SetFont("Helvetica", 'I', 6);
	$p->Cell($pjg, 3,$identitas['Alamat1'], 0, 0, 'C');
	$p->SetFont("Helvetica", 'B', 14);
	$p->Cell(20);
	$p->Cell($pjg, 7, $jdl2, 0, 1, 'C');
	$p->SetFont("Helvetica", 'I', 6);
    $p->Cell($pjg, 3,
      "Telp. ".$identitas['Telepon'].", Fax. ".$identitas['Fax'], 0, 1, 'C');
    $p->Ln(8);
	if($orientation == 'L') $length = 275;
	else $length = 190;
    $p->Cell($length, 0, '', 1, 1);
    $p->Ln(2);
}
?>
