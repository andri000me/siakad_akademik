<?php

session_start();
include_once "../dwo.lib.php";
include_once "../db.mysql.php";
include_once "../connectdb.php";
include_once "../parameter.php";
include_once "../cekparam.php";
  
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
  KonfirmasiTanggal("../$_SESSION[mnux].bayarmhs.xls3.php", "Cetak");
}

function Cetak() {
  // *** Init PDF
header("Content-type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename=Laporan-Pembayaran-OPS-DETAIL");
header("Expires:0");
header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
header("Pragma: public");
	$table = '<table>';

  BuatIsinya($_SESSION['TahunID'], $_SESSION['ProdiID'], $table);

  echo "</table>";
}

function BuatIsinya($TahunID, $ProdiID, $p) {
	echo $p;
  $s = "select b.*, b2.TanggalBuat, m.Nama as NM, bn.Nama as _Nama, b2.Jumlah as _Jumlah
    from bayarmhsw2 b2 left outer join bayarmhsw b on b2.BayarMhswID=b.BayarMhswID
    left outer join mhsw m on m.MhswID=b.MhswID
    left outer join bipotnama bn on bn.BIPOTNamaID=b2.BIPOTNamaID
    where b.KodeID = '".KodeID."'
      and '$_SESSION[TglMulai]' <= b2.TanggalBuat
      and b2.TanggalBuat <= '$_SESSION[TglSelesai]'
      and b.NA = 'N'
      and b2.NA = 'N'
    order by b2.TanggalBuat,b2.BIPOTNamaID";
    
  $r = _query($s);
  $arrNama = array();
  $arrBID = array();
  $arrJML = array();
  $arrTTL = array();
  $arrTGL = array();
  $arrNPM = array();
  $arrMhsw = array();
  $s1 = "SELECT * from bipotnama where NA='N'";
    $r1 = _query($s1);
	    while ($w1 = _fetch_array($r1)){
	    //if (array_search($NamaTagihan, $arrNama) === false)
	    //$arrNama[] = $NamaTagihan;
	    	if (array_search($w1['NamaTagihan'], $arrNama) === false)	$arrNama[] = $w1['Nama'];
	  	}
  while ($w = _fetch_array($r)) {
    //$NamaTagihan = ($w['Nama']=="Pembayaran") ? $w['TambahanNama']:$w['Nama'];
    $NamaTagihan = $w['_Nama'];
    if (array_search($w['BayarMhswID'], $arrBID) === false)
      $arrBID[] = $w['BayarMhswID'];
	  $arrNPM[$w['BayarMhswID']] = $w['MhswID'];
	  $arrMhsw[$w['BayarMhswID']] = $w['NM'];
	  $arrTGL[$w['BayarMhswID']] = $w['TanggalBuat'];
    $arrJML[$w['BayarMhswID']][$NamaTagihan] += $w['_Jumlah'];
    $arrTTL[$NamaTagihan] += $w['_Jumlah'];
  }
  
  // Tampilkan
  echo "<tr><th>Tanggal</th><th>NPM</th><th>Nama</th><th>No. Bukti</th>";
  foreach ($arrNama as $Nama) {
   echo "<th>$Nama</th>";
  }
  echo "</tr>";
  foreach ($arrBID as $id) {
    $_id = $id;
    echo "<tr><td>".FormatTanggal($arrTGL[$id])."</td>";
	echo "<td style=\"mso-number-format:'\@'\">".$arrNPM[$id]."</td>";
	echo "<td>".$arrMhsw[$id]."</td>";
	echo "<td style=\"mso-number-format:'\@'\">".$_id."</td>";
    foreach ($arrNama as $nama) {
      $jml = $arrJML[$id][$nama];
      echo "<td>".$jml."</td>";
    }
    echo "</tr>";
  }
  // Tampilkan Total
  echo "<tr><td colspan=4>Total</td>";
  foreach ($arrNama as $nama) {
    $jml = $arrTTL[$nama];
    echo "<td>$jml</td>";
  }
  echo "</tr>";
}
?>
