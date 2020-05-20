<?php
//Author: Irvandy Goutama
// Email: irvandygoutama@gmail.com

session_start();
include_once "../sisfokampus1.php";
HeaderSisfoKampus("Edit Wawancara");

TampilkanJudul("Kelas Mahasiswa - Gelombang $_REQUEST[gel]");

$gos = (empty($_REQUEST['gos']))? "EditWawancara" : $_REQUEST['gos'];
$gos();

	// *** Helper Functions ***
	
	function GetOptionsFromData($sourceArray, $chosen, $blank='1')
	{	
			$optresult = "";
			if($blank == '1')	
			{ 	$optresult .= "<option value='' selected></option>"; }
			else { $optresult .= "<option value=''></option>"; }
			for($i=0; $i < count($sourceArray); $i++)
			{	if($chosen == $sourceArray[$i])
				{	$optresult .= "<option value='$sourceArray[$i]' selected>$sourceArray[$i]</option>"; }
				else
				{ 	$optresult .= "<option value='$sourceArray[$i]'>$sourceArray[$i]</option>"; }
			}
			return $optresult;
	}
		
	// Mengedit/Memasukan data baru ke dalam table `wawancara`
	function EditWawancara()
	{	
		$sisfo = GetFields('identitas', 'Kode', KodeID, '*');
		$wid = $_REQUEST['wPMB'];
		$md = $_REQUEST['md']+0;
		if($md == 0) 
		{	$jdl = 'Edit Data Wawancara';
			$w = GetFields('wawancara w left outer join pmb p on w.PMBID=p.PMBID', 'WawancaraID', $_REQUEST['wPMB'], 
								'w.*, p.Nama, p.DetailNilai, p.NilaiUjian, p.TahunLulus, p.Pilihan1, p.Pilihan2, p.Pilihan3');
			$strwid = "
			<tr>
				<td class=inp>Wawancara ID: </td>
				<td class=ul1><input type=hidden name='wid' value='$wid'><b>$wid</b></td>
			</tr>";
		}
		else if($md == 1)
		{	$jdl = 'Masukkan Data Wawancara';
			$w = array();
			
			$w['PMBID']=$_REQUEST['wPMB'];
			$s="select * from `pmb` where PMBID='$w[PMBID]'";
			$r=_query($s);
			$n=_num_rows($r);
			$w=_fetch_array($r);
			
			if($n==1)
			{	$w['Tanggal'] = date('Y-m-d');
				$w['JamMulaiWawancara'] = '08:00';
				$w['JamSelesaiWawancara'] = '09:00';
			}
			else die(ErrorMsg('Error', "Ada Nomor PMB yang ganda. Harap dibenarkan dulu. o.0"));
			$strwid = "";//"<input type=hidden name='wid' value='' size=40 maxlength=50>=<i> Akan diberikan secara otomatis</i> =";
		}
		
		else die(ErrorMsg('Error', "Mode edit tidak dikenali. Hubungi Sysadmin untuk informasi lebih lanjut." ));
		
		//** Tampilkan Form Parameters **
		$selecttanggalwawancara = GetDateOption($w['Tanggal'],'Tanggal');
		$optjamwawancara = GetTimeOption(substr($w['JamMulaiWawancara'], 0, 5), 'JamMulai');
		$optakhirjamwawancara = GetTimeOption(substr($w['JamSelesaiWawancara'], 0, 5), 'JamSelesai');
		$optkelamin = GetOption2('kelamin', "concat(Kelamin, ' - ', Nama)", 'Kelamin', $w['Kelamin'], '', 'Kelamin');
		$radkel = GetRadio("select Nama, Kelamin from kelamin order by Nama", "Kelamin", "Nama", "Kelamin", $w['KelaminID'], ", ");    
		$optprodi = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)", 'ProdiID', $w['Pilihan1'], '', 'ProdiID');
		$optprodi2 = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)", 'ProdiID', $w['Pilihan2'], '', 'ProdiID');
		$optprodi3 = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)", 'ProdiID', $w['Pilihan3'], '', 'ProdiID');
		$optsaranprodi = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)", 'ProdiID', $w['SaranProdi'], '', 'ProdiID');
		$optpewawancara = GetOption2('presenter', "Nama", 'Nama', $w['Pewawancara'], '', 'PresenterID');
		$optpewawancara2 = GetOption2('presenter', "Nama", 'Nama', $w['Pewawancara2'], '', 'PresenterID');
		
		$arrayjpendamping = array('OrangTua', 'Wali', 'Sponsor');
		$optjenispendamping = GetOptionsFromData($arrayjpendamping, $w['JenisPendamping']);
		$arraycacat = array('Cacat', 'Tidak');
		$optcacat = GetOptionsFromData($arraycacat, $w['Cacat']);
		$arraykeuangan = array('Cash', 'Cicil');
		$optkeuangan = GetOptionsFromData($arraykeuangan, $w['Keuangan']);
		$arrayyatidak = array('Ya', 'Tidak');
		$opttatatertib = GetOptionsFromData($arrayyatidak, $w['PatuhTataTertib']);
		$optproporsional = GetOptionsFromData($arrayyatidak, $w['Proporsional']);
		$optmengganggu = GetOptionsFromData($arrayyatidak, $w['Mengganggu']);
		$optmembahayakan = GetOptionsFromData($arrayyatidak, $w['Membahayakan']);
		$optrasional1 = GetOptionsFromData($arrayyatidak, $w['Rasional1']);
		$optrasional2 = GetOptionsFromData($arrayyatidak, $w['Rasional2']);
		$optrasional3 = GetOptionsFromData($arrayyatidak, $w['Rasional3']);
		$optrasional4 = GetOptionsFromData($arrayyatidak, $w['Rasional4']);
		$arrayrekomendasi = array('Rekomendasi', 'Tidak Rekomendasi');
		$optrekomendasi = GetOptionsFromData($arrayrekomendasi, $w['HasilWawancara']);
		
		$rokokck = ($w['CheckRokok']=='on'? 'checked' : '');
		$mirasck = ($w['CheckMiras']=='on'? 'checked' : '');
		$narkoback = ($w['CheckNarkoba']=='on'? 'checked' : '');
		$judick = ($w['CheckJudi']=='on'? 'checked' : '');
		
		CheckFormScript("Pewawancara");
		TampilkanJudul($jdl);
		
		echo "<table class=box cellspacing=1 width=100%>
			<form name='editwawanform' action='?' method=POST onSubmit=\"return CheckForm(this)\">
			<input type=hidden name='md' value='$md'/>
			<input type=hidden name='gelombang' value='$gelombang'/>
			<input type=hidden name='mnux' value='$_SESSION[mnux]'/>
			<input type=hidden name='gos' value='WawancaraSav'/>
			
			<tr><th class=ttl colspan=6>Data Wawancara</th></tr>
			$strwid
			<tr>
				<td class=inp>Tanggal Wawancara</td>
				<td class=ul1>$selecttanggalwawancara</td>
				<td class=inp>Jam Wawancara</td>
				<td class=ul1 colspan=3>$optjamwawancara - $optakhirjamwawancara</td>
			</tr>
			<tr>
				<td class=inp>Nama Pewawancara 1</td>
				<td class=ul1><select name='Pewawancara'>$optpewawancara</select></td>
				<td class=inp>Nama Pewawancara 2</td>
				<td class=ul1 colspan=3><select name='Pewawancara2'>$optpewawancara2</select></td>		
			</tr>
			<tr><th class=ttl colspan=6>Data Pribadi</th></tr>
			<tr>
				<td class=inp>No. PMB:</td>
				<td class=ul1 colspan=5>
					<input type=text name='dNoPMB' value='$w[PMBID]' size=30 maxlength=50 disabled/>
					<input type=hidden name='NoPMB' value='$w[PMBID]'></td>
			</tr>
			<tr>
				<td class=inp>Nama: </td>
				<td class=ul1>
					<input type=text name='dWNama' value='$w[Nama]' size=30 maxlength=50 disabled/>
					<input type=hidden name='WNama' value='$w[Nama]'></td>
				</td>
			</tr>
			<tr>
				<td class=inp>Prodi 1: </td>
				<td class=ul1 colspan=3>
					<select name='Prodi' onChange=\"javascript:editwawanform.label1.value=editwawanform.Prodi.value\" disabled>$optprodi</select></td>
			</tr>
			<tr>
				<td class=inp>Prodi 2: </td>
				<td class=ul1>
					<select name='Prodi2' onChange=\"javascript:editwawanform.label2.value=editwawanform.Prodi2.value\" disabled>$optprodi2</select></td>
			</tr>
			<tr>
				<td class=inp>Prodi 3: </td>
				<td class=ul1>
					<select name='Prodi3' onChange=\"javascript:editwawanform.label3.value=editwawanform.Prodi3.value\" disabled>$optprodi3</select></td>
			</tr>
			<tr>
				<td class=inp>Datang bersama Ortu/Wali: </td>
				<td class=ul1>
					<select name='JenisPendamping'>$optjenispendamping</select></td>
				<td class=inp>Nama Pendamping: </td>
				<td class=ul1 colspan=3>
					<input type=text name='NamaPendamping' value='$w[NamaPendamping]' size=30 maxlength=50 /></td>
			</tr>
			<tr>
				<td class=inp>Anak ke: </td>
				<td class=ul1>
					<input type=text name='AnakKe' value='$w[AnakKe]' size=2 maxlength=2>
					dari&nbsp<input type=text name='DariKe' value='$w[DariKe]' size=2 maxlength=2></td>
				<td class=inp>Pekerjaan Pendamping: </td>
				<td class=ul1 = colspan=3>
					<input type=text name='PekerjaanPendamping' value='$w[PekerjaanPendamping]' size=30 maxlength=50></td>	
			</tr>
			<tr>
				<td class=inp>Catatan tentang Keluarga: </td>
				<td class=ul1 colspan=5>
					<textarea name='CatatanKeluarga' cols=70 row=2>$w[CatatanKeluarga]</textarea></td>
			</tr>
			
			<tr><th class=ttl colspan=6>Identifikasi Fisik Aplikan</th></tr>
			<tr>
				<td class=inp>Berat Badan: </td>
				<td class=ul1>
					<input type=text name='BeratBadan' value='$w[BeratBadan]' size=3 maxlength=5/></td>
				<td class=inp>Tinggi Badan: </td>
				<td class=ul1>
					<input type=text name='TinggiBadan' value='$w[TinggiBadan]' size=3 maxlength=5 /></td>
				<td class=inp rowspan=2>Proporsional: </td>
				<td class=ul1 rowspan=2>
					<select name='Proporsional' >$optproporsional</select></td>
				</td>
			</tr>			
			<tr>
				<td class=inp>Kelamin: </td>
				<td class ul1 colspan=5><select name='Kelamin'>$optkelamin</select></td>
			</tr>
			<tr>
				<td class=inp>Cacat: </td>
				<td class=ul1>
					<select name='Cacat'>$optcacat</select></td>
				<td class=inp>Keterangan: </td>
				<td class=ul1>
					<input type=text name='KeteranganCacat' value='$w[KeteranganCacat]' size=30 maxlength=50 /></td>
				<td class=inp>Mengganggu: </td>
				<td class=ul1>
					<select name='Mengganggu' >$optmengganggu</select></td>
				</td>	
			</tr>
			<tr>
				<td class=inp>Indikasi Kecanduan: </td>
				<td class=ul1 colspan=3> 
					<input type=checkbox name='CheckRokok' $rokokck>Rokok &nbsp&nbsp&nbsp&nbsp     
					<input type=checkbox name='CheckMiras' $mirasck>Miras &nbsp&nbsp&nbsp&nbsp    
					<input type=checkbox name='CheckNarkoba' $narkoback>Narkoba			
				</td>
				<td class=inp rowspan=2>Membahayakan</td>
				<td class=ul1 rowspan=2><select name='Membahayakan'>$optmembahayakan</select></td>
			</tr>	
			<tr>
				<td></td>
				<td class=ul1 colspan=3><input type=checkbox name='CheckJudi' $judick>Judi&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp      
					Lainnya: <input type=text name='KecanduanLainnya' value='$w[KecanduanLainnya]'>
				</tr>
			<tr><th class=ttl colspan=6>Motif</th></tr>
			<tr>
				<td class=inp>Tahun Kelulusan SMU/SMK: </td>
				<td class=ul1 colspan=3>
					<input type=text name='dTahunLulus' value='$w[TahunLulus]' size=8 maxlength=10 disabled/>
					<input type=hidden name='TahunLulus' value='$w[TahunLulus]'></td>
				<td class=inp rowspan=2>Rasional: </td>
				<td class=ul1 rowspan=2><select name='Rasional1'>$optrasional1</select></td>	
			</tr>
			<tr>
				<td class=inp>Kegiatan Setelah Kelulusan: </td>
				<td class=ul1 colspan=3>
					<textarea name='KegiatanSetelahKelulusan' cols=70 rows=1>$w[KegiatanSetelahKelulusan]</textarea></td>
				</tr>
			<tr>
				<td class=inp>Alasan Masuk $sisfo[Kode]: </td>
				<td class=ul1 colspan=3>
					<textarea name='AlasanMasuk' cols=70 rows=3>$w[AlasanMasuk]</textarea></td>
				<td class=inp>Rasional: </td>
				<td class=ul1><select name='Rasional2'>$optrasional2</select></td>	
			</tr>
			<tr>
				<td class=inp>Harapan Calon Mahasiswa: </td>
				<td class=ul1 colspan=3>
					<textarea name='Harapan' cols=70 rows=3>$w[Harapan]</textarea></td>
				<td class=inp>Rasional: </td>
				<td class=ul1><select name='Rasional3'>$optrasional3</select></td>
			</tr>
			<tr>
				<td class=inp>Harapan Pendamping</td>
				<td class=ul1 colspan=3>
					<textarea name='HarapanPendamping' cols=70 rows=3>$w[HarapanPendamping]</textarea></td>
				<td class=inp>Rasional: </td>
				<td class=ul1><select name='Rasional4'>$optrasional4</select></td>
			</tr>
			<tr><th class=ttl colspan=6>Kemampuan</th></tr>
			<tr>
				<td class=inp>Pilihan Prodi 1:</td>
				<td class=ul1><input type=text name='label1' value='$w[Pilihan1]' disabled/></td>
				<td class=inp rowspan=2>Saran Program Studi: </td>
				<td class=ul1 rowspan=2>
					<select name='SaranProdi'>$optsaranprodi</select></td>
				<th class=ttl colspan=2 rowspan=2>Hasil Rekomendasi</th></tr>
			<tr>
				<td class=inp>Pilihan Prodi 2:</td>
				<td class=ul1><input type=text name='label2' value='$w[Pilihan2]' disabled/></td>
			<tr>
			<tr>
				<td class=inp>Pilihan Prodi 3:</td>
				<td class=ul1><input type=text name='label3' value='$w[Pilihan3]' disabled/></td>
			<tr>
				<td class=inp>Pilihan Keuangan: </td>
				<td class=ul1><select name='Keuangan'>$optkeuangan</select></td>
				<td class=inp>Rencana Pembayaran: </td>
				<td class=ul1>
					<textarea name='RencanaPembayaran' cols=30 rows=2>$w[RencanaPembayaran]</textarea></td>
				<td colspan=2 rowspan=2 align=center><select name='Rekomendasi'>$optrekomendasi</select></td>
			</tr>
			<tr>
				<td class=inp>Mematuhi Tata Tertib: </td>
				<td class=ul1 colspan=3>
					<select name='PatuhTataTertib'>$opttatatertib</select></td>
			</tr>
			<tr>
			<tr><th class=ttl colspan=6>Tambahan</th></td>
				<td colspan=4></td>
			</tr>
			<tr>
				<td class=inp>Catatan: </td>
				<td class=ul1 colspan=5>
					<textarea name='CatatanAkhir' cols=100 rows=4>$w[CatatanAkhir]</textarea></td>
			<tr>
			<tr>
				<td class=ul1 colspan=6 align=center>
					<input type=submit name='Simpan' value='Simpan' />
					<input type=reset name='Reset' value='Reset'>
					<input type=button name='Batal' value='Batal' 
						onClick=\"window.close()\" />
				</td>
			</tr>
			</form></table>
			";
			
	}
	
	function WawancaraSav()
	{	$md = $_REQUEST['md']+0;
		$wid = $_REQUEST['wid'];
		$Tanggal= "$_REQUEST[Tanggal_y]-$_REQUEST[Tanggal_m]-$_REQUEST[Tanggal_d]";
		$JamMulaiWawancara = "$_REQUEST[JamMulai_h]:$_REQUEST[JamMulai_n]";
		$JamSelesaiWawancara = "$_REQUEST[JamSelesai_h]:$_REQUEST[JamSelesai_n]";
		$Pewawancara = $_REQUEST['Pewawancara'];
		$Pewawancara2 = $_REQUEST['Pewawancara2'];
		$NoPMB = $_REQUEST['NoPMB'];
		$WNama = sqling($_REQUEST['WNama']);
		$JenisPendamping = $_REQUEST['JenisPendamping'];
		$NamaPendamping = sqling($_REQUEST['NamaPendamping']);
		$AnakKe = $_REQUEST['AnakKe']+0;
		$DariKe = $_REQUEST['DariKe']+0;
		$PekerjaanPendamping = sqling($_REQUEST['PekerjaanPendamping']);
		$CatatanKeluarga = sqling($_REQUEST['CatatanKeluarga']);
		$BeratBadan = $_REQUEST['BeratBadan']+0;
		$TinggiBadan = $_REQUEST['TinggiBadan']+0;
		$Cacat = $_REQUEST['Cacat'];
		$KeteranganCacat = sqling($_REQUEST['KeteranganCacat']);
		$CheckRokok = $_REQUEST['CheckRokok'];
		$CheckMiras = $_REQUEST['CheckMiras'];
		$CheckNarkoba = $_REQUEST['CheckNarkoba'];
		$CheckJudi = $_REQUEST['CheckJudi'];
		$KecanduanLainnya = sqling($_REQUEST['KecanduanLainnya']);
		$KegiatanSetelahKelulusan = sqling($_REQUEST['KegiatanSetelahKelulusan']);
		$AlasanMasuk = sqling($_REQUEST['AlasanMasuk']);
		$Harapan = sqling($_REQUEST['Harapan']);
		$HarapanPendamping = sqling($_REQUEST['HarapanPendamping']);
		$SaranProgram = $_REQUEST['SaranProgram'];
		$Keuangan = $_REQUEST['Keuangan'];
		$RencanaPembayaran = sqling($_REQUEST['RencanaPembayaran']);
		$PatuhTataTertib = $_REQUEST['PatuhTataTertib'];
		$CatatanAkhir = sqling($_REQUEST['CatatanAkhir']);
		$Proporsional = $_REQUEST['Proporsional'];
		$Mengganggu = $_REQUEST['Mengganggu'];
		$Membahayakan = $_REQUEST['Membahayakan'];
		$Rasional1 = $_REQUEST['Rasional1'];
		$Rasional2 = $_REQUEST['Rasional2'];
		$Rasional3 = $_REQUEST['Rasional3'];
		$Rasional4 = $_REQUEST['Rasional4'];
		$Rekomendasi = $_REQUEST['Rekomendasi'];
		
		$gelombang = GetaField('pmbperiod', "KodeID='".KodeID."' and NA", 'N', "PMBPeriodID");
		
		// Save data
		if($md==0)
		{	$s="update `wawancara`
			set Tanggal = '$Tanggal', JamMulaiWawancara = '$JamMulaiWawancara', JamSelesaiWawancara = '$JamSelesaiWawancara',
				Pewawancara = '$Pewawancara', Pewawancara2 = '$Pewawancara2',
				JenisPendamping = '$JenisPendamping', NamaPendamping = '$NamaPendamping', AnakKe = '$AnakKe', 
				DariKe = '$DariKe', PekerjaanPendamping = '$PekerjaanPendamping', 
				CatatanKeluarga = '$CatatanKeluarga', BeratBadan = '$BeratBadan', TinggiBadan = '$TinggiBadan',
				Cacat = '$Cacat', KeteranganCacat = '$KeteranganCacat',
				CheckRokok = '$CheckRokok', CheckMiras = '$CheckMiras', CheckNarkoba = '$CheckNarkoba',
				CheckJudi = '$CheckJudi', KecanduanLainnya = '$KecanduanLainnya', 
				KegiatanSetelahKelulusan = '$KegiatanSetelahKelulusan', AlasanMasuk = '$AlasanMasuk', Harapan = '$Harapan',
				HarapanPendamping = '$HarapanPendamping',  
				SaranProgram = '$SaranProgram', Keuangan = '$Keuangan', RencanaPembayaran = '$RencanaPembayaran',
				PatuhTataTertib = '$PatuhTataTertib', CatatanAkhir = '$CatatanAkhir',
				Proporsional = '$Proporsional', Mengganggu = '$Mengganggu', Membahayakan = '$Membahayakan',
				Rasional1 = '$Rasional1', Rasional2 = '$Rasional2', Rasional3 = '$Rasional3', Rasional4 = '$Rasional4',
				HasilWawancara = '$Rekomendasi', TanggalEdit=now(), LoginEdit='$_SESSION[_Login]'
			where WawancaraID = '$wid' ";
			$r=_query($s);
		}
		else if($md==1)
		{	$s="insert into `wawancara`
				(Tanggal, JamMulaiWawancara, JamSelesaiWawancara, Pewawancara, PMBID,
				Pewawancara2, 
				JenisPendamping, NamaPendamping, AnakKe, DariKe, 
				PekerjaanPendamping, CatatanKeluarga, BeratBadan, TinggiBadan, 				
				Cacat, KeteranganCacat, CheckRokok, CheckMiras, CheckNarkoba, 
				CheckJudi, KecanduanLainnya, KegiatanSetelahKelulusan, AlasanMasuk, 
				Harapan, HarapanPendamping, SaranProgram, Keuangan, 
				RencanaPembayaran, PatuhTataTertib, CatatanAkhir,
				Proporsional, Mengganggu, Membahayakan,
				Rasional1, Rasional2, Rasional3, Rasional4,
				HasilWawancara, KodeID, PMBPeriodID, TanggalBuat, LoginBuat)
				values ('$Tanggal', '$JamMulaiWawancara', '$JamSelesaiWawancara', '$Pewawancara', '$NoPMB', 
				'$Pewawancara2', 
				'$JenisPendamping', '$NamaPendamping', '$AnakKe', '$DariKe',
				'$PekerjaanPendamping', '$CatatanKeluarga', '$BeratBadan', '$TinggiBadan', 
				'$Cacat', '$KeteranganCacat', '$CheckRokok', '$CheckMiras', '$CheckNarkoba', 
				'$CheckJudi', '$KecanduanLainnya', '$KegiatanSetelahKelulusan', '$AlasanMasuk', 
				'$Harapan', '$HarapanPendamping', '$SaranProgram', '$Keuangan', 
				'$RencanaPembayaran', '$PatuhTataTertib', '$CatatanAkhir',
				'$Proporsional', '$Mengganggu', '$Membahayakan',
				'$Rasional1', '$Rasional2', '$Rasional3', '$Rasional4',
				'$Rekomendasi', '".KodeID."', '$gelombang', now(), '$_SESSION[_Login]')	
			";
			$r=_query($s);
		}
	
		echo Konfirmasi("Berhasil", "Data berhasil disimpan.<br>");
		
		ClosingScript();
	}
	
	function ClosingScript()
	{	echo "<SCRIPT>
					opener.location='../index.php?mnux=$_SESSION[mnux]&gos=';
					self.close();
				</SCRIPT>";
	}
?>