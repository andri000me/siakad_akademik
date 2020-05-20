<?php
session_start();

  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
  include_once "../header_pdf.php";

// Init
$pdf = new PDF('P', 'mm', 'A4');
$pdf->SetTitle("Pengumuman Penilaian - $TahunID");

// *** Parameters ***
$JadwalID = $_REQUEST['JadwalID']+0;
$ProdiID = GetSetVar('ProdiID');

// *** Main ***
if ($JadwalID == 0) {
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
  $JadwalID = GetSetVar('JadwalID');
  $jdwl = GetFields("jadwal j
    left outer join dosen d on d.Login = j.DosenID and d.KodeID = '".KodeID."'
    left outer join prodi prd on prd.ProdiID = j.ProdiID and prd.KodeID = '".KodeID."'
    left outer join program prg on prg.ProgramID = j.ProgramID and prg.KodeID = '".KodeID."'
    left outer join mk mk on mk.MKID = j.MKID
    left outer join hari huas on huas.HariID = date_format(j.UASTanggal, '%w')
	LEFT OUTER JOIN kelas k ON k.KelasID = j.NamaKelas
    ",
    "j.JadwalID", $JadwalID,
    "j.*, concat(d.Gelar1, ' ',d.Nama, ', ', d.Gelar) as DSN, d.NIDN,
    prd.Nama as _PRD, prg.Nama as _PRG,
    mk.Sesi,
    date_format(j.UASTanggal, '%d-%m-%Y') as _UASTanggal,
    date_format(j.UASTanggal, '%w') as _UASHari,
    huas.Nama as HRUAS,
    LEFT(j.UASJamMulai, 5) as _UASJamMulai, LEFT(j.UASJamSelesai, 5) as _UASJamSelesai, k.Nama AS namaKelas
    ");
  $TahunID = $jdwl['TahunID'];
  $thn = GetFields('tahun', "KodeID = '".KodeID."' and ProdiID = '$jdwl[ProdiID]' and ProgramID = '$jdwl[ProgramID]' and TahunID", $TahunID, "*");
  // Buat Header
  BuatHeader($jdwl, $thn, $p);
  BuatIsinya($jdwl, $p);
  BuatFooter($jdwl, $p);
}
function BuatFooter($jdwl, $p) {
  global $arrID;
  $t = 5;
  $p->Ln(4);
  $p->Cell(130);
  $p->Cell(60, $t, $arrID['Kota'] . ", " . date('d M Y'), 0, 1);
  $p->Cell(130);
  $p->Cell(60, $t, "Dosen Pengasuh,", 0 , 1);
  $p->Ln(10);

  $p->Cell(130);
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell(60, $t, $jdwl['DSN'], 0, 1);
  $p->Cell(130);
  $p->SetFont('Helvetica', '', 9);
  $p->Cell(60, $t, 'NIDN: ' . $jdwl['NIDN'], 0, 1);
}
function BuatIsinya($jdwl, $p) {
  $t =  6;
  $lbr1 = 10;
  BuatHeaderTabel($jdwl, $p);
  $s = "select k.*, left(m.Nama, 22) as NamaMhsw
    from krs k
      left outer join mhsw m on k.MhswID = m.MhswID and m.KodeID = '".KodeID."'
    where k.JadwalID = '$jdwl[JadwalID]'
    order by m.MhswID";
  $r = _query($s);
  $n = 0;
  $p->SetFont('Helvetica', '', 8);
  while ($w = _fetch_array($r)) {
    $n++;
    $p->Cell(8, $t, $n, 'LB', 0);
    $p->Cell(25, $t, $w['MhswID'], 'B', 0);
    $p->Cell(60, $t, $w['NamaMhsw'], 'B', 0);
    $p->Cell($lbr1, $t, $w['_Presensi'], 'B', 0, 'C');
    $p->Cell($lbr1, $t, $w['Tugas1'], 'B', 0, 'C');
    $p->Cell($lbr1, $t, $w['Tugas2'], 'B', 0, 'C');
    $p->Cell($lbr1, $t, $w['Tugas3'], 'B', 0, 'C');
	$p->Cell($lbr1, $t, $w['Tugas4'], 'B', 0, 'C');
	$p->Cell($lbr1, $t, $w['Tugas5'], 'B', 0, 'C');
    $p->Cell($lbr1, $t, $w['UTS'], 'B', 0, 'C');
    $p->Cell($lbr1, $t, $w['UAS'], 'B', 0, 'C');
    
    $p->Cell($lbr1, $t, $w['NilaiAkhir'], 'B', 0, 'C');
    $p->Cell($lbr1, $t, $w['GradeNilai'], 'BR', 0, 'C');
    $p->Ln($t);
  }
}
function BuatHeaderTabel($jdwl, $p) {
  $t = 4;
  $lbr1 = 10;
  $p->SetFont('Helvetica', 'B', 8);
  $p->Cell(8, $t, '', 'LTR', 0);
  $p->Cell(25, $t, '', 'TR', 0);
  $p->Cell(60, $t, '', 'TR', 0);
  $p->Cell($lbr1, $t, 'Khdn', 'TR', 0, 'C');
  $p->Cell($lbr1, $t, 'Tgs1', 'TR', 0, 'C');
  $p->Cell($lbr1, $t, 'Tgs2', 'TR', 0, 'C');
  $p->Cell($lbr1, $t, 'Tgs3', 'TR', 0, 'C');
  $p->Cell($lbr1, $t, 'Pres.', 'TR', 0, 'C');
  $p->Cell($lbr1, $t, 'Lab', 'TR', 0, 'C');
  $p->Cell($lbr1, $t, 'UTS', 'TR', 0, 'C');
  $p->Cell($lbr1, $t, 'UAS', 'TR', 0, 'C');
  $p->Cell($lbr1, $t, 'Akhir', 'TR', 0, 'C');
  $p->Cell($lbr1, $t, 'Grade', 'TR', 0, 'C');
  $p->Ln($t/2);
  $p->Cell(8, $t, 'No.', 'LR', 0, 'C');
  $p->Cell(25, $t, 'NIM', 'R', 0, 'C');
  $p->Cell(60, $t, 'Nama Mhsw', 'R', 0, 'C');
  $p->Cell($lbr1, $t, '', 'R', 0, 'C');
  $p->Cell($lbr1, $t, '', 'R', 0, 'C');
  $p->Cell($lbr1, $t, '', 'R', 0, 'C');
  $p->Cell($lbr1, $t, '', 'R', 0, 'C');
  $p->Cell($lbr1, $t, '', 'R', 0, 'C');
  $p->Cell($lbr1, $t, '', 'R', 0, 'C');
  $p->Cell($lbr1, $t, '', 'R', 0, 'C');
  $p->Cell($lbr1, $t, '', 'R', 0, 'C');
  $p->Cell($lbr1, $t, '', 'R', 0, 'C');
  $p->Cell($lbr1, $t, '', 'R', 0, 'C');
  $p->Ln($t/2);
  $p->Cell(8, $t, '', 'LBR', 0);
  $p->Cell(25, $t, '', 'BR', 0);
  $p->Cell(60, $t, '', 'BR', 0);
  $p->Cell($lbr1, $t, number_format($jdwl['Presensi']).'%', 'BR', 0, 'C');
  $p->Cell($lbr1, $t, number_format($jdwl['Tugas1']).'%', 'BR', 0, 'C');
  $p->Cell($lbr1, $t, number_format($jdwl['Tugas2']).'%', 'BR', 0, 'C');
  $p->Cell($lbr1, $t, number_format($jdwl['Tugas3']).'%', 'BR', 0, 'C');
  $p->Cell($lbr1, $t, number_format($jdwl['Tugas4']).'%', 'BR', 0, 'C');
  $p->Cell($lbr1, $t, number_format($jdwl['Tugas5']).'%', 'BR', 0, 'C');
  $p->Cell($lbr1, $t, number_format($jdwl['UTS']).'%', 'BR', 0, 'C');
  $p->Cell($lbr1, $t, number_format($jdwl['UAS']).'%', 'BR', 0, 'C');
  $p->Cell($lbr1, $t, '', 'BR', 0, 'C');
  $p->Cell($lbr1, $t, '', 'BR', 0, 'C');
  $p->Ln($t);
}
function BuatHeader($jdwl, $thn, $p) {
  $t = 5; $lbr = 190;
  $p->AddPage('P');

  $arr = array();
  $arr[] = array('Mata Kuliah', ':', $jdwl['MKKode'] . '   ' . $jdwl['Nama']);
  $arr[] = array('Dosen Pengasuh', ':', $jdwl['DSN']);
  $arr[] = array('Kelas / Thn Akd', ':', $jdwl['namaKelas'] . ' / ' . $thn['Nama'],
    'Program Studi', ':', $jdwl['_PRD'] . ' ('. $jdwl['_PRG'].')');
  $arr[] = array('Semester / SKS', ':', $jdwl['Sesi'] . ' / ' . $jdwl['SKS'],
    'Hari / Tgl Ujian', ':', $jdwl['HRUAS'] . 
    ' / ' . $jdwl['_UASTanggal'] .
    ' / ' . $jdwl['_UASJamMulai'] . ' - ' . $jdwl['_UASJamSelesai']);
  // Tampilkan
  $p->SetFont('Helvetica', 'B', 12);
  $p->Cell($lbr, 8, 'Detail Nilai Mata Kuliah Mahasiswa', 0, 1, 'C');
  $p->SetFont('Helvetica', '', 9);
  foreach ($arr as $a) {
    // Kolom 1
    $p->SetFont('Helvetica', 'I', 9);
    $p->Cell(25, $t, $a[0], 0, 0);
    $p->Cell(4, $t, $a[1], 0, 0, 'C');
    $p->SetFont('Helvetica', 'B', 9);
    $p->Cell(70, $t, $a[2], 0, 0);
    // Kolom 2
    $p->SetFont('Helvetica', 'I', 9);
    $p->Cell(25, $t, $a[3], 0, 0);
    $p->Cell(4, $t, $a[4], 0, 0, 'C');
    $p->SetFont('Helvetica', 'B', 9);
    $p->Cell(70, $t, $a[5], 0, 0);
    $p->Ln($t);
  }
  $p->Ln(4);
}

?>