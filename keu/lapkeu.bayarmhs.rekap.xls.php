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
  KonfirmasiTanggal("lapkeu.bayarmhs.rekap.xls.php", "Cetak");
}

function Cetak(){
	global $TahunID, $ProdiID;
	header("Content-type:application/vnd.ms-excel");
	header("Content-Disposition:attachment;filename=laporan_pembayaran_".$_SESSION['TglMulai'].".xls");
	header("Expires:0");
	header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
	header("Pragma: public");
	?><!-- 	
	Author	: Arisal Yanuarafi
	Start	: 28 Mei 2012 -->
	
	<style>
	table,font,h3,h1 { font-family:'Courier New'; line-height:100%; }
	.header{ font-family:Times; font-size:32px; line-height:90%; }
	.garis {height:0px; line-height:0px;}
	
	</style>
<?php
	$s = "SELECT b.BayarMhswID, b.Jumlah, m.MhswID,b.PMBID, m.Nama,p.Nama as NM,b.TanggalBuat from bayarmhsw b 
	left outer join mhsw m on m.MhswID=b.MhswID 
	left outer join pmb p on p.PMBID=b.PMBID 
	where b.Tanggal >= '$_SESSION[TglMulai]' 
	AND b.Tanggal <= '$_SESSION[TglSelesai]' 
	and (b.Bank='OTOMAT' or b.Bank='BANK Nagari') 
	and b.NA='N' group by b.BayarMhswID order by b.Tanggal";
	$r = _query($s);
	$no = 0;
	echo "<h3>Laporan Pembayaran Mahasiswa ke Bank</h3>
			Tanggal ".TanggalFormat($_SESSION['TglMulai'])." s/d ".TanggalFormat($_SESSION['TglSelesai']);
	echo "<table border=1 cellpadding=\"5\" cellspacing=\"5\">
				<tr>
					<th>No.</th>
					<th>Nama</th>
					<th>NPM</th>
					<th>Nomor Bukti</th>
					<th>Tanggal Bayar</th>
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
	while ($w = _fetch_array($r)) {
		$Nama = (empty($w['Nama'])) ? $w['NM']:$w['Nama'];
			$Total = $w['Jumlah'];
			/*$c = GetFields("bayarmhsw b
							left outer join bayarmhsw2 sks on sks.BayarMhswID=b.BayarMhswID AND sks.BIPOTNamaID=2 
							left outer join bayarmhsw2 pma on pma.BayarMhswID=b.BayarMhswID AND pma.BIPOTNamaID=9
							left outer join bayarmhsw2 pem on pem.BayarMhswID=b.BayarMhswID AND pem.BIPOTNamaID=10
							left outer join bayarmhsw2 pot on pot.BayarMhswID=b.BayarMhswID AND pot.BIPOTNamaID in ('3','12','13')
							left outer join bayarmhsw2 den on den.BayarMhswID=b.BayarMhswID AND den.BIPOTNamaID=14", "b.BayarMhswID", $w['BayarMhswID'], 
							"sum(sks.Jumlah) as SKS,
							sum(pem.Jumlah) as PEM,
							sum(pma.Jumlah) as PMA,
							sum(pot.Jumlah) as POT,
							sum(den.Jumlah) as DEN");*/
			$s1 = "SELECT Jumlah,BIPOTNamaID from bayarmhsw2 where BayarMhswID='$w[BayarMhswID]' AND NA='N'";
			$r1 = _query($s1); $c['SKS']=0; $c['SKP']=0; $c['PEM']=0; $c['PMA']=0; $c['POT']=0; $c['DEN']=0;$c['PS']=0;$c['KP']=0;$c['KKN']=0;$c['Skripsi']=0;
			while ($w1 = _fetch_array($r1)) {
				$c['SKS'] = ($w1['BIPOTNamaID'] == '15' || $w1['BIPOTNamaID'] == '2')? $c['SKS']+$w1['Jumlah'] : $c['SKS'] ;
				$c['SKP'] = ($w1['BIPOTNamaID'] == '4')? $c['SKP']+$w1['Jumlah'] : $c['SKP'] ;
				$c['PMA'] = ($w1['BIPOTNamaID'] == '9')? $c['PMA']+$w1['Jumlah'] : $c['PMA'] ;
				$c['PEM'] = ($w1['BIPOTNamaID'] == '10')? $c['PEM']+$w1['Jumlah'] : $c['PEM'] ;
				$c['DEN'] = ($w1['BIPOTNamaID'] == '14')? $c['DEN']+$w1['Jumlah'] : $c['DEN'] ;
				$c['POT'] = ($w1['BIPOTNamaID'] == '3' || $w1['BIPOTNamaID'] == '12' || $w1['BIPOTNamaID'] == '13')? $c['POT']+$w1['Jumlah'] : $c['POT'] ;
				$c['PS'] = ($w1['BIPOTNamaID'] == '20')? $c['PS']+$w1['Jumlah'] : $c['PS'] ;
				$c['KP'] = ($w1['BIPOTNamaID'] == '7')? $c['KP']+$w1['Jumlah'] : $c['KP'] ;
				$c['KKN'] = ($w1['BIPOTNamaID'] == '8')? $c['KKN']+$w1['Jumlah'] : $c['KKN'] ;
				$c['Skripsi'] = ($w1['BIPOTNamaID'] == '6')? $c['Skripsi']+$w1['Jumlah'] : $c['Skripsi'] ;
			}
			
			$no++;
			echo "<tr>
					<td>$no</td>
					<td>".ucwords(strtolower($Nama))."</td>
					<td class='text' style=\"mso-number-format:'\@'\">$w[MhswID]</td>
					<td class='text' style=\"mso-number-format:'\@'\">$w[BayarMhswID]</td>
					<td class='text' style=\"mso-number-format:'\@'\">".TanggalFormat($w['TanggalBuat'])."</td>
					<td align=right>".$c['SKS']."</td>
					<td align=right>".$c['SKP']."</td>
					<td align=right>".$c['PMA']."</td>
					<td align=right>".$c['PEM']."</td>
					<td align=right>".$c['PS']."</td>
					<td align=right>".$c['KP']."</td>
					<td align=right>".$c['KKN']."</td>
					<td align=right>".$c['Skripsi']."</td>
					<td align=right>".$c['DEN']."</td>
					<td align=right>".$c['POT']."</td>
					<td align=right>".$Total."</td>
					</tr>
					";
			$_Total += $w['Jumlah'];
		}
		
	echo "</table><h1>Total Pembayaran: Rp ".number_format($_Total)."</h1>";
}