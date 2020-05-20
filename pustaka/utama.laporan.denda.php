<?php

session_start();
include_once "../dwo.lib.php";
include_once "../db.mysql.php";
include_once "../connectdb.php";
include_once "../parameter.php";
include_once "../cekparam.php";
include_once "../header_pdf.php";
  
// *** Parameters ***
$ProdiID = GetSetVar('_pustakaProdiID');
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
  KonfirmasiTanggal("../$_SESSION[mnux].laporan.denda.php", "Cetak");
}

function Cetak() {
  // *** Init PDF
  $pdf = new PDF();
  $pdf->SetAutoPageBreak(true, 5);
  $pdf->SetTitle("Laporan Denda");
  $pdf->AddPage();
  $lbr = 190;

  BuatIsinya($_SESSION['_pustakaProdiID'], $pdf);

  $pdf->Output();
}

function BuatIsinya($ProdiID, $p) {
  $lbr = 190; $t = 5;
  $prd = (!empty($_SESSION['_pustakaProdiID']) ? GetaField("prodi","ProdiID",$_SESSION['_pustakaProdiID'],"Nama"): "Semua Prodi");
  $p->SetFont('Helvetica', 'B', 14);
  $p->Cell($lbr, $t, "Laporan Pembayaran Denda", 0, 1, 'C');
  $p->SetFont('Helvetica', 'B', 12);
  $p->Cell($lbr, $t, "Periode: ".TanggalFormat($_SESSION['TglMulai'])." s/d ".TanggalFormat($_SESSION['TglSelesai']), 0, 1, 'C');
  $p->Cell($lbr, $t, "Program Studi: $prd", 0, 1, 'C');
  $p->Ln(2);
  // Buat header tabel
  $t = 5;
  $p->SetFont('Helvetica', 'B', 8);
  $p->Cell(10, $t, 'Nmr', 1, 0);
  $p->Cell(20, $t, 'Tanggal', 1, 0);
  $p->Cell(25, $t, 'NIM', 1, 0);
  $p->Cell(80, $t, 'Nama', 1, 0);
  $p->Cell(22, $t, 'Keterlambatan', 1, 0,'C');
  $p->Cell(20, $t, 'Denda (Rp)', 1, 0,'C');
  $p->Ln($t);
  
  $whr = (!empty($_SESSION['_pustakaProdiID']) ? " and m.ProdiID='$_SESSION[_pustakaProdiID]' ": "");
  $s = "select s.AnggotaID, m.Nama, s2.TanggalKembali, datediff(date_format(s2.TanggalKembali,'%Y-%m-%d'),s2.TanggalHarusKembali) as Keterlambatan, s2.Denda
  			 from pustaka_sirkulasi2 s2 
			 left outer join pustaka_sirkulasi s on s.SirkulasiID=s2.SirkulasiID
			 left outer join mhsw m on m.MhswID = s.AnggotaID
			where
      '$_SESSION[TglMulai]' <= s2.TanggalKembali
      and s2.TanggalKembali <= '$_SESSION[TglSelesai]'
		and s2.Denda > 0
	  $whr
    order by  s.TanggalPinjam, m.ProdiID,m.ProgramID";
  $r = _query($s); $n=0;
   while ($w = _fetch_array($r)) {
    $n++;
    $p->SetFont('Helvetica', '', 8);
    $p->Cell(10, $t, $n, 'LB', 0, 'R');
    $p->Cell(20, $t, $w['TanggalKembali'], 'B', 0);
	$p->Cell(25, $t, $w['AnggotaID'], 'B', 0);
	$p->Cell(80, $t, $w['Nama'], 'B', 0);
	$p->Cell(22, $t, $w['Keterlambatan'], 'B', 0,'C');
	$p->Cell(20, $t, number_format($w['Denda'],0), 'BR', 0,'R');
    
    $p->Ln($t);
	
	$totDenda += $w['Denda'];
   }
   $p->SetFont('Helvetica', 'B', 8);
    $p->Cell(157, $t, 'Total', 'LB', 0, 'R');
	$p->Cell(20, $t, number_format($totDenda,0), 'BR', 0,'R');
	$p->Ln($t);
}
?>
