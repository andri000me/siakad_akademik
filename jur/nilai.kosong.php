<?php
// Author : Irvandy Goutama
// Email  : irvandygoutama@gmail.com
// Start  : 1 Juni 2009

session_start();

  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
  include_once "../fpdf.php";

// *** Parameters ***
	$JadwalID = $_REQUEST['JadwalID'];
	$jdwl = GetFields("jadwal j
		left outer join dosen d on d.Login = j.DosenID and d.KodeID = '".KodeID."'
		left outer join prodi prd on prd.ProdiID = j.ProdiID and prd.KodeID = '".KodeID."'
		left outer join program prg on prg.ProgramID = j.ProgramID and prg.KodeID = '".KodeID."'
		left outer join mk mk on mk.MKID = j.MKID
		  left outer join hari huas on huas.HariID = date_format(j.UASTanggal, '%w')
		",
		"j.JadwalID", $JadwalID,
		"j.*, concat(d.Nama, ', ', d.Gelar) as DSN, d.NIDN,
		prd.Nama as _PRD, prg.Nama as _PRG, mk.Sesi,
		date_format(j.UASTanggal, '%d-%m-%Y') as _UASTanggal,
		date_format(j.UASTanggal, '%w') as _UASHari,
		huas.Nama as HRUAS,
		LEFT(j.UASJamMulai, 5) as _UASJamMulai, LEFT(j.UASJamSelesai, 5) as _UASJamSelesai
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
$pdf->SetTitle("Daftar Nilai Siswa");

$s = "select k.MhswID, m.Nama
    from krs k
      left outer join mhsw m on m.MhswID = k.MhswID and m.KodeID = '".KodeID."'
    where k.JadwalID = '$jdwl[JadwalID]'
    order by k.MhswID";
$r = _query($s);
$n = _num_rows($r);

$maxentryperpage = 15;
$maxentryoflastpage = 8;
$pages = floor($n/$maxentryperpage);
$lastpageentry = $n%$maxentryperpage;
if($lastpageentry == 0)
{	$pages -= 1;
    $lastpageentry = $maxentryperpage;
}
$totalpage = $pages;
if($lastpageentry > $maxentryoflastpage) $totalpage += 2;
else $totalpage += 1;

// Buat semua halaman tanpa footer
for($i = 0; $i< $pages; $i++)
{ 	$start = $i*$maxentryperpage;
	$s1 = "select k.MhswID, m.Nama
		from krs k
		  left outer join mhsw m on m.MhswID = k.MhswID and m.KodeID = '".KodeID."'
		where k.JadwalID = '$jdwl[JadwalID]'
		order by k.MhswID
		limit $start, $maxentryperpage";
	$r1 = _query($s1);
	
	$pdf->AddPage('L');
	$pdf->SetAutoPageBreak(true, 5);
	// Buat Header Logo
	HeaderLogo("DAFTAR NILAI MAHASISWA", $pdf, 'L');
	// Buat header dulu
	BuatHeader($jdwl, $pdf);
	// Tampilkan datanya
	AmbilDetail($jdwl, $r1, $start, $pdf);
	// Buat footer
	BuatFooter($jdwl, ($i+1), $totalpage, $pdf);
}

//Buat halaman terakhir dengan footer
$start = $i*$maxentryperpage;

if($lastpageentry > $maxentryoflastpage)
{	
	$s1 = "select k.MhswID, m.Nama
		from krs k
		  left outer join mhsw m on m.MhswID = k.MhswID and m.KodeID = '".KodeID."'
		where k.JadwalID = '$jdwl[JadwalID]'
		order by k.MhswID
		limit $start, $maxentryperpage";
	$r1 = _query($s1);
	$pdf->AddPage('L');
	$pdf->SetAutoPageBreak(true, 5);
	// Buat Header Logo
	HeaderLogo("ABSENSI DAFTAR MAHASISWA", $pdf, 'L');
	// Buat header dulu
	BuatHeader($jdwl, $pdf);
	// Tampilkan datanya
	AmbilDetail($jdwl, $r1, $start, $pdf);
	// Buat footer
	BuatFooter($jdwl, ($i+1), $totalpage,  $pdf);
	
	$pdf->AddPage('L');
	$pdf->SetAutoPageBreak(true, 4);
	// Buat Header Logo
	HeaderLogo("ABSENSI DAFTAR MAHASISWA", $pdf, 'L');
	// Buat header dulu
	BuatHeader($jdwl, $pdf);
	// Buat rekap kehadiran dan tanda tangan
	BuatEnding($jdwl, $pdf);
	// Buat footer
	BuatFooter($jdwl, $i+2, $totalpage, $pdf);
}
else
{	$s1 = "select k.MhswID, m.Nama
		from krs k
		  left outer join mhsw m on m.MhswID = k.MhswID and m.KodeID = '".KodeID."'
		where k.JadwalID = '$jdwl[JadwalID]'
		order by k.MhswID
		limit $start, $maxentryperpage";
	$r1 = _query($s1);
	
	$pdf->AddPage('L');
	$pdf->SetAutoPageBreak(true, 5);
	// Buat Header Logo
	HeaderLogo("ABSENSI DAFTAR MAHASISWA", $pdf, 'L');
	// Buat header dulu
	BuatHeader($jdwl, $pdf);
	// Tampilkan datanya
	AmbilDetail($jdwl, $r1, $start, $pdf);
	// Buat rekap kehadiran dan tanda tangan
	BuatEnding($jdwl, $pdf);
	// Buat footer
	BuatFooter($jdwl, ($i+1), $totalpage, $pdf);
}

$pdf->Output();

// *** Functions ***
function BuatEnding($jdwl, $p) {
  global $arrID;
  
  $lbrkolom = 12; $Kolom = 14; $t = 8;
  // Footer
  $p->Cell(78, $t, 'Jumlah Mhsw Hadir :', 'LBR', 0, 'R');
  $p->Cell($lbrkolom, $t, '', 'BR', 0);
  for($i = 0; $i < $Kolom; $i++) $p->Cell($lbrkolom, $t, '', 'BR', 0);
  $p->Ln($t);
  
  $p->Cell(78, $t, 'Paraf Dosen Pengajar :', 'LBR', 0, 'R');
  $p->Cell($lbrkolom, $t, '', 'BR', 0);
  for($i = 0; $i < $Kolom; $i++) $p->Cell($lbrkolom, $t, '', 'BR', 0);
  $p->Ln($t);
  
  $t = 4.5;
  $p->Ln(5);
  $p->Cell(200);
  $p->Cell(60, $t, $arrID['Kota'] . ", ___________________", 0, 1);
  $p->Cell(200);
  $p->Cell(60, $t, "Dosen Pengasuh,", 0 , 1);
  $p->Ln(20);

  $p->Cell(200);
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell(60, $t, $jdwl['DSN'], 0, 1);
  $p->Cell(200);
  $p->SetFont('Helvetica', '', 9);
  $p->Cell(60, $t, 'NIDN: ' . $jdwl['NIDN'], 0, 1);
}
function AmbilDetail($jdwl, $r, $start, $p) {
  $Kolom = 14; $lbrkolom = 12;
  
  $n = $start; $t = 8; 
  $p->SetFont('Helvetica', '', 7);
  while ($w = _fetch_array($r)) {
    $n++;
    $p->Cell(8, $t, $n, 'LBR', 0, 'C');
    $p->Cell(20, $t, $w['MhswID'], 'BR', 0);
    $p->Cell(50, $t, $w['Nama'], 'BR', 0);
	for($i = 0; $i < $Kolom; $i++) $p->Cell($lbrkolom, $t, '', 'BR', 0);
	$p->Cell($lbrkolom, $t, '', 'BR', 0);
	$p->Ln($t);
  }
}
function BuatHeaderTabel($p) {
  $t = 6;
  $s = 12;
  $Kolom = 15;
  $p->SetFont('Helvetica', 'B', 9);
  // Baris 1
  $p->Cell(8, $t, '', 'LTR', 0, 'C');
  $p->Cell(20, $t, '', 'TR', 0);
  $p->Cell(50, $t, '', 'TR', 0, 'C');
  $p->Cell($s*$Kolom, $t, 'KOMPONEN NILAI', 'TR', 0,'C');
  $p->Cell($s, $t, '', 'TR', 0, 'C');
  $p->Ln($t);
  
  // Baris 2 
  $p->Cell(8, $t, 'No.', 'LR', 0, 'C');
  $p->Cell(20, $t, 'N I M', 'R', 0, 'C');
  $p->Cell(50, $t, 'NAMA MAHASISWA', 'R', 0, C);
  $p->Cell(2*$s, $t, 'Kehadiran', 'R', 0, 'C');
  $p->Cell(2*$s, $t, 'JH', 'R', 0, 'C');
  $p->Ln($t);
  
  // Baris 2 
  $p->Cell(8, $t, '', 'LBR', 0, 'C');
  $p->Cell(20, $t, '', 'BR', 0);
  $p->Cell(50, $t, '', 'BR', 0, 'C');
  for($i = 0; $i < $Kolom; $i++) $p->Cell($s, $t, '', 'BR', 0, C);
  $p->Cell($s, $t, '', 'BR', 0,' C');
  $p->Ln($t);
}
function BuatHeader($jdwl, $p) {
  $NamaTahun = GetaField('tahun', "KodeID='".KodeID."' and TahunID='$jdwl[TahunID]' and ProdiID",
    $jdwl['ProdiID'], 'Nama');
  $t = 6; $lbr = 200;

  $arr = array();
  $arr[] = array('Mata Kuliah', ':', $jdwl['MKKode'] . ' - ' . $jdwl['Nama']);
  $arr[] = array('Kelas / Thn Akd', ':', $jdwl['NamaKelas'] . '- ( Ruang: ' . $jdwl['RuangID'] . ' ) '.
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
  $p->SetFont('Helvetica', '', 9);
  foreach ($arr as $a) {
    // Kolom 1
    $p->SetFont('Helvetica', 'I', 9);
    $p->Cell(25, $t, $a[0], 0, 0);
    $p->Cell(4, $t, $a[1], 0, 0, 'C');
    $p->SetFont('Helvetica', 'B', 9);
    $p->Cell(120, $t, $a[2], 0, 0);
    // Kolom 2
    $p->SetFont('Helvetica', 'I', 9);
    $p->Cell(25, $t, $a[3], 0, 0);
    $p->Cell(4, $t, $a[4], 0, 0, 'C');
    $p->SetFont('Helvetica', 'B', 9);
    $p->Cell(25, $t, $a[5], 0, 0);
    $p->Ln($t);
  }
  $p->Ln(4);
  BuatHeaderTabel($p);
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

function BuatFooter($jdwl, $page, $totalpage, $p)
{	$t = 6;
    $p->SetFont("Helvetica", '', 10);
	$p->Ln(4);
	$p->Cell(10, $t, '', 'T', 0); 
	$p->Cell($length, $t, 'Halaman: '.$page.' / '.$totalpage, 'T', 0);
}
?>
