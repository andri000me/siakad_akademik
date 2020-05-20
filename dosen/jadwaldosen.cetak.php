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
$DosenID = GetSetVar('DosenID');
$dsn = GetFields('dosen', "Login='$DosenID' and KodeID", KodeID, "*");

// *** Main ***
$pdf = new PDF();
$pdf->SetTitle("Jadwal Dosen");
$pdf->AddPage();

// Buat header dulu
BuatHeader($TahunID, $dsn, $pdf);
// Tampilkan datanya
AmbilJadwal($TahunID, $dsn, $pdf);

$pdf->Output();

// *** Functions ***
function BuatHeader($TahunID, $dsn, $p) {
  $lbr = 190;
  $t = 5;
  $p->SetFont('Helvetica', 'B', 14);
  $p->Cell($lbr, $t, "Jadwal Mengajar - $TahunID", 0, 1, 'C');
  $p->Cell($lbr, $t, "Dosen: $dsn[Nama], $dsn[Gelar]", 0, 1, 'C');
  $p->Ln(2);
}
function AmbilJadwal($TahunID, $dsn, $p) {
  $s = "select j.*,
      left(j.JamMulai, 5) as _JM,
      left(j.JamSelesai, 5) as _JS, k.Nama as _Kelas
    from jadwal j LEFT outer join kelas k on k.KelasID=j.NamaKelas
    where j.TahunID = '$TahunID'
      and j.DosenID like '$dsn[Login]'
      and j.KodeID = '".KodeID."'
    order by j.HariID, j.JamMulai, j.JamSelesai";
  $r = _query($s);
  $n = 0; $t = 6; $hr = -25; $ttl = 0;
  while ($w = _fetch_array($r)) {
    if ($hr != $w['HariID']) {
      $hr = $w['HariID'];
      $NamaHari = GetaField('hari', 'HariID', $hr, 'Nama');
      TampilkanHeaderTabel($NamaHari, $p);
    }
    $n++;
    $ttl += $w['SKS'];
    $p->SetFont('Helvetica', '', 9);
    $p->Cell(7, $t, $n, 1, 0);
    $p->Cell(20, $t, $w['_JM'] . '-' . $w['_JS'], 1, 0);
    $p->Cell(24, $t, $w['MKKode'], 1, 0);
    $p->Cell(70, $t, substr($w['Nama'], 0, 35), 1, 0);
    $p->Cell(8, $t, $w['SKS'], 1, 0, 'R');
    $p->Cell(18, $t, $w['_Kelas'], 1, 0);
    $p->Cell(18, $t, $w['RuangID'], 1, 0);
    $p->Cell(14, $t, $w['ProgramID'], 1, 0);
    $p->Cell(14, $t, $w['ProdiID'], 1, 0);
    
    $p->Ln($t);
  }
  $p->SetFont('Helvetica', '', 10);
  $p->Cell(100, $t, "Total SKS: ". $ttl, 0, 1);
}
function TampilkanHeaderTabel($NamaHari, $p) {
  $t = 5;
  $p->Ln(2);
  $p->SetFont('Helvetica', 'B', 10);
  $p->Cell(100, $t, $NamaHari, 0, 1);
  
  $p->SetFont('Helvetica', 'BI', 8);
  $p->Cell(7, $t, 'Nr', 1, 0);
  $p->Cell(20, $t, 'Jam Kuliah', 1, 0);
  $p->Cell(24, $t, 'Kode', 1, 0);
  $p->Cell(70, $t, 'Matakuliah', 1, 0);
  $p->Cell(8, $t, 'SKS', 1, 0);
  $p->Cell(18, $t, 'Kelas', 1, 0);
  $p->Cell(18, $t, 'Ruang', 1, 0);
  $p->Cell(14, $t, 'PRG', 1, 0);
  $p->Cell(14, $t, 'Prodi', 1, 0);
  $p->Ln($t);
}
?>
