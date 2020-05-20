<?php
//	Author: Arisal Yanuarafi
//	09 Agustus 2013
//	setelah lebaran 1434 H, untuk UBH 

// *** Parameter ***
$wisuda = GetFields('wisuda',"NA='N' and KodeID",KodeID,"*");
$tglNow = date('Y-m-d');
if ($tglNow > $wisuda['TglSelesai']) die('Bukan masa pendaftaran Wisuda.');

if ($_SESSION['_LevelID']==1) $MhswID = GetSetVar('MhswID');
elseif ($_SESSION['_LevelID']==120) $MhswID = $_SESSION['_Login'];
else die (errorMsg("Tidak berhak","Anda tidak berhak mengakses modul ini<hr>Opsi: <a href='?mnux'>Kembali</a>"));

$Prodinya = GetaField('mhsw', "MhswID", $MhswID,"ProdiID");
$sksProdi = GetaField('prodi',"ProdiID",$Prodinya,"TotalSKS");
$KurikulumID = GetaField('mhsw', "MhswID", $MhswID,"KurikulumID");
if (empty($KurikulumID))(errorMsg("Kurikulum Belum Diset","Silakan pilih kurikulum anda di laman Profil Mahasiswa.<hr>Opsi: <a href='akses-profilmhs'>Set Sekarang</a>"));
$s1 = "SELECT k.BobotNilai,k.SKS from krs k,mk m,kurikulum u where
                    k.NA='N'   
                    AND  m.MKID=k.MKID 
                    AND u.KurikulumID=m.KurikulumID 
                    AND u.NA = 'N'
                    AND k.BobotNilai > 0
                    AND k.Tinggi = '*'
                    AND m.NA='N'
                    AND k.MhswID='$MhswID' group by k.Nama";
    $r1 = _query($s1);$bobot=0;$sks=0;
    while ($w1 = _fetch_array($r1)) {
        $bobot += $w1['BobotNilai']*$w1['SKS'];
        $sks += $w1['SKS'];
    }
 
//echo $sks;
TampilkanJudul('Blanko Calon Wisudawan');

//if ($_SESSION['_LevelID']==120) die('');

if ($sks >= $sksProdi){
	TampilkanFormWisuda();	
}
else {
$s1 = "SELECT k.BobotNilai,k.SKS from krs k,mk m,kurikulum u where
                    k.NA='N'   
                    AND  m.MKID=k.MKID 
                    AND m.KurikulumID = '$KurikulumID'
                    AND u.KurikulumID=m.KurikulumID 
                    AND k.BobotNilai > 0
                    AND k.Tinggi = '*'
                    AND m.NA='N'
                    AND k.MhswID='$MhswID' group by k.Nama";
    $r1 = _query($s1);$bobot=0;$sks=0;
    while ($w1 = _fetch_array($r1)) {
        $bobot += $w1['BobotNilai']*$w1['SKS'];
        $sks += $w1['SKS'];
    }
    //echo $KurikulumID;
	if ($sks >= $sksProdi){ 
		TampilkanFormWisuda();
	}else { die(errorMsg("Belum bisa mendaftar","Anda belum bisa mengajukan Permohonan wisuda, karena sks belum mencukupi. Total SKS untuk bisa mendaftar wisuda minimal $sksProdi SKS, sementara SKS Anda baru $sks SKS. <br /><hr><b>Saran: </b>Silakan lakukan kliring nilai terlebih dahulu."));}
}


