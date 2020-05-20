<?php
// Author: Irvandy Goutama
// Start Date: 31 Januari 2009

session_start();

  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
  //include_once "../header_pdf.php";
  include_once "../fpdf.php";
  
// *** Parameters ***
$prodi = GetSetVar('prodi');
$gelombang = GetaField('pmbperiod', "KodeID='".KodeID."' and NA", 'N', "PMBPeriodID");

//$thn = GetFields('tahun', "TahunID='$TahunID' and KodeID='".KodeID."' and ProdiID='$ProdiID' and ProgramID", $ProgramID, "*");

$lbr = 280;
$lbr1 = 100;
$lbr2 = 80;

$pdf = new FPDF();
$pdf->SetTitle("Kartu Ujian Seleksi Masuk");
$cardheight = 98;
$countToThree=3;
$s = "select PMBID, Nama, Alamat, RuangID, Pilihan1, Pilihan2 from `pmb` where Pilihan1 = '$prodi' and PMBPeriodID='$gelombang' and (RuangID like '_%' or RuangID is not NULL)";
$r = _query($s);
$n = _num_rows($r);

while($w = _fetch_array($r))
{
	if($countToThree == 3)
	{	$countToThree = 0;
		$pdf->AddPage('P', 'A4');
	}
	
	$currentheight = $cardheight*$countToThree;
	
	$pdf->SetFont('Helvetica', 'B', 9);
	$pdf->SetY($currentheight+28);
	$pdf->Cell($lbr1, 9, "KARTU UJIAN SELEKSI MASUK", 0, 0, 'C');
	$pdf->Cell($lbr2, 9, "JADWAL UJIAN SELEKSI MASUK", 0, 1, 'C');
	$pdf->Ln(9);

	// Tampilkan datanya

	HeaderLogo($currentheight, $pdf);
	AmbilKartu($w['PMBID'], $w['Nama'], $w['Alamat'], $w['Kota'], $w['Pilihan1'], $w['Pilihan2'], 37+$currentheight, $pdf);
	AmbilJadwal($w['RuangID'], $w['Pilihan1'], $gelombang, 37+$currentheight, $pdf);

	$pdf->SetY($currentheight+$cardheight);
	
	if($countToThree !=2)
	{
		$pdf->Cell(190, 0, "", 1, 1);
	}
	
	$countToThree++;
}
$pdf->Output();

// *** Functions ***

function AmbilKartu($PMBID, $Nama, $Alamat, $Kota, $Pilihan1, $Pilihan2, $Y, $p)
{  
	if(!empty($Pilihan1))
	{
		$s1 = "select ProdiID, Nama from `prodi` where ProdiID='$Pilihan1' ";
		$r1 = _query($s1);
		$w1 = _fetch_array($r1);
		$Pilihan1String = $Pilihan1.' - '.$w1['Nama'];
	}
	else { $Pilihan1String = '<Belum pilih>'; }
	if(!empty($Pilihan2))
	{
		$s1 = "select ProdiID, Nama from `prodi` where ProdiID='$Pilihan2' ";
		$r1 = _query($s1);
		$w1 = _fetch_array($r1);
		$Pilihan2String = $Pilihan2.' - '.$w1['Nama'];
	}
	else { $Pilihan2String = '<Belum pilih>'; }
	
	$t = 2; $mrg = 20; $pjg = 25;
  $p->SetFont('Helvetica', '', 8);
  $p->SetY($Y);
  
  $p->Cell($pjg, $t, 'Nomer Test', 0, 0, 'R');
  $p->Cell(3, $t, ':', 0);
  $p->Cell(90, $t, $PMBID, 0, 1);
  $p->Ln($t);
  $p->Cell($pjg, $t, 'Nama Peserta', 0, 0, 'R');
  $p->Cell(3, $t, ':', 0);
  $p->Cell(90, $t, $Nama, 0, 1);
  $p->Ln($t);
  
  // MultiCell($w, $h, $txt, $border=0, $align='J', $fill=false)
  //$sisfo = GetFields('identitas', 'Kode', KodeID, '*');
  $p->Cell($pjg, $t, 'Alamat', 0, 0, 'R');
  $p->Cell(3, $t, ':', 0);
  
  $_almt = explode("\n", $Alamat);
  for ($i = sizeof($_almt); $i <= 2; $i++)
    $Alamat .= "\r\n";
  $p->MultiCell(90, $t, $Alamat, 0);
  $p->Ln(4);
  
  $p->Cell($pjg, $t, 'Pilihan I', 0, 0, 'R');
  $p->Cell(3, $t, ':', 0);
  $p->Cell(90, $t, $Pilihan1String, 0, 1);
  $p->Ln($t);
  
  $p->Cell($pjg, $t, 'Pilihan II', 0, 0, 'R');
  $p->Cell(3, $t, ':', 0);
  $p->Cell(90, $t, $Pilihan2String, 0, 1);
  $p->Ln(6);
  
  $p->Cell(80, $t, 'Bekasi '.date('d').' '.strtoupper(date('M')).','.date('Y'), 0, 1, 'R');
  $p->Ln(3);

  $p->Cell($pjg, $t, 'Peserta', 0, 0, 'R');
  $p->Cell(47, $t, 'Petugas', 0, 1, 'R');
  $p->Ln(30);
}

function AmbilJadwal($ruang, $prodi, $gel, $Y, $p) {
  // Buat headernya dulu
  $p->SetFont('Helvetica', 'B', 8);
  $t = 4;

  $p->SetY($Y);
  $p->SetX(115);
  $p->Cell(20, $t, 'Hari/Tanggal', 1, 0, 'C');
  $p->Cell(14, $t, 'Jam', 1, 0, 'C');
  $p->Cell(30, $t, 'Mata Uji', 1, 0, 'C');
  $p->Cell(18, $t, 'Ruang', 1, 0, 'C');
  $p->Ln($t);

  // Ambil Isinya
  $s = "select pu1.TanggalUjian, pu2.Nama, pu1.Urutan 
			from prodiusm pu1 left outer join pmbusm pu2 on pu1.PMBUSMID = pu2.PMBUSMID
			where pu1.ProdiID = '$prodi' and pu1.PMBPeriodID = '$gel' ";
  $r = _query($s);

  $n = 0; 
  $t = 4;

  $arrRuang = explode("|", $ruang);
  
  while ($w = _fetch_array($r)) {
    
    $p->SetFont('Helvetica', '', 8);
    $p->SetX(115);
	$p->Cell(20, $t, substr($w['TanggalUjian'], 0, 10), 1, 0, 'C');
	$p->Cell(14, $t, substr($w['TanggalUjian'], 11, 5), 1, 0, 'C');
	$p->Cell(30, $t, $w['Nama'], 1, 0);
	$p->Cell(18, $t, $arrRuang[$n], 1, 0);
	$p->Ln($t);
	$n++;
  }
}

function HeaderLogo($Y, $p)
{	$pjg = 110;
	$p->SetY($Y);
	$logo = (file_exists("../img/logo.gif"))? "../img/logo.gif" : "img/logo.gif";
    $p->Image($logo, 12, $Y+8, 18);
	$p->SetY($Y+5);
    $p->SetFont("Helvetica", '', 8);
    $p->Cell($pjg, 6, "YAYASAN KESEJAHTERAAN ANAK BANGSA", 0, 1, 'C');
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

?>