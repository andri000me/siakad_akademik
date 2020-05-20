<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 17 Oktober 2008

session_start();

include_once "../dwo.lib.php";
include_once "../db.mysql.php";
include_once "../connectdb.php";
include_once "../parameter.php";
include_once "../cekparam.php";
include_once "../header_pdf2.php";

// *** Parameters ***
$TahunID = GetSetVar('TahunID');
$ProdiID = GetSetVar('ProdiID');

// *** Init PDF
$pdf = new PDF('L','mm','A4');
$pdf->SetTitle("Rekap Biaya Registrasi Mahasiswa");
$lbr = 290;

BuatIsinya($TahunID, $ProdiID, $pdf);

$pdf->Output();

// *** Functions ***
function BuatIsinya($TahunID, $ProdiID, $p) {
  global $lbr;
  $t = 6;
  $whr_prodi = ($ProdiID == '')? '' : "and k.ProdiID = '$ProdiID' ";
  $s = "select m.Nama as _nama, k.MhswID as _MhswID, date_format(bm.Tanggal,'%d-%m-%Y') as _tgl, k.Biaya as _biaya, k.Bayar as _bayar, bm.Keterangan as _ket
  		from khs k left outer join bayarmhsw bm on k.TahunID = bm.TahunID and bm.MhswID = k.MhswID
  		left outer join mhsw m on k.MhswID = m.MhswID
  		where k.TahunID = '$TahunID' and  k.Sesi = '1' $whr_prodi group by k.MhswID order by m.Nama";
  $q = _query($s);
  BuatHeaderTable($TahunID, $ProdiID, $p);
  $n = 0;
  $rec = 0;
  $totalJum = 0;
  $totalSisa = 0;
  while ($w = _fetch_array($q)){
  	$n++;
	$rec++;
	if ($rec >= 20){
		$rec = 1;
		BuatHeaderTable($TahunID, $ProdiID, $p);
	}
	//$s2 = "select date_format(bm.Tanggal,'%d-%m-%Y') as _tgl, k.Biaya as _biaya, k.Bayar as _bayar from khs k left outer join bayarmhsw bm on k.TahunID = bm.TahunID and bm.MhswID = k.MhswID 
	//		where k.MhswID = '$w[MhswID]' and k.Sesi = '1'";
	//$q2 = _query($s2);
	//$w2 = _fetch_array($q2);
		
	$sisa = ($w[_biaya] - $w[_bayar] == 0)? 'Lunas' :  number_format($w[_biaya] - $w[_bayar],0,'.',',');
	  $p->SetFont('Helvetica', '', 9);
	  $p->Cell(8, $t, $n, 1, 0, 'C');
	  $p->Cell(80, $t, $w[_nama], 1, 0, 'C');
	  $p->Cell(30, $t, $w[_MhswID], 1, 0, 'C');
	  $p->Cell(30, $t, $w[_tgl], 1, 0, 'C');
	  $p->Cell(30, $t, number_format($w[_bayar],0,'.',','), 1, 0, 'C');
	  $p->Cell(30, $t, number_format($w[_bayar],0,'.',','), 1, 0, 'C');
	  $p->Cell(30, $t, $sisa, 1, 0, 'C');
	  $p->Cell(40, $t, $w[_ket], 1, 1, 'C');
	  
	  $totalJum += $w[_bayar];
	  $totalSisa += ($w[_biaya] - $w[_bayar]);
  }
  
  // buat jumlah
  $p->SetFont('Helvetica', 'B', 9);
	  $p->Cell(178, $t, '', 0, 0, 'C');
	  $p->Cell(30, $t, number_format($totalJum,0,'.',','), 1, 0, 'C');
	  $p->Cell(30, $t, number_format($totalSisa,0,'.',','), 1, 1, 'C');
	  
	// buat footer
	BuatFooter($p);
  
}
function BuatHeadertable($TahunID, $ProdiID, $p) {
  global $lbr;
  $t = 6;
  $prd = GetaField('prodi', "ProdiID = '$ProdiID' and KodeID", KodeID, 'Nama');
  $p->AddPage();
  $p->SetFont('Helvetica', 'B', 14);
  $p->Cell($lbr, $t, "Laporan Pembayaran Registrasi Mahasiswa -- $TahunID", 0, 1, 'C');
  $p->Cell($lbr, $t, "Program Studi: $prd", 0, 1, 'C');
  $p->Ln(4);
  
  $p->SetFont('Helvetica', 'B', 10);
  $p->Cell(8, $t*2, 'NO', 1, 0, 'C');
  $p->Cell(80, $t*2, 'N A M A', 1, 0, 'C');
  $p->Cell(30, $t*2, 'NIM', 1, 0, 'C');
  $p->Cell(60, $t, 'UANG KULIAH', 1, 0, 'C');
  $p->Cell(30, $t*2, 'JUMLAH', 1, 0, 'C');
  $p->Cell(30, $t*2, 'SISA', 1, 0, 'C');
  $p->Cell(40, $t*2, 'KETERANGAN', 1, 1, 'C');
  
  $p->setXY($p->getX()+118,$p->getY()-$t);
  $p->Cell(30, $t, 'TGL BAYAR', 1, 0, 'C');
  $p->Cell(30, $t, 'SEMESTER 1', 1, 1, 'C');
}

function BuatFooter($p){
  $t = 6;
  $p->Ln($t*2);
  $p->SetFont('Helvetica', '', 10);
  $p->Cell(218, $t, '', 0, 0, 'C');
  $p->Cell(60, $t, 'Jakarta, '.date('d-m-Y'), 0, 1, 'C');
  $pjbt = GetFields('pejabat','KodeJabatan','KABAA','*');
  $p->Cell(218, $t, '', 0, 0, 'C');
  $p->Cell(60, $t, $pjbt[Jabatan], 0, 1, 'C');
  
  $p->Cell(218, $t*4, '', 0, 1, 'C');
  
  $p->Cell(218, $t, '', 0, 0, 'C');
  $p->Cell(60, $t, $pjbt[Nama], 0, 1, 'C');
  $p->Cell(218, $t, '', 0, 0, 'C');
  $p->Cell(60, $t, 'NIP. '.$pjbt[NIP], 0, 1, 'C');
}
?>
