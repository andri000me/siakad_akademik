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
  KonfirmasiTanggal("../$_SESSION[mnux].jualformulir.xls.php", "Cetak");
}

function Cetak() {
header("Content-type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename=Laporan-Pembayaran-Formulir");
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
  echo  "<p><font size=+3>Laporan Penjualan Formulir</font></p>";
  echo  "<p><font size=+1>Periode $Mulai ~ $Selesai</font></p>";
}
function BuatIsinya($period, $p) {
  $s = "select j.*, a.Nama, a.AplikanID,
      date_format(j.Tanggal, '%d-%m-%Y') as TGL,
      j.Jumlah as JML
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
	$refersal = (GetaField('ubh_bank.djamboe_bankaccount', 'user_id',$w['AplikanID'], 'flag')=='R' ? "Reversal":"Payment");
	$nomorbukti = GetaField('ubh_bank.djamboe_bankaccount', 'user_id',$w['AplikanID'], 'nomor_bukti');
	echo  "<tr>
				<td>".$n."</td>
				<td>".$w['TGL']."</td>
				<td>$nomorbukti</td>
				<td>".$w['AplikanID']."</td>
				<td>".$w['Nama']."</td>
				<td>$w[JML]</td>
				<td>$refersal</td>
				<td>".(substr($w['AplikanID'],0,2)=='CM' ? "SPP":"Bank")."</td>
			</tr>";
  }
  BuatTotalnya($jml, $p);
}
function BuatHeaderTabel($fid, $p) {
  $FRM = GetaField('pmbformulir', 'PMBFormulirID', $fid, 'Nama');

	echo  "<table><tr><th>Nmr</th>
				<th>Tanggal</th>
				<th>Kode Daftar</th>
				<th>Nama</th>
				<th>Bukti Setoran</th>
				<th>Jumlah</th>
				<th>Bayar Melalui</th>
				<th>Keterangan</th>
			</tr>";
  
}
function BuatTotalnya($jml, $p) {
  $t = 6;
  $_jml = $jml;
  echo  "<tr><td colspan=3>TOTAL</td>
  				<td>$_jml</td></tr>";
}
?>