function TampilkanFormWisuda() {
if ($_SESSION['_Login']=='auth0rized') $MhswID = $_SESSION['MhswID'];
elseif ($_SESSION['_LevelID']==120) $MhswID = $_SESSION['_Login'];
else die (errorMsg("Tidak berhak","Anda tidak berhak mengakses modul ini"));
$w 				= GetFields('mhsw m left outer join tugasakhir t on t.MhswID=m.MhswID', "m.MhswID", $MhswID, "m.*, t.Judul,date_format(TanggalLahir,'%m')-1 as Bln,date_format(TanggalLahir,'%d') as dat, date_format(TanggalLahir,'%Y') as thn");
$Pria 			= ($w['Kelamin']=='P') ? 'checked' : '';
$Wanita 		=  ($w['Kelamin']=='W') ? 'checked' : '';
$t 				= GetFields('tugasakhir', "TAID", $w['TAID'], "*,date_format(TglUjian,'%m')-1 as Bln,date_format(TglUjian,'%d') as dat, date_format(TglUjian,'%Y') as thn,
														date_format(TglDaftar,'%m')-1 as BlnDaftar,date_format(TglDaftar,'%d') as datDaftar, date_format(TglDaftar,'%Y') as thnDaftar,
														date_format(TglMulai,'%m')-1 as BlnMulai,date_format(TglMulai,'%d') as datMulai, date_format(TglMulai,'%Y') as thnMulai,
														date_format(TglSelesai,'%m')-1 as BlnSelesai,date_format(TglSelesai,'%d') as datSelesai, date_format(TglSelesai,'%Y') as thnSelesai");
														
?><script>
$(function(){
$('.TanggalLahir').datepicker({changeMonth:true,changeYear: true,altField: "#AltTanggal",altFormat: "DD, d MM yy",minDate:"-1200M -2D", maxDate: "-200M"});
$( ".TanggalLahir" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
$( ".TanggalLahir" ).datepicker( "option", "showAnim", "fold" );
$( ".TanggalLahir" ).datepicker( "setDate", new Date(<?php echo $w['thn'].','.$w['Bln'].','.$w['dat']?>) );
$('.TanggalSidang').datepicker({changeMonth: true,changeYear: true,
			altField: "#AltTanggalSd",altFormat: "DD, d MM yy",minDate: "-5Y -3M -0D", maxDate: "+0M"
		});
	$( ".TanggalSidang" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
	
	$('.TanggalDaftar').datepicker({changeMonth: true,changeYear: true,
			altField: "#AltTanggalDf",altFormat: "DD, d MM yy",minDate: "-5Y -3M -0D", maxDate: "+0M"
		});
	$( ".TanggalDaftar" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
	$('.TanggalSelesai').datepicker({changeMonth: true,changeYear: true,
			altField: "#AltTanggalSl",altFormat: "DD, d MM yy",minDate: "-5Y -3M -0D", maxDate: "+0M"
		});
	$( ".TanggalSelesai" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
	$('.TanggalMulai').datepicker({changeMonth: true,changeYear: true,
			altField: "#AltTanggalMl",altFormat: "DD, d MM yy",minDate: "-5Y -3M -0D", maxDate: "+0M"
		});
	$( ".TanggalMulai" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
	<?php if (!empty($t['thn'])) { ?>
	$( ".TanggalDaftar" ).datepicker( "setDate", new Date(<?php echo $t['thnDaftar'].','.$t['BlnDaftar'].','.$t['datDaftar']?>) ); 
	$( ".TanggalSidang" ).datepicker( "setDate", new Date(<?php echo $t['thn'].','.$t['Bln'].','.$t['dat']?>) ); 
	$( ".TanggalMulai" ).datepicker( "setDate", new Date(<?php echo $t['thnMulai'].','.$t['BlnMulai'].','.$t['datMulai']?>) ); 
	$( ".TanggalSelesai" ).datepicker( "setDate", new Date(<?php echo $t['thnSelesai'].','.$t['BlnSelesai'].','.$t['datSelesai']?>) ); <?php } ?>
});
</script>

<?php
$judul = str_replace("''", "'", $w['Judul']);
$judul = str_replace('""', '"', $judul);
echo "<hr><div class='well' align='center'>Periksalah data-data berikut, jika belum lengkap atau ada kesalahan harap diisi dan dikoreksi. </div>";
echo "<form class='form-horizontal' enctype='multipart/form-data' method=post action='baa/wisuda.permohonan.php' target='_blank'>
<input type=hidden name='gos' value='SAV'>
<input type='hidden' value='$MhswID' name='MhswID'>
<label class='control-label'>Nama</label><div class='controls'><input type=text Name='Nama' value='$w[Nama]' title='Teliti kembali data ini, Sesuaikan dengan Ijazah terakhir!'> <sup>Sesuaikan dengan Ijazah terakhir. Perhatikan Format penulisan, Huruf Besar/Huruf Kecil!</sup></div><br>
<label class='control-label'>Jenis Kelamin</label><div class='controls'><input type=radio value='P' name='Kelamin' $Pria> Pria <input type=radio name='Kelamin' value='W' $Wanita> Wanita</div><br/>
<label class='control-label'>Tempat Lahir</label><div class='controls'><input type=text Name='TempatLahir' value='$w[TempatLahir]' title='Teliti kembali data ini, Sesuaikan dengan Ijazah terakhir!'> <sup>Sesuaikan dengan Ijazah terakhir, Perhatikan Format Penulisan!</sup></div><br>
<label class='control-label'>Tanggal Lahir</label><div class='controls'>
						<input type=text Name='TanggalLahirIjazah' value='$w[TanggalLahirIjazah]' title='Teliti kembali data ini, Sesuaikan dengan Ijazah terakhir!'> <sup>Sesuaikan dengan Ijazah terakhir, Perhatikan Format Penulisan!</sup></div>
<label class='control-label'>Alamat Tetap</label><div class='controls'><textarea name='Alamat' cols=80>$w[Alamat]</textarea></div>
<label class='control-label'>No. Telp</label><div class='controls'><input type=text Name='Telepon' value='$w[Telepon]'> *) boleh tidak diisi</div>
<label class='control-label'>Handphone</label><div class='controls'><input type=text Name='Handphone' value='$w[Handphone]'  > <sup>Wajib diisi!</sup></div>
<label class='control-label'>Email Aktif</label><div class='controls'><input type=text name='Email' size=60  value='$w[Email]' > <sup>Wajib diisi!</sup></div>
<label class='control-label'>Website/blog Pribadi</label><div class='controls'><input type=text name='Website' size=50 value='$w[Website]'> *) boleh tidak diisi</div>
<label class='control-label'>Nama Ayah Kandung</label><div class='controls'><input type=text Name='NamaAyah'  value='$w[NamaAyah]' size=50> <sup>Wajib diisi!</sup></div>
<label class='control-label'>Nama Ibu Kandung</label><div class='controls'><input type=text Name='NamaIbu'  value='$w[NamaIbu]' size=50> <sup>Wajib diisi!</sup></div>
<hr>
<label class='control-label'>Judul Tugas Akhir</label><div class='controls'><input type=text Name='JudulTA'  value='$judul' size=60> <sup>Wajib diisi!</sup></div>
<label class='control-label'>Pembimbing I</label><div class='controls'><input type=text name='PembimbingI' size=50 value='$t[Pembimbing]'> <sup>Wajib diisi!</sup></div>
<label class='control-label'>Pembimbing II</label><div class='controls'><input type=text name='PembimbingII' size=50 value='$t[Pembimbing2]'> <sup>Wajib diisi!</sup></div>
<label class='control-label'>Tanggal Daftar TA/Skripsi</label><div class='controls'><input type=text name='TanggalDaftar' value='$t[TglDaftar]' class='TanggalDaftar'>
																				<input type=text disabled=true id='AltTanggalDf' size=30> <sup>Wajib diisi!</sup></div>
<label class='control-label'>Tanggal Mulai TA/Skripsi</label><div class='controls'><input type=text name='TanggalMulai' value='$t[TglMulai]' class='TanggalMulai'>
																				<input type=text disabled=true id='AltTanggalMl' size=30> <sup>Wajib diisi!</sup></div>
<label class='control-label'>Tanggal Selesai TA/Skripsi</label><div class='controls'><input type=text name='TanggalSelesai' value='$t[TglSelesai]' class='TanggalSelesai'>
																				<input type=text disabled=true id='AltTanggalSl' size=30> <sup>Wajib diisi!</sup></div>
<label class='control-label'>Tanggal Lulus Sidang</label><div class='controls'><input type=text name='TanggalSidang' value='$t[TglUjian]' class='TanggalSidang'>
																				<input type=text disabled=true id='AltTanggalSd' size=30> <sup>Wajib diisi!</sup></div>

<hr>
<div class='controls'>".(file_exists("foto/wisudawan/kecil/".$w['FotoWisuda']) ? "<img src='foto/wisudawan/kecil/".$w['FotoWisuda']."'>" : '')."</div>
<label class='control-label'>Upload Foto</label><div class='controls'><input type=file class='' name='foto' id='foto' value=''> <sup>Wajib diisi!</sup><br>
Ketentuan Foto:
	<ol>
	<li>Foto Berwarna.</li>
	<li>Ketentuan berfoto sama dengan Foto Ijazah. (lihat kembali pengumuman wisuda)</li>
	<li>Tipe file yang bisa diupload hanya jpg/jpeg</li>
	<li>Foto tidak boleh menggunakan border atau frame studio.</li></ol></div>


<label class='control-label'>Upload Skripsi</label><div class='controls'><input type=file class='' name='skripsi' id='skripsi' value=''> ".(!empty($w['Skripsi']) ? "File skripsi sudah ada, bila ingin revisi silakan upload ulang." : 'file skripsi belum ada, silakan upload.')." <sup>Wajib diisi!</sup><br>
Ketentuan Skripsi:
	<ol><li>File yang diterima adalah doc/docx/pdf/zip.</li>
	<li>File skripsi harus dalam versi penuh, bukan abstrak.</li>
	</ol></div>

 							<div class=\"form-actions\">
								<button type=\"submit\" class=\"btn btn-primary\">Ajukan Permohonan Wisuda</button>
								<button class=\"btn\" type=button onclick=\"location.href='?mnux=loginprc&gos=berhasil'\">Batal</button>
							  </div></form>"; 
}

?>