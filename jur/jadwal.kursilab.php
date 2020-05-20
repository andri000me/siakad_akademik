<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 22 Agustus 2008

session_start();

  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
  include_once "../fpdf.php";

// *** Parameters ***
$_jdwlProdi = GetSetVar('_jdwlProdi');
$_jdwlProg = GetSetVar('_jdwlProg');
$_jdwlTahun = GetSetVar('_jdwlTahun');
$id = GetSetVar('id');

// Init
$pdf = new FPDF('P', 'mm', 'A4');
$pdf->SetTitle("Daftar Nomer Kursi AS Laboratorium - $TahunID");
$pdf->SetAutoPageBreak(true, 4);

// *** Main ***
if ($id == 0) {
  // Maka cetak semua
  $whr_prog = (empty($_jdwlProg))? "" : "and ProgramID = '$_jdwlProg'";
  $s = "select j.JadwalID 
    from jadwal j left outer join mk m on j.MKID = m.MKID
    where j.KodeID = '".KodeID."'
      and j.ProdiID = '$_jdwlProdi'
	  and TahunID = '$_jdwlTahun'
	  $whr_prog
	  and m.Responsi = 'Y'
    order by j.HariID, j.JamMulai";
  $r = _query($s);
  while ($w = _fetch_array($r)) {
    CetakKursi($w['JadwalID'], $pdf);
  }
}
else CetakKursi($id, $pdf);

$pdf->Output();


// *** Functions ***
function CetakKursi($id, $pdf) {
  $jdwl = GetFields("jadwal j
    left outer join dosen d on d.Login = j.DosenID and d.KodeID = '".KodeID."'
    left outer join prodi prd on prd.ProdiID = j.ProdiID and prd.KodeID = '".KodeID."'
    left outer join program prg on prg.ProgramID = j.ProgramID and prg.KodeID = '".KodeID."'
    left outer join mk mk on mk.MKID = j.MKID
      left outer join hari huas on huas.HariID = date_format(j.UASTanggal, '%w')
    ",
    "j.JadwalID", $id,
    "j.*, concat(d.Nama, ', ', d.Gelar) as DSN,
    prd.Nama as _PRD, prg.Nama as _PRG,
    mk.Sesi,
    date_format(j.UASTanggal, '%d-%m-%Y') as _UASTanggal,
    date_format(j.UASTanggal, '%w') as _UASHari,
    huas.Nama as HRUAS,
    LEFT(j.UASJamMulai, 5) as _UASJamMulai, LEFT(j.UASJamSelesai, 5) as _UASJamSelesai
    ");
  $TahunID = $jdwl['TahunID'];
  $thn = GetFields('tahun', "KodeID = '".KodeID."' and ProdiID = '$jdwl[ProdiID]' and ProgramID = '$jdwl[ProgramID]' and TahunID", $TahunID, "*");
  // Cetak
  BuatHeaderKursi($jdwl, $thn, $pdf);
  BuatIsinya($jdwl, $pdf);
}
function BuatIsinya($jdwl, $p) {
  $sudahbayar = "and (h.Biaya - h.Bayar + h.Tarik - h.Potongan) <= 0";
  $sudahpenuhiabsen = "and (select count(pm.PresensiMhswID) from presensimhsw pm left outer join jenispresensi jp on pm.JenisPresensiID=jp.JenisPresensiID where pm.KRSID=k.KRSID and jp.Nilai = 0) < $jdwl[MaxAbsen]";
  $s = "select k.MhswID, m.Nama
    from krs k
      left outer join mhsw m on m.MhswID = k.MhswID and m.KodeID = '".KodeID."'
      left outer join khs h on k.KHSID = h.KHSID
    where k.JadwalID = '$jdwl[JadwalID]'
      $sudahbayar
	  $sudahpenuhiabsen
    order by k.MhswID";
  $r = _query($s); $t = 7; $n = 0;
  // Header
  $p->SetFont('Helvetica', 'B', 10);
  $p->Cell(72, $t, 'Nama Mahasiswa', 'B', 0);
  $p->Cell(25, $t, 'N I M', 'B', 0);
  $p->Cell(15, $t, 'No. Kursi', 'B', 0, 'R');
  $p->Cell(20, $t, 'TTD', 'B', 0, 'C');
  $p->Ln($t);
  // Data
  $p->SetFont('Helvetica', '', 9);
  while ($w = _fetch_array($r)) {
    $n++;
    $p->SetFont('Helvetica', '', 9);
    $p->Cell(72, $t, $w['Nama'], 0, 0);
    $p->Cell(25, $t, $w['MhswID'], 0, 0);
    $p->SetFont('Helvetica', 'B', 9);
    $p->Cell(15, $t, $n, 0, 0, 'C');
    $p->Cell(20, $t, '', 'B', 0, 'C');
    $p->Ln($t);
  }
  $p->SetFont('Helvetica', '', 9);
  $p->Cell(132, 2, ' ', 'B', 1);
  $p->Cell(132, $t, 'Jumlah Peserta Ujian : ' . $n, 0, 1);
  $p->Cell(132, $t, 'Catatan: Mahasiswa yang tidak tercetak di daftar berarti belum melunasi kewajibannya.', 0, 1);
}
function BuatHeaderKursi($jdwl, $thn, $p) {
  $t = 6; $lbr = 190;
  $p->AddPage('P');
  HeaderLogo('ABSENSI UJIAN AKHIR SEMESTER LAB', $p, 'P');
  
  $arr = array();
  $arr[] = array('Mata Kuliah', $jdwl['MKKode'] . '   ' . $jdwl['Nama']);
  $arr[] = array('Dosen Pengasuh', $jdwl['DSN']);
  $arr[] = array('Program Studi', $jdwl['_PRD'] . ' ('. $jdwl['_PRG'].')', 'Semester / SKS', $jdwl['Sesi'] . ' / ' . $jdwl['SKS']);
  $arr[] = array('Kelas / Thn Akd', $jdwl['NamaKelas'] . ' / ' . $thn['Nama'], 'Hari / Tgl Ujian', $jdwl['HRUAS'] . 
    ' / ' . $jdwl['_UASTanggal'] .
    ' / ' . $jdwl['_UASJamMulai'] . ' - ' . $jdwl['_UASJamSelesai']);
  // Tampilkan
  $p->SetFont('Helvetica', '', 9);
  foreach ($arr as $a) {
    $p->SetFont('Helvetica', 'I', 9);
    $p->Cell(30, $t, $a[0], 0, 0);
    $p->Cell(4, $t, ':', 0, 0, 'C');
    $p->SetFont('Helvetica', 'B', 9);
    $p->Cell(70, $t, $a[1], 0, 0);
	
	if(!empty($a[2]))
	{
		$p->SetFont('Helvetica', 'I', 9);
		$p->Cell(30, $t, $a[2], 0, 0);
		$p->Cell(4, $t, ':', 0, 0, 'C');
		$p->SetFont('Helvetica', 'B', 9);
		$p->Cell(70, $t, $a[3], 0, 0);
    }
	$p->Ln($t);
  }
  $p->Ln(2);
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
