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
$MhswID = $_REQUEST['MhswID'];

// *** Main
$pdf = new FPDF('P');
$pdf->SetTitle("Data Detail Mahasiswa - $thn");
$pdf->SetAutoPageBreak(true, 5);

if (empty($MhswID))
{   //die(ErrorMsg("Gagal", "Error."));
    $ss = "select MhswID from mhsw";
	$rr = _query($ss);
	while($ww = _fetch_array($rr))
	{	$pdf->AddPage('P');
		HeaderLogo("Data Detail Mahasiswa", $pdf, 'P');
		Isinya($ww['MhswID'], $pdf);
		TambahFoto($ww['MhswID'], $pdf);
	}
}
else
{
	$pdf->AddPage('P');
	HeaderLogo("Data Detail Mahasiswa", $pdf, 'P');
	Isinya($MhswID, $pdf);
	TambahFoto($MhswID, $pdf);	
}

$pdf->Output();

// *** Functions ***
function TambahFoto($MhswID, $p)
{	$Foto = GetaField('mhsw', "MhswID='$MhswID' and KodeID", KodeID, 'Foto');
	$FotoPath = (empty($Foto))? "gambar.gif" : $Foto;
	//$pic = (file_exists("../img/$FotoPath"))? "../img/$FotoPath" : "../$FotoPath";
	//$p->Image($pic, 150, 30, 25);
}
function Isinya($MhswID, $p) {
  $lbr = 190; $t = 6;
  //JudulKolomnya($p);
  $mhsw = GetFields('mhsw', "MhswID='$MhswID' and KodeID", KodeID, '*');
  
  $kolom1 = 40;
  $kolom1b = 40;
  $kolom2 = 50;
  
  $p->Ln(5);
  
  $p->SetFont('Helvetica', 'B', 12);
  $p->Cell($kolom1, $t, 'NPM :', 0, 0, 'R');
  $p->SetFont('Helvetica', '', 12);
  $p->Cell($kolom2, $t, $mhsw['MhswID'], 0, 1);
  
  $p->SetFont('Helvetica', 'B', 12);
  $p->Cell($kolom1, $t, 'Nama :', 0, 0, 'R');
  $p->SetFont('Helvetica', '', 12);
  $p->Cell($kolom2, $t, $mhsw['Nama'], 0, 1);
  
  $p->SetFont('Helvetica', 'B', 12);
  $p->Cell($kolom1, $t, 'Program :', 0, 0, 'R');
  $p->SetFont('Helvetica', '', 12);
  $p->Cell($kolom2, $t, $mhsw['ProgramID'].' - '.GetaField('program', "ProgramID='$mhsw[ProgramID]' and KodeID", KodeID, 'Nama'), 0, 1);
  
  $p->SetFont('Helvetica', 'B', 12);
  $p->Cell($kolom1, $t, 'Program Studi :', 0, 0, 'R');
  $p->SetFont('Helvetica', '', 12);
  $p->Cell($kolom2, $t, $mhsw['ProdiID'].' - '.GetaField('prodi', "ProdiID='$mhsw[ProdiID]' and KodeID", KodeID, 'Nama'), 0, 1);
  
  $p->SetFont('Helvetica', 'B', 12);
  $p->Cell($kolom1, $t, 'Dosen Pembimbing :', 0, 0, 'R');
  $p->SetFont('Helvetica', '', 12);
  $p->Cell($kolom2, $t, GetaField('dosen', "Login='$mhsw[DosenID]' and KodeID", KodeID, 'Nama'), 0, 1);
  
  $t = 5;
  $p->Cell(0, $t, '', 'B', 0, 0);
  $p->Ln(2*$t);
  
  $p->SetFont('Helvetica', 'B', 10);
  $p->Cell(50, $t, '', 0, 0);
  $p->Cell(90, $t, 'DATA PRIBADI', '', 0, 'C');
  $p->Cell(50, $t, '', 0, 1);
  $p->Ln(2);
  
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell($kolom1b, $t, 'Tempat Lahir :', 0, 0, 'R');
  $p->SetFont('Helvetica', '', 9);
  $p->Cell($kolom2, $t, $mhsw['TempatLahir'], 0, 0);
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell($kolom1b, $t, 'Alamat :', 0, 0, 'R');
  $p->SetFont('Helvetica', '', 9);
  $p->Cell($kolom2, $t, $mhsw['Alamat'], 0, 1);
  
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell($kolom1b, $t, 'Tanggal Lahir :', 0, 0, 'R');
  $p->SetFont('Helvetica', '', 9);
  $p->Cell($kolom2, $t, $mhsw['TanggalLahir'], 0, 0);  
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell($kolom1b, $t, 'RT :', 0, 0, 'R');
  $p->SetFont('Helvetica', '', 9);
  $p->Cell($kolom2, $t, $mhsw['RT'], 0, 1);
  
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell($kolom1b, $t, 'Jenis Kelamin :', 0, 0, 'R');
  $p->SetFont('Helvetica', '', 9);
  $p->Cell($kolom2, $t, GetaField('kelamin', 'Kelamin', $mhsw['Kelamin'], 'Nama'), 0, 0);
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell($kolom1b, $t, 'RW :', 0, 0, 'R');
  $p->SetFont('Helvetica', '', 9);
  $p->Cell($kolom2, $t, $mhsw['RW'], 0, 1);
  
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell($kolom1b, $t, 'Warga Negara :', 0, 0, 'R');
  $p->SetFont('Helvetica', '', 9);
  $p->Cell($kolom2, $t, $mhsw['WargaNegara'], 0, 0);
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell($kolom1b, $t, 'Kota/Kabupaten :', 0, 0, 'R');
  $p->SetFont('Helvetica', '', 9);
  $p->Cell($kolom2, $t, $mhsw['Kota'], 0, 1);
  
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell($kolom1b, $t, 'Agama :', 0, 0, 'R');
  $p->SetFont('Helvetica', '', 9);
  $p->Cell($kolom2, $t, GetaField('agama', "Agama", $mhsw['Agama'], 'Nama'), 0, 0);
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell($kolom1b, $t, 'Kode Pos :', 0, 0, 'R');
  $p->SetFont('Helvetica', '', 9);
  $p->Cell($kolom2, $t, $mhsw['KodePos'], 0, 1);
  
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell($kolom1b, $t, 'Status Sipil :', 0, 0, 'R');
  $p->SetFont('Helvetica', '', 9);
  $p->Cell($kolom2, $t, GetaField('statussipil', "StatusSipil", $mhsw['StatusSipil'], 'Nama'), 0, 0);
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell($kolom1b, $t, 'Propinsi :', 0, 0, 'R');
  $p->SetFont('Helvetica', '', 9);
  $p->Cell($kolom2, $t, $mhsw['Propinsi'], 0, 1);
  
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell($kolom1b, $t, 'Telepon :', 0, 0, 'R');
  $p->SetFont('Helvetica', '', 9);
  $p->Cell($kolom2, $t, $mhsw['Telepon'], 0, 0);
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell($kolom1b, $t, 'Negara :', 0, 0, 'R');
  $p->SetFont('Helvetica', '', 9);
  $p->Cell($kolom2, $t, $mhsw['Negara'], 0, 1);
  
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell($kolom1b, $t, 'Handphone :', 0, 0, 'R');
  $p->SetFont('Helvetica', '', 9);
  $p->Cell($kolom2, $t, $mhsw['Handphone'], 0, 0); 
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell($kolom1b, $t, 'Email :', 0, 0, 'R');
  $p->SetFont('Helvetica', '', 9);
  $p->Cell($kolom2, $t, $mhsw['Email'], 0, 1);
  
  
  // Riwayat Pendidikan
  $p->Cell(0, $t, '', 'B', 0, 0);
  $p->Ln(2*$t);
  
  $p->SetFont('Helvetica', 'B', 10);
  $p->Cell(50, $t, '', 0, 0);
  $p->Cell(90, $t, 'RIWAYAT PENDIDIKAN', '', 0, 'C');
  $p->Cell(50, $t, '', 0, 1);
  $p->Ln(2);
  
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell($kolom1b, $t, 'Sekolah Asal :', 0, 0, 'R');
  $p->SetFont('Helvetica', '', 9);
  $AsalSekolah = GetaField('asalsekolah', 'SekolahID', $mhsw['AsalSekolah'], 'Nama'); 
  $p->Cell($kolom2, $t, (empty($AsalSekolah))? $mhsw['AsalSekolah'] : $AsalSekolah, 0, 0);
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell($kolom1b, $t, 'Jurusan :', 0, 0, 'R');
  $p->SetFont('Helvetica', '', 9);
  $p->Cell($kolom2, $t, GetaField('jurusansekolah', "JurusanSekolahID", $mhsw['JurusanSekolah'], 'Nama').' - '.GetaField('jurusansekolah', "JurusanSekolahID", $mhsw['JurusanSekolah'], 'NamaJurusan'), 0, 1);
  
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell($kolom1b, $t, 'Tahun Lulus :', 0, 0, 'R');
  $p->SetFont('Helvetica', '', 9);
  $p->Cell($kolom2, $t, $mhsw['TahunLulus'], 0, 0);
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell($kolom1b, $t, 'Nilai Sekolah :', 0, 0, 'R');
  $p->SetFont('Helvetica', '', 9);
  $p->Cell($kolom2, $t, $mhsw['NilaiSekolah'], 0, 1);

  
  $p->Cell(0, $t, '', 'B', 0, 0);
  $p->Ln(2*$t);
    
  $p->SetFont('Helvetica', 'B', 10);
  $p->Cell(50, $t, '', 0, 0);
  $p->Cell(90, $t, 'ORANG TUA', '', 0, 'C');
  $p->Cell(50, $t, '', 0, 1);
  $p->Ln(2);
  
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell($kolom1b, $t, 'Nama Ayah :', 0, 0, 'R');
  $p->SetFont('Helvetica', '', 9);
  $p->Cell($kolom2, $t, $mhsw['NamaAyah'], 0, 0);
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell($kolom1b, $t, 'NamaIbu :', 0, 0, 'R');
  $p->SetFont('Helvetica', '', 9);
  $p->Cell($kolom2, $t, $mhsw['NamaIbu'], 0, 1);
  
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell($kolom1b, $t, 'Agama Ayah :', 0, 0, 'R');
  $p->SetFont('Helvetica', '', 9);
  $p->Cell($kolom2, $t, GetaField('agama', "Agama", $mhsw['AgamaAyah'], 'Nama'), 0, 0); 
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell($kolom1b, $t, 'AgamaIbu :', 0, 0, 'R');
  $p->SetFont('Helvetica', '', 9);
  $p->Cell($kolom2, $t, GetaField('agama', "Agama", $mhsw['AgamaIbu'], 'Nama'), 0, 1);
  

  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell($kolom1b, $t, 'Pendidikan Ayah :', 0, 0, 'R');
  $p->SetFont('Helvetica', '', 9);
  $p->Cell($kolom2, $t, GetaField('pendidikanortu', "Pendidikan", $mhsw['PendidikanAyah'], 'Nama'), 0, 0); 
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell($kolom1b, $t, 'Pendidikan Ibu :', 0, 0, 'R');
  $p->SetFont('Helvetica', '', 9);
  $p->Cell($kolom2, $t, GetaField('pendidikanortu', "Pendidikan", $mhsw['PendidikanIbu'], 'Nama'), 0, 1);
  
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell($kolom1b, $t, 'Pekerjaan Ayah :', 0, 0, 'R');
  $p->SetFont('Helvetica', '', 9);
  $p->Cell($kolom2, $t, GetaField('pekerjaanortu', "Pekerjaan", $mhsw['PekerjaanAyah'], 'Nama'), 0, 0); 
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell($kolom1b, $t, 'Pekerjaan Ibu :', 0, 0, 'R');
  $p->SetFont('Helvetica', '', 9);
  $p->Cell($kolom2, $t, GetaField('pekerjaanortu', "Pekerjaan", $mhsw['PekerjaanIbu'], 'Nama'), 0, 1);
  
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell($kolom1b, $t, 'Status Hidup Ayah :', 0, 0, 'R');
  $p->SetFont('Helvetica', '', 9);
  $p->Cell($kolom2, $t, $mhsw['StatusHidupAyah'], 0, 0); 
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell($kolom1b, $t, 'Status Hidup Ibu :', 0, 0, 'R');
  $p->SetFont('Helvetica', '', 9);
  $p->Cell($kolom2, $t, $mhsw['StatusHidupIbu'], 0, 1);
  
  $p->Ln($t);
  
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell($kolom1b, $t, 'Alamat :', 0, 0, 'R');
  $p->SetFont('Helvetica', '', 9);
  $p->Cell($kolom2, $t, $mhsw['AlamatOrtu'], 0, 0);
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell($kolom1b, $t, 'RT :', 0, 0, 'R');
  $p->SetFont('Helvetica', '', 9);
  $p->Cell($kolom2, $t, $mhsw['RTOrtu'], 0, 1);
  
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell($kolom1b, $t, 'Kota :', 0, 0, 'R');
  $p->SetFont('Helvetica', '', 9);
  $p->Cell($kolom2, $t, $mhsw['KotaOrtu'], 0, 0);
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell($kolom1b, $t, 'RW :', 0, 0, 'R');
  $p->SetFont('Helvetica', '', 9);
  $p->Cell($kolom2, $t, $mhsw['RWOrtu'], 0, 1);
  
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell($kolom1b, $t, 'Kode Pos :', 0, 0, 'R');
  $p->SetFont('Helvetica', '', 9);
  $p->Cell($kolom2, $t, $mhsw['KodePosOrtu'], 0, 0);
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell($kolom1b, $t, 'Telepon :', 0, 0, 'R');
  $p->SetFont('Helvetica', '', 9);
  $p->Cell($kolom2, $t, $mhsw['TeleponOrtu'], 0, 1);
  
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell($kolom1b, $t, 'Propinsi :', 0, 0, 'R');
  $p->SetFont('Helvetica', '', 9);
  $p->Cell($kolom2, $t, $mhsw['PropinsiOrtu'], 0, 0);
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell($kolom1b, $t, 'Handphone :', 0, 0, 'R');
  $p->SetFont('Helvetica', '', 9);
  $p->Cell($kolom2, $t, $mhsw['RWOrtu'], 0, 1);

  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell($kolom1b, $t, 'Negara :', 0, 0, 'R');
  $p->SetFont('Helvetica', '', 9);
  $p->Cell($kolom2, $t, $mhsw['NegaraOrtu'], 0, 0);
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell($kolom1b, $t, 'Email :', 0, 0, 'R');
  $p->SetFont('Helvetica', '', 9);
  $p->Cell($kolom2, $t, $mhsw['EmailOrtu'], 0, 1);
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
function Tanggal_mhs($string, $format='Y-m-d', $divider='-')
{	$arrBulan = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
	$arrMonth = array();
	$arrMonth['Jan'] = 'Januari';
	$arrMonth['Feb'] = 'Februari';
	$arrMonth['Mar'] = 'Maret';
	$arrMonth['Apr'] = 'April';
	$arrMonth['May'] = 'Mei';
	$arrMonth['Jun'] = 'Juni';
	$arrMonth['Jul'] = 'Juli';
	$arrMonth['Aug'] = 'Agustus';
	$arrMonth['Sep'] = 'September';
	$arrMonth['Oct'] = 'Oktober';
	$arrMonth['Nov'] = 'November';
	$arrMonth['Dec'] = 'Desember';
	
	$pos = 0; $tahun = ''; $bulan = ''; $hari = '';
	$formatArr = explode($divider, $format);
	$stringArr = explode($divider, $string);
	$n = 0;
	foreach($formatArr as $part)
	{	
		if($part == 'Y') 
		{	$tahun = substr($string, $pos, 4);
			$pos += 4+1;
		}
		else if($part == 'y') 
		{	$temptahun = substr($string, $pos, 2);
			$pos += 2+1;
			if($temptahun > 30) $tahun = '19'.$temptahun;
			else $tahun = '20'.$temptahun;
		}
		else if($part == 'm') 
		{	$bulan = $arrBulan[substr($string, $pos, 2)+0];
			$pos += 2+1;
		}
		else if($part == 'M') 
		{	$bulan = $arrMonth[substr($string, $pos, 3)];
			$pos += 3+1;
		}
		else if($part == 'd') 
		{	$hari = substr($string, $pos, 2);
			$pos += 2+1;
		}
		/*if($part == 'Y') $tahun = $stringArr[$n];	
		else if($part == 'y') 
		{	if($tahun > 30) $tahun = '19'.$stringArr[$n];
			else $tahun = '20'.$stringArr[$n];
		}
		else if($part == 'm') $bulan = $arrBulan[$stringArr[$n]+0];
		else if($part == 'M') $bulan = $arrMonth[$stringArr[$n]];
		else if($part == 'd') $hari = $stringArr[$n];
		$n++;*/
	}
	return $hari.' '.$bulan.' '.$tahun;
}
?>
