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
$PMBID = $_REQUEST['id'];
$gelombang = GetaField('pmbperiod', "KodeID='".KodeID."' and NA", 'N', "PMBPeriodID");

$prodi = GetaField('pmb', 'PMBID', $PMBID, 'Pilihan1' );

$lbr = 280;
$lbr1 = 100;
$lbr2 = 80;

$pdf = new FPDF();
$pdf->SetTitle("Kartu Ujian Seleksi Masuk");
$pdf->AddPage('P', 'A4');
$pdf->SetFont('Helvetica', 'B', 9);
$pdf->SetY(28);
$pdf->Cell($lbr1, 9, "KARTU UJIAN SELEKSI MASUK", 0, 0, 'C');
$pdf->Cell($lbr2, 9, "JADWAL UJIAN SELEKSI MASUK", 0, 0, 'C');
$pdf->Ln(9);

$s = "select PMBID, Nama, Alamat, RuangID, Pilihan1, Pilihan2, Pilihan3, PMBFormulirID from `pmb` where PMBID='$PMBID' and KodeID='".KodeID."'";
$r = _query($s);
$w = _fetch_array($r);
$prodi1 = $w['Pilihan1'];

$s = "update pmb set CetakKartu = CetakKartu+1 where PMBID='$PMBID' and KodeID='".KodeID."'";
$r = _query($s);

// Tampilkan datanya
HeaderLogo($pdf);
AmbilKartu($w['PMBID'], $w['Nama'], $w['Alamat'], $w['Kota'], $w['Pilihan1'], $w['Pilihan2'], $w['Pilihan3'], 37, $pdf);
AmbilJadwal($w, $gelombang, 37, $pdf);

$pdf->SetY(98);
$pdf->Cell(190, 0, "", 1, 1);

$pdf->Output();

// *** Functions ***

function AmbilKartu($PMBID, $Nama, $Alamat, $Kota, $Pilihan1, $Pilihan2, $Pilihan3, $Y, $p)
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
	
	$t = 2; $mrg = 20;
	$pjg = 25;
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
  
  //$sisfo = GetFields('identitas', 'Kode', KodeID, '*');
  // MultiCell($w, $h, $txt, $border=0, $align='J', $fill=false)
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
  
  if($Pilihan2String != '<Belum pilih>'){
  $p->Cell($pjg, $t, 'Pilihan II', 0, 0, 'R');
  $p->Cell(3, $t, ':', 0);
  $p->Cell(90, $t, $Pilihan2String, 0, 1);
  $p->Ln($t);
  }
  
  $p->Ln(6);
  
  $KotaIdentitas = GetaField('identitas', "Kode", KodeID, 'Kota');
  
  $p->Cell(80, $t, $KotaIdentitas.' '.date('d').' '.date('M').','.date('Y'), 0, 1, 'R');
  $p->Ln(3);

  $p->Cell($pjg, $t, 'Peserta', 0, 0, 'R');
  $p->Cell(47, $t, 'Petugas', 0, 1, 'R');
  $p->Ln(30);
}
function AmbilJadwal($pmb, $gel, $Y, $p) {
  // Buat headernya dulu
  $p->SetFont('Helvetica', 'B', 8);
  $t = 4;

  $p->SetY($Y);
  $p->SetX(100);
  $p->Cell(20, $t, 'Hari/Tanggal', 1, 0, 'C');
  $p->Cell(22, $t, 'Jam', 1, 0, 'C');
  $p->Cell(25, $t, 'Mata Uji', 1, 0, 'C');
  $p->Cell(35, $t, 'Ruang', 1, 0, 'C');
  $p->Ln($t);
  
  $pmbformulir = GetFields('pmbformulir', "PMBFormulirID='$pmb[PMBFormulirID]' and KodeID", KodeID, 'USM, Wawancara');
  
  // Ambil Isinya dari Ujian Saringan Masuk
  if($pmbformulir['USM'] == 'Y')
  {
	  $s = "select ru.*, r.Nama as _Ruang, pu.TanggalUjian, pu.JamMulai, pu.JamSelesai, pu2.Nama
				from ruangusm ru 
					left outer join ruang r on r.RuangID=ru.RuangID and r.KodeID='".KodeID."'
					left outer join prodiusm pu on ru.ProdiUSMID = pu.ProdiUSMID
					left outer join pmbusm pu2 on pu.PMBUSMID = pu2.PMBUSMID
				where ru.PMBID='$pmb[PMBID]' and ru.PMBPeriodID='$gel' and ru.KodeID='".KodeID."'
				order by pu.TanggalUjian, pu.JamMulai, pu.JamSelesai";
	  $r = _query($s);
	  $n = 0; 
	  $t = 4;
	
	  while ($w = _fetch_array($r)) {
		$Jam = substr($w['JamMulai'], 0, 5).' - '.substr($w['JamSelesai'], 0, 5);
		$p->SetFont('Helvetica', '', 8);
		$p->SetX(100);
		$p->Cell(20, $t, substr($w['TanggalUjian'], 0, 10), 1, 0, 'C');
		$p->Cell(22, $t, $Jam, 1, 0, 'C');
		$p->Cell(25, $t, $w['Nama'], 1, 0);
		$p->Cell(35, $t, $w['_Ruang'], 1, 0);
		$p->Ln($t);
		$n++;
	  }
	}

	// Ambil isi jadwal wawancara
	if($pmbformulir['Wawancara'] == 'Y')
	{	$s = "select w.*, r.Nama as _Ruang
				from wawancara w 
					left outer join ruang r on r.RuangID=w.RuangID
				where w.PMBID='$pmb[PMBID]' and w.KodeID='".KodeID."'
				order by w.Tanggal, w.JamMulai, w.JamSelesai";
		$r = _query($s);
		while($w = _fetch_array($r))
		{	$Jam = substr($w['JamMulai'], 0, 5).' - '.substr($w['JamSelesai'], 0, 5);
			$p->SetFont('Helvetica', '', 8);
			$p->SetX(100);
			$p->Cell(20, $t, $w['Tanggal'], 1, 0, 'C');
			$p->Cell(22, $t, $Jam, 1, 0, 'C');
			$p->Cell(25, $t, 'Wawancara', 1, 0);
			$p->Cell(35, $t, $w['_Ruang'], 1, 0);
			$p->Ln($t);
			$n++;
		}	
	}
}

function HeaderLogo($p)
{	$pjg = 110;
	$logo = (file_exists("../img/logo.jpg"))? "../img/logo.jpg" : "img/logo.jpg";
    $identitas = GetFields('identitas', "Kode", KodeID, 'Nama, Alamat1, Telepon, Fax');
	$p->Image($logo, 12, 8, 18);
	$p->SetY(5);
    $p->SetFont("Helvetica", '', 8);
    $p->Cell($pjg, 6, "$identitas[Yayasan]", 0, 1, 'C');
    $p->SetFont("Helvetica", 'B', 10);
    $p->Cell($pjg, 7, "$identitas[Nama]", 0, 1, 'C');
    
    $p->SetFont("Helvetica", 'I', 6);
    $p->Cell($pjg, 3, "$identitas[Alamat1]", 0, 1, 'C');
    $p->Cell($pjg, 3,
      "Telp. $identitas[Telepon], Fax. $identitas[Fax]", 0, 1, 'C');
    $p->Ln(3);
    $p->Cell(190, 0, "", 1, 1);
    $p->Ln(2);
}

?>

