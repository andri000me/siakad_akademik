<?php

session_start();
include_once "../dwo.lib.php";
include_once "../db.mysql.php";
include_once "../connectdb.php";
include_once "../parameter.php";
include_once "../cekparam.php";
  
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
  KonfirmasiTanggal("../$_SESSION[mnux].uangkesehatanxls.php", "Cetak");
}

function Cetak() {
header("Content-type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename=Laporan-Pembayaran-UangKesehatan");
header("Expires:0");
header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
header("Pragma: public");
echo "<style>td {mso-number-format:'\@';}</style>";
	$table = '';
  BuatJudulLaporan($_SESSION['_PMBPeriodID'], $table);
  BuatIsinya($_SESSION['_PMBPeriodID'], $table);

  echo "</table>";
}
function BuatJudulLaporan($_fid, $p) {
  $Mulai = FormatTanggal($_SESSION['TglMulai']);
  $Selesai = FormatTanggal($_SESSION['TglSelesai']);
  echo  "<p><font size=+3>Laporan Penjualan Uang Kesehatan</font></p>";
  echo  "<p><font size=+1>Periode $Mulai ~ $Selesai</font></p>";
}
function BuatIsinya($period, $p) {
  $s = "select j.*,
      date_format(j.Tanggal, '%d-%m-%Y') as TGL,
      j.Jumlah as JML
    from pmbklinikbayar j
    where j.PMBPeriodID = '$period'
      and j.KodeID = '".KodeID."'
      and ('$_SESSION[TglMulai]' <= j.TanggalBuat
      and j.TanggalBuat <= '$_SESSION[TglSelesai]')
      and j.NA = 'N'
    order by j.PMBKlinikBayarID, j.TanggalBuat";
  //echo "Select: $s";
  $r = _query($s);
  BuatHeaderTabel($p);
  $n = 0; $t = 5; $jml = 0;
  while ($w = _fetch_array($r)) {
    $n++;
    $jml += $w['Jumlah'];
	echo  "<tr>
				<td>".$n."</td>
				<td>".$w['TGL']."</td>
				<td>$w[AplikanID]</td>
				<td>$w[PMBID]</td>
				<td>$w[ProdiID]</td>
				<td>$w[Nama]</td>
				<td>$w[JML]</td>
			</tr>";
  }
  BuatTotalnya($jml, $p);
}
function BuatHeaderTabel($p) {
	echo  "<table><tr><th>Nmr</th>
				<th>Tanggal</th>
				<th>PIN</th>
				<th>No.Pendaftaran</th>
				<th>Jur.</th>
				<th>Nama</th>
				<th>Jumlah</th>
			</tr>";
  
}
function BuatTotalnya($jml, $p) {
  $t = 6;
  $_jml = $jml;
  echo  "<tr><td colspan=6>TOTAL</td>
  				<td>$_jml</td></tr>";
}
?>
