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
  KonfirmasiTanggal("../$_SESSION[mnux].mhswbayar.php", "Cetak");
}

function Cetak() {
  // *** Init PDF
  $pdf = new PDF();
  $pdf->SetTitle("Pembayaran Mahasiswa Per Periode");
  $pdf->AddPage();
  $lbr = 190;

  BuatIsinya($_SESSION['TahunID'], $_SESSION['ProdiID'], $pdf);

  $pdf->Output();
}

function BuatIsinya($TahunID, $ProdiID, $p) {
  global $lbr;
  $_prd = ($ProdiID == '')? '' : "and m.ProdiID = '$ProdiID' ";
  $s = "select b.BayarMhswID, b.BayarMhswRef,
      date_format(b.Tanggal, '%d-%m-%Y') as TGL,
      b.TahunID, b.RekeningID, b.MhswID, b.TrxID,
      b.Bank, b.BuktiSetoran, b.Tanggal,
      b.Jumlah, b.JumlahLain,
      (left(m.Nama,20)) as NamaMhsw,
      format(b.Jumlah, 0) as JML
    from bayarmhsw b
      left outer join mhsw m on m.MhswID = b.MhswID and m.KodeID = '".KodeID."'
    where b.KodeID = '".KodeID."'
      and b.TahunID = '$TahunID'
      and b.PMBMhswID = 1
      and b.NA = 'N'
      and '$_SESSION[TglMulai]' <= b.Tanggal
      and b.Tanggal <= '$_SESSION[TglSelesai]'
      $_prd
    order by b.Tanggal, b.MhswID";
  $r = _query($s);
  $n = 0; $t = 6;
  BuatHeadernya($TahunID, $ProdiID, $p);
  $ttl = 0;
  while ($w = _fetch_array($r)) {
    $n++;
	$JML = $w['TrxID']*$w['Jumlah'];
	$_JML = number_format($JML, 0, ',', '.'); 
    $ttl += $JML;
    $p->SetFont('Helvetica', '', 10);
    $p->Cell(10, $t, $n, 'LB', 0);
    $p->Cell(22, $t, $w['TGL'], 'B', 0);
    $p->Cell(28, $t, $w['MhswID'], 'B', 0);
    $p->Cell(48, $t, $w['NamaMhsw'], 'B', 0);
    $p->Cell(23, $t, $_JML, 'B', 0, 'R');
    $p->Cell(22, $t, $w['Bank'], 'B', 0,'C');
    $p->Cell(34, $t, $w['BuktiSetoran'], 'BR', 0);
    $p->Ln($t);
  }
  $p->Cell($lbr, 1, ' ', 1, 1);
  $_ttl = number_format($ttl);
  $p->SetFont('Helvetica', 'B', 11);
  $p->Cell(105, 7, 'TOTAL :', 0, 0, 'R');
  $p->Cell(30, 7, $_ttl, 0, 0, 'R');
}
function BuatHeadernya($TahunID, $ProdiID, $p) {
  global $lbr;
  $t = 6;
  $TglMulai = FormatTanggal($_SESSION['TglMulai']);
  $TglSelesai = FormatTanggal($_SESSION['TglSelesai']);
  $p->SetFont('Helvetica', 'B', 12);
  $p->Cell($lbr, $t, "Laporan Pembayaran Mahasiswa", 0, 1, 'C');
  $p->Cell($lbr, $t, "Rentang Pembayaran: $TglMulai sampai $TglSelesai", 0, 1, 'C');
  $p->Ln(2);
  
  $t = 6;
  $p->SetFont('Helvetica', 'BI', 10);
  $p->Cell(10, $t, 'Nmr', 1, 0);
  $p->Cell(22, $t, 'Tanggal', 1, 0);
  $p->Cell(28, $t, 'N I M', 1, 0);
  $p->Cell(48, $t, 'Nama Mahasiswa', 1, 0);
  $p->Cell(23, $t, 'Jumlah', 1, 0);
  $p->Cell(22, $t, 'Bank/Tunai', 1, 0);
  $p->Cell(34, $t, 'No. Bukti', 1, 0);
  
  $p->Ln($t);
}
?>
