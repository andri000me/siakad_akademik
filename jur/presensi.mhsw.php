<?php

session_start();

  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
  include_once "../header_pdf.php";

// *** Parameters ***
$TahunID = GetSetVar('TahunID');
$ProdiID = GetSetVar('ProdiID');
$ProgramID = GetSetVar('ProgramID');
$HariID = GetSetVar('HariID');
$JadwalID = $_REQUEST['JadwalID']+0;
$lbr = 190;

// Init
$pdf = new PDF('P', 'mm', 'A4');
$pdf->SetTitle("Laporan Kehadiran Kuliah Mhsw - $TahunID");
$pdf->SetAutoPageBreak(true, 5);

// *** Main ***
if ($JadwalID == 0) {
  $whr_prodi = (empty($ProdiID))? "" : "and ProdiID = '$ProdiID'";
  $whr_tahun = (empty($TahunID))? "" : "and TahunID = '$TahunID'";
  $s = "select JadwalID
    from jadwal
    where KodeID = '".KodeID."'
      $whr_prodi
	  $whr_tahun
    order by HariID, JamMulai, JamSelesai";
  $r = _query($s);
  while ($w = _fetch_array($r)) {
    CetakPresensiMhsw($w['JadwalID'], $pdf);
  }
}
else CetakPresensiMhsw($JadwalID, $pdf);

$pdf->Output();

// *** Functions ***
function CetakPresensiMhsw($id, $p) {
  $jdwl = GetFields("jadwal j
    left outer join dosen d on d.Login = j.DosenID and d.KodeID = '".KodeID."'
    left outer join prodi prd on prd.ProdiID = j.ProdiID and prd.KodeID = '".KodeID."'
    left outer join program prg on prg.ProgramID = j.ProgramID and prg.KodeID = '".KodeID."'
    left outer join mk mk on mk.MKID = j.MKID
    left outer join hari huas on huas.HariID = date_format(j.UASTanggal, '%w')
    left outer join jenisjadwal jj on jj.JenisJadwalID = j.JenisJadwalID 
	LEFT OUTER JOIN kelas k ON k.KelasID = j.NamaKelas
	",
    "j.JadwalID", $id,
    "j.*, concat(d.Nama, ', ', d.Gelar) as DSN,
    prd.Nama as _PRD, prg.Nama as _PRG,
    mk.Sesi,
    date_format(j.UASTanggal, '%d-%m-%Y') as _UASTanggal,
    date_format(j.UASTanggal, '%w') as _UASHari,
    huas.Nama as HRUAS,
    LEFT(j.UASJamMulai, 5) as _UASJamMulai, LEFT(j.UASJamSelesai, 5) as _UASJamSelesai,
	jj.Nama as _NamaJenisJadwal, jj.Tambahan, k.Nama AS namaKelas
    ");
  BuatHeaderDulu($jdwl, $p);
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
  $s = "select pm.MhswID, m.Nama, sum(pm.Nilai) as HDR
    from presensimhsw pm
      left outer join mhsw m on m.MhswID = pm.MhswID and m.KodeID = '".KodeID."'
    where pm.JadwalID = '$jdwl[JadwalID]'
    group by pm.MhswID";
  $r = _query($s); $n = 0; $t = 6;
  // Buat header dulu
  $p->SetFont('Helvetica', 'BI', 9);
  $p->Cell(10, $t, 'No', 'BT', 0);
  $p->Cell(28, $t, 'NPM', 'BT', 0);
  $p->Cell(64, $t, 'Mahasiswa', 'BT', 0);
  $p->Cell(20, $t, 'Hadir', 'BT', 0, 'R');
  $p->Cell(20, $t, 'Persen', 'BT', 0, 'R');
  $p->Ln($t);
  // Isinya
  $p->SetFont('Helvetica', '', 9);
  while ($w = _fetch_array($r)) {
    $n++;
    $persen = ($jdwl['Kehadiran'] > 0)? $w['HDR']/$jdwl['Kehadiran']*100 : 0;
    $_persen = number_format($persen, 2);
    $p->Cell(10, $t, $n, 'B', 0);
    $p->Cell(28, $t, $w['MhswID'], 'B', 0);
    $p->Cell(64, $t, $w['Nama'], 'B', 0);
    $p->Cell(20, $t, $w['HDR'].'/'.$jdwl['Kehadiran'], 'B', 0, 'R');
    $p->Cell(20, $t, $_persen.'%', 'B', 0, 'R');
    $p->Ln($t);
  }
}
function BuatHeaderDulu($jdwl, $p) {
  global $lbr;
  
  $NamaTahun = NamaTahun($jdwl['TahunID']);
  $NamaProdi = GetaField('prodi', "KodeID='".KodeID."' and ProdiID", $jdwl['ProdiID'], 'Nama');
  $TagTambahan = ($jdwl['Tambahan'] == 'Y')? "<b>( $jdwl[_NamaJenisJadwal] )</b>" : "";
  $arr = array();
  $arr[] = array('Matakuliah', ':', $jdwl['MKKode'],
    'Dosen Pengasuh', ':', $jdwl['DSN']);
  $arr[] = array('Matakuliah', ':', $jdwl['Nama'].' '.$TagTambahan,
    'Kelas', ':', $jdwl['namaKelas'] . ' / ' . $jdwl['ProgramID']);
  $arr[] = array('Semester / SKS', ':', $jdwl['Sesi'] . ' / ' . $jdwl['SKS'],
    'Program Studi', ':', $NamaProdi);
  $arr[] = array('Tahun Akademik', ':', $NamaTahun, 
    'Kehadiran', ':', $jdwl['Kehadiran'].' / '.$jdwl['RencanaKehadiran']);
  $p->AddPage('P');
  
  $p->SetFont('Helvetica', 'B', 12);
  $p->Cell($lbr, 6, "Laporan Kehadiran Mahasiswa", 0, 1, 'C');
  
  $t = 5;
  $p->SetFont('Helvetica', '', 9);
  foreach ($arr as $a) {
    $p->Cell(25, $t, $a[0], 0, 0);
    $p->Cell(4, $t, $a[1], 0, 0);
    $p->Cell(80, $t, $a[2], 0, 0);
    
    $p->Cell(25, $t, $a[3], 0, 0);
    $p->Cell(4, $t, $a[4], 0, 0);
    $p->Cell(50, $t, $a[5], 0, 0);
    $p->Ln($t);
  }
  $p->Ln(4);
}
?>
