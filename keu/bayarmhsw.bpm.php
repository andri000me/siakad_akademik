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
$lbr = 190;
$mrg = 10;
$id = sqling($_REQUEST['id']);
$trx = $_REQUEST['trx']+0;

// Init PDF
$pdf = new PDF();
$pdf->SetTitle("Kartu Hasil Studi");
$pdf->AddPage();

if ($trx == 1) {
  TampilkanHeader($id, $pdf);
  TampilkanDetailBayar($id, $pdf);
}
elseif ($trx == -1) {
  TampilkanPenarikan($id, $pdf);
}

$pdf->Output();

// *** functions ***
function TampilkanDetailBayar($id, $p) {
  global $arrID;
  $s = "select b2.*, bn.Nama
    from bayarmhsw2 b2
      left outer join bipotnama bn on b2.BIPOTNamaID = bn.BIPOTNamaID
    where b2.BayarMhswID = '$id'
      and b2.NA = 'N'
    order by bn.Urutan";
  $r = _query($s);
  $t = 5; $n = 0; $ttl = 0;
  // Header tabel
  $p->SetFont('Helvetica', 'B', 10);
  $p->Cell(10, $t, 'Nmr', 1, 0);
  $p->Cell(130, $t, 'Keterangan', 1, 0);
  $p->Cell(30, $t, 'Jumlah', 1, 0, 'R');
  $p->Ln($t);
  // Datanya
  $p->SetFont('Helvetica', '', 9);
  while ($w = _fetch_array($r)) {
    $n++;
    $ttl += $w['Jumlah'];
    $_jml = number_format($w['Jumlah']);
    $p->Cell(10, $t, $n, 1, 0);
    $p->Cell(130, $t, $w['Nama'], 1, 0);
    $p->Cell(30, $t, $_jml, 1, 0, 'R');
    $p->Ln($t);
  }
  $max = 10;
  for ($i = $n; $i < $max; $i++) {
    $p->Cell(10, $t, '', 1, 0);
    $p->Cell(130, $t, '', 1, 0);
    $p->Cell(30, $t, '', 1, 0);
    $p->Ln($t);
  }
  $_ttl = number_format($ttl);
  $p->Ln(1);
  $p->Cell(140, $t, 'Total :', 'LBT', 0, 'R');
  $p->Cell(30, $t, $_ttl, 'BTR', 0, 'R');
  $p->Ln($t);
  
  // Buat footernya
  $p->Ln(3);
  $p->SetFont('Helvetica', '', 10);
  $p->Cell(100);
  $p->Cell(80, $t, $arrID['Kota'] . ', ' . date('d M Y'), 0, 1, 'C');
  $p->Ln(14);
  $p->Cell(100);
  $p->Cell(80, $t, '( ................................. )', 0, 1, 'C');
}
function TampilkanPenarikan($id, $p) {
  $lbr = 190;
  $t = 6;
  $p->SetFont('Helvetica', 'B', 14);
  $p->Cell($lbr, $t, "Bukti Penarikan Mahasiswa", 0, 1, 'C');
  $p->Ln(4);
  
  // Datanya
  $p->SetFont('Helvetica', '', 10);
  $w = GetFields('bayarmhsw', "BayarMhswID='$id' and KodeID", KodeID, 
    "*, date_format(Tanggal, '%d %M %Y') as _Tanggal, format(Jumlah, 2) as _Jumlah");
  $Nama = GetaField('mhsw', "MhswID='$w[MhswID]' and KodeID", KodeID, 'Nama');
  
  $arr = array();
  $arr[] = array('No. Penarikan', ':', $w['BayarMhswID']);
  $arr[] = array('Tanggal', ':', $w['_Tanggal']);
  $arr[] = array('NIM/NPM', ':', $w['MhswID']);
  $arr[] = array('Nama Mahasiswa', ':', $Nama);
  $arr[] = array('Jumlah Penarikan', ':', 'Rp. ' . $w['_Jumlah']);
  $arr[] = array('Keterangan', ':', $w['Keterangan']);
  $arr[] = '';
  $arr[] = '';
  // Footer
  $Penerima = GetaField('karyawan', "Login='$w[LoginBuat]' and KodeID", KodeID, 'Nama');
  $arrID = GetFields('identitas', 'Kode', KodeID, '*');
  $arr[] = array('', '', $arrID['Kota'] . ', ' . $w['_Tanggal']);
  $arr[] = array();
  $arr[] = array();
  $arr[] = array('', '', $Penerima);
  // Tampilkan
  $t = 5; $mrg = 16;
  foreach ($arr as $a) {
    $p->Cell($mrg);
    $p->Cell(46, $t, $a[0], 0, 0);
    $p->Cell(5, $t, $a[1], 0, 0);
    $p->Cell(90, $t, $a[2], 0, 0);
    $p->Ln($t);
  }
  
  
}
function TampilkanHeader($id, $p) {
  global $lbr, $mrg;
  $t = 7;
  $p->SetFont('Helvetica', 'B', 14);
  $p->Cell($lbr, $t, "Bukti Pembayaran Mahasiswa", 0, 1, 'C');
  $p->SetFont('Helvetica', 'B', 10);
  $p->Cell($lbr, $t, "Nomer: " . $id, 0, 1, 'C');
  $p->Ln(3);
  
  $byr = GetFields('bayarmhsw', "BayarMhswID='$id' and KodeID", KodeID, "*");
  $mhsw = GetFields('mhsw', "MhswID = '$byr[MhswID]' and KodeID", KodeID, '*');
  $prd = GetaField('prodi', "ProdiID = '$mhsw[ProdiID]' and KodeID", KodeID, 'Nama');
  $arr = array();
  $arr[] = array('NPM/NIRM', ':', $byr['MhswID'],
    'Nama Mhsw', ':', $mhsw['Nama']);
  $arr[] = array('Tahun Akd', ':', $byr['TahunID'],
    'Program Studi', ':', $prd);
  $arr[] = array('Nama Bank', ':', $byr['Bank'],
    'Bukti Setoran', ':', $byr['BuktiSetoran']);
  
  $t = 5;
  $p->SetFont('Helvetica', '', 9);
  foreach ($arr as $a) {
    $p->Cell(30, $t, $a[0], 0, 0);
    $p->Cell(3, $t, $a[1], 0, 0);
    $p->Cell(50, $t, $a[2], 0, 0);
    
    $p->Cell(30, $t, $a[3], 0, 0);
    $p->Cell(3, $t, $a[4], 0, 0);
    $p->Cell(50, $t, $a[5], 0, 0);
    $p->Ln($t);
  }
  $p->Ln(2);
  $p->SetFont('Helvetica', 'B', 10);
  $p->Cell($lbr, $t, "Detail Pembayaran", 0, 1, 'C');
}
?>
