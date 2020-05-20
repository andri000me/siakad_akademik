<?php
session_start();
//	Author: Arisal Yanuarafi
//	15 September 2013
  
  if (!empty($_REQUEST['gos']) && ($_REQUEST['gos'] != 'SAV')) {
	  include_once "../dwo.lib.php";
	  include_once "../db.mysql.php";
	  include_once "../connectdb.php";
	  include_once "../parameter.php";
	  include_once "../cekparam.php";
  }
  
// *** Parameter ***
if ($_SESSION['_LevelID']==1) $MhswID = GetSetVar('MhswID');
elseif ($_SESSION['_LevelID']==120) $MhswID = $_SESSION['_Login'];
else die (errorMsg("Tidak berhak","Anda tidak berhak mengakses modul ini<hr>Opsi: <a href='?mnux'>Kembali</a>"));
$s 			= "SELECT mk.Nama from mk, krs where mk.MKKode=krs.MKKode and krs.NA='N' AND mk.PraktekKerja='Y' AND krs.MhswID='$MhswID'";
$r 			= _query($s);
while ($w2 	= _fetch_array($r)) { 
	$ada .= $w2['Nama'];
}
if (!empty($ada)){
	$gos		= (empty($_REQUEST['gos']))? 'TampilkanFormPraktek' : $_REQUEST['gos'];
	$gos();
}
else die(errorMsg("Belum Terdaftar","Anda belum mengambil Matakuliah KP/PL/PLK<br /><hr><b>Saran:</b> Daftarkan terlebih dahulu Matakuliah KP/PL/PLK pada KRS Anda, atau hubungi Jurusan."));


