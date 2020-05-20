<?php

session_start();
include_once "../dwo.lib.php";
include_once "../db.mysql.php";
include_once "../connectdb.php";
include_once "../parameter.php";
include_once "../cekparam.php";
include_once "../header_pdf2.php";
error_reporting(E_ALL);
  
// *** Parameters ***
$ProdiID = GetSetVar('_pustakaProdiID');
// Tgl Mulai
$TglMulai_y = GetSetVar('TglMulai_y', date('Y'));
$TglMulai_m = GetSetVar('TglMulai_m', date('m'));
$TglMulai_d = GetSetVar('TglMulai_d', '01');
$_SESSION['TglMulai'] = "$TglMulai_y-$TglMulai_m-$TglMulai_d";
// Tgl Selesai
$TglSelesai_y = GetSetVar('TglSelesai_y', date('Y'));
$TglSelesai_m = GetSetVar('TglSelesai_m', date('m'));
$TglSelesai_d = GetSetVar('TglSelesai_d', date('d'));
$_SESSION['TglSelesai'] = "$TglSelesai_y-$TglSelesai_m-$TglSelesai_d";

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'KonfirmasiTgl' : $_REQUEST['gos'];
$gos();

// *** Functions ***
function KonfirmasiTgl() {
  KonfirmasiTanggal("../$_SESSION[mnux].laporan.pengunjung.pdf.php", "Cetak");
}

function Cetak() {
  // *** Init PDF
  $pdf = new PDF();
  $pdf->SetAutoPageBreak(true, 5);
  $pdf->SetTitle("Laporan Pengunjung Pustaka");
  $pdf->AddPage('L','A4');
  $lbr = 210;

  BuatIsinya($_SESSION['_pustakaProdiID'], $pdf);

  $pdf->Output();
}

