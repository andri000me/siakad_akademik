<?php	session_start();
	error_reporting(0);
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
	$line		= GetSetVar('line');
	$ProdiID 	= GetaField('mhsw', "MhswID", $MhswID, "ProdiID");
	$DataProdi	= GetFields('prodi', "NA='N' AND ProdiID", $ProdiID, "*");
	$Kurikulum 	= GetSetVar('_kliringKurikulum');
	
    //------------------------
	// DB untuk nilai terendah
	//------------------------
	$NilaiTerendah = GetFields("nilai", "NA='N' AND Nama='E' AND ProdiID", $ProdiID, "*");


	$Identitas 		= GetFields("identitas", "Kode", KodeID, "*");
	$Mhsw 			= GetFields("mhsw", "NA='N' AND MhswID", $MhswID, "*");
	$DataFakultas 	= GetFields("fakultas", "NA='N' AND FakultasID", $DataProdi['FakultasID'], "*");
	$Jenjang		= GetFields("jenjang", "NA='N' AND JenjangID", $DataProdi['JenjangID'], "Nama, Keterangan");

	$Skripsi 		= GetFields("wisudawan", "MhswID", $MhswID, "*");
	$mh 			= GetFields('mhsw m left outer join wisudawan w on m.MhswID=w.MhswID', "m.KodeID='".KodeID."' and m.MhswID", $MhswID, 					
						'm.*,w.TanggalLahirFinal,w.NomerIjazah,w.NomerTranskrip,w.NomerSeri');
	
	// -----------------------------
	// Konfig. Narasi S1/S2
	// -----------------------------
	
	$nrsFakultas = $DataFakultas['FakultasID']=='08'? "Fakultas/Program":"Fakultas";
	$nrsJurusan = $DataFakultas['FakultasID']=='08'? "Program Studi":"Jurusan";
	$nrsProgramStudi = ($DataFakultas['FakultasID']=='08' || $Mhsw['ProgramID']=='J') ? "Konsentrasi":"Program Studi";
	$nrsSkripsi = $DataFakultas['FakultasID']=='08'? "Tesis":"Skripsi";
	$nrsBorder = $DataFakultas['FakultasID']=='08'? "margin-top:-5px; border: 0px":"margin-top:-5px; border: 0px";
	
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
		font-family:  Arial, Verdana;
		padding: 0px;
		background: #ffffff;
		line-height:1.5em;
	}
	.image {
		font-size: 18px;
		font-family: 'Century';
		font-weight: bold;
		color: #000;
		float:left;
		width:110px;
	}
	.kampus {
		font-family: 'Century';
		font-weight: bold;
		color: #000;
	}
	.universitas {
		font-size: 28px;
		font-family: 'Plantagenet Cherokee';
		font-weight: bold;
		color: #000;
		letter-spacing:1px;
	}
	.nomor-transkrip {
		font-size: 11px;
		font-family: 'Arial';
		color: #000;
		line-height:0.01em;
		padding-bottom:20px;
	}
	.alamat {
		font-size: 11px;
		font-family: 'Century';
		color: #000;
	}
	.tabel {
		font-family: Tahoma, Arial, Verdana;
		text-align:center;
		border-top:1px dotted #000000;
		border-bottom:1px dotted #000000;
		padding: 2px;
	}
	.tabelbawah {
		border-top:1px solid #000000;
		margin-bottom:2px;
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
	.break { page-break-before: always; }
	.break td {padding-bottom:10px; }
	</style>
	<style media='print'>
	.onlyscreen {
		display: none;
	}
	@media print
	{    
	    .noprint, .noprint *
	    {
	        display: none !important;
	    }
	}
	</style>
	</head>

	<body>
	<center>
	<div class='noprint'>
	<form method=post action=?>";
	$s6 = "select KurikulumID,KurikulumKode,Nama
    from kurikulum
    where ProdiID = '$ProdiID' order by Nama";
$r6 = _query($s6);
    $optkurikulum = "<option value=''></option>";
    while($w6 = _fetch_array($r6))
    {  $ck = ($w6['KurikulumID'] == $_SESSION['_kliringKurikulum'])? "selected" : '';
       $optkurikulum .=  "<option value='$w6[KurikulumID]' $ck>$w6[Nama]</option>";
    }
    $_inputKurikulum = "<select name='_kliringKurikulum' onChange='this.form.submit()'>$optkurikulum</select>";
    echo "Pilih Kurikulum $_inputKurikulum";
echo "
	</form>
	</div>
	<div style='width:650px; text-align:center; background: #ffffff;'>";

			$top = 125 + sqling($_GET['top']);
			echo "<div style='margin-top:".$top."px;'></div>
			<div style='font-size:18px; font-weight:bold; line-height: 0.5em;'><u>TRANSKRIP AKADEMIK</u></div>
			<div style='float:right; position:absolute;top:".$top."px;margin-left:580px; font-size:12px'>".$mh['NomerSeri']."</div>
			<br />
			<div class='nomor-transkrip'>Nomor : ".$mh['NomerTranskrip']."</div>
			<div style='text-align:left; padding-left:30px'>
			<table>
			<tr>
			<td>Diberikan Kepada</td><td>:</td><td><div style='font-size:13px' font-weight:bold'><strong>".$Mhsw['Nama']."</strong></div></td>
			</tr>
			<tr>
			<td>Tempat Lahir</td><td>:</td><td>".(str_replace("`","'",$Mhsw['TempatLahir']))."</td>
			</tr>
			<tr>
			<td>Tanggal Lahir</td><td>:</td><td>";

			if($Mhsw['TanggalLahir'] != "")
			{	
				$TanggalLahirFinal = GetaField('wisudawan', "MhswID", $Mhsw['MhswID'], 'TanggalLahirFinal');
				$TanggalLahir = (empty($TanggalLahirFinal))? TanggalFormat($Mhsw['TanggalLahir']) : $TanggalLahirFinal; 
				echo $TanggalLahir;
			}
			
			echo "</td>
			</tr>
			<tr>
			<td>Nomor Pokok Mahasiswa</td><td>:</td><td>".$Mhsw['MhswID']."</td>
			</tr>
			<tr>
			<td>$nrsFakultas</td><td>:</td><td>".$DataFakultas['Nama']."</td>
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
			$Jurusan = (empty($DataProdi['Jurusan'])) ? $DataProdi['Nama'] : $DataProdi['Jurusan'];
			$nrsKonsentrasi = $DataFakultas['FakultasID']=='08'? GetaField('konsentrasi',"KonsentrasiID",$Mhsw['KonsentrasiID'],'Nama'):(($Mhsw['ProgramID']=='P') ? "PSKGJ ".$DataProdi['Nama']:($Mhsw['ProgramID']=='J' ? "Pendidikan Jasmani Kesehatan dan Rekreasi":$DataProdi['Nama']));
				echo "<tr>
				<td>$nrsJurusan</td><td>:</td><td>".$Jurusan."</td>
				</tr>";
				if (!empty($nrsKonsentrasi)){
					echo "<tr>
					<td>$nrsProgramStudi</td><td>:</td><td>".$nrsKonsentrasi."</td>
					</tr>";
				}
			}
			$top=(empty($_GET['top']))? 10:$_GET['top'];
			echo "<tr>
			<td>Jenjang Pendidikan</td><td>:</td><td>".$Jenjang['Keterangan']." (".$Jenjang['Nama'].")</td>
			</tr>
			<tr>
			<td valign=top>Status Prodi</td><td valign=top>:</td><td>".($DataProdi['Akreditasi']!="" ? "Terakreditasi $DataProdi[Akreditasi], No: ".$DataProdi['NoSKBAN'] : "")."</td>
			</tr>
			<tr>
			<td valign=top>Status Institusi</td><td valign=top>:</td><td>Terakreditasi B, No: 0371/SK/BAN-PT/Akred/PT/I/2017</td>
			</tr>
			</table>
			</div>

			<div class='onlyscreen' style='text-align:center'>
			<br />
			<form action=? method='get'>Jika hasil cetak kurang pas, turunkan <input type='text' name='top' value='$top' size=2> mm <input type='submit' value='set'></form>
			<form action=? method='get'>Turunkan <input type='text' name='line' value='$line' size=2> matakuliah <input type='submit' value='set'></form>
			<a href='javascript:void(0);' onClick='window.print()'><img src='../img/printer.gif' border='0' alt='Cetak' title='Cetak'></a>
			</div>
			<br />

			<div class='tabelbawah'></div>
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
	$Baris			= $line-6;
	//$KurikulumID 	= GetaField('kurikulum', "Nama='Kurikulum Default' AND ProdiID='$ProdiID' AND NA='N' AND KodeID", KodeID,"KurikulumID");
	$KurikulumID	= $_SESSION['_kliringKurikulum'];
	$whr			.= (!empty($KurikulumID))? " AND m.KurikulumID='$KurikulumID' " : '';
	$update 		= _query("UPDATE mhsw set KurikulumID='$KurikulumID' where MhswID='$MhswID'");
	$whr			= (!empty($KurikulumID))? " AND m.KurikulumID='$KurikulumID' " : '';
	$query3 		= _query("select m.Nama,kl.Nama as Klasifikasi, kl.Singkatan, m.MKID, m.MKKode, m.SKS, Max(k.NilaiAkhir), Max(k.BobotNilai) as BobotNilai
							 from krs k left outer join mk m on m.MKID=k.MKID 
										left outer join jenismk kl on kl.JenisMKID=m.JenisMKID where
										k.NA='N' and k.MhswID='".$MhswID."'
										AND k.BobotNilai is Not NULL
										AND k.GradeNilai != 'E'
										AND k.GradeNilai != '-'
										AND k.GradeNilai != ''
										AND m.NA='N'
										$whr
										group by m.Nama
										order by kl.Urutan,m.MKKode");
			$JenisMK = 'lkajsdfkasdf';
			while($rowdafmk = mysql_fetch_array($query3))
			{ 	
				if ($DataProdi['JenjangID']=='C') {
						if (($Baris == 34 || $Baris == 35) && $pagebreak == 0){
					$pagebreak=1;
						echo "
						<tr class=\"break\"><td colspan=7>&nbsp;</td></tr>
						<tr><td colspan=7><div class='tabelbawah'></div></td></tr>
						<tr>
								<td class='tabel' width='10%'>&nbsp;</td>
								<td class='tabel' width='15%'>Kode</td>
								<td class='tabel' width='50%'>Mata Kuliah</td>
								<td class='tabel' width='5%'>SKS</td>
								<td class='tabel' width='6%'>Nilai</td>
								<td class='tabel' width='7%'>Bobot</td>
								<td class='tabel' width='7%'>Mutu</td>
								</tr>";
						}
					}
					elseif ($DataProdi['JenjangID']=='E') {
						if (($Baris == 32 || $Baris == 33 || $Baris == 34) && $pagebreak == 0){
					$pagebreak=1;
						echo "
						<tr class=\"break\"><td colspan=7>&nbsp;</td></tr>
						<tr><td colspan=7><div class='tabelbawah'></div></td></tr>
						<tr>
								<td class='tabel' width='10%'>&nbsp;</td>
								<td class='tabel' width='15%'>Kode</td>
								<td class='tabel' width='50%'>Mata Kuliah</td>
								<td class='tabel' width='5%'>SKS</td>
								<td class='tabel' width='6%'>Nilai</td>
								<td class='tabel' width='7%'>Bobot</td>
								<td class='tabel' width='7%'>Mutu</td>
								</tr>";
						}
					}
				if ($JenisMK != $rowdafmk['Klasifikasi']){
				echo "<tr>
				<td colspan='7'><b>".$rowdafmk['Klasifikasi'].($rowdafmk['Singkatan'] ? " (".$rowdafmk['Singkatan'] .")":'')."</b></td>
				</tr>"; 
				$Kelompok=0;
				$Baris++;
				}
				else $Kelompok++;
			
			$JenisMK = $rowdafmk['Klasifikasi'];
			$counter2 = 1;
			$Baris++;
					
					$bobot1 = $rowdafmk['BobotNilai'];
					$Grade = GetaField('nilai', "Bobot='$bobot1' AND ProdiID", $ProdiID, 'Nama');
					$MKID = GetaField('krs', "MKID='$rowdafmk[MKID]' AND MhswID", $MhswID, "MKID");
					$kali = $rowdafmk['SKS'] * $rowdafmk['BobotNilai'];
					$kali1 = number_format($kali, 2, ",", ".");
					$abjada = $abjadnya;
					$kali2 += $rowdafmk['SKS'];
					$kali3 += $kali;
					$kali4 += $counter2;
					$kali5 += $bobot;
					
					echo "<tr>
					<td>&nbsp;</td>
					<td valign='top'>".$rowdafmk['MKKode']."</td>
					<td valign='top'>".$rowdafmk['Nama']."</td>
					<td valign='top' style='text-align:center'>".$rowdafmk['SKS']."</td>
					<td valign='top' style='padding-left:10px;'>".$Grade."</td>
					<td valign='top' style='text-align:center'>".$bobot1."</td>
					<td valign='top' style='text-align:right; padding-right:15px'>".$kali1."</td>
					</tr>";

					
				//}
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

			$ipkumul = $kali3/$kali2;
			$ipkumul1 = number_format($ipkumul, 2, ",", ".");
			$update = _query("UPDATE mhsw set IPK='$ipkumul' where MhswID='$MhswID'");

			$tglyudisium1 = TanggalFormat($Skripsi['TglSidang'], 1);

			echo "<div style='text-align:left; padding-left:30px'>
			<table>
			<tr><td><b>Jumlah Mata Kuliah</td><td><b>:</td><td><b>".$kali4."</td></tr>
			<tr><td><b>I.P Kumulatif</td><td><b>:</td><td><b>".$ipkumul1."</td></tr>
			<tr><td><b>Yudisium</td><td><b>:</td><td><b>".GetaField('wisudawan',"MhswID",$MhswID,"Predikat")."</td></tr>
			<tr><td><b>Tanggal Yudisium</td><td><b>:</td><td><b>".$tglyudisium1."</td></tr>
			<tr><td valign='top'><b>Judul $nrsSkripsi</td><td valign='top'><b>:</td><td><div style='$nrsBorder; height:65px; width:450px; padding:5px 10px 10px 5px'><b>".str_replace('\"','"',FixStr($Skripsi['Judul']))."</div></td></tr>
			</table></div><br />";

			$waktucetak = TanggalFormat(GetaField('wisuda',"WisudaID", $Skripsi['WisudaID'],'TglWisuda'));

			echo "<div style='text-align:left; padding-left:420px'>".ucwords(strtolower($DataFakultas['Kota'])).", ".$waktucetak."</div>
			<div style='text-align:left; padding-left:60px; float:left'><b>".$DataFakultas['Jabatan'].",</b></div>
			<div style='text-align:left; padding-left:420px'><b>".$Identitas['Jabatan'].",</b></div>
			<div style='text-align:left; padding-left:60px; padding-top:90px; font-weight:bold;float:left'>".$DataFakultas['Pejabat']."</div>
			<div style='text-align:left; padding-left:420px; padding-top:90px; font-weight:bold'>".$Identitas['Pejabat']."</div>";


	echo "</div>
	</center>
	</body>
	</html>";
function ResetNilaiTertinggi($mhsw) {
  $s = "update krs set Tinggi = '' where MhswID='$mhsw[MhswID]' and JadwalID<>'0' and KodeID='".KodeID."' ";
  $r = _query($s);
}

function BuatNilaiTertinggi($mhsw) {
  // Ambil semuanya dulu
  $s = "select k.KRSID, k.MKKode, k.BobotNilai, k.GradeNilai, k.SKS, k.Tinggi
    from krs k left outer join jadwal j on k.JadwalID=j.JadwalID
				left outer join jenisjadwal jj on jj.JenisJadwalID=j.JenisJadwalID and jj.Tambahan = 'N'
    where k.KodeID = '".KodeID."'
      and k.MhswID = '$mhsw[MhswID]'
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