<?php

session_start();
include_once "../dwo.lib.php";
include_once "../db.mysql.php";
include_once "../connectdb.php";
include_once "../parameter.php";
include_once "../cekparam.php";
include_once "../header_pdf.php";
  
// *** Parameters ***
$_PMBPeriodID = GetSetVar('_PMBPeriodID');
// Tgl Mulai
$TglMulai_y = GetSetVar('TglMulai_y', date('Y'));
$TglMulai_m = GetSetVar('TglMulai_m', date('m'));
$TglMulai_d = GetSetVar('TglMulai_d', date('d'));
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
  KonfirmasiTanggal("../$_SESSION[mnux].jualformulir.php", "Cetak");
}

function Cetak() {
  // *** Init PDF
  $pdf = new PDF();
  $pdf->SetTitle("Penjualan Formulir");
  $pdf->AddPage();
  $lbr = 190;
  BuatJudulLaporan($_SESSION['_PMBPeriodID'], $pdf);
  BuatIsinya($_SESSION['_PMBPeriodID'], $pdf);

  $pdf->Output();
}
function BuatJudulLaporan($_fid, $p) {
  $lbr = 190; $t = 6;
  $Mulai = FormatTanggal($_SESSION['TglMulai']);
  $Selesai = FormatTanggal($_SESSION['TglSelesai']);
  $p->SetFont('Helvetica', 'B', 10);
  $p->Cell($lbr, $t, "Laporan Penjualan Formulir", 0, 1, 'C');
  $p->Cell($lbr, $t, "Periode: $Mulai ~ $Selesai", 0, 1, 'C');
  $p->Ln(2);
}
function BuatIsinya($period, $p) {
  $s = "select j.*, a.Nama as _Nama,
      date_format(j.Tanggal, '%d-%m-%Y') as TGL,
      format(j.Jumlah, 0) as JML
    from pmbformjual j left outer join aplikan a on a.AplikanID=j.AplikanID
    where j.PMBPeriodID = '$period'
      and j.KodeID = '".KodeID."'
      and '$_SESSION[TglMulai]' <= j.Tanggal
      and j.Tanggal <= '$_SESSION[TglSelesai]'
      and j.NA = 'N'
    order by j.PMBFormulirID, j.Tanggal";
  //echo "Select: $s";
  $r = _query($s);
  
  $n = 0; $t = 5; $jml = 0; $_fid = 'a9879sadf'; $_fid0 = $_fid;
  while ($w = _fetch_array($r)) {
    if ($_fid != $w['PMBFormulirID']) {
      if ($_fid != $_fid0) {
        BuatTotalnya($jml, $p);
      }
      $_fid = $w['PMBFormulirID'];
      $jml = 0; $n = 0;
      BuatHeaderTabel($_fid, $p);
    }
    $n++;
    $jml += $w['Jumlah'];
    $p->SetFont('Helvetica', '', 8);
    $p->Cell(13, $t, $n, 'LB', 0);
    $p->Cell(22, $t, $w['TGL'], 'B', 0);
    $p->Cell(50, $t, $w['BuktiSetoran'], 'B', 0);
    $p->Cell(85, $t, (!empty($w['_Nama']) ? strtoupper($w['_Nama']):"---belum login---"), 'B', 0);
    $p->Cell(22, $t, $w['JML'], 'BR', 0, 'R');
    
    $p->Ln($t);
  }
  BuatTotalnya($jml, $p);
}
function BuatHeaderTabel($fid, $p) {
  $FRM = GetaField('pmbformulir', 'PMBFormulirID', $fid, 'Nama');
  $t = 6;
  $p->SetFont('Helvetica', 'B', 9);
  // Judul Formulir
  $p->Cell(190, $t, $FRM, 0, 1);
  
  // Judul Kolom
  $p->Cell(13, $t, 'Nmr', 1, 0);
  $p->Cell(22, $t, 'Tanggal', 1, 0);
  $p->Cell(50, $t, 'Bukti Setoran', 1, 0);
  $p->Cell(85, $t, 'Nama Pembeli', 1, 0);
  $p->Cell(22, $t, 'Jumlah', 1, 0, 'R');
  
  $p->Ln($t);
}
function BuatTotalnya($jml, $p) {
  $t = 6;
  $_jml = number_format($jml);
  $p->SetFont('Helvetica', 'B', 10);
  $p->Cell(112, $t, 'TOTAL :', 0, 0, 'R');
  $p->Cell(25, $t, $_jml, 0, 0, 'R');
  $p->Ln($t+1);
}
?>
