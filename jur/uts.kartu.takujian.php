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
$TahunID = GetSetVar('TahunID');
$ProdiID = GetSetVar('ProdiID');

// *** Init PDF
$pdf = new FPDF();
$pdf->SetTitle("Daftar Mahasiswa Yang Tidak Bisa Ujian");
$pdf->SetAutoPageBreak(true, 5);
$lbr = 190;

$pdf->AddPage();
HeaderLogo("Daftar Mahasiswa Yang Tidak Memenuhi", $pdf, 'P', 'Syarat Mengikuti Ujian Tengah Semester(UTS)');
    
BuatIsinya($TahunID, $ProdiID, $pdf);
$pdf->Ln(8);
BuatIsinya2($TahunID, $ProdiID, $pdf);

$pdf->Output();

// *** FUnctions ***
function BuatIsinya($TahunID, $ProdiID, $p) {
  $maxentryperpage = 45;
  
  BuatHeader($TahunID, 'Belum Administrasi', ceil($ttl/$maxentryperpage)+1, $p);
  
  $whr_prodi = (empty($ProdiID))? '' : "and h.ProdiID = '$ProdiID' ";
  $whr_tahun = (empty($TahunID))? '' : "and h.TahunID = '$TahunID' ";
  $s = "select h.*,
      m.Nama as NamaMhsw, 
      d.Nama as NamaPA, d.Gelar,
	  h.Biaya - h.Potongan - h.Bayar as _Hutang
    from khs h
      left outer join mhsw m on m.MhswID = h.MhswID and m.KodeID = '".KodeID."'
      left outer join dosen d on d.Login = m.PenasehatAkademik and d.KodeID = '".KodeID."'
    where h.KodeID = '".KodeID."'
      and h.TahunID = '$TahunID'
      and h.Biaya - h.Potongan - h.Bayar > 0
      $whr_prodi
	  $whr_tahun
    order by h.MhswID";
  $r = _query($s);
  
  $n = 0; $t = 5; 
  
  if(_num_rows($r) > 0)
  {
	  while ($w = _fetch_array($r)) {
		$n++;
		$NamaPA = (empty($w['NamaPA']))? '(Belum diset)' : $w['NamaPA'];
		$p->SetFont('Helvetica', '', 8);
		$p->Cell(15, $t, $n, 'LB', 0); 
		$p->Cell(25, $t, $w['MhswID'], 'B', 0);
		$p->Cell(70, $t, $w['NamaMhsw'], 'B', 0);
		$p->Cell(30, $t, $w['_Hutang'], 'B', 0, 'R');
		$p->Cell(50, $t, 'Administrasi', 'B', 0);
		$p->Ln($t);
	  }	  
  }
  else
  {	  $p->SetFont('Helvetica', 'B', 8);
	  $p->Cell(0, $t, '(Tidak ada mahasiswa yang dapat dicetak)', 0, 1, 'C');
  }
  $p->Ln($t);
  $p->SetFont('Helvetica', 'B', 10);
  $p->Cell(100, $t, 'Jumlah Mahasiswa: '.$n, 0, 0);
  $p->Ln($t);
}

function BuatHeader($TahunID, $Title, $page, $p) {
  global $lbr;
  $t = 6;
  
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell(190, $t, $Title, 1, 1, 'C');
  
  // Header tabel
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell(15, $t, 'Nmr', 1, 0);
  $p->Cell(25, $t, 'N I M', 1, 0);
  $p->Cell(70, $t, 'Nama Mahasiswa', 1, 0);
  $p->Cell(30, $t, 'Hutang', 1, 0, 'C');
  $p->Cell(50, $t, 'Keterangan', 1, 0);
  $p->Ln($t);
}

