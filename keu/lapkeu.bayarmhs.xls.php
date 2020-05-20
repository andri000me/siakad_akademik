<?php
session_start();
	  include_once "../dwo.lib.php";
	  include_once "../db.mysql.php";
	  include_once "../connectdb.php";
	  	
$TahunID = GetSetVar('TahunID');
$ProdiID = GetSetVar('ProdiID');
	//if ($_GET['pR']==1){
		header("Content-type:application/vnd.ms-excel");
		header("Content-Disposition:attachment;filename=Lapkeu-Pembayaran-Mahasiswa-$ProdiID-$TahunID.xls");
		header("Expires:0");
		header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
		header("Pragma: public");
	//}
?><style> 
	td, th, body{font-family:"Courier New", Courier, monospace; vertical-align:text-top}
	th{ background-color: #09F; color:#FFF;}
	td{  mso-number-format:"\@"; }
    a{text-decoration:none}
    a:hover{text-decoration:none}</style>
<?php
$tbl = '';
//if (empty($_GET['pR'])) echo "<center><a href='?pR=1&TahunID=$TahunID&ProdiID=$ProdiID'><img src=../img/excel.png>Download</a></center>";
BuatHeader($tbl);

function BuatHeader($tbl) {
	global $ProdiID,$TahunID;
	$Tahun = GetaField('tahun', "ProdiID='$ProdiID' AND TahunID",$TahunID,'Nama');
	$Jurusan = GetaField('prodi', "ProdiID",$ProdiID,'Nama');
	$tbl .= "<table border=1 cellpadding='0' cellspacing=0>
				<tr>
					<td colspan=20 border=0 align=center><h3>LAPORAN PEMBAYARAN MAHASISWA<br />
									JURUSAN ".strtoupper($Jurusan)."<br />
									$Tahun</h3></td>
				</tr>";
	BuatHeaderTabel($tbl);
}

function BuatHeaderTabel($tbl) {
	$tbl .="<tr>
				<th rowspan=2>No.</th>
				<th rowspan=2>NPM</th>
				<th rowspan=2>Nama</th>
				<th colspan=2>SKS</th>
				<th colspan=2>Uang Kuliah</th>
				<th rowspan=2>PMA/BT</th>
				<th rowspan=2>Pembangunan</th>
				<th rowspan=2>Penunjang</th>
				<th rowspan=2>APTISI</th>
				<th rowspan=2>ASKES</th>
				<th rowspan=2>Uang IT</th>
				<th rowspan=2>Regis/PKKMB</th>
				<th rowspan=2>Kec</th>
				<th rowspan=2>Koperasi</th>
				<th rowspan=2>Senat</th>
				<th rowspan=2>Skripsi</th>
				<th rowspan=2>KKN-PPM</th>
				<th rowspan=2>Peradilan Semu</th>
				<th rowspan=2>PLK</th>
				<th rowspan=2>AktifKembali</th>
				<th rowspan=2>Denda</th>
				<th rowspan=2>Potongan Anak Karyawan</th>
				<th rowspan=2>Jumlah</th>
				<th rowspan=2>Tanggal Bayar</th>
				<th rowspan=2>Nomor Bukti</th>
				<th rowspan=2>Jumlah Bayar</th>
			</tr>";
	BuatIsi($tbl);
}

function BuatIsi($tbl) {
	global $TahunID, $ProdiID;
	$whr = (empty($ProdiID))? '':"AND m.ProdiID='$ProdiID'";
	// QUERYNYA tuh disini !!!!
	$s = "SELECT b.*,m.Nama as _NM,bm.BIPOTMhswID from bayarmhsw2 b2 
			left outer join bipotmhsw bm on bm.BIPOTMhswID=b2.BIPOTMhswID
			left outer join bayarmhsw b on b.BayarMhswID=b2.BayarMhswID
			left outer join mhsw m on m.MhswID=b.MhswID
			 where b.TahunID='$TahunID' $whr and b.NA='N'  group by b.MhswID order by b.MhswID";
	$r = _query($s);$no=0;
	while ($w = _fetch_array($r)) {
		$no++;
		// ** Uang Kuliah Teori
		$UKT = '';
		$UKT = GetaField('bipotmhsw', "TahunID='$TahunID' AND NA='N' AND BIPOTNamaID in (15,2) AND MhswID", $w['MhswID'], "sum(Jumlah*Besar)");
		$SKST = GetaField('bipotmhsw', "TahunID='$TahunID' AND NA='N' AND BIPOTNamaID in (15,2)  AND MhswID", $w['MhswID'], "sum(Jumlah)");
		// ** Jika Uang Kuliah Teori dan praktek tidak ditemukan coba cari tanpa field TambahanNama, siapa tau mhs ini angkatan Gaek
		if (empty($UKT) && empty($UKP)) {
			$UKT = GetaField('bipotmhsw', "TahunID='$TahunID' AND NA='N' AND BIPOTNamaID=4 AND MhswID", $w['MhswID'], "sum(Jumlah*Besar)");
			$SKST = GetaField('bipotmhsw', "TahunID='$TahunID' AND NA='N' AND BIPOTNamaID=4 AND MhswID", $w['MhswID'], "sum(Jumlah)");
		}
		// ** PMA
		$PMA='';
		$PMA = GetaField('bipotmhsw', "TahunID='$TahunID' AND NA='N' AND BIPOTNamaID=9 AND MhswID", $w['MhswID'], "sum(Jumlah*Besar)");
		// ** ASKES
		$ASKES ='';
		$ASKES = GetaField('bipotmhsw', "TahunID='$TahunID' AND NA='N' AND BIPOTNamaID=10 AND TambahanNama like 'ASKES' AND MhswID", $w['MhswID'], "sum(Jumlah*Besar)");
		// ** Uang IT
		$IT = '';
		$IT = GetaField('bipotmhsw', "TahunID='$TahunID' AND NA='N' AND BIPOTNamaID=10 AND TambahanNama in ('IT','Uang IT') AND MhswID", $w['MhswID'], "sum(Jumlah*Besar)");
		// ** Regis
		$Regis = '';
		$Regis = GetaField('bipotmhsw', "TahunID='$TahunID' AND NA='N' AND BIPOTNamaID=10 AND TambahanNama in ('REGIS', 'PKKMB') AND MhswID", $w['MhswID'], "sum(Jumlah*Besar)");
		// ** KEC
		$KEC ='';
		$KEC = GetaField('bipotmhsw', "TahunID='$TahunID' AND NA='N' AND BIPOTNamaID=10 AND TambahanNama like 'KEC' AND MhswID", $w['MhswID'], "sum(Jumlah*Besar)");
		// ** Uang Koperasi
		$Kop ='';
		$Kop = GetaField('bipotmhsw', "TahunID='$TahunID' AND NA='N' AND BIPOTNamaID=10 AND (TambahanNama like 'SIMP. KOPERASI%' or TambahanNama like 'Uang KOPERASI') AND MhswID", $w['MhswID'], "sum(Jumlah*Besar)");
		// ** Uang Senat
		$Senat = '';
		$Senat = GetaField('bipotmhsw', "TahunID='$TahunID' AND NA='N' AND BIPOTNamaID=10 AND TambahanNama like 'Senat' AND MhswID", $w['MhswID'], "sum(Jumlah*Besar)");
		// ** Uang APTISI
		$Aptisi = '';
		$Aptisi = GetaField('bipotmhsw', "TahunID='$TahunID' AND NA='N' AND BIPOTNamaID=10 AND TambahanNama like 'APTISI' AND MhswID", $w['MhswID'], "sum(Jumlah*Besar)");
		// ** Uang Pembangunan Tahap II
		$Pembangunan2 = '';
		$Pembangunan2 = GetaField('bipotmhsw', "TahunID='$TahunID' AND NA='N' AND BIPOTNamaID=10 AND TambahanNama like '%Pembangunan%' AND MhswID", $w['MhswID'], "sum(Jumlah*Besar)");
		// ** Uang Penunjang Penyelenggaraan Pendidikan
		$Penunjang = '';
		$Penunjang = GetaField('bipotmhsw', "TahunID='$TahunID' AND NA='N' AND BIPOTNamaID=10 AND TambahanNama like 'Penunjang Penyelenggaraan%' AND MhswID", $w['MhswID'], "sum(Jumlah*Besar)");
		// ** Denda
		$Denda ='';
		$Denda = GetaField('bipotmhsw', "TahunID='$TahunID' AND NA='N' AND BIPOTNamaID=14 AND MhswID", $w['MhswID'], "sum(Jumlah*Besar)");
		// ** AktifKembali
		$AktifKembali ='';
		$AktifKembali = GetaField('bipotmhsw', "TahunID='$TahunID' AND NA='N' AND BIPOTNamaID=17 AND MhswID", $w['MhswID'], "sum(Jumlah*Besar)");
		// ** Praktikum
		$Praktikum ='';$SKSPraktikum='';
		$Praktikum = GetaField('bipotmhsw', "TahunID='$TahunID' AND NA='N' AND BIPOTNamaID=4 AND MhswID", $w['MhswID'], "sum(Jumlah*Besar)");
		$SKSPraktikum = GetaField('bipotmhsw', "TahunID='$TahunID' AND NA='N' AND BIPOTNamaID=4 AND MhswID", $w['MhswID'], "sum(Jumlah)");
		// *** Jumlah Biaya ***
		$Jumlah = $UKT+$Praktikum+$Pembangunan2+$PMA+$ASKES+$IT+$Regis+$KEC+$Kop+$Senat+$Denda+$AktifKembali+$Penunjang;
		// *** Potongan
		$Potongan = '';
		$Potongan = GetaField('bipotmhsw', "TahunID='$TahunID' AND NA='N' AND TrxID='-1' AND MhswID", $w['MhswID'], "sum(Jumlah*Besar)");
		// *** Bayar
		$Bayar ='';
		$Bayar = GetaField('bayarmhsw', "TahunID='$TahunID' AND NA='N' AND MhswID", $w['MhswID'], "sum(Jumlah)");
		$sByr = "SELECT * from bayarmhsw where MhswID='$w[MhswID]' AND TahunID='$TahunID' AND NA='N' limit 3";
		$rByr = _query($sByr); $pemb1='';$pemb2='';$pemb3='';$nob=0;$tgl1='';$tgl2='';$tgl3='';$bukti1='';$bukti2='';$bukti3='';
		while ($wByr = _fetch_array($rByr)) {
			$nob++;
			if ($nob==1) {
				$pemb1 = number_format($wByr['Jumlah']);
				$tgl1 = TanggalFormat($wByr['Tanggal']);
				$bukti1 = $wByr['BayarMhswID'];
			}
			if ($nob==2) {
				$pemb2 = "<br><font color=red><b>".number_format($wByr['Jumlah']);
				$tgl2 = "<br><font color=red><b>".TanggalFormat($wByr['Tanggal']);
				$bukti2 = "<br><font color=red><b>".$wByr['BayarMhswID'];
			}
			if ($nob==3) {
				$pemb3 = "<br><b>".number_format($wByr['Jumlah']);
				$tgl3 = "<br><b>".TanggalFormat($wByr['Tanggal']);
				$bukti3 = "<br><b>".$wByr['BayarMhswID'];
			}
		}
		// *** Biaya
		$Biaya = $Jumlah - $Potongan;
		$Tunggakan = $Biaya - $Bayar;
		$MhswID = $w['MhswID'];
		$tbl .= "<tr valign=top>
					<td>".$no."</td>
					<td class='text'>".$MhswID."</td>
					<td>".ucwords(strtolower($w['_NM']))."</td>
					<td>".number_format($SKST)."</td>
					<td>".number_format($SKSPraktikum)."</td>
					<td>".number_format($UKT)."</td>
					<td>".number_format($Praktikum)."</td>
					<td>".number_format($PMA)."</td>
					<td>".number_format($Pembangunan2)."</td>
					<td>".number_format($Penunjang)."</td>
					<td>".number_format($Aptisi)."</td>
					<td>".number_format($ASKES)."</td>
					<td>".number_format($IT)."</td>
					<td>".number_format($Regis)."</td>
					<td>".number_format($KEC)."</td>
					<td>".number_format($Kop)."</td>
					<td>".number_format($Senat)."</td>
					<td>".number_format($AktifKembali)."</td>
					<td>".number_format($Denda)."</td>
					<td>".number_format($Jumlah)."</td>
					<td>".number_format($Potongan)."</td>
					<td>".number_format($Biaya)."</td>
					<td>".number_format($Bayar)."</td>
					<td>".number_format($Tunggakan)."</td>
					<td>".$tgl1.$tgl2.$tgl3."</td>
					<td class='text'>".$bukti1.$bukti2.$bukti3."</td>
					<td>".$pemb1.$pemb2.$pemb3."</td>
				</tr>";
				
	}
	$tbl .= "</table>";
	echo $tbl;
	/*echo "<script>window.close()</script>"; */
}
