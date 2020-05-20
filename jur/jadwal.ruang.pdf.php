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
$HariID = GetSetVar('HariID');

$lbr = 190;

$pdf = new PDF();
$pdf->SetTitle("Jadwal Matakuliah per Ruang - $TahunID");
$pdf->AddPage();

// Buat header dulu
BuatHeader($TahunID, $pdf);
// Tampilkan datanya
AmbilJadwal($HariID, $TahunID, $pdf);
// Buat footer
BuatFooter($pdf);

$pdf->Output();

// *** Functions ***
function BuatFooter($p) {

  global $arrID;
  $mrg = 130;
  $t = 6;
  // Tanda tangan
  $pjbt = GetFields('pejabat', "KodeID='".KodeID."' and KodeJabatan", 'KABAA', "*");
  $p->Ln(4); 
  $p->Cell($mrg);
  $p->Cell(60, $t, $arrID['Kota'] . ", " . date('d M Y'), 0, 1);
  $p->Cell($mrg);
  $p->Cell(60, $t, $pjbt['Jabatan'], 0 , 1);
  $p->Ln(10);

  $p->Cell($mrg);
  $p->SetFont('Helvetica', 'B', 8);
  $p->Cell(60, $t, $pjbt['Nama'], 0, 1);
  $p->Cell($mrg);
  $p->SetFont('Helvetica', '', 8);
  $p->Cell(60, $t, 'NIP: ' . $pjbt['NIP'], 0, 1);
}
function HeaderTabel($p) {
  $p->SetFont('Helvetica', 'B', 8);
  $t = 6;
  $p->Cell(8, $t, 'No', 'LBT', 0);
  $p->Cell(12, $t, 'Hari', 'BT', 0);
  $p->Cell(18, $t, 'Jam', 'BT', 0);

  $p->Cell(18, $t, 'Kode MK', 'BT', 0);
  $p->Cell(70, $t, 'Matakuliah', 'BT', 0);
  $p->Cell(8, $t, 'SKS', 'BT', 0);
  $p->Cell(20, $t, 'Kelas', 'BT', 0, 'C');
  $p->Cell(31, $t, 'UAS', 'BTR', 0);
  $p->Ln($t);
}
function AmbilJadwal($HariID, $TahunID, $p) {
  global $lbr;
  // Ambil Isinya
  if (!empty($HariID)) $whr = " And j.HariID='$HariID' ";
  $s = "select m.Nama as NamaKelasID, j.*,
      j.Nama as MK,
      h.Nama as HR, k.KampusID, k.Nama as NamaKampus,
      LEFT(j.JamMulai, 5) as JM, LEFT(j.JamSelesai, 5) as JS,
      if (d.Nama is NULL or d.Nama = '', 'Belum diset', concat(d.Nama, ', ', d.Gelar)) as DSN,
      date_format(j.UASTanggal, '%d-%m-%Y') as _UASTanggal,
      date_format(j.UASTanggal, '%w') as _UASHari,
      huas.Nama as HRUAS,
    if (j.JadwalRefID != 0,'(LAB)','') as _lab,
      LEFT(j.UASJamMulai, 5) as _UASJamMulai, LEFT(j.UASJamSelesai, 5) as _UASJamSelesai
    from jadwal j
      left outer join hari h on h.HariID = j.HariID
      left outer join dosen d on d.Login = j.DosenID and d.KodeID = '".KodeID."'
      left outer join hari huas on huas.HariID = date_format(j.UASTanggal, '%w')
      left outer join ruang r on r.RuangID = j.RuangID and r.KodeID = '".KodeID."'
      left outer join kampus k on k.KampusID = r.KampusID and k.KodeID = '".KodeID."'
    left outer join kelas m on m.KelasID=j.NamaKelas
    where j.KodeID = '".KodeID."'
    $whr
      and j.TahunID = '$_SESSION[TahunID]'
    order by k.Nama, r.RuangID, j.HariID, j.JamMulai";
  $r = _query($s);
  $n = 0; $_rg = 'asdijf;asldkjf';
  $t = 6;

  while ($w = _fetch_array($r)) {
    if ($_rg != $w['RuangID']) {
      $_rg = $w['RuangID'];
      $p->SetFont('Helvetica', '', 7);
      //$p->Cell($lbr, 8, 'Ruang: ' . $w['RuangID'] . ', Gedung: ' . $w['KampusID'], 0, 1);
      $p->Cell(80, 8, 'Ruang: ' . $w['RuangID'], 0, 0);
      $p->Cell(80, 8, 'Kampus: ' . $w['KampusID'], 0, 1);
      HeaderTabel($p);
    }
    $n++;

    $p->SetFont('Helvetica', '', 7);
    $p->Cell(8, $t, $n, 'LB', 0, 'R');
    $p->Cell(12, $t, $w['HR'], 'B');
    $p->Cell(18, $t, $w['JM'] . '-' . $w['JS'], 'B');
    $p->Cell(18, $t, $w['MKKode'], 'B');
    $p->Cell(70, $t, $w['MK'].' '.$w[_lab], 'B');
    $p->Cell(8, $t, $w['SKS'], 'B', 0, 'C');
    $p->Cell(20, $t, $w['NamaKelasID'], 'B', 0);
    $p->Cell(14, $t, $w['HRUAS'], 'B', 0);
    $p->Cell(17, $t, $w['_UASTanggal'], 'BR', 0);
    $p->Ln($t);
  }
}
function BuatHeader($TahunID, $p) {
  global $lbr;
  $NamaTahun = NamaTahun($TahunID);
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell($lbr, 4, "Jadwal Kuliah per Ruang", 0, 1, 'C');
  $p->SetFont('Helvetica', '', 13);
  $p->Cell($lbr, 6, "Semester $NamaTahun", 0, 1, 'C');
  $p->Ln(2);
}
?>
