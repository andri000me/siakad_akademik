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
  include_once "../header_pdf.php";

// *** Parameters ***
$khsid = $_REQUEST['khsid'];
$khs = GetFields("khs", "KHSID", $khsid, "*");
if (empty($khs))
  die(ErrorMsg("Error",
    "Data mahasiswa tidak ditemukan.<br />
    Hubungi Sysadmin untuk informasi lebih lanjut.
    <hr size=1 color=silver />
    <input type=button name='Tutup' value='Tutup'
      onClick=\"window.close()\" />"));

$mhsw = GetFields("mhsw m
  left outer join dosen d on d.Login = m.PenasehatAkademik and d.KodeID = '".KodeID."' ",
  "m.KodeID='".KodeID."' and m.MhswID", $khs['MhswID'],
  "m.MhswID, m.Nama, m.PenasehatAkademik, m.StatusAwalID, m.StatusMhswID,
  m.TotalSKS,
  if (d.Nama is NULL or d.Nama = '', 'Belum diset', concat(d.Nama, ', ', d.Gelar)) as PA");

$lbr = 190;

$pdf = new PDF();
$pdf->SetTitle("Lembar Rencana Studi");
$pdf->AddPage();
$pdf->SetFont('Helvetica', 'B', 16);
$pdf->Cell($lbr, 9, "Lembar Rencana Studi", 0, 1, 'C');
$pdf->SetFont('Helvetica', 'BI', 9);
$pdf->Cell($lbr, 7, "(LRS digunakan sebagai panduan dalam melakukan bimbingan KRS kepada Penasehat Akademik)", 0, 1, 'C');

// Buat header dulu
BuatHeader($khs, $mhsw, $pdf);
// Tampilkan datanya
AmbilKRS($khs, $mhsw, $pdf);
// Buat footer
$pdf->Cell($lbr, 1, '', 1, 1);
BuatFooter($khs, $mhsw, $pdf);

$pdf->Output();

// *** Functions ***
function BuatFooter($khs, $mhsw, $p) {
  global $arrID;
  $t = 6;
  // Tanda tangan
  $p->Ln(4);
  $p->Cell(130);
  $p->Cell(60, $t, $arrID['Kota'] . ", " . date('d M Y'), 0, 1);
  $p->Cell(130);
  $p->Cell(60, $t, "Dosen Penasehat Akademik", 0 , 1);
  $p->Ln(10);
  // Ambil nama pejabat
  $p->Cell(130);
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell(60, $t, $mhsw['PA'], 0, 1);
  $p->Cell(130);
  $p->SetFont('Helvetica', '', 9);
  $p->Cell(60, $t, 'NIK: ' . $pjbt['NIP'], 0, 1);
}
function AmbilKRS($khs, $mhsw, $p) {
  // Buat headernya dulu
  $p->SetFont('Helvetica', 'B', 9);
  $t = 6;
  
  $p->Cell(8, $t, 'No', 1, 0);
  $p->Cell(14, $t, 'Hari', 1, 0);
  $p->Cell(22, $t, 'Jam', 1, 0);

  $p->Cell(20, $t, 'Kode MK', 1, 0);
  $p->Cell(50, $t, 'Matakuliah', 1, 0);
  $p->Cell(10, $t, 'SKS', 1, 0);
  $p->Cell(50, $t, 'Dosen Pengasuh', 1, 0);
  $p->Cell(16, $t, 'Ruang', 1, 1);

  // Ambil Isinya
  $s = "select j.*,
      left(j.Nama, 25) as MK,
      h.Nama as HR, 
      LEFT(j.JamMulai, 5) as JM, LEFT(j.JamSelesai, 5) as JS,
      if (d.Nama is NULL or d.Nama = '', 'Belum diset', left(concat(d.Nama, ', ', d.Gelar), 25)) as DSN,
	  jj.Nama as _NamaJenisJadwal, jj.Tambahan
    from jadwal j
      left outer join hari h on h.HariID = j.HariID
      left outer join dosen d on d.Login = j.DosenID and d.KodeID = '".KodeID."'
	  left outer join jenisjadwal jj on jj.JenisJadwalID=j.JenisJadwalID
    where j.KodeID = '".KodeID."'
      and j.TahunID = '$khs[TahunID]'
      and j.ProdiID = '$khs[ProdiID]'
    order by j.ProgramID, j.HariID, j.JamMulai";
  $r = _query($s);
  $n = 0; $_h = 'akjsdfh'; $_p = 'la;skdjfadshg';
  $t = 6;

  while ($w = _fetch_array($r)) {
    $n++;
    if ($_p != $w['ProgramID']) {
      $_p = $w['ProgramID'];
      $_prg = GetaField('program', "KodeID='".KodeID."' and ProgramID", $_p, 'Nama');
      $p->SetFont('Helvetica', 'B', 10);
      $p->Cell(190, $t, $_prg, 1, 1, 'C');
    }
    if ($_h != $w['HR']) {
      $_h = $w['HR'];
      $hr = $w['HR'];
    } else $hr = '-';
    $p->SetFont('Helvetica', '', 8);
    $p->Cell(8, $t, $n, 'LB', 0, 'R');
    $p->Cell(14, $t, $hr, 'B');
    $p->Cell(22, $t, $w['JM'] . ' - ' . $w['JS'], 'B');
    $p->Cell(20, $t, $w['MKKode'], 'B');
	$TagTambahan = ($w['Tambahan'] == 'Y')? "( $w[_NamaJenisJadwal] )" : "";
    $p->Cell(50, $t, $w['MK'].' '.$TagTambahan, 'B');
    $p->Cell(10, $t, $w['SKS'], 'B', 0, 'C');
    $p->Cell(50, $t, $w['DSN'], 'B');
    $p->Cell(16, $t, $w['RuangID'], 'BR', 1);
  }
}
function BuatHeader($khs, $mhsw, $p) {
  $prodi = GetaField('prodi', "KodeID='".KodeID."' and ProdiID", $khs['ProdiID'], 'Nama');
  $prg   = GetaField('program', "KodeID='".KodeID."' and ProgramID", $khs['ProgramID'], 'Nama');
  $thn   = GetaField('tahun', "KodeID='".KodeID."' and TahunID='$khs[TahunID]' and ProdiID='$khs[ProdiID]' and ProgramID", $khs['ProgramID'], 'Nama');
  
  $data = array();
  $data[] = array('Nama', ':', $mhsw['Nama'], 'Tahun Akademik', ':', $thn);
  $data[] = array('NIM', ':', $mhsw['MhswID'], 'Program Studi', ':', $prodi);
  $data[] = array('Dosen PA', ':', $mhsw['PA'], 'Prg Pendidikan', ':', $prg);
  
  foreach ($data as $d) {
    $p->SetFont('Helvetica', 'I', 9);
    $p->Cell(24, 5, $d[0], 0, 0);
    $p->Cell(4, 5, $d[1], 0, 0);
    
    $p->SetFont('Helvetica', 'B', 9);
    $p->Cell(74, 5, $d[2], 0, 0);
    
    $p->SetFont('Helvetica', 'I', 9);
    $p->Cell(26, 5, $d[3], 0, 0);
    $p->Cell(4, 5, $d[4], 0, 0);
    
    $p->SetFont('Helvetica', 'B', 9);
    $p->Cell(50, 5, $d[5], 0, 1);
  }
  $p->Ln(2);
}
?>