function BuatIsinya($ProdiID, $p) {
  $lbr = 260; $t = 5;
  $prd = (!empty($_SESSION['_pustakaProdiID']) ? GetaField("prodi","ProdiID",$_SESSION['_pustakaProdiID'],"Nama"): "Semua Prodi");
  $p->SetFont('Helvetica', 'B', 14);
  $p->Cell($lbr, $t, "Laporan Pengunjung Pustaka", 0, 1, 'C');
  $p->SetFont('Helvetica', 'B', 12);
  $p->Cell($lbr, $t, "Periode: ".TanggalFormat($_SESSION['TglMulai'])." s/d ".TanggalFormat($_SESSION['TglSelesai']), 0, 1, 'C');
  $p->Cell($lbr, $t, "Program Studi: $prd", 0, 1, 'C');
  $p->Ln(2);
  
  $whr = (!empty($_SESSION['_pustakaProdiID']) ? " and m.ProdiID='$_SESSION[_pustakaProdiID]' ": "");
  $a = "SELECT m.ProdiID, m.Nama from prodi m where 1 $whr";
  $b = _query($a);
  while($c = _fetch_array($b)){
  // Buat header tabel
  $p->SetFont('Helvetica', 'B', 10);
  $p->Cell(20, $t, "Prodi ".$c['Nama'], '', 0);
  $p->Ln($t);
  $p->SetFont('Helvetica', 'B', 8);
  $p->Cell(10, $t, 'Nmr', 1, 0);
  $p->Cell(20, $t, 'Tanggal', 1, 0);
  $p->Cell(15, $t, 'Jam', 1, 0);
  $p->Cell(25, $t, 'NPM', 1, 0);
  $p->Cell(80, $t, 'Nama', 1, 0);
  $p->Cell(70, $t, 'Prodi', 1, 0);
  $p->Cell(20, $t, 'Jenjang', 1, 0);
  $p->Cell(20, $t, 'Program', 1, 0, 'R');
  $p->Ln($t);
  
  $s = "select k.Login, m.Nama, p.Nama as PRD, k.Tanggal, k.Jam, j.Nama as Jenjang, pr.Nama as PRG from pustaka_kunjungan k 
  			left outer join mhsw m on m.MhswID=k.Login
			left outer join prodi p on p.ProdiID=m.ProdiID
			left outer join jenjang j on j.JenjangID=p.JenjangID
			left outer join program pr on pr.ProgramID = m.ProgramID
			where
      '$_SESSION[TglMulai]' <= k.Tanggal
      and k.Tanggal <= '$_SESSION[TglSelesai]'
	  and m.ProdiID = '$c[ProdiID]'
    order by  k.Tanggal, m.ProdiID,m.ProgramID";
  $r = _query($s); $n=0;
   while ($w = _fetch_array($r)) {
    $n++;
    $p->SetFont('Helvetica', '', 8);
    $p->Cell(10, $t, $n, 'LB', 0, 'R');
    $p->Cell(20, $t, $w['Tanggal'], 'B', 0);
    $p->Cell(15, $t, $w['Jam'], 'B', 0);
    $p->Cell(25, $t, $w['Login'], 'B', 0);
	$p->Cell(80, $t, $w['Nama'], 'B', 0);
	$p->Cell(70, $t, $w['PRD'], 'B', 0);
	$p->Cell(20, $t, $w['Jenjang'], 'B', 0);
	$p->Cell(20, $t, $w['PRG'], 'BR', 0, 'R');
    
    $p->Ln($t);
   }
   $p->SetFont('Helvetica', 'B', 10);
   $p->Cell(260, $t, 'Total '.$n, 'LBR', 0, 'R');
   $p->Ln($t*2);
  }

  // === DOSEN
  $p->SetFont('Helvetica', 'B', 10);
  $p->Cell(20, $t, "Dosen", '', 0);
  $p->Ln($t);
  $p->SetFont('Helvetica', 'B', 8);
  $p->Cell(10, $t, 'Nmr', 1, 0);
  $p->Cell(20, $t, 'Tanggal', 1, 0);
  $p->Cell(15, $t, 'Jam', 1, 0);
  $p->Cell(25, $t, 'Login', 1, 0);
  $p->Cell(80, $t, 'Nama', 1, 0);
  $p->Ln($t);
  
  $s = "select k.Login, m.Nama, k.Tanggal, k.Jam from pustaka_kunjungan k 
        left outer join pustaka_anggota m on m.AnggotaID=k.Login
      where
      '$_SESSION[TglMulai]' <= k.Tanggal
      and k.Tanggal <= '$_SESSION[TglSelesai]'
    and m.InstitusiID = 'DOS'
    order by  k.Tanggal,m.Nama";
  $r = _query($s); $n=0;
   while ($w = _fetch_array($r)) {
    $n++;
    $p->SetFont('Helvetica', '', 8);
    $p->Cell(10, $t, $n, 'LB', 0, 'R');
    $p->Cell(20, $t, $w['Tanggal'], 'B', 0);
    $p->Cell(15, $t, $w['Jam'], 'B', 0);
    $p->Cell(25, $t, $w['Login'], 'B', 0);
  $p->Cell(80, $t, $w['Nama'], 'B', 0);
    
    $p->Ln($t);
   }
   $p->SetFont('Helvetica', 'B', 10);
   $p->Cell(260, $t, 'Total '.$n, 'LBR', 0, 'R');
   $p->Ln($t*2);
  

  // === KARYAWAN
  $p->SetFont('Helvetica', 'B', 10);
  $p->Cell(20, $t, "Karyawan", '', 0);
  $p->Ln($t);
  $p->SetFont('Helvetica', 'B', 8);
  $p->Cell(10, $t, 'Nmr', 1, 0);
  $p->Cell(20, $t, 'Tanggal', 1, 0);
  $p->Cell(15, $t, 'Jam', 1, 0);
  $p->Cell(25, $t, 'Login', 1, 0);
  $p->Cell(80, $t, 'Nama', 1, 0);
  $p->Ln($t);
  
  $s = "select k.Login, m.Nama, k.Tanggal, k.Jam  from pustaka_kunjungan k 
        left outer join pustaka_anggota m on m.AnggotaID=k.Login
      where
      '$_SESSION[TglMulai]' <= k.Tanggal
      and k.Tanggal <= '$_SESSION[TglSelesai]'
    and m.InstitusiID = 'KAR'
    order by  k.Tanggal,m.Nama";
  $r = _query($s); $n=0;
   while ($w = _fetch_array($r)) {
    $n++;
    $p->SetFont('Helvetica', '', 8);
    $p->Cell(10, $t, $n, 'LB', 0, 'R');
    $p->Cell(20, $t, $w['Tanggal'], 'B', 0);
    $p->Cell(15, $t, $w['Jam'], 'B', 0);
    $p->Cell(25, $t, $w['Login'], 'B', 0);
  $p->Cell(80, $t, $w['Nama'], 'B', 0);
    
    $p->Ln($t);
   }
   $p->SetFont('Helvetica', 'B', 10);
   $p->Cell(260, $t, 'Total '.$n, 'LBR', 0, 'R');
   $p->Ln($t*2);
  

  // === MHS Luar
  $p->SetFont('Helvetica', 'B', 10);
  $p->Cell(20, $t, "Mahasiswa Luar Biasa", '', 0);
  $p->Ln($t);
  $p->SetFont('Helvetica', 'B', 8);
  $p->Cell(10, $t, 'Nmr', 1, 0);
  $p->Cell(20, $t, 'Tanggal', 1, 0);
  $p->Cell(15, $t, 'Jam', 1, 0);
  $p->Cell(25, $t, 'Login', 1, 0);
  $p->Cell(80, $t, 'Nama', 1, 0);
  $p->Cell(70, $t, 'Institusi', 1, 0);
  $p->Ln($t);
  
  $s = "select k.Login, m.Nama, k.Tanggal, k.Jam, m.NamaInstitusi  from pustaka_kunjungan k 
        left outer join pustaka_anggota m on m.AnggotaID=k.Login
      where
      '$_SESSION[TglMulai]' <= k.Tanggal
      and k.Tanggal <= '$_SESSION[TglSelesai]'
    and m.InstitusiID = 'MHL'
    order by  k.Tanggal,m.Nama";
  $r = _query($s); $n=0;
   while ($w = _fetch_array($r)) {
    $n++;
    $p->SetFont('Helvetica', '', 8);
    $p->Cell(10, $t, $n, 'LB', 0, 'R');
    $p->Cell(20, $t, $w['Tanggal'], 'B', 0);
    $p->Cell(15, $t, $w['Jam'], 'B', 0);
    $p->Cell(25, $t, $w['Login'], 'B', 0);
  $p->Cell(80, $t, $w['Nama'], 'B', 0);
  $p->Cell(70, $t, $w['NamaInstitusi'], 'B', 0);
    
    $p->Ln($t);
   }
   $p->SetFont('Helvetica', 'B', 10);
   $p->Cell(260, $t, 'Total '.$n, 'LBR', 0, 'R');
   $p->Ln($t*2);
  

  // === Umum
  $p->SetFont('Helvetica', 'B', 10);
  $p->Cell(20, $t, "Umum", '', 0);
  $p->Ln($t);
  $p->SetFont('Helvetica', 'B', 8);
  $p->Cell(10, $t, 'Nmr', 1, 0);
  $p->Cell(20, $t, 'Tanggal', 1, 0);
  $p->Cell(15, $t, 'Jam', 1, 0);
  $p->Cell(25, $t, 'Login', 1, 0);
  $p->Cell(80, $t, 'Nama', 1, 0);
  $p->Cell(70, $t, 'Institusi', 1, 0);
  $p->Ln($t);
  
  $s = "select k.Login, m.Nama, k.Tanggal, k.Jam,m.NamaInstitusi  from pustaka_kunjungan k 
        left outer join pustaka_anggota m on m.AnggotaID=k.Login
      where
      '$_SESSION[TglMulai]' <= k.Tanggal
      and k.Tanggal <= '$_SESSION[TglSelesai]'
    and m.InstitusiID = 'UMM'
    order by  k.Tanggal,m.Nama";
  $r = _query($s); $n=0;
   while ($w = _fetch_array($r)) {
    $n++;
    $p->SetFont('Helvetica', '', 8);
    $p->Cell(10, $t, $n, 'LB', 0, 'R');
    $p->Cell(20, $t, $w['Tanggal'], 'B', 0);
    $p->Cell(15, $t, $w['Jam'], 'B', 0);
    $p->Cell(25, $t, $w['Login'], 'B', 0);
  $p->Cell(80, $t, $w['Nama'], 'B', 0);
  $p->Cell(70, $t, $w['NamaInstitusi'], 'B', 0);
    
    $p->Ln($t);
   }
   $p->SetFont('Helvetica', 'B', 10);
   $p->Cell(260, $t, 'Total '.$n, 'LBR', 0, 'R');
   $p->Ln($t*2);
  
}
?>
