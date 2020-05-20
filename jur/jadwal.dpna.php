<?php
session_start();

  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
  include_once "../header_pdf.php";

// *** Parameters ***
$_jdwlProdi = GetSetVar('_jdwlProdi');
$_jdwlProg = GetSetVar('_jdwlProg');
$_jdwlTahun = GetSetVar('_jdwlTahun');
$id = GetSetVar('id');

// Init
$pdf = new FPDF('L', 'mm', 'A4');
$pdf->SetTitle("D P N A - $TahunID");

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
    CetakDPNA($w['JadwalID'], $pdf);
  }
}
else CetakDPNA($id, $pdf);

$pdf->Output();


// *** functions ***
function CetakDPNA($id, $pdf) {
  $jdwl = GetFields("jadwal j
    left outer join dosen d on d.Login = j.DosenID and d.KodeID = '".KodeID."'
    left outer join prodi prd on prd.ProdiID = j.ProdiID and prd.KodeID = '".KodeID."'
    left outer join program prg on prg.ProgramID = j.ProgramID and prg.KodeID = '".KodeID."'
    left outer join mk mk on mk.MKID = j.MKID
    left outer join hari huas on huas.HariID = date_format(j.UASTanggal, '%w')
    ",
    "j.JadwalID", $id,
    "j.*, concat(d.Gelar1, ' ', d.Nama, ', ', d.Gelar) as DSN,
    prd.Nama as _PRD, prg.Nama as _PRG,
    mk.Sesi,
    date_format(j.UASTanggal, '%d-%m-%Y') as _UASTanggal,
    date_format(j.UASTanggal, '%w') as _UASHari,
    huas.Nama as HRUAS,
    LEFT(j.UASJamMulai, 5) as _UASJamMulai, LEFT(j.UASJamSelesai, 5) as _UASJamSelesai
    ");
  $TahunID = $jdwl['TahunID'];
  $thn = GetFields('tahun', "KodeID = '".KodeID."' and ProdiID = '$jdwl[ProdiID]' and ProgramID = '$jdwl[ProgramID]' and TahunID", $TahunID, "*");
  BuatHeaderDPNA($jdwl, $thn, $pdf);
  AmbilDataSiswa($jdwl, $pdf);
  BuatFooterDPNA($jdwl, $pdf);
}
function BuatFooterDPNA($jdwl, $p) {
  global $arrID;
  $p->Cell(186, 0, '', 1, 0);
  $p->Cell(8, 0, '');
  $p->Cell(86, 0, '', 1, 1);
  $p->Ln(3);
  
  $t = 5;
  // array footer
  $arrF = array();
  $arrF[] = array('Pengawas I', ':', '................', '', '', '', $arrID['Kota'].',');
  $arrF[] = array('Pengawas II', ':', '................', '', '', '', 'Dosen Pengasuh,');
  $arrF[] = array('Jumlah Peserta', '', '', ':', $jdwl['JumlahMhsw'], 'Peserta');
  $arrF[] = array('Jumlah Peserta Saat Ujian Berlangsung', '', '', ':', '.....', 'Peserta');
  $arrF[] = array('', '', '', '', '', '', $jdwl['DSN']);
  
  // Nilai
  $arrN = AmbilNilai2($jdwl['ProdiID'], $jml);
  $max = ($jml > sizeof($arrF))? $jml : sizeof($arrF);
  for ($i = 0; $i < $max; $i++) {
    // Tampilkan footer
    $ft = $arrF[$i];
    $p->Cell(23, $t, $ft[0], 0, 0);
    $p->Cell(4, $t, $ft[1], 0, 0);
    $p->Cell(36, $t, $ft[2], 0, 0);
    $p->Cell(4, $t, $ft[3], 0, 0);
    $p->Cell(8, $t, $ft[4], 0, 0, 'C');
    $p->Cell(20, $t, $ft[5], 0, 0);
    // Tampilkan nilai
    if ($i == 0) {
      $p->Cell(35, $t, $arrN[$i][0], 'LTBR', 0, 'C');
    }
    else {
      if ($i <= $jml) {
        $grs1 = ($i == $jml-1)? 'LB' : 'L';
        $grs2 = ($i == $jml-1)? 'B' : 0;
        $grs3 = ($i == $jml-1)? 'BR' : 'R';
        $p->Cell(10, $t, $arrN[$i][0], $grs1, 0);
        $p->Cell(4, $t, '-', $grs2, 0, 'C');
        $p->Cell(15, $t, $arrN[$i][1], $grs2, 0);
        $p->Cell(6, $t, $arrN[$i][2], $grs3, 0);
      }
      else $p->Cell(35, $t, '', 0, 0);
    }
    // Dosen
    $p->Cell(4, $t, ' ', 0, 0);
    $p->Cell(60, $t, $ft[6], 0, 0);
    $p->Cell(48, $t, $ft[6], 0, 0);
    $p->Ln($t);
  }
}
function AmbilNilai2($ProdiID, &$jml) {
  $s = "select NilaiMin, NilaiMax, Nama
    from nilai
    where KodeID = '".KodeID."'
      and ProdiID = '$ProdiID'
      and NA = 'N'
    order by NilaiMax desc";
  $r = _query($s);
  $a = array();
  $a[] = array('Interval Nilai');
  while ($w = _fetch_array($r)) {
    $a[] = array($w['NilaiMin'], $w['NilaiMax'], $w['Nama']);
  }
  $jml = sizeof($a);
  return $a;
}
function AmbilDataSiswa($jdwl, $p) {
  // Buat Header
  $t = 5;
  // Hdr1
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell(50, $t, 'Nama Mahasiswa', 'T', 0);
  $p->Cell(20, $t, 'NIM', 'T', 0);
  $p->Cell(14, $t, 'TTD', 'T', 0, 'C');
  $p->Cell(14, $t, 'TTD', 'T', 0, 'C');
  $p->Cell(14, $t, 'Aktp', 'T', 0, 'C');
  $p->Cell(14, $t, 'Tugas', 'T', 0, 'C');
  $p->Cell(14, $t, 'MID', 'T', 0, 'C');
  $p->Cell(14, $t, 'UAS', 'T', 0, 'C');
  $p->Cell(18, $t, 'Jumlah', 'T', 0, 'C');
  $p->Cell(14, $t, 'NILAI', 'T', 0, 'C');
  
  $p->Cell(8, $t, '', 0, 0);
  $p->Cell(10, $t, 'No.', 'T', 0);
  $p->Cell(20, $t, 'NIM', 'T', 0);
  $p->Cell(14, $t, 'TTD', 'T', 0, 'C');
  $p->Cell(14, $t, 'Nilai', 'T', 0, 'C');
  $p->Cell(28, $t, 'Ket.', 'T', 0, 'C');
  $p->Ln($t);
  // Hdr2
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell(50, $t, '', 'B', 0);
  $p->Cell(20, $t, '', 'B', 0);
  $p->Cell(14, $t, 'MID', 'B', 0, 'C');
  $p->Cell(14, $t, 'UAS', 'B', 0, 'C');
  $p->Cell(14, $t, '10%', 'B', 0, 'C');
  $p->Cell(14, $t, '20%', 'B', 0, 'C');
  $p->Cell(14, $t, '30%', 'B', 0, 'C');
  $p->Cell(14, $t, '40%', 'B', 0, 'C');
  $p->Cell(18, $t, 'Akhir', 'B', 0, 'C');
  $p->Cell(14, $t, '', 'B', 0, 'C');

  $p->Cell(8, $t, '', 0, 0);
  $p->Cell(10, $t, 'Urut', 'B', 0);
  $p->Cell(20, $t, '', 'B', 0);
  $p->Cell(14, $t, 'UAS', 'B', 0, 'C');
  $p->Cell(14, $t, 'Akhir', 'B', 0, 'C');
  $p->Cell(28, $t, '', 'B', 0, 'C');
  $p->Ln($t);
  
  // Data siswa
  $p->SetFont('Helvetica', '', 9);
  $t = 8;
  $s = "select k.MhswID, LEFT(m.Nama, 24) as NamaMhsw
    from krs k
      left outer join mhsw m on m.MhswID = k.MhswID and m.KodeID = '".KodeID."'
      left outer join khs h on k.KHSID = h.KHSID
    where k.JadwalID = '$jdwl[JadwalID]'
    order by k.MhswID";
  $r = _query($s); $n = 0; $ttk = '........';
  while ($w = _fetch_array($r)) {
    $n++;
    $p->Cell(50, $t, $w['NamaMhsw'], 0, 0);
    $p->Cell(20, $t, $w['MhswID'], 0, 0);
    $p->Cell(14, $t, $ttk, 0, 0, 'C');
    $p->Cell(14, $t, $ttk, 0, 0, 'C');
    $p->Cell(14, $t, $ttk, 0, 0, 'C');
    $p->Cell(14, $t, $ttk, 0, 0, 'C');
    $p->Cell(14, $t, $ttk, 0, 0, 'C');
    $p->Cell(14, $t, $ttk, 0, 0, 'C');
    $p->Cell(18, $t, '.........', 0, 0, 'C');
    $p->Cell(14, $t, $ttk, 0, 0, 'C');
    $p->Cell(8, $t, '', 0, 0);
    $p->Cell(10, $t, $n, 0, 0);
    $p->Cell(20, $t, $w['MhswID'], 0, 0);
    $p->Cell(14, $t, $ttk, 0, 0, 'C');
    $p->Cell(14, $t, $ttk, 0, 0, 'C');
    $p->Cell(28, $t, $ttk, 0, 0, 'C');
    $p->Ln($t);
  }
}
function BuatHeaderDPNA($jdwl, $thn, $p) {
  $t = 4;
  $p->AddPage('L');
  HeaderLogo('Daftar Lengkap Siswa dan Nilai', $p, 'P');
  // Kop 2
  $p->SetXY(170, 10);
  $p->SetFont('Helvetica', '', 8);
  $mrg0 = 195; $lbr0 = 85;
  $identitas = GetFields('identitas', 'Kode', KodeID, '*');
  $p->Cell($mrg0);
  $p->Cell($lbr0, $t, '', 0, 1, 'C');
  $p->Cell($mrg0);
  $p->Cell($lbr0, $t, $identitas['Yayasan'], 0, 1, 'C');
  $p->Cell($mrg0);
  
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell($lbr0, 5, $identitas['Nama'], 0, 1, 'C');
  $p->Cell($mrg0);
  $p->Cell($lbr0, 0, '', 1, 1);
  
  $t = 5;
  $p->SetFont('Helvetica', '', 9);
  $d = array();
  $d[] = array('', '', '', '', '', '', 'Matakuliah', ':', $jdwl['Nama']);
  $d[] = array('', '', '', '', '', '', 'Dosen Pengasuh', ':', $jdwl['DSN']);
  $d[] = array('Kode Mata Kuliah', ':', $jdwl['MKKode'], 'Dosen Pengasuh', ':', $jdwl['DSN'], 'Program Studi', ':', $jdwl['_PRD']);
  $d[] = array('Mata Kuliah', ':', $jdwl['Nama'], 'Semester / SKS', ':', $jdwl['Sesi'].' / '. $jdwl['SKS'], 'Kelas / Thn Akd.', ':', $jdwl['NamaKelas'] . ' / ' . $thn['Nama'] );
  $d[] = array('Kelas', ':', $jdwl['NamaKelas'] . ' ('. $jdwl['_PRG'] .')', 'Program Studi', ':', $jdwl['_PRD'], 'Semester / SKS', ':', $jdwl['Sesi'] . '/ '. $jdwl['SKS']);
  $d[] = array('Tahun Akademik', ':', $thn['Nama'], 'Hari / Tanggal Ujian', ':', 
    $jdwl['HRUAS'] . ' / '. $jdwl['_UASTanggal'] . ' ' . $jdwl['_UASJamMulai'] . '-' . $jdwl['_UASJamSelesai'], 
    'Hari / Tgl Ujian', ':', $jdwl['HRUAS'] . ' / ' . $jdwl['_UASTanggal']);
  // Tampilkan
  //$p->Image("../img/DPNA.gif", 80, 47, 26);
  foreach ($d as $_d) {
    $p->Cell(26, $t, $_d[0], 0, 0);
    $p->Cell(4, $t, $_d[1], 0, 0);
    $p->Cell(70, $t, $_d[2], 0, 0);
    
    $p->Cell(30, $t, $_d[3], 0, 0);
    $p->Cell(4, $t, $_d[4], 0, 0);
    $p->Cell(60, $t, $_d[5], 0, 0);
    
    $p->Cell(26, $t, $_d[6], 0, 0);
    $p->Cell(4, $t, $_d[7], 0, 0);
    $p->Cell(60, $t, $_d[8], 0, 0);
    $p->Ln($t);
  }
  $p->Ln($t);
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
