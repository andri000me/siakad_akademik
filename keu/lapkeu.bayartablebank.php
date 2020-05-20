<?php
session_start(); 
include_once "../dwo.lib.php";
include_once "../db.mysql.php";
include_once "../connectdb.php";
include_once "../parameter.php";
include_once "../cekparam.php";
error_reporting(E_ALL);
// *** Parameters ***
$TahunID = GetSetVar('TahunID');
$ProdiID = GetSetVar('ProdiID');

	header("Content-type:application/vnd.ms-excel");
	header("Content-Disposition:attachment;filename=laporan_pembayaran_bank_$ProdiID_".$_SESSION['TahunID'].".xls");
	header("Expires:0");
	header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
	header("Pragma: public");
	?><!-- 	
	Author	: Arisal Yanuarafi
	Start	: 3 Agustus 2015 -->
	
	<style>
	table,font,h3,h1 { font-family:'Courier New'; line-height:100%; }
	.header{ font-family:Times; font-size:32px; line-height:90%; }
	.garis {height:0px; line-height:0px;}
	td{
	  mso-number-format:"\@";/*force text*/
	}
	</style>
<?php
	$no = 0;
	echo "<h3>Rekapitulasi Pembayaran Mahasiswa ke Bank</h3>";
	echo "<table border=1 cellpadding=\"5\" cellspacing=\"5\">
				<tr>
					<th>No.</th>
					<th>ProdiID</th>
					<th>Total Pembayaran</th>
				</tr>";$no=0;
			
$prd = GetaField('prodi', "ProdiID", $ProdiID, "KodeLama");
$s = "SELECT sum(nilai_bayar) as Bayar from ubh_bank.pembayaran where prodi='$prd' and tahun_ajaran='$TahunID' and flag=1";
$r = _query($s);
while ($w = _fetch_array($r)){
	$no++;
			echo "<tr>
					<td>$no</td>
					<td>$ProdiID</td>
					<td>".number_format($w['Bayar'],0)."</td>
					</tr>
					";
}
		
	echo "</table>";