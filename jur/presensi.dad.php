<?php
session_start();

  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
  include_once "../fpdf.php";

// *** Parameters ***
	$JadwalID = $_REQUEST['JadwalID'];
	$SKS = $_REQUEST['SKS'];
	$jdwl = GetFields("jadwal j
		left outer join dosen d on d.Login = j.DosenID and d.KodeID = '".KodeID."'
		left outer join prodi prd on prd.ProdiID = j.ProdiID and prd.KodeID = '".KodeID."'
		left outer join program prg on prg.ProgramID = j.ProgramID and prg.KodeID = '".KodeID."'
		left outer join mk mk on mk.MKID = j.MKID
		LEFT OUTER JOIN kelas k ON k.KelasID = j.NamaKelas 
		left outer join jadwaluts jut on jut.JadwalID = j.JadwalID
		left outer join jadwaluas jua on jua.JadwalID = j.JadwalID
		left outer join hari huts on huts.HariID = date_format(jut.Tanggal, '%w')
		left outer join hari huas on huas.HariID = date_format(jua.Tanggal, '%w')
		",
		"j.JadwalID", $JadwalID,
		"j.*, concat(d.Gelar1, ' ', d.Nama, ', ', d.Gelar) as DSN, d.NIDN,
		prd.Nama as _PRD, prg.Nama as _PRG, mk.Sesi,
		date_format(jua.Tanggal, '%d-%m-%Y') as _UASTanggal,
		date_format(jut.Tanggal, '%d-%m-%Y') as _UTSTanggal,
		date_format(jut.Tanggal, '%w') as _UTSHari,
		date_format(jua.Tanggal, '%w') as _UASHari,
		huts.Nama as HRUTS,
		huas.Nama as HRUAS,
		LEFT(jut.JamMulai, 5) as _UTSJamMulai, LEFT(jut.JamSelesai, 5) as _UTSJamSelesai,
		LEFT(jua.JamMulai, 5) as _UASJamMulai, LEFT(jua.JamSelesai, 5) as _UASJamSelesai, 
		k.Nama AS namaKelas
		");

if (empty($jdwl))
  die(ErrorMsg("Error",
    "Data jadwal tidak ditemukan.<br />
    Hubungi Sysadmin untuk informasi lebih lanjut.
    <hr size=1 color=silver />
    <input type=button name='Tutup' value='Tutup'
      onClick=\"window.close()\" />"));

$lbr = 280;

$pdf = new FPDF();
$pdf->SetTitle("Daftar Presensi Dosen");

/*
if($SKS == 2) $totalentry = 14;
else if($SKS == 3) $totalentry = 16;
else if($SKS == 4) $totalentry = 14;
else $totalentry = 14;
*/
$totalentry = GetaField('jadwal', "KodeID='".KodeID."' and JadwalID", $_REQUEST['JadwalID'], 'RencanaKehadiran');

// Buat semua halaman tanpa footer

$s1 = "select k.MhswID, m.Nama
	from krs k
	  left outer join mhsw m on m.MhswID = k.MhswID and m.KodeID = '".KodeID."'
	where k.JadwalID = '$jdwl[JadwalID]'
	order by k.MhswID";
$r1 = _query($s1);

$pdf->AddPage('P');
$pdf->SetAutoPageBreak(true, 5);
// Buat Header Logo
HeaderLogo("DAFTAR PRESENSI DOSEN", $pdf, 'P');
// Buat header dulu
BuatHeader($jdwl, $pdf);
// Tampilkan datanya
AmbilDetail($jdwl, $totalentry, $pdf);

//Buat halaman terakhir dengan footer
$pdf->Output();

// *** Functions ***
function AmbilDetail($jdwl, $numberofentry, $p) {
  $t = 13; 
  $p->SetFont('Helvetica', '', 9);
  for($i = 1; $i <= $numberofentry; $i++) {
    $p->Cell(30, $t, $i, 'LBR', 0, 'C');
    $p->Cell(40, $t, '', 'BR', 0);
    $p->Cell(80, $t, '', 'BR', 0);
	$p->Cell(40, $t, '', 'BR', 0);
	$p->Ln($t);
  }
}
function BuatHeaderTabel($p) {
  $t = 6;
  $s = 12;
  
  $p->SetFont('Helvetica', 'B', 10);
  
  $p->Cell(30, $t, 'PERTEMUAN', 1, 0, 'C');
  $p->Cell(40, $t, 'TANGGAL', 1, 0, 'C');
  $p->Cell(80, $t, 'MATERI KULIAH', 1, 0,'C');
  $p->Cell(40, $t, 'PARAF', 1, 0, 'C');
  $p->Ln($t);
}
function BuatHeader($jdwl, $p) {
  $NamaTahun = GetaField('tahun', "KodeID='".KodeID."' and TahunID='$jdwl[TahunID]' and ProdiID",
    $jdwl['ProdiID'], 'Nama');
  $t = 6; $lbr = 200;

  $arr = array();
  $arr[] = array('Mata Kuliah', ':', $jdwl['MKKode'] . ' - ' . $jdwl['Nama']);
  $arr[] = array('Kelas / Thn Akd', ':', $jdwl['NamaKelas'] . ' ( Ruang: ' . $jdwl['RuangID'] . ' ) '.
    ' / ' . $NamaTahun, 
    'Dosen Pengasuh', ':', $jdwl['DSN']);
  $arr[] = array('Semester / SKS', ':', $jdwl['Sesi'] . ' / ' . $jdwl['SKS'],
    'Hari / Tgl UTS', ':', $jdwl['HRUTS'] . 
    ' / ' . $jdwl['_UTSTanggal'] .
    ' / ' . $jdwl['_UTSJamMulai'] . ' - ' . $jdwl['_UTSJamSelesai']);
  $arr[] = array('Program Studi', ':', $jdwl['_PRD'] . ' ('. $jdwl['_PRG'].')',
	'Hari / Tgl UAS', ':', $jdwl['HRUAS'] . 
    ' / ' . $jdwl['_UASTanggal'] .
    ' / ' . $jdwl['_UASJamMulai'] . ' - ' . $jdwl['_UASJamSelesai']);
  
  // Tampilkan
  $p->SetFont('Helvetica', '', 8);
  foreach ($arr as $a) {
    // Kolom 1
    $p->SetFont('Helvetica', 'I', 8);
    $p->Cell(25, $t, $a[0], 0, 0);
    $p->Cell(4, $t, $a[1], 0, 0, 'C');
    $p->SetFont('Helvetica', 'B', 8);
    $p->Cell(73, $t, $a[2], 0, 0);
    // Kolom 2
    $p->SetFont('Helvetica', 'I', 8);
    $p->Cell(25, $t, $a[3], 0, 0);
    $p->Cell(4, $t, $a[4], 0, 0, 'C');
    $p->SetFont('Helvetica', 'B', 8);
    $p->Cell(25, $t, $a[5], 0, 0);
    $p->Ln($t);
  }
  $p->Ln(4);
  BuatHeaderTabel($p);
}

function HeaderLogo($jdl, $p, $orientation='P')
{	$pjg = 90;
	$logo = (file_exists("../img/logo.jpg"))? "../img/logo.jpg" : "img/logo.jpg";
    $identitas = GetFields('identitas', 'Kode', KodeID, 'Nama, Alamat1, Telepon, Fax');
	$p->Image($logo, 12, 8, 16);
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
		$p->Cell(80, 7, $jdl, 0, 1, 'C');
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