function BuatIsinya2($TahunID, $ProdiID, $p) {
  $maxentryperpage = 45;
  
  BuatHeader2($TahunID, 'Quota Absensi', ceil($ttl/$maxentryperpage)+1, $p);
  
  $whr_prodi = (empty($ProdiID))? '' : "j.ProdiID = '$ProdiID' ";
  $whr_tahun = (empty($TahunID))? '' : "and j.TahunID = '$TahunID' ";
  $s = "select DISTINCT(p.MhswID), count(p.PresensiID) as _countAbsensi,
      m.Nama as NamaMhsw, j.Nama,
      d.Nama as NamaPA, d.Gelar,
	  j.MaxAbsen
    from presensimhsw p
      left outer join presensi p2 on p.PresensiID=p2.PresensiID
	  left outer join jadwal j on j.JadwalID=p.JadwalID and j.KodeID= '".KodeID."'
	  left outer join jenispresensi jp on p.JenisPresensiID=jp.JenisPresensiID
	  left outer join mhsw m on m.MhswID = p.MhswID and m.KodeID = '".KodeID."'
	  left outer join dosen d on d.Login = m.PenasehatAkademik and d.KodeID = '".KodeID."'
    where 
      $whr_prodi
	  $whr_tahun
	  and p2.Pertemuan <= (j.RencanaKehadiran/2)
	  and jp.Nilai = 0
	group by p.MhswID, j.JadwalID
    order by p.MhswID";
  $r = _query($s);
  
  $n = 0; $t = 5; 
  
  if(_num_rows($r) > 0)
  {
	  while ($w = _fetch_array($r)) {
		if($w['_countAbsensi'] > ($w['MaxAbsen']))
	    {	$n++;
			$NamaPA = (empty($w['NamaPA']))? '(Belum diset)' : $w['NamaPA'];
			$p->SetFont('Helvetica', '', 8);
			$p->Cell(10, $t, $n, 'LB', 0); 
			$p->Cell(25, $t, $w['MhswID'], 'B', 0);
			$p->Cell(60, $t, $w['NamaMhsw'], 'B', 0);
			$p->Cell(10, $t, $w['_countAbsensi'], 'B', 0, 'C');
			$p->Cell(10, $t, $w['MaxAbsen'], 'B', 0, 'C');
			$p->Cell(75, $t, $w['Nama'], 'BR', 0);
			$p->Ln($t);
		}
	  }	  
  }
  else
  {	  $p->SetFont('Helvetica', 'B', 8);
	  $p->Cell(0, $t, '(Tidak ada mahasiswa yang dapat dicetak)', 0, 1, 'C');
  }
  $p->Ln($t);
  $p->SetFont('Helvetica', 'B', 12);
  $p->Cell(100, $t, 'Jumlah Mahasiswa: '.$n, 0, 0);
  
}

function BuatHeader2($TahunID, $Title, $page, $p) {
  global $lbr;
  $t = 6;
  
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell(190, $t, $Title, 1, 1, 'C');
  
  // Header tabel
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell(10, $t, 'Nmr', 1, 0);
  $p->Cell(25, $t, 'N I M', 1, 0);
  $p->Cell(60, $t, 'Nama Mahasiswa', 1, 0);
  $p->Cell(10, $t, 'Absen', 1, 0, 'C');
  $p->Cell(10, $t, 'Max.', 1, 0, 'C');
  $p->Cell(75, $t, 'Mata Kuliah', 1, 0);
  $p->Ln($t);
}

function HeaderLogo($jdl, $p, $orientation='P', $jdltambahan='')
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
      $identitas['Alamat1'], 0, 0, 'C');
    
	if($orientation == 'L')
	{
		$p->SetFont("Helvetica", 'B', 16);
		$p->Cell(20, 7, '', 0, 0);
		$p->Cell($pjg, 7, $jdltambahan, 0, 1, 'C');
	}
	else
	{	$p->SetFont("Helvetica", 'B', 12);
		$p->Cell(80, 7, $jdltambahan, 0, 1, 'R');
	}
	
	$p->SetFont("Helvetica", 'I', 6);
	$p->Cell($pjg, 3,
      "Telp. ".$identitas['Telepon'].", Fax. ".$identitas['Fax'], 0, 1, 'C');
    $p->Ln(3);
	if($orientation == 'L') $length = 275;
	else $length = 190;
    $p->Cell($length, 0, '', 1, 1);
    $p->Ln(2);
}

?>