function TampilkanFormPraktek() {
	global $MhswID;
	RandomStringScript();
	Scriptnya();
	TampilkanJudul('Pendaftaran KP/PL/PLK');
	$w 	= GetFields('praktekkerja', "NA='N' and MhswID", $MhswID, "*, date_format(TanggalBuat,'%Y-%m-%d') as Tanggal, date_format(TanggalBuat,'%H:%i') as Jam");
	if (empty($w['MhswID'])){
echo "<hr>Syarat Pengajuan KP/PKL/PL: <br>
	<ul>
		<li>Telah menempuh minimal 108 SKS.
		<li>Terdaftar sebagai mahasiswa aktif Semester Ganjil Tahun Akademik 2013/2014
		<li>Klik tombol 'Ajukan Permohonan KP/PKL/PL' untuk pengajuan KP/PKL/PL anda.
		<li>Setelah diajukan, anda dapat memantau proses Pengajuan KP/PKL/PL anda, apakah DISETUJUI atau TIDAK di ruangan ini
	</ul>
<ul>Isilah kolom-kolom berikut untuk mendaftarkan KP/PL/PLK Anda:</ul>";
echo "<form class='form-horizontal' enctype='multipart/form-data' method=post action='?mnux=jur/praktek.form'>
<input type=hidden name='gos' value='SAV'>
<input type='hidden' value='$MhswID' name='MhswID'>
<label class='control-label'>Nama Perusahaan</label><div class='controls'><input type=text Name='NamaPerusahaan' value='$w[NamaPerusahaan]' size=50></div>
<label class='control-label'>Alamat</label><div class='controls'><input type=text Name='AlamatPerusahaan' value='$w[AlamatPerusahaan]' size=70></div>
<label class='control-label'>Kota</label><div class='controls'><input type=text Name='KotaPerusahaan' value='$w[KotaPerusahaan]'></div>
<label class='control-label'>Deskripsi Pekerjaan</label><div class='controls'><input type=text Name='NamaPekerjaan' value='$w[NamaPekerjaan]' size=50></div>
 							<div class=\"form-actions\">
								<button type=\"submit\" class=\"btn btn-primary\">Ajukan Permohonan KP/PKL/PL</button>
								<button class=\"btn\" type=button onclick=\"location.href='?mnux=loginprc&gos=berhasil'\">Batal</button>
							  </div></form>
"; 
	}
	else {
		$mhs = GetFields('mhsw', "MhswID", $MhswID, "Nama,ProdiID");
		$Prd = GetaField('prodi', "ProdiID", $mhs['ProdiID'], 'Nama');
		$FID = GetaField('prodi', "ProdiID", $mhs['ProdiID'], 'FakultasID');
		$fak = GetaField('fakultas', "FakultasID",$FID,"Nama");
		$pem = GetFields('praktekkerja p left outer join dosen d on d.Login=p.Pembimbing1
											left outer join dosen dd on dd.Login=p.Pembimbing2', "p.NA='N' and p.MhswID", $MhswID, "
											concat(d.Gelar1, ' ', d.Nama,', ',d.Gelar) as satu,
											concat(dd.Gelar1, ' ', dd.Nama,', ',dd.Gelar) as dua");
		if ($w['Lulus']=='Y') {
			echo "Anda sudah mengikuti KP/PKL/PL dan dinyatakan lulus dengan nilai ".$w['GradeNilai'];
		}
		else {
		if ($w['Status']==0) {$Status = "proses";$Tombol = '<input type="button" value="Cetak Surat Penunjukan Pembimbing KP/PL/PLK" onclick="javascript:fnCetakPersetujuan()"><br>Surat ini dibawa ke Ketua Jurusan '.$Prd.' untuk penunjukan pembimbing praktek dan ditandatangani, kemudian surat ini dibawa ke bagian Akademik Fakultas '.$fak.' untuk diinput ke Portal Universitas Bung Hatta.';}
		if ($w['Status']==1) {$Status = "diterima";$Tombol = "<br><br>Untuk selanjutnya, silakan:
		<ul>
		<li>Cetak <a href='#' rel='tooltip' title=\"SK Penunjukan Pembimbing KP/PL/PLK\" onclick=\"javascript:fnCetakSK()\">SK Penunjukan Pembimbing KP/PL/PLK</a> sebanyak 5 rangkap.
    	<li>Cetak Surat Penelitian anda di Ruangan Surat Penelitian sebanyak yang anda perlukan.
    	<li>Bawa SK Penunjukan Pembimbing PK dan Surat Penelitian anda ke Bagian Akademik Fakultas ".ucwords(strtolower($fak))." untuk ditandatangani Dekan ".ucwords(strtolower($fak)).".
    	<li>Selesai ditandatangi Dekan Fakultas ".$fak.", silahkan berikan SK Penunjukan Pembimbing kepada:
		<ol>
        <li>Administrasi Fakultas ".$fak."
        <li>Pembimbing I :<b>".$pem['satu']."</b>
        <li>Pembimbing II :<b>".$pem['dua']."</b>
        <li>Program Studi ".$Prd." Fakultas ".$fak."
        <li>Simpan untuk kebutuhan anda sendiri 
		</ol>
    	<li>Setelah itu, buat <a href='#' onclick=\"javascript:modalPopup('jur/ajx/praktek.surat.pengantar','Cetak Surat Pengantar','".$MhswID."')\">Surat Pengantar</a> ke instansi/perusahaan tempat anda KP/PKL/PL sebanyak yang anda perlukan. </ul>";}
		if ($w['Status']==2) {$Status = "ditolak";$Tombol = '<input type="button" value="Ajukan Kembali" onclick="javascript:fnAjukanKembali()"> untuk mengulangi permohonan KP/PKL/PL.';}
		echo "<center><div width=700 style='text-align:justify;width:700px'>Anda sudah pernah mengajukan permohonan KP/PL/PLK pada tanggal: <b>".TanggalFormat($w['Tanggal'])."</b> pukul <b>$w[Jam]</b>. Berikut adalah status permohonan Anda:</div>
		<table class='box' width=700>
		<tr><td class='inp' width=200>Nama</td><td><b>$mhs[Nama]</b></td></tr>
		<tr><td class='inp'>NPM</td><td>$MhswID</td></tr>
		<tr><td class='inp'>Program Studi</td><td>$Prd</td></tr>
		<tr><td class='inp'>Nama Perusahaan</td><td>$w[NamaPerusahaan]</td></tr>
		<tr><td class='inp'>Alamat Perusahaan</td><td>$w[AlamatPerusahaan]</td></tr>
		<tr><td class='inp'>Kota</td><td>$w[KotaPerusahaan]</td></tr>
		<tr><td class='inp'>Status Permohonan</td><td align='justify'><img src='img/".(($Status=='proses')? 'proses.png' : $Status.'.gif')."'>
																".ucfirst($Status).". <br></td></tr></table>
		<div width=700 style='text-align:justify;width:700px'>$Tombol</div>
		</form></center>";
	}
	}
}

function SAV() {
	global $MhswID;
	$w 	= GetFields('praktekkerja', "MhswID", $MhswID, "*");
	$NamaPerusahaan = sqling($_POST['NamaPerusahaan']);
	$AlamatPerusahaan = sqling($_POST['AlamatPerusahaan']);
	$KotaPerusahaan = sqling($_POST['KotaPerusahaan']);
	$NamaPekerjaan = sqling($_POST['NamaPekerjaan']);
	$mhs = GetFields('mhsw', "MhswID", $MhswID, "ProdiID, ProgramID");
	$TahunID = GetaField('tahun', "NA='N' AND ProdiID='$mhs[ProdiID]' AND ProgramID='$mhs[ProgramID]' AND KodeID", KodeID, "TahunID"); 
	if (empty($w['MhswID'])){
	$s = "INSERT INTO praktekkerja(TahunID,MhswID,NamaPerusahaan,AlamatPerusahaan, KotaPerusahaan, NamaPekerjaan,TglDaftar, Lulus, LoginBuat, TanggalBuat) values
			('$TahunID','$MhswID','$NamaPerusahaan', '$AlamatPerusahaan', '$KotaPerusahaan', '$NamaPekerjaan', now(), 'N', '$_SESSION[_Login]', now())";
	$r = _query($s);
	}else{
	$s = "UPDATE praktekkerja set TahunID='$TahunID',
						NamaPerusahaan='$NamaPerusahaan',
						AlamatPerusahaan='$AlamatPerusahaan',
						KotaPerusahaan='$KotaPerusahaan',
						NamaPekerjaan='$NamaPekerjaan',
						NA='N'
						where MhswID='$MhswID' ";
	$r = _query($s);					
	}
	//echo $s;
	BerhasilSimpan('?mnux=jur/praktek.form',100);
}

function ctkSuratPersetujuan() {
	global $MhswID;
	$mhsw = GetFields('mhsw', "MhswID", $MhswID, '*');
	$Identitas = GetFields('identitas', "Kode", KodeID,'*');
	$DataProdi = GetFields('prodi', "ProdiID", $mhsw['ProdiID'],"*");
	$DataFakultas = GetFields('fakultas',"FakultasID",$DataProdi['FakultasID'], "*");
	$praktek = GetFields('praktekkerja', "NA='N' AND MhswID", $MhswID, "*, date_format(TanggalBuat,'%Y-%m-%d') as Tanggal");
	
	echo "<html><head><title>Pengajuan KP/PKL/PL ".$mhsw['Nama']." - NPM ".$mhsw['MhswID']."</title>";
			echo "<style>
			body {
				margin-left: 0 px;
				font-family: Georgia, Tahoma, Verdana, Arial;
				font-size: 13px;
			}
			td {
				font-size: 13px;
				font-family: Georgia, Tahoma, Verdana, Arial;
			}
			.logo {
				font-size: 14px;
				font-family: Arial, Tahoma, Verdana, Arial;
				font-weight: bold;
				color: #0000ff;
			}
			.kampus {
				font-size: 16px;
				font-family: Arial, Georgia, Tahoma, Verdana, Arial;
				font-weight: bold;
				color: #0000ff;
			}
			.fakultas {
				font-size: 18px;
				font-family: Tahoma, Verdana, Arial;
				font-weight: bold;
				color: #0000ff;
			}
			.alamat {
				font-size: 10px;
				font-family: Tahoma, Arial, Verdana;
			}
			.tabel {
				text-align:center;
				border-top:2px solid #000000;
				border-bottom:1px dotted #000000;
			}
			.judulsurat {
				font-size: 18px;
				font-family: Georgia, Tahoma, Verdana, Arial;
				font-weight: bold;
			}
			.isisurat {
				font-size: 13px;
				font-family: Georgia, Tahoma, Verdana, Arial;
			}
			.catatan {
				font-size: 11px;
				font-family: Tahoma, Verdana, Arial;
			}
			</style>
			<style media='print'>
			.onlyscreen {
				display: none;
			}
			</style>
			</head>";

			echo "<body>
			<center>
			<div style='width:650px; text-align:center; background: #ffffff;'>

			<table style='width:100%'>
			<tr>
			<td width='10%' class='alamat' style='text-align:left;' valign='top'><img src='../img/ubh_logo.png' width='100' height='98' border='0' alt='Cetak Bukti' title='Cetak Bukti'></td>
			<td width='90%' valign='top' class='alamat' style='text-align:left; padding-left:10px'>
			<div class='logo'>".strtoupper($Identitas['Yayasan'])."</div>
			<div class='kampus'>".strtoupper($Identitas['Nama'])."</div>
			<div class='fakultas'>FAKULTAS ".strtoupper($DataFakultas['Nama'])."</div>
			<div class='alamat'>".ucfirst($DataFakultas['Alamat'])." ".ucfirst($DataFakultas['Kota'])."<br />
			Email: <font color='#0000ff'>".strtolower($DataFakultas['Email'])."</font> Website: <font color='#0000ff'>".strtolower($DataFakultas['Website'])."</font></div>
			</td>
			</tr>
			</table>

			<table cellspacing='0' cellpadding='0' style='width:100%;'>
			<tr>
			<td class='tabel' style='width:650px; height:2px'><img src='../img/transparant.png' height='1' border='0'></td>
			</tr>
			</table>
			<br /><br />

			<div class='judulsurat' style='text-align:center'>SURAT PERSETUJUAN KP/PKL/PLK</div>
			<br /><br />

			<div class='isisurat' style='text-align:Justify'>Yang bertanda tangan dibawah ini, Ketua Jurusan ".str_replace(" Dan ", " dan ",ucwords(strtolower($DataProdi['Nama'])))." Fakultas ".str_replace(" Dan ", " dan ",ucwords(strtolower($DataFakultas['Nama'])))." ".ucwords(strtolower($Identitas['Nama']))." menyatakan bahwa mahasiswa yang tercantum dibawah ini sudah memenuhi persyaratan dan memberikan persetujuan KP/PKL/PLK terhadap:

			<br /><br />

			<div style='padding-left:50px'>
			<table cellspacing='0' cellpadding='0' style='width:100%;'>
			<tr>
			<td width='25%'>Nama</td>
			<td width='5%'>:</td>
			<td width='70%'><b>".$mhsw['Nama']."</b></td>
			</tr>
			<tr style='height:40px'>
			<td>NPM</td>
			<td>:</td>
			<td>".$mhsw['MhswID']."</td>
			</tr>
			<tr>
			<td>Nama Instansi/ Perusahaan KP/PKL/PL</td>
			<td>:</td>
			<td>".$praktek['NamaPerusahaan']."</td>
			</tr>
			<tr>
			<td style='height:40px'>Lokasi KP/PKL/PL</td>
			<td style='height:40px'>:</td>
			<td style='height:40px'>".$praktek['AlamatPerusahaan']."</td>
			</tr>
			<tr>
			<td>Tugas Khusus</td>
			<td>:</td>
			<td>".$praktek['NamaPekerjaan']."</td>
			</tr>
			<tr>
			<td style='height:40px'>Tgl Pengajuan</td>
			<td style='height:40px'>:</td>
			<td style='height:40px'>".TanggalFormat($praktek['Tanggal'])."</td>
			</tr>
			</table>
			</div>
			<br /><br />

			Dengan pembimbing sebagai berikut:
			<ol>
			<li>Pembimbing I :<br /><br />
			<li>Pembimbing II:
			</ol>
			<br />

			Demikian hal ini disampaikan untuk dapat dimaklumi.
			</div>
			<br />

			<div style='text-align:left; padding-left:400px'>Padang,<br />
			Ketua Jurusan ".str_replace(" Dan ", " dan ",ucwords(strtolower($DataProdi['namajurusan'])))."
			<br /><br /><br /><br /><br /><br />
			<b><u>".$DataProdi['Pejabat']."</u></b>
			</div>
			<br /><br />

			<div class='catatan' style='text-align:left; width:100%; padding-right:300px'><u>Catatan</u>
			<br />Mahasiswa bersangkutan agar membawa salinan Surat Persetujuan ini ke Bagian Akademik Fakultas ".str_replace(" Dan ", " dan ",ucwords(strtolower($DataFakultas['Nama'])))." ".ucwords(strtolower($Identitas['Nama']))." untuk perubahan status KP/PKL/PL pada Portal Universitas Bung Hatta</div>

			<script>
			window.print();
			</script>

			<div class='onlyscreen' style='text-align:center'>
			<form>
			<input type='button' value='Klik Untuk Mencetak Surat Persetujuan KP/PKL/PL' onClick='window.print()' style='font: 11px Tahoma,Verdana,Arial;' />
			</form>
			</div>";

			echo "<br />
			</div>
			</center>
			</body>
			</html>";
}
function Scriptnya() {
	global $MhswID;
 	?><script>
			  function fnCetakPersetujuan() {
				var _rnd = randomString();
				lnk = "<?php echo $_SESSION['mnux']?>.php?gos=ctkSuratPersetujuan&_rnd="+_rnd+"&ui=win";
				win2 = window.open(lnk, "", "width=700, height=550,left=200,top=0, scrollbars");
				if (win2.opener == null) childWindow.opener = self;
			  }
			  function fnCetakSK() {
				var _rnd = randomString();
				lnk = "<?php echo $_SESSION['mnux']?>.SK.php?MhswID=<?php echo $MhswID?>&_rnd="+_rnd+"&ui=win";
				win2 = window.open(lnk, "", "width=700, height=550,left=200,top=0, scrollbars");
				if (win2.opener == null) childWindow.opener = self;
			  }
		</script><?php
 }