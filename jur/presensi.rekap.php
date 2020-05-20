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
$TahunID = GetSetVar('TahunID');
$ProdiID = GetSetVar('ProdiID');
$ProgramID = GetSetVar('ProgramID');
$HariID = GetSetVar('HariID');
$lbr = 190;

// Init
$pdf = new PDF('P', 'mm', 'A4');
$pdf->SetTitle("Rekap Kehadiran Kuliah - $TahunID");
$pdf->AddPage('P');

// *** Main ***
BuatHeader($TahunID, $ProdiID, $pdf);
BuatRekap($TahunID, $ProdiID, $ProgramID, $HariID, $pdf);

$pdf->Output();

// *** Functions ***
function BuatHeader($TahunID, $ProdiID, $p) {
  global $lbr;
  $NamaTahun = NamaTahun($TahunID);
  $NamaProdi = GetaField('prodi', "KodeID = '".KodeID."' and ProdiID", $ProdiID, 'Nama');
  $p->SetFont('Helvetica', 'B', 12);
  $p->Cell($lbr, 6, "Rekap Kehadiran Kuliah - $NamaTahun", 0, 1, 'C');
  $p->SetFont('Helvetica', 'I', 10);
  $p->Cell($lbr, 6, "Program Studi $NamaProdi", 0, 1, 'C');
}
function BuatRekap($TahunID, $ProdiID, $ProgramID, $HariID, $p) {
  global $lbr;
  
  $whr_program = ($ProgramID == '')? '' : "and j.ProgramID = '$ProgramID' ";
  $whr_hari = ($HariID == '')? '' : "and j.HariID = '$HariID' ";

  $s = "select j.*, left(concat(d.Nama, ', ', d.Gelar), 25) as DSN,
      left(j.Nama, 22) as MKNama,
      prd.Nama as _PRD, prg.Nama as _PRG,
      mk.Sesi, h.Nama as _HR,
      left(j.JamMulai, 5) as _JM, left(j.JamSelesai, 5) as _JS,
      date_format(j.UASTanggal, '%d-%m-%Y') as _UASTanggal,
      date_format(j.UASTanggal, '%w') as _UASHari,
      huas.Nama as HRUAS,
      LEFT(j.UASJamMulai, 5) as _UASJamMulai, LEFT(j.UASJamSelesai, 5) as _UASJamSelesai, k.Nama AS namaKelas
    from jadwal j
      left outer join dosen d on d.Login = j.DosenID and d.KodeID = '".KodeID."'
      left outer join prodi prd on prd.ProdiID = j.ProdiID and prd.KodeID = '".KodeID."'
      left outer join program prg on prg.ProgramID = j.ProgramID and prg.KodeID = '".KodeID."'
      left outer join mk mk on mk.MKID = j.MKID
      left outer join hari huas on huas.HariID = date_format(j.UASTanggal, '%w')
      left outer join hari h on h.HariID = j.HariID 
	  LEFT OUTER JOIN kelas k ON k.KelasID = j.NamaKelas
    where j.NA = 'N'
      and j.TahunID = '$TahunID'
      and j.ProdiID = '$ProdiID'
      $whr_program
      $whr_hari
    order by j.ProgramID, j.HariID, j.JamMulai, j.JamSelesai
    ";
  $r = _query($s);
  $n = 0; $t = 5;

  $prghr = ';lasdkjf;asdf';
  while ($w = _fetch_array($r)) {
    if ($prghr != $w['ProgramID'].$w['HariID']) {
      $prghr = $w['ProgramID'].$w['HariID'];
      
      $p->SetFont('Helvetica', 'B', 10);
      $p->Cell($lbr, 10, $w['_HR'] . " -- (". $w['_PRG'] . ")", 'B', 1);
      TampilkanHeaderTabel($p);
      $n = 0;
    }
    $persen = ($w['RencanaKehadiran'] == 0)? 0 : $w['Kehadiran']/$w['RencanaKehadiran']*100;
    $persen = number_format($persen, 2);
    $n++;
    $p->SetFont('Helvetica', '', 7);
    $p->Cell(10, $t, $n, 'B', 0);
    $p->Cell(20, $t, $w['MKKode'], 'B', 0);
    $p->Cell(40, $t, $w['MKNama'], 'B', 0);
    $p->Cell(6, $t, $w['SKS'], 'B', 0, 'C');
    $p->Cell(15, $t, $w['namaKelas'], 'B', 0);
    $p->Cell(22, $t, $w['_JM'].'-'.$w['_JS'], 'B', 0);
    $p->Cell(50, $t, $w['DSN'], 'B', 0);
    $p->Cell(15, $t, $w['Kehadiran'] . "/" . $w['RencanaKehadiran'], 'B', 0, 'R');
    $p->Cell(10, $t, $persen, 'B', 0, 'R');
    $p->Ln($t);
  }
}
function TampilkanHeaderTabel($p) {
  $p->SetFont('Helvetica', 'IB', 9);
  $t = 5;
  $p->Cell(10, $t, 'No.', 'B', 0);
  $p->Cell(20, $t, 'Kode', 'B', 0);
  $p->Cell(38, $t, 'Mata Kuliah', 'B', 0);
  $p->Cell(9, $t, 'SKS', 'B', 0);
  $p->Cell(12, $t, 'Kelas', 'B', 0);
  $p->Cell(20, $t, 'Jam Kuliah', 'B', 0);
  $p->Cell(50, $t, 'Dosen Pengasuh', 'B', 0);
  $p->Cell(15, $t, 'Hadir', 'B', 0, 'R');
  $p->Cell(10, $t, 'Persen', 'B', 0, 'C');
  $p->Ln($t);
}

function CetakJadwal($JadwalID, $p) {
  TampilkanHeader($jdwl, $p);
}

function TampilkanHeader($jdwl, $p) {
  $lbr = 190;
  $p->SetFont('Helvetica', 'B', 11);
  $p->Cell($lbr, 6, "Rekap Kehadiran Kuliah - $jdwl[TahunID]", 1, 1, 'C');
}
?>
