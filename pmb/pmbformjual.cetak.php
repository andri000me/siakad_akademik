<?php
// Author : Irvandy Goutama
// Email  : irvandygoutama@gmail.com
// Start  : 30 Mei 2009

session_start();

  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
  include_once "../fpdf.php";

// *** Parameters ***
$id = $_REQUEST['id'];
$gel = $_REQUEST['gel'];
	
$lbr = 280;

$pdf = new FPDF();
$pdf->SetTitle("Formulir Pendaftaran");

// Buat semua halaman tanpa footer

$pdf->AddPage('P');
$pdf->SetAutoPageBreak(true, 5);

// Buat Header Logo
HeaderLogo('', $pdf, 'P');
// Buat Judul
$pdf->SetFont('Helvetica', 'B', 12);
$pmbformulir = GetFields('pmbformulir', "PMBFormulirID='$id' and KodeID", KodeID, "*");
$pdf->Cell(0, 10, $pmbformulir['Nama'], 0, 1, 'C');
// Tampilkan isiannya  
Isinya($pmbformulir, $pdf);

//Buat halaman terakhir dengan footer
$pdf->Output();

// *** Functions ***
function Isinya($pmbformulir, $p) {
  $t = 3.8; 
  $lettersize = 8;
  //$style = array('width' => 0.5, 'cap' => 'round', 'join' => 'round', 'dash' => '0, 10', 'color' => array(0, 0, 0));
  //SetLineStyle($style, $p);
  
  $arrBaris = array();  
  // I. Identifikasi Pendaftaran
  $arrBaris[] = array('R', 'Identifikasi Pendaftaran');
  $arrBaris[] = array();
  $arrBaris[] = array('N', 'No. Formulir', ':', '=LINES=', '=LINES=', '=LINES=');
  $arrBaris[] = array('N', 'Nama Calon Mahasiswa', ':', '=LINES=', '=LINES=', '=LINES=');
  $arrBaris[] = array('N', 'Alamat Lengkap', ':', '=LINES=', '=LINES=', '=LINES=');
  $arrBaris[] = array('', '', '', '=LINES=', 'RT =LINES=', 'RW =LINES=');
  $arrBaris[] = array('', '', '', 'Kota =LINES=', 'Propinsi =LINES=', 'Kode Pos =LINES=');
  $arrBaris[] = array('N', 'Telepon Rumah / HP', ':', '=LINES=', '=LINES=', '=LINES=');
  $arrBaris[] = array('N', 'Alamat Email', ':', '=LINES=', '=LINES=', '=LINES=');
  $arrBaris[] = array('N', 'Khusus untuk calon dari luar kota');
  $arrBaris[] = array('', 'Alamat yang bisa dihubungi', ':', '=LINES=', '=LINES=', '=LINES=');
  $arrBaris[] = array('', '', '', '=LINES=', 'RT=LINES=', 'RW=LINES=');
  $arrBaris[] = array('', '', '', 'Kota=LINES=', 'Propinsi=LINES=', 'Kode Pos=LINES=');  
  $arrBaris[] = array('N', 'Tempat Tinggal', ':', 'Asrama/Sendiri/Keluarga/Indekos/Lain-lain *)');
  $arrBaris[] = array('N', 'Status', ':', 'Belum Menikah/Menikah/Janda/Duda *)');
  $arrBaris[] = array('N', 'Jenis Kelamin', ':', 'Pria/Wanita *)');
  $arrBaris[] = array('N', 'Agama', ':', '=LINES=', '=LINES=', '=LINES=');
  $arrBaris[] = array('N', 'Pilihan Program Studi', ':', '=LINES=');
  $arrBaris[] = array('N', 'Jenjang Studi', ':', 'S1/D3');
  $arrBaris[] = array('N', 'Kewarganegaraan', ':', 'WNI/WNA *)');
  $arrBaris[] = array();
  
  // II . Data Otang Tua/ Wali
  $arrBaris[] = array('R', 'Data Orang Tua/Wali');
  $arrBaris[] = array();
  $arrBaris[] = array('N', 'Nama Orang Tua/Wali', ':', '=LINES=', '=LINES=', '=LINES=');
  $arrBaris[] = array('N', 'Alamat Lengkap', ':', '=LINES=', '=LINES=', '=LINES=');
  $arrBaris[] = array('', '', '', '=LINES=', 'RT=LINES=', 'RW=LINES=');
  $arrBaris[] = array('', '', '', 'Kota=LINES=', 'Propinsi=LINES=', 'Kode Pos=LINES=');
  $arrBaris[] = array('N', 'Telepon Rumah/HP', ':', '=LINES=', '=LINES=', '=LINES=');
  $arrBaris[] = array('N', 'Pekerjaan Orang Tua/Wali', ':', '=LINES=', '=LINES=', '=LINES=');
  $arrBaris[] = array('N', 'Pendidikan Orang Tua/Wali', ':', '=LINES=', '=LINES=', '=LINES=');
  $arrBaris[] = array('N', 'Keadaan Ayah/Ibu', ':', 'Ayah', ': Masih Hidup/Sudah Meninggal *)');
  $arrBaris[] = array('', '', '', 'Ibu', ': Masih Hidup/Sudah Meninggal *)');
  $arrBaris[] = array('N', 'Biaya Studi', ':', 'Orang Tua/Wali/Ikatan Dinas/Sendiri/Beasiswa/Lain2 *)');
  $arrBaris[] = array();
  
  // III. Data Ijazah Terakhir
  $arrBaris[] = array('R', 'Data Ijazah Terakhir');
  $arrBaris[] = array();
  $arrBaris[] = array('N', 'Nama Asal Sekolah', ':', '=LINES=', '=LINES=', '=LINES=');
  $arrBaris[] = array('N', 'Alamat Sekolah', ':', '=LINES=', '=LINES=', '=LINES=');
  $arrBaris[] = array('N', 'Nilai UAN', ':', '=LINES=', '=LINES=', '=LINES=');
  $arrBaris[] = array('N', 'Tahun Ijazah', ':', '=LINES=', '=LINES=', '=LINES=');
  $arrBaris[] = array();
  
  $arrBaris[] = array('R', 'Detail Pekerjaan (Bila sudah bekerja)');
  $arrBaris[] = array();
  $arrBaris[] = array('N', 'Nama Perusahaan', ':', '=LINES=', '=LINES=', '=LINES=');
  $arrBaris[] = array('N', 'Alamat Perusahaan', ':', '=LINES=', '=LINES=', '=LINES=');
  $arrBaris[] = array('N', 'No. Telepon dan Fax', ':', '=LINES=', '=LINES=', '=LINES=');
  $arrBaris[] = array('N', 'Jabatan Saat Ini', ':', '=LINES=', '=LINES=', '=LINES=');
  $arrBaris[] = array();
  
  $Kolom1 = 50;
  $IndenKolom1 = 5;
  $Kolom2 = 5;
  $Kolom3 = 45;
  $Kolom4 = 45;
  $Kolom5 = 45;
  
  $n = 0; $nr = 0;
  foreach($arrBaris as $baris)
  { 
	$type = $baris[0];
	if($type=='R')
	{	$style='B';
		$Kolom1x = $Kolom1;
		$nr++;
		$numbering = UbahKeRomawiLimit99($nr).'.';
	}
	else if($type=='N')
	{	$style='';
		$Kolom1x = $Kolom1-$IndenKolom1;
		$p->Cell($IndenKolom1, $t, '', 0, 0);
		$n++;
		$numbering = $n.'.';
	}
	else
	{   $style='';
		$Kolom1x = $Kolom1-$IndenKolom1;
		$p->Cell($IndenKolom1, $t, '', 0, 0);
		$numbering = '';
	}
	$p->SetFont('Helvetica', $style, $lettersize);
    
	// Print kolom 1
	$p->Cell($Kolom1x, $t, $numbering.' '.$baris[1], 0, 0);
	
	// Print kolom 2
	$p->Cell($Kolom2, $t, $baris[2], 0, 0);
    
	$arrLines = array();
	// Print kolom 3
	if($baris[3] == '=LINES=') $arrLines[] = array($p->GetX(), $Kolom3);
	else if(strpos($baris[3], '=LINES=')>0) 
	{	$stringwidth = $p->GetStringWidth(TRIM(str_replace('=LINES=', '', $baris[3])));
		$arrLines[] = array($p->GetX()+$stringwidth, $Kolom3-$stringwidth);
	}
	$p->Cell($Kolom3, $t, str_replace('=LINES=', '', $baris[3]), 0, 0);
	
	// Print kolom 4
	if(TRIM($baris[4]) == '=LINES=') $arrLines[] = array($p->GetX(), $Kolom4);
	else if(strpos($baris[4], '=LINES=')>0) 
	{	$stringwidth = $p->GetStringWidth(TRIM(str_replace('=LINES=', '', $baris[4])));
		$arrLines[] = array($p->GetX()+$stringwidth, $Kolom4-$stringwidth);
	}
	$p->Cell($Kolom4, $t, TRIM(str_replace('=LINES=', '', $baris[4])), 0, 0);
	
	if(TRIM($baris[5]) == '=LINES=') $arrLines[] = array($p->GetX(), $Kolom5);
	else if(strpos($baris[5], '=LINES=')>0) 
	{	$stringwidth = $p->GetStringWidth(TRIM(str_replace('=LINES=', '', $baris[5])));
		$arrLines[] = array($p->GetX()+$stringwidth, $Kolom5-$stringwidth);
	}
	// Print kolom 5 
	$p->Cell($Kolom5, $t, TRIM(str_replace('=LINES=', '', $baris[5])), 0, 0);
	
	// Print garis
	if(!empty($arrLines))
	{
		$Y = $p->GetY();
		$count = count($arrLines);
		foreach($arrLines as $line)
		{	$p->SetXY($line[0]+1, $Y);
			$p->Cell($line[1], $t-1.3, '', 'B', 0);
		}
	}
	
	$p->Ln($t);
  }
  
  // Tambahkan Pas Foto dann Tanda Tanda Tangan di bagian paling bawah
  $p->Ln($t);
  $p->Cell(10, $t, '', 0, 0);
  $p->Cell(25, 30, '3x4', 1, 0, 'C');
  
  $p->Cell(100, $t, '', 0, 0);
  $p->Cell(20, $t, GetaField('identitas', 'Kode', KodeID, 'Kota').', ______________');
  $p->Ln(8);
  
  $p->Cell(140, $t, '', 0, 0);
  $p->Cell(20, $t, 'Tanda Tangan Pendaftar', 0, 0);
  $p->Ln(27);
  $p->Cell(140, $t, '', 0, 0);
  $p->Cell(20, $t, '(______________________)', 0, 0);
  
  $p->Ln(12);
  $p->SetFont('Helvetica', 'B', $lettersize);
  $p->Cell(0, $t, 'Keterangan : *) : Lingkari pilihan anda', 0, 0);
  $p->Ln($t);
  
  $arrLampirkan = array();
  if($pmbformulir['Prasyarat'] == 'Y')
  {	$arrPrasyarat = explode('|', $pmbformulir['PrasyaratExtra']);
	foreach($arrPrasyarat as $persyarat)
	{	$arr = explode('~', $persyarat);
		// $arr[0] adalah PMBFormSyaratID, $arr[1] adalah 'Y' atau 'N' digunakan, $arr[2] adalah Tambahan input untuk prasyarat
		
		$pmbformsyarat = GetFields('pmbformsyarat', "PMBFormSyaratID='$arr[0]' and KodeID", KodeID, "*");
		if($arr[1] == 'Y')
		{   if($pmbformsyarat['AdaScript']=='N')
			{	$arrLampirkan[] = $pmbformsyarat['Nama'];
			}
		}
	}
  }
  
  $p->Cell(20, $t, 'Lampirkan :', 0, 0);
  
  $n = 0;
  foreach($arrLampirkan as $lampirkan)
  {	$n++;
	if($n == 1) $p->Cell(0, $t, $lampirkan, 0, 0);
	else 
	{	$p->Cell(20, $t, '', 0, 0);
		$p->Cell(0, $t, $lampirkan, 0, 0);
	}
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
		$p->Cell($pjg, 7, 'Formulir Pendaftaran', 0, 1, 'C');
	}
	else
	{	$p->SetFont("Helvetica", 'B', 12);
		$p->Cell(80, 7, 'Formulir Pendaftaran', 0, 1, 'C');
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

function UbahKeRomawiLimit99($integer)
{	$arrRomanOnes = array('I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX');
	if($integer<10) return $arrRomanOnes[$integer-1]; 
	else
	{	if($integer<100)
		{	$arrRomanTens = array('X', 'XX', 'XXX', 'XL', 'L', 'LX', 'LXX', 'LXXX', 'XC');
			$integertens = floor($integer/10);
			return $arrRomanTens[$integertens-1].$arrRomanOnes[$integer-1];
		}
		else
		{	return 'FAIL';
		}
	}
}

function SetLineStyle($style, $p) {
	extract($style);
	if (isset($width)) {
		$width_prev = $p->LineWidth;
		$p->SetLineWidth($width);
		$p->LineWidth = $width_prev;
	}
	if (isset($cap)) {
		$ca = array('butt' => 0, 'round'=> 1, 'square' => 2);
		if (isset($ca[$cap]))
			$p->_out($ca[$cap] . ' J');
	}
	if (isset($join)) {
		$ja = array('miter' => 0, 'round' => 1, 'bevel' => 2);
		if (isset($ja[$join]))
			$p->_out($ja[$join] . ' j');
	}
	if (isset($dash)) {
		$dash_string = '';
		if ($dash) {
			if(ereg('^.+, ', $dash))
				$tab = explode(', ', $dash);
			else
				$tab = array($dash);
			$dash_string = '';
			foreach ($tab as $i => $v) {
				if ($i > 0)
					$dash_string .= ' ';
				$dash_string .= sprintf('%.2f', $v);
			}
		}
		if (!isset($phase) || !$dash)
			$phase = 0;
		$p->_out(sprintf('[%s] %.2f d', $dash_string, $phase));
	}
	if (isset($color)) {
		list($r, $g, $b) = $color;
		$p->SetDrawColor($r, $g, $b);
	}
}
?>
