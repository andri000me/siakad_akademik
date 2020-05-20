<?php	session_start();
	/* 	Author	: Arisal Yanuarafi
		Start	: 21 Agustus 2013 3:19 AM
	*/
	
	include_once "../dwo.lib.php";
  	include_once "../db.mysql.php";
  	include_once "../connectdb.php";
  	include_once "../parameter.php";
  	include_once "../cekparam.php";
	
	// *** Parameter ***
	$MhswID		= GetSetVar('_TrMhswID');
	$ProdiID 	= GetaField('mhsw', "MhswID", $MhswID, "ProdiID");
	$DataProdi	= GetFields('prodi', "NA='N' AND ProdiID", $ProdiID, "*");
	
    //------------------------
	// DB untuk nilai terendah
	//------------------------
	$NilaiTerendah = GetFields("nilai", "NA='N' AND Nama='E' AND ProdiID", $ProdiID, "*");


	$Identitas 		= GetFields("identitas", "Kode", KodeID, "*");
	$Mhsw 			= GetFields("mhsw", "NA='N' AND MhswID", $MhswID, "*");
	$DataFakultas 	= GetFields("fakultas", "NA='N' AND FakultasID", $DataProdi['FakultasID'], "*");
	$Jenjang		= GetFields("jenjang", "NA='N' AND JenjangID", $DataProdi['JenjangID'], "Nama, Keterangan");

	$Skripsi 		= GetFields("wisudawan", "MhswID", $MhswID, "*");

	echo "<html>
	<head>
	<title>Transkrip Akademik ".$Mhsw['Nama']." - NPM ".$Mhsw['MhswID']."</title>
	<style type='text/css'>
	body {
		margin-left: 0 px;
		font-family:  Arial, Tahoma, Verdana;
		font-size: 10px;
		padding: 0px;
		background: #ffffff;
	}
	td {
		font-size: 11px;
		font-family: 'Courier New', Arial, Tahoma, Verdana;
		padding: 0px;
		background: #ffffff;
	}
	.logo {
		font-size: 18px;
		font-family: Georgia, 'Book Antiqua', 'Times New Roman', Tahoma, Tahoma, Verdana;
		font-weight: bold;
		color: #0000ff;
	}
	.kampus {
		font-family: Tahoma, Arial, Verdana;
		font-weight: bold;
		color: #0000ff;
	}
	.fakultas {
		font-size: 16px;
		font-family: Arial, Tahoma, Verdana;
		font-weight: bold;
		color: #0000ff;
	}
	.alamat {
		font-size: 8px;
		font-family: Tahoma, Arial, Verdana;
		color: #0000ff;
	}
	.tabel {
		font-family: Tahoma, Arial, Verdana;
		text-align:center;
		border-top:2px solid #000000;
		border-bottom:1px dotted #000000;
		padding: 2px;
	}
	.tabelbawah {
		border-top:1px solid #000000;
	}
	.catatan {
		padding-top:10px;
		font-size: 10px;
		font-family: Arial, Tahoma, Verdana;
		padding: 0px;
		background: #ffffff;
	}
	.tabel_bawah {
		font-family: Tahoma, Arial, Verdana;
		border-top:3px solid #000000;
		border-bottom:1px solid #000000;
	}
	</style>
	<style media='print'>
	.onlyscreen {
		display: none;
	}
	</style>
	<script type='text/javascript'>
	window.print();
	</script>
	</head>

	<body>
	<center>
	<div style='width:650px; text-align:center; background: #ffffff;'>";



			echo "<div class='fakultas'>FAKULTAS ".strtoupper($DataFakultas['Nama'])."</div>
			<div class='logo'>".$Identitas['Nama']."</div>
			<div style='font-size:16px; font-weight:bold'><u>TRANSKRIP AKADEMIK SEMENTARA</u></div>
			<br />
			<div style='text-align:left; padding-left:30px'>
			<table>
			<tr>
			<td>Diberikan Kepada</td><td>:</td><td><div style='font-size:14px' font-weight:bold'>".$Mhsw['Nama']."</div></td>
			</tr>
			<tr>
			<td>Tempat Lahir</td><td>:</td><td>".$Mhsw['TempatLahir']."</td>
			</tr>
			<tr>
			<td>Tanggal Lahir</td><td>:</td><td>";

			if($Mhsw['TanggalLahir'] != "")
			{	
				$TanggalLahirFinal = GetaField('mhsw', "MhswID", $Mhsw['MhswID'], 'TanggalLahirIjazah');
				$TanggalLahir = (empty($TanggalLahirFinal))? TanggalFormat($Mhsw['TanggalLahir']) : $TanggalLahirFinal; 
				echo $TanggalLahir;
			}
			
			echo "</td>
			</tr>
			<tr>
			<td>Nomor Pokok Mahasiswa</td><td>:</td><td>".$Mhsw['MhswID']."</td>
			</tr>
			<tr>
			<td>Fakultas</td><td>:</td><td>".str_replace(" Dan ", " dan ",ucwords(strtolower($DataFakultas['Nama'])))."</td>
			</tr>";

			if($DataFakultas['Nama'] == "Hukum")
			{
				echo "<tr>
				<td>Program Studi</td><td>:</td><td>".ucwords(strtolower($DataProdi['Nama']))."</td>
				</tr>

				<tr>
				<td>Program Kekhususan</td><td>:</td><td>".GetaField('program_kekhususan', "pk_status!='0' AND pk_status!='1' AND pk_status!='3' AND MhswID",$Mhsw['MhswID'],"pk_namapilihan")."</td>
				</tr>";

			}else{

				echo "<tr>
				<td>Jurusan</td><td>:</td><td>".$DataProdi['Nama']."</td>
				</tr>

				<tr>
				<td>Program Studi</td><td>:</td><td>".ucwords(strtolower($DataProdi['Nama']))."</td>
				</tr>";
			}

			echo "<tr>
			<td>Jenjang Pendidikan</td><td>:</td><td>".$Jenjang['Nama']."</td>
			</tr>
			<tr>
			<td>Status</td><td>:</td><td>".($DataProdi['Akreditasi']!="" ? "Terakreditasi No: ".$DataProdi['NoSKBAN'] : "")."</td>
			</tr>
			</table>
			</div>

			<div class='onlyscreen' style='text-align:center'>
			<br />
			<a href='javascript:void(0);' onClick='window.print()'><img src='../img/printer2.gif' border='0' alt='Cetak' title='Cetak'></a>
			</div>
			<br />

			<table width='100%' cellspacing='0' cellpadding='0'>
			<tr>
			<td class='tabel' width='10%'>&nbsp;</td>
			<td class='tabel' width='15%'>Kode</td>
			<td class='tabel' width='50%'>Mata Kuliah</td>
			<td class='tabel' width='5%'>SKS</td>
			<td class='tabel' width='6%'>Nilai</td>
			<td class='tabel' width='7%'>Bobot</td>
			<td class='tabel' width='7%'>Mutu</td>
			</tr>";
	ResetNilaiTertinggi($MhswID);
  	BuatNilaiTertinggi($MhswID);
	$KurikulumID 	= GetaField('kurikulum', "ProdiID='$ProdiID' AND NA='N' AND KodeID", KodeID,"KurikulumID");
	$query3 		= _query("select distinct(m.Nama) as Nama,kl.Nama as Klasifikasi, m.MKSetara, kl.Singkatan, m.MKID, m.MKKode,m.SKS
							 from mk m
										left outer join jenismk kl on kl.JenisMKID=m.JenisMKID where
										m.NA='N'
										AND m.KurikulumID='$KurikulumID'
										group by m.MKKode
										order by kl.Urutan,m.MKKode");
			$JenisMK = 'lkajsdfkasdf';
			while($rowdafmk = _fetch_array($query3))
			{ if ($JenisMK != $rowdafmk['Klasifikasi']){
				echo "<tr>
				<td colspan='7'><b>".$rowdafmk['Klasifikasi'].($rowdafmk['Singkatan'] ? " (".$rowdafmk['Singkatan'] .")":'')."</b></td>
				</tr>"; 
			}
			$JenisMK = $rowdafmk['Klasifikasi'];
			$counter2 = 1;
	/*$query4 		= _query("SELECT distinct(m.Nama) as Nama,k.MKKode, m.SKS, Max(k.NilaiAkhir), Max(k.BobotNilai) as BobotNilai, k.NA
					FROM krs k left outer join mk m on m.MKKode=k.MKKode
					WHERE k.MhswID = '".$MhswID."'
					AND k.NA = 'N'
					AND m.NA='N'
					AND m.JenisMKID='$rowmkkla[JenisMKID]'
					AND k.BobotNilai is Not NULL
					AND k.GradeNilai != 'E'
					AND k.GradeNilai != '-'
					AND k.GradeNilai != ''
					GROUP BY k.MKKode
					ORDER BY k.Nama ASC");

				
				while($rowdafmk = _fetch_array($query4))
				{ $no++;*/
					$rowdafmk['MKSetara'] .= ".".$rowdafmk['MKKode'].".";
					$bobot1 = GetaField('krs',"LOCATE(concat('.',MKKode,'.'),'$rowdafmk[MKSetara]')
												and NA='N' and Tinggi = '*'  and Nama='$rowdafmk[Nama]' and MhswID", $MhswID,"max(BobotNilai)");
					$bobotnya = $bobot1;
					$MK = '';
					if ($bobot1 == ''){
						$MK = GetFields('krs',"NA='N' and Tinggi = '*' and Nama like '$rowdafmk[Nama]' and MhswID", $MhswID,"max(BobotNilai) as BBT,Nama");
						$bobot1 = $MK['BBT'];
					}
					if (empty($bobot1)){
						$MK = GetFields('krs',"NA='N' and Tinggi = '*' and MKKode like '$rowdafmk[MKKode]' and MhswID", $MhswID,"max(BobotNilai) as BBT,Nama");
						$bobot1 = $MK['BBT'];
					}
					$bobot1 = number_format($bobot1, 2);
					$Grade = GetaField('nilai', "Bobot='$bobot1' AND ProdiID", $ProdiID, 'Nama');
					$MKID = GetaField('krs', "NA='N' and MKKode='$rowdafmk[MKKode]' AND MhswID", $MhswID, "MKID");
					if (empty($MKID)) $update = _query("UPDATE krs set MKID='$rowdafmk[MKID]' where MKKode='$rowdafmk[MKKode]' and NA='N' AND MhswID='$MhswID'");
					
					if ($bobot1 > 0){ 
					echo "<tr>
					<td>&nbsp;</td>
					<td valign='top'>".$rowdafmk['MKKode']."</td>
					<td valign='top'>".$rowdafmk['Nama']."</td>
					<td valign='top' style='text-align:center'>".$rowdafmk['SKS']."</td>
					<td valign='top' style='padding-left:10px;'>".$Grade."</td>
					<td valign='top' style='text-align:center'>".$bobot1."</td>";

					$kali = $rowdafmk['SKS'] * $bobot1;
					$kali1 = number_format($kali, 2, ",", ".");

					echo "<td valign='top' style='text-align:right; padding-right:15px'>".$kali1."</td>
					</tr>";

					$abjada = $abjadnya;
					$kali2 += $rowdafmk['SKS'];
					$kali3 += $kali;
					$kali4 += $counter2;
					$kali5 += $bobot;
				//}
				}
			} //while mk klasifikasi

			$kali3a = number_format($kali3, 2, ",", ".");

			echo "<tr>
			<td class='tabel_bawah' colspan='3' style='text-align:right; padding:3px 50px'>Jumlah</td>
			<td class='tabel_bawah' style='text-align:center'>".$kali2."</td>
			<td class='tabel_bawah'>&nbsp;</td>
			<td class='tabel_bawah'>&nbsp;</td>
			<td class='tabel_bawah' style='text-align:right; padding-right:15px'>".$kali3a."</td>
			</tr>
			</table>";
			// UPDATE TOTAL SKS
			
			if ($DataProdi['ProdiID'] =='PGSD') {
			_query("UPDATE mhsw set TotalSKS='".$kali2."' where MhswID='".$MhswID."' ");
			}
			
			$ipkumul = $kali3/$kali2;
			$ipkumul1 = number_format($ipkumul, 2, ",", ".");

			$tglyudisium1 = TanggalFormat($Skripsi['TglSidang']);

			echo "<div style='text-align:left; padding-left:30px'>
			<table>
			<tr><td>Jumlah Mata Kuliah</td><td>:</td><td>".$kali4."</td></tr>
			<tr><td>I.P Kumulatif</td><td>:</td><td>".$ipkumul1."</td></tr>
			<tr><td>Yudisium</td><td>:</td><td>".GetaField('wisudawan',"MhswID",$MhswID,"Predikat")."*)</td></tr>
			<tr><td>Tanggal Yudisium</td><td>:</td><td>".$tglyudisium1."</td></tr>
			<tr><td valign='top'>Judul Skripsi</td><td valign='top'>:</td><td><div style='border:1px solid #000000; height:65px; width:450px; padding:5px 10px 10px 5px'>".FixStr($Skripsi['Judul'])."</div></td></tr>
			</table></div><br />";

				/* $sqlctk2 -> db_Select("staf_jabatan", "*", "sjb_id='".$rowctk['pct_transkrip']."' ");
				$rowctk2 = $sqlctk2 -> db_Fetch();

				$sqljbt = new db;
				$sqljbt -> db_Select("struktural_administrasi", "*", "strukadm_jabatan='".$rowctk['pct_transkrip']."' ");
				$rowjbt = $sqljbt -> db_Fetch();

				$data = explode(".", $rowjbt['strukadm_pemegang']);
				$data1 = $data[0];
				$data2 = $data[1];

				$sqldkn = new db;
				$sqldkn -> db_Select("dosen", "*", "dosen_id='".$data2."' ");
				$rowdkn = $sqldkn -> db_Fetch();

				if($rowfak['fak_dekan'] == $rowdkn['dosen_id'])
				{
					$namajabatan = $rowctk2['sjb_nama'];
				}else{
					$namajabatan = "a.n. Dekan<br />
					".$rowctk2['sjb_nama'];
				}
			}else{

				$sqldkn = new db;
				$sqldkn -> db_Select("dosen", "*", "dosen_id='".$rowfak['fak_dekan']."' ");
				$rowdkn = $sqldkn -> db_Fetch();

				$namajabatan = "Dekan";
			} */

			$waktucetak = TanggalFormat(date('Y-m-d'));

			echo "<div style='text-align:left; padding-left:440px'>".ucwords(strtolower($DataFakultas['Kota'])).", ".$waktucetak."</div>
			<div style='text-align:left; padding-left:440px'><b>".$DataFakultas['Jabatan'].",</b></div>
			<div style='text-align:left; padding-left:440px; padding-top:50px; font-weight:bold'>".$DataFakultas['Pejabat']."</div>";


	echo "</div>
	</center>
	</body>
	</html>";
function ResetNilaiTertinggi($mhsw) {
  $s = "update krs set Tinggi = '' where MhswID='$mhsw' and JadwalID<>'0' and KodeID='".KodeID."' ";
  $r = _query($s);
}

function BuatNilaiTertinggi($mhsw) {
  // Ambil semuanya dulu
  $s = "select k.KRSID, k.MKKode, k.BobotNilai, k.GradeNilai, k.SKS, k.Tinggi
    from krs k left outer join jadwal j on k.JadwalID=j.JadwalID
				left outer join jenisjadwal jj on jj.JenisJadwalID=j.JenisJadwalID and jj.Tambahan = 'N'
    where k.KodeID = '".KodeID."'
      and k.MhswID = '$mhsw'
    order by k.MKKode";
  $r = _query($s);
  
  while ($w = _fetch_array($r)) {
    $ada = GetFields('krs', "Tinggi='*' and KRSID<>'$w[KRSID]' and MhswID='$mhsw[MhswID]' and MKKode", $w['MKKode'], '*');
    // Jika nilai sekarang lebih tinggi
    if ($w['BobotNilai'] > $ada['BobotNilai']) {
      $s1 = "update krs set Tinggi='*' where KRSID='$w[KRSID]' ";
      $r1 = _query($s1);
      // Cek yg lalu, kalau tinggi, maka reset
      if ($ada['Tinggi'] == '*') {
        $s1a = "update krs set Tinggi='' where KRSID='$ada[KRSID]' ";
        $r1a = _query($s1a);
      }
    }
    // Jika yg lama lebih tinggi, maka ga usah diapa2in
    else {
    }
  }
}

?>