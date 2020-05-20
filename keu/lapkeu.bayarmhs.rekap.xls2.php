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
	header("Content-Disposition:attachment;filename=laporan_pembayaran_".$_SESSION['TahunID'].".xls");
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
					<th>Teori</th>
					<th>Praktek</th>
					<th>PMA</th>
					<th>Her-Registrasi</th>
					<th>Peradilan Semu</th>
					<th>KP/PL/PLK</th>
					<th>KKN</th>
					<th>Skripsi</th>
					<th>Denda</th>
					<th>Potongan</th>
					<th>Total</th>
				</tr>";$no=0;


				$c['SKS'] = GetaField('bayarmhsw2 b2 left outer join bayarmhsw b on b.BayarMhswID = b2.BayarMhswID',"b2.BIPOTNamaID in ('15','2') and b2.NA='N' and b.TahunID", $_SESSION['TahunID'],"SUM(b2.Jumlah)");
				$c['SKP'] = GetaField('bayarmhsw2 b2 left outer join bayarmhsw b on b.BayarMhswID = b2.BayarMhswID',"b2.BIPOTNamaID in ('4')  and b2.NA='N' and b.TahunID", $_SESSION['TahunID'],"SUM(b2.Jumlah)");
				$c['PMA'] = GetaField('bayarmhsw2 b2 left outer join bayarmhsw b on b.BayarMhswID = b2.BayarMhswID',"b2.BIPOTNamaID in('9')  and b2.NA='N' and b.TahunID", $_SESSION['TahunID'],"SUM(b2.Jumlah)");
				$c['PEM'] = GetaField('bayarmhsw2 b2 left outer join bayarmhsw b on b.BayarMhswID = b2.BayarMhswID',"b2.BIPOTNamaID in('10')  and b2.NA='N' and b.TahunID", $_SESSION['TahunID'],"SUM(b2.Jumlah)");
				$c['DEN'] = GetaField('bayarmhsw2 b2 left outer join bayarmhsw b on b.BayarMhswID = b2.BayarMhswID',"b2.BIPOTNamaID in('14')  and b2.NA='N' and b.TahunID", $_SESSION['TahunID'],"SUM(b2.Jumlah)");
				$c['POT'] = GetaField('bayarmhsw2 b2 left outer join bayarmhsw b on b.BayarMhswID = b2.BayarMhswID',"b2.BIPOTNamaID in('3','12','13')  and b2.NA='N' and b.TahunID", $_SESSION['TahunID'],"SUM(b2.Jumlah)");
				$c['PS'] = GetaField('bayarmhsw2 b2 left outer join bayarmhsw b on b.BayarMhswID = b2.BayarMhswID',"b2.BIPOTNamaID in('20')  and b2.NA='N' and b.TahunID", $_SESSION['TahunID'],"SUM(b2.Jumlah)");
				$c['KP'] = GetaField('bayarmhsw2 b2 left outer join bayarmhsw b on b.BayarMhswID = b2.BayarMhswID',"b2.BIPOTNamaID in('7')  and b2.NA='N' and b.TahunID", $_SESSION['TahunID'],"SUM(b2.Jumlah)");
				$c['KKN'] = GetaField('bayarmhsw2 b2 left outer join bayarmhsw b on b.BayarMhswID = b2.BayarMhswID',"b2.BIPOTNamaID in('8')  and b2.NA='N' and b.TahunID", $_SESSION['TahunID'],"SUM(b2.Jumlah)");
				$c['Skripsi'] = GetaField('bayarmhsw2 b2 left outer join bayarmhsw b on b.BayarMhswID = b2.BayarMhswID',"b2.BIPOTNamaID in('6')  and b2.NA='N' and b.TahunID", $_SESSION['TahunID'],"SUM(b2.Jumlah)");
				$Total = $c['SKS'] + $c['SKP'] + $c['PMA'] + $c['PEM'] + $c['DEN'] + $c['POT'] + $c['PS'] + $c['KP'] + $c['KKN'] + $c['Skripsi'];
			
			$no++;
			echo "<tr>
					<td>$no</td>
					<td align=right>".number_format($c['SKS'])."</td>
					<td align=right>".number_format($c['SKP'])."</td>
					<td align=right>".number_format($c['PMA'])."</td>
					<td align=right>".number_format($c['PEM'])."</td>
					<td align=right>".number_format($c['PS'])."</td>
					<td align=right>".number_format($c['KP'])."</td>
					<td align=right>".number_format($c['KKN'])."</td>
					<td align=right>".number_format($c['Skripsi'])."</td>
					<td align=right>".number_format($c['DEN'])."</td>
					<td align=right>".number_format($c['POT'])."</td>
					<td align=right>".number_format($Total)."</td>
					</tr>
					";
			echo "<tr><td colspan=12><i>*) Rekap Her-Registrasi termasuk uang kuliah mahasiswa baru 2015. (Khusus rekap 20151).</td></tr>";
			
		
	echo "</table>";