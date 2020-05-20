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
$TglMulai_d = GetSetVar('TglMulai_d', '01');
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
  KonfirmasiTanggal("../$_SESSION[mnux].bayarbulan.php", "Cetak");
}

function Cetak() {
  // *** Init PDF
  $pdf = new PDF();
  $pdf->SetTitle("Rekap Pembayaran Mahasiswa Per Bulan");
  $pdf->AddPage('L', 'Legal');
  $lbr = 190;

  BuatIsinya($_SESSION['TahunID'], $_SESSION['ProdiID'], $pdf);

  $pdf->Output();
}

function BuatIsinya($TahunID, $ProdiID, $p) {
  $s = "select b2.*, bn.Singkatan, bn.Urutan, b.Tanggal,bm.TrxID
    from bayarmhsw2 b2
      left outer join bayarmhsw b on b2.BayarMhswID = b.BayarMhswID
      left outer join bipotnama bn on b2.BIPOTNamaID = bn.BIPOTNamaID
	  left outer join bipotmhsw bm on bm.BIPOTMhswID=b2.BIPOTMhswID
    where b.KodeID = '".KodeID."'
      and b.TahunID = '$TahunID'
      and '$_SESSION[TglMulai]' <= b.Tanggal
      and b.Tanggal <= '$_SESSION[TglSelesai]'
      and b.NA = 'N'
      and b2.NA = 'N'
    order by b.Tanggal,bn.Urutan";
  $r = _query($s);
  $arrNama = array();
  $arrTGL = array();
  $arrJML = array();
  $arrTTL = array();
  while ($w = _fetch_array($r)) {
    if (array_search($w['Singkatan'], $arrNama) === false)
      $arrNama[] = $w['Singkatan'];
    if (array_search($w['Tanggal'], $arrTGL) === false)
      $arrTGL[] = $w['Tanggal'];
    $arrJML[$w['Tanggal']][$w['Singkatan']] += $w['Jumlah'];
    $arrTTL[$w['Singkatan']] += $w['Jumlah'];
  }
  
  // Tampilkan
  $t = 4; $lbr0 = 20; $lbr = 23;
  $p->SetFont('Helvetica', 'B', 8);
  $p->Cell($lbr0, $t, 'Tanggal', 1, 0);
  foreach ($arrNama as $Nama) {
    $p->Cell($lbr, $t, $Nama, 1, 0, 'C');
  }
  $p->Ln($t);
  // Data
  $p->SetFont('Helvetica', '', 8);
  foreach ($arrTGL as $tgl) {
    $_tgl = FormatTanggal($tgl);
    $p->Cell($lbr0, $t, $_tgl, 1, 0);
    foreach ($arrNama as $nama) {
      $jml = number_format($arrJML[$tgl][$nama]);
      $p->Cell($lbr, $t, $jml, 1, 0, 'R');
    }
    $p->Ln($t);
  }
  $p->Ln(2);
  // Tampilkan Total
  $p->Cell($lbr0, $t, 'Total:', 1, 0, 'R');
  foreach ($arrNama as $nama) {
    $jml = number_format($arrTTL[$nama]);
    $p->Cell($lbr, $t, $jml, 1, 0, 'R');
  }
  $p->Ln($t);
}
?>
