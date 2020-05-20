<?php
session_start();

  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
  
// *** Parameters ***
$id = sqling($_REQUEST['id']);
//$id = '200820009';

$kwi = GetFields('pmbformjual', "KodeID='".KodeID."' and PMBFormJualID", $id, "*");
if (empty($kwi))
  die(ErrorMsg('Error',
    "Data penjualan tidak ditemukan.<br />
    Hubungi Sysadmin untuk informasi lebih lanjut.
    <hr size=1 color=silver />
    <input type=button name='Tutup' value='Tutup' onClick=\"window.close()\" />"));
$gels = GetFields('pmbperiod', "KodeID='".KodeID."' and PMBPeriodID", $kwi['PMBPeriodID'], '*');

// PDF
require("../fpdf.php");

class PDF extends FPDF {
  function Header() {
  }
}

// Mulai...
$lbr = 154;

$pdf = new PDF('P', 'mm', 'A4');
$pdf->SetTitle("Kwitansi Penjualan Formulir");
$pdf->AddPage();

BuatHalaman(0, $kwi, $gels, $pdf);
BuatHalaman(98, $kwi, $gels, $pdf);
BuatHalaman(196, $kwi, $gels, $pdf);

$pdf->Output();

function BuatHalaman($Y, $kwi, $gels, $p)
{

if($Y!=0) $p->SetY($Y);
$p->Image("../img/kwitansi.jpg", 8, $Y+8, 190);
if($Y!=0) $p->SetY($Y+10);
$p->SetFont('Helvetica', 'B', 14);
$p->Cell(30);
$p->Cell($lbr, 9, "", 0, 1, 'C');

BuatIsinya($kwi, $gels, $p);
BuatFooter($kwi, $gels, $p);

}

// *** Functions ***
function BuatFooter($kwi, $gels, $p) {
  $mrg = 30; $knn = 49; $t = 5;
  $p->SetFont('Helvetica', '', 9);
  $p->SetFillColor(224,235,255);
  
  $p->Ln(10.5);
  $p->Cell($mrg);
  //$tempstring = GetaField('aplikan a left outer join presenter p on a.PresenterID=p.PresenterID',  "a.AplikanID='$kwi[AplikanID]' and a.KodeID", KodeID, 'p.Nama');
  $s = "select p.Nama,a.AplikanID,a.HashPassword from aplikan a left outer join presenter p on a.PresenterID=p.PresenterID 
			where a.AplikanID='$kwi[AplikanID]' and a.KodeID='".KodeID."'";
  $r = _query($s);
  $w = _fetch_array($r);
  $p->Cell(100, $t, 'Login: '.$w['AplikanID'].' | Password: '.$w['HashPassword'], 0, 0, 'C');
  $p->Cell($knn, $t, $w['Nama'], 0, 1, 'C');

}

function BuatIsinya($kwi, $gels, $p) {
  include_once "../terbilang.php";
  $terbilang = ucwords(SpellNumberID($kwi['Jumlah'])) . ' Rupiah';
  
  $mrg = 30; $t = 5; $k = 71; $k2=43;

  $p->SetFont('Helvetica', '', 10);
  
  // Baris 1 - No. Kwitansi
  $p->Ln(2);
  $p->Cell($mrg);
  $p->Cell(30, $t, '', 0, 0);
  $p->Cell(103, $t, $kwi['PMBFormJualID'], 0, 1, 'R');
  $p->Ln(6);
  
  // Baris 2 - Nama
  $p->Cell($mrg);
  $p->Cell($k2, $t, '', 0, 0);
  $p->Cell($k, $t, $kwi['Nama'], 0, 1);

  // Baris 3 -  Terbilang
  $p->Cell($mrg);
  $p->Cell($k2, $t, '', 0, 0);
  $p->Cell($k, $t, $terbilang, 0, 1);
  
  // Baris 4 - Untuk pembayaran
  $p->Cell($mrg);
  $p->Cell($k2, $t, '', 0, 0);
  $p->Cell($k, $t, 'Pembelian formulir PMB  ' . $gels['Nama'], 0, 1);
  
  // Baris 5 - Program Studi
  $p->Cell($mrg);
  $p->Cell($k2, $t, '', 0, 0);
	$Aplikan = GetFields('aplikan','AplikanID',$kwi[AplikanID],'*');
  $arr = explode(',', $Aplikan[ProdiID]);
   $ProdiIDs = GetaField('program',"ProgramID",$Aplikan[ProgramID],'Nama');
    $_ProdiIDs = GetaField('prodi',"ProdiID",$Aplikan[ProdiID],'JenjangID');
   $JenjangIDs = GetaField('jenjang',"JenjangID",$_ProdiIDs,'Nama');
  foreach($arr as $isi){
  $NamaProdi = GetaField('prodi', 'ProdiID', $isi, 'Nama');
  $nilai.= $NamaProdi.' ';
  }
  $nilai=substr($nilai,0,-1);
	
	$p->Cell($k, $t, $nilai.' '.$JenjangIDs.' '.$ProdiIDs, 0, 1);
  //$p->Cell($k, $t, $kwi['ProdiID'], 0, 1);
  
  // Baris 6 - Gelombang & Nomor Aplikan
  $p->Cell($mrg);
  $p->Cell($k2+2, $t, '', 0, 0);
  $p->Cell($k, $t, $gels['PMBPeriodID'], 0, 0);
  $p->Cell(0, $t, $kwi['AplikanID'], 0, 1);
  
  // Baris 7 - Rupiahnya & Tanggal Cetak Kwitansi
  $current_date = date('d-m-Y');
  $p->Cell($mrg);
  $p->Cell($k2+8, $t, '', 0, 0);
  $p->Cell($k-15, $t, $kwi['Jumlah'], 0, 0);
  $p->Cell(0, $t, GetaField('identitas', 'Kode', KodeID, 'Kota').', '.$current_date, 0, 1);
  
}
?>
