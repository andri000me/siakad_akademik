<?php
// Author : Arisal Yanuarafi
// Email  : arisal.yanuarafi@yahoo.com	
// Start  : 28 November 2011

session_start();

  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
  include_once "../header_pdf.php";

// Init

$pdf = new PDF('P', 'mm', 'Legal');


// *** Parameters ***
$id = sqling($_REQUEST['id']);

// *** Main ***
if ($id == 0) {
  // Maka cetak semua
  $s = "select JadwalID
    from jadwal
    where KodeID = '".KodeID."'
      and ProdiID = '$_jdwlProdi'
    order by HariID, JamMulai";
  $r = _query($s);
  while ($w = _fetch_array($r)) {
    CetakNilai($w['JadwalID'], $pdf);
  }
}
else CetakNilai($id, $pdf);


$pdf->Output();
// *** Functions ***
function CetakNilai($jid, $p) {
  $jadwaluts = GetFields("jadwal jad, dosen do, jadwaluts ju left outer join jadwal j on j.JadwalID=ju.JadwalID and j.KodeID='".KodeID."'
										 left outer join hari h on h.HariID=date_format(ju.Tanggal, '%w')
										 left outer join prodi prd on j.ProdiID=prd.ProdiID
										 left outer join program prg on j.ProgramID=prg.ProgramID", 
								"jad.JadwalID=ju.JadwalID and do.Login=jad.DosenID and ju.JadwalUTSID='$jid' and ju.KodeID", KodeID, 
								"concat(do.Gelar1, ' ', do.Nama, ', ', do.Gelar) as NmDosen, jad.*, do.*, ju.*, 
								date_format(ju.Tanggal, '%d-%m-%Y') as TglUTS,
    date_format(ju.Tanggal, '%w') as _HariUTS,
								LEFT(ju.JamMulai, 5) as _JM, LEFT(ju.JamSelesai, 5) as _JS,  
								    j.MKKode, j.Nama, LEFT(j.JamMulai, 5) as JamMulaiUTS, 
									h.Nama as HariUTS, ju.RuangID as NMRuang,
									LEFT(j.JamSelesai, 5) as JamSelesaiUTS,
									j.ProdiID, j.ProgramID,
									prd.Nama as _PRD, prg.Nama as _PRG");
								
	$thnutsID = GetFields('jadwal', "JadwalID", $jadwaluts[JadwalID], "TahunID");
  	$TahunID = $thnutsID['TahunID'];
  	$thn = GetFields('tahun', "KodeID = '".KodeID."' and ProdiID = '$jadwaluts[ProdiID]' and ProgramID = '$jadwaluts[ProgramID]' and TahunID", $TahunID, "*");
  // Buat Header
  BuatHeader($jadwaluts, $thn, $p);
  BuatIsinya($jadwaluts, $p);
  BuatFooter($jadwaluts, $jdwl, $p);
}
function BuatFooter($jadwaluts,$jdwl, $p) {
  global $arrID;
  //Array Bulan
 
$array_bulan = array(1=>'Januari','Februari','Maret', 'April', 'Mei', 'Juni','Juli','Agustus','September','Oktober', 'November','Desember');
 
$bulan = $array_bulan[date('n')];
 
  $t = 5;
  $p->Ln(1);
  $p->SetFont('Helvetica', 'B', 8);
  $p->Cell(180, $t, "*) Kehadiran Kurang Dari Persyaratan, Dosen Berwenang Memberi Nilai E.", 0 , 'L');
  $p->Ln(4);
  $p->SetFont('Helvetica','', 7);
  //$p->Cell(180, $t, "Catatan: Bagi mahasiswa yang namanya tidak terproses, silakan konfirmasi ke HELPDESK Pustikom di help.bunghatta.ac.id", 0 , 'L');
  $p->Ln(6);
  $p->Cell(41);
  $p->SetFont('Helvetica','', 9);
  $p->Cell(41, $t, "Nama", 0 , 'L');
  $p->Cell(56, $t, "Tanda Tangan", 0 , 'L');
  $p->Cell(60, $t, $arrID['Kota'] . ", " . TanggalFormat($jadwaluts['Tanggal']), 0, 1);
  $p->Cell(138);
  $p->Cell(60, $t, "Dosen Pengasuh,", 0 , 1);
  $p->Ln(13);

  
  $p->Cell(28, $t, "Pengawas:", 0 , 'L');
  $p->Cell(46, $t, "1. ____________________", 0 , 'L');
  $p->Cell(64, $t, "_______________________", 0 , 'L');
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell(60, $t, $jadwaluts['NmDosen'], 0, 1);
  $p->Cell(138);
  $p->SetFont('Helvetica','', 9);
  $p->Cell(60, $t, 'NIDN: ' . $jadwaluts['NIDN'], 0, 1);
  $p->Cell(28);
  $p->Cell(46, $t, "2. ____________________", 0 , 'L');
  $p->Cell(60, $t, "_______________________", 0 , 'L');
  $p->Ln(8);
  $p->Cell(1);

}
function BuatIsinya($jadwaluts, $p) {
  $t =  12.4;
  BuatHeaderTabel($p);
  $s = "select u.*, UPPER(m.Nama) as NamaMhsw
    from utsmhsw u, mhsw m
      where u.JadwalUTSID = '$jadwaluts[JadwalUTSID]'
	  and m.MhswID=u.MhswID
    order by m.MhswID";
  $r = _query($s);
  $n = 0;
  $p->SetFont('Helvetica','', 10);
  while ($w = _fetch_array($r)) {
    $n++;
    $p->Cell(13);
    $p->Cell(10, $t, $n, 'LTBR', 0,'C');
    $p->Cell(32, $t, $w['MhswID'], 'TBR', 0);
    $p->Cell(55, $t, $w['NamaMhsw'], 'TBR', 0);
    $p->Cell(15, $t, '', 'TBR', 0, 'C');
    $p->Cell(20, $t, '', 'TBR', 0, 'C');
    $p->Cell(35, $t, $n, 'TBR', 0, 'L');
    $p->Ln($t);
  }
  if ($n==17) {
    $p->Cell(20, $t, '', '', 1, 'C');
  $p->Cell(20, $t, '', '', 1, 'C');
  $p->Cell(20, $t, '', '', 1, 'C');
  }
  if ($n==18) {
  	$p->Cell(20, $t, '', '', 1, 'C');
	$p->Cell(20, $t, '', '', 1, 'C');
	}
	if ($n==19) {
  	$p->Cell(20, $t, '', '', 1, 'C');
	}
}

function BuatHeaderTabel($p) {
  $t = 4;
  $p->SetFont('Helvetica', 'B', 10);
  $p->Cell(13);
  $p->Cell(10, $t, 'No.', 'LTR', 0, 'C');
  $p->Cell(32, $t, 'NPM', 'TR', 0,'C');
  $p->Cell(55, $t, 'Nama Mahasiswa', 'TR', 0,'C');
  $p->Cell(15, $t, 'Nilai', 'TR', 0, 'C');
  $p->Cell(20, $t, 'Jumlah', 'TR', 0, 'C');
  $p->Cell(35, $t, 'Tanda Tangan', 'TR', 0, 'C');
  $p->Ln($t);
  $p->Cell(13);
  $p->Cell(10, $t, ' ', 'LBR', 0);
  $p->Cell(32, $t, '', 'BR', 0);
  $p->Cell(55, $t, '', 'BR', 0);
  $p->Cell(15, $t, '(Angka)', 'BR', 0, 'C');
  $p->Cell(20, $t, 'Nil.Huruf', 'BR', 0, 'C');
  $p->Cell(35, $t, ' ', 'BR', 0, 'C');
  $p->Ln($t);
}
function BuatHeader($jadwaluts, $thn, $p) {
  $t = 5; $lbr = 190;
  $p->AddPage('P');
  $JadwalID=$jadwaluts['JadwalID'];
	$jdwl = GetFields("jadwal j
    left outer join dosen d on d.Login = j.DosenID and d.KodeID = '".KodeID."'
    left outer join prodi prd on prd.ProdiID = j.ProdiID and prd.KodeID = '".KodeID."'
    left outer join program prg on prg.ProgramID = j.ProgramID and prg.KodeID = '".KodeID."'
    left outer join mk mk on mk.MKID = j.MKID
    left outer join hari huas on huas.HariID = date_format(j.UTSTanggal, '%w') 
	LEFT OUTER JOIN kelas k ON k.KelasID = j.NamaKelas
    ",
    "j.JadwalID", $JadwalID,
    "j.*, concat(d.Gelar1, ' ',d.Nama, ', ', d.Gelar) as DSN, d.NIDN,
    prd.Nama as _PRD, prg.Nama as _PRG,
    mk.Sesi,
    date_format(j.UTSTanggal, '%d-%m-%Y') as _UTSTanggal,
    date_format(j.UTSTanggal, '%w') as _UTSHari,
    huas.Nama as HRUTS,
    LEFT(j.UTSJamMulai, 5) as _UTSJamMulai, LEFT(j.UTSJamSelesai, 5) as _UTSJamSelesai, k.Nama AS namaKelas
    ");
  $arr = array();
  $arr[] = array('Mata Kuliah', ':', $jdwl['MKKode'] . '   ' . $jdwl['Nama'], 
    'Dosen Pengasuh', ':', $jdwl['DSN']);
  $arr[] = array('Kelas / Thn Akd', ':', $jdwl['namaKelas'] . ' / ' . $thn['Nama'],
    'Ruang Ujian', ':', $jadwaluts['NMRuang']);
  $arr[] = array('Semester / SKS', ':', $jdwl['Sesi'] . ' / ' . $jdwl['SKS'] . ' ' . $jdwl['ProdiID'],
    'Tanggal / Jam Ujian', ':', $jadwaluts['HariUTS'] . ', ' .$jadwaluts['TglUTS'] .
    ' / ' . $jadwaluts['_JM'] . ' - ' . $jadwaluts['_JS']);
  // Tampilkan
  $p->SetFont('Helvetica', 'B', 10);
  $p->Cell($lbr, 8, 'Daftar Hadir dan Nilai Ujian Tengah Semester', 0, 1, 'C');
  $p->SetFont('Helvetica','', 9);
  foreach ($arr as $a) {
    // Kolom 1
    $p->SetFont('Helvetica','', 9);
	$p->Cell(5, $t,'' , 0, 0);
    $p->Cell(25, $t, $a[0], 0, 0);
    $p->Cell(4, $t, $a[1], 0, 0, 'C');
    $p->SetFont('Helvetica', 'B', 9);
    $p->Cell(70, $t, $a[2], 0, 0);
    // Kolom 2
    $p->SetFont('Helvetica','', 9);
	$p->Cell(5, $t,'' , 0, 0);
    $p->Cell(25, $t, $a[3], 0, 0);
    $p->Cell(4, $t, $a[4], 0, 0, 'C');
    $p->SetFont('Helvetica', 'B', 9);
    $p->Cell(70, $t, $a[5], 0, 0);
    $p->Ln($t);
  }
  $p->Ln(4);
}
