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
$MhswID = ($_SESSION['_LevelID']==120) ? $_SESSION['_Login'] : GetSetVar('MhswID');
// *** Cek dulu matakuliah skripsinya
$s 			= "SELECT mk.Nama, krs.KRSID from mk, krs where mk.MKKode=krs.MKKode and krs.NA='N' AND mk.TugasAkhir='Y' AND mk.Komprehensif='Y' AND krs.MhswID='$MhswID'";
$r 			= _query($s);
while ($w2 	= _fetch_array($r)) { 
	$ada .= $w2['KRSID'];
}
// Jika ada lanjutkan
// if (!empty($ada)){
	$gos		= (empty($_REQUEST['gos']))? 'TampilkanForm' : $_REQUEST['gos'];
	$gos($ada);
// }
// jika belum tampilkan pesan
// else die(errorMsg("Belum Terdaftar","Anda belum mengambil Matakuliah TA/Skripsi<br /><hr><b>Saran:</b> Daftarkan terlebih dahulu Matakuliah TA/Skripsi pada KRS Anda, atau hubungi Jurusan."));


function TampilkanForm($KRSID) {
	global $MhswID;
	RandomStringScript();
	Scriptnya();
	TampilkanJudul('Pengajuan Judul dan Pembimbing TA/Skripsi/Tesis');
	$w 	= GetFields('ta', "NA='N' and MhswID", $MhswID, "*, date_format(TanggalBuat,'%Y-%m-%d') as Tanggal, date_format(TanggalBuat,'%H:%i') as Jam");
	if (empty($w['MhswID'])){
		$m = GetFields('mhsw', "MhswID", $MhswID, "*");
		$Pengaturan = GetFields('ta_pengaturan', "ProdiID", $m['ProdiID'], "*");
		$LulusPraktekKerja = ($Pengaturan['LulusPraktekKerja']=='Y')? "<li>Lulus sidang KP/PKL/PL</li>" : '';
		$TahunID = GetaField('tahun', "NA='N' AND ProdiID='$m[ProdiID]' AND ProgramID='$m[ProgramID]' AND KodeID", KodeID, "Nama"); 
echo "<hr>Syarat Pengajuan Judul dan Pembimbing TA/Skripsi/Tesis: <br>
	<ol>
		".$LulusPraktekKerja."
		<li>Telah menempuh minimal 90 SKS (D3) / 120 SKS (S1) / 30 SKS (S2) </li>
		<li>Terdaftar sebagai mahasiswa aktif ".$TahunID."</li>
		</ol>
		Langkah pengajuan:
		<ul>
		<li>Lengkapi formulir berikut lalu Klik tombol 'Ajukan Permohonan Judul dan Pembimbing TA/Skripsi/Tesis'.</li>
		<li>Setelah diajukan, anda dapat memantau proses Pengajuan TA/Skripsi/Tesis anda, apakah DISETUJUI atau TIDAK di halaman ini</li>
	</ul>
<ul>Isilah kolom berikut untuk mendaftarkan TA/Skripsi/Tesis Anda:</ul>";
echo "<form class='form-horizontal' enctype='multipart/form-data' method=post action='?mnux=jur/tesis.form'>
<input type=hidden name='gos' value='SAV'>
<input type='hidden' value='$MhswID' name='MhswID'>
<label class='control-label'>Judul Tesis</label><div class='controls'><input type=text Name='Judul' value='$w[Judul]' size=50>
<br><sup>Huruf Besar Setiap Awal Kata dan sesuaikan dengan Standar EYD.</sup></div>
<label class='control-label'>Pembimbing I</label><div class='controls'><input type=text Name='Pembimbing1' value='$w[Pembimbing_1]' size=50>
<br><sup>Huruf Besar Setiap Awal Kata dan Lengkap dengan gelar akademik dosen.</sup></div>
<label class='control-label'>Pembimbing II</label><div class='controls'><input type=text Name='Pembimbing2' value='$w[Pembimbing_2]' size=50>
<br><sup>Huruf Besar Setiap Awal Kata dan Lengkap dengan gelar akademik dosen.</sup></div>
<label class='control-label'>Alamat</label><div class='controls'><input type=text Name='Alamat' value='$mhs[Alamat]' size=50>
<br></div>
<label class='control-label'>Telp. / Handphone</label><div class='controls'><input type=text Name='Handphone' value='$mhs[Handphone]' size=50>
<br></div>
<label class='control-label'>Instansi Asal</label><div class='controls'><input type=text Name='Instansi' value='$mhs[Instansi]' size=50>
<br></div>
<label class='control-label'>Jabatan</label><div class='controls'><input type=text Name='Jabatan' value='$mhs[Jabatan]' size=50>
<br></div>
 							<div class=\"form-actions\">
								<button type=\"submit\" class=\"btn btn-primary\">Ajukan Permohonan Judul dan Pembimbing</button>
								<button class=\"btn\" type=button onclick=\"location.href='?mnux=loginprc&gos=berhasil'\">Batal</button>
							  </div></form>"; 
	}
	else {
		$mhs = GetFields('mhsw', "MhswID", $MhswID, "*");
		$Prd = GetaField('prodi', "ProdiID", $mhs['ProdiID'], 'Nama');
		$FID = GetaField('prodi', "ProdiID", $mhs['ProdiID'], 'FakultasID');
		$fak = GetaField('fakultas', "FakultasID",$FID,"Nama");
		$pem = GetFields('ta t left outer join dosen d on d.Login=t.Pembimbing', "t.NA='N' and t.MhswID", $MhswID, "
											concat(d.Gelar1, ' ', d.Nama,', ',d.Gelar) as satu");
		$rowpemb2 	= GetaField('tadosen t left outer join dosen d on d.Login=t.DosenID', "t.TAID", $w['TAID'], "concat(d.Gelar1,' ',d.Nama, ', ', d.Gelar)");
		if ($w['Lulus']=='Y') {
			echo "Anda sudah melaksanakan TA/Skripsi/Tesis dan dinyatakan lulus dengan nilai ".$w['GradeNilai'];
		}
		else {
		if ($w['Status']==0) {$Status = "proses";$Tombol = '<input type="button" value="Cetak Surat Penunjukan Pembimbing" onclick="javascript:fnCetakPersetujuan()"><br>Surat ini dibawa ke Ketua Prodi '.$Prd.' untuk penunjukan pembimbing TA/Skripsi/Tesis dan ditandatangani, kemudian dibawa ke bagian Tata Usaha '.$fak.' untuk diinput ke Portal Universitas Bung Hatta.';}
		if ($w['Status']==1) {$Status = "diterima";$Tombol = "<br><br>Untuk selanjutnya, silakan:
		<ul>
		<li>Cetak <a href='#' rel='tooltip' title=\"SK Penunjukan Pembimbing TA/Skripsi/Tesis\" onclick=\"javascript:fnCetakSK()\">SK Penunjukan Pembimbing TA/Skripsi/Tesis</a> sebanyak 5 rangkap.
    	<li>Cetak Surat Penelitian sebanyak yang anda perlukan.
    	<li>Bawa SK Penunjukan Pembimbing TA/Skripsi/Tesis dan Surat Penelitian anda ke Tata Usaha ".ucwords(strtolower($fak))." untuk ditandatangani.
    	<li>Setelah ditandatangi, silahkan berikan SK Penunjukan Pembimbing kepada:
		<ol>
        <li>Tata Usaha
        <li>Pembimbing I :<b>".$pem['satu']."</b>
        <li>Pembimbing II :<b>".$rowpemb2."</b>
        <li>Program Studi ".$Prd." Fakultas ".$fak."
        <li>Simpan untuk kebutuhan anda sendiri 
		</ol>
    	<li>Setelah itu, buat <a href='#' onclick=\"javascript:modalPopup('jur/ajx/ta.surat.pengantar','Cetak Surat Pengantar','".$MhswID."')\">Surat Pengantar</a> ke instansi/perusahaan tempat anda melakukan penelitian sebanyak yang anda perlukan. </ul>";}
		if ($w['Status']==2) {$Status = "ditolak";$Tombol = '<input type="button" value="Ajukan Kembali" onclick="javascript:fnAjukanKembali()"> untuk mengulangi permohonan TA/Skripsi/Tesis.';}
		echo "<center><div width=700 style='text-align:justify;width:700px'>Anda sudah pernah mengajukan permohonan TA/Skripsi/Tesis pada tanggal: <b>".TanggalFormat($w['Tanggal'])."</b> pukul <b>$w[Jam]</b>. Berikut adalah status permohonan Anda:</div>
		<table class='box' width=700>
		<tr><td class='inp' width=200>Nama</td><td><b>$mhs[Nama]</b></td></tr>
		<tr><td class='inp'>NPM</td><td>$MhswID</td></tr>
		<tr><td class='inp'>Program Studi</td><td>$Prd</td></tr>
		<tr><td class='inp'>Judul TA/Skripsi/Tesis</td><td>$w[Judul]</td></tr>
		<tr><td class='inp'>Status Permohonan</td><td align='justify'><img src='img/".(($Status=='proses')? 'proses.png' : $Status.'.gif')."'>
																".ucfirst($Status).". <br></td></tr></table>
		<div width=700 style='text-align:justify;width:700px'>$Tombol</div>
		</form></center>";
		echo "<hr>
		<ul><h3>Tanda Persetujuan dan Permohonan Seminar Proposal</h3></ul>
 							<div class=\"form-actions\">
 								<a class=\"btn btn-primary\" href='#' onclick=\"fnSetujuProposal()\">Tanda Persetujuan Seminar Proposal</a>
								<a class=\"btn btn-primary\" href='#' onclick=\"fnMohonProposal()\">Cetak Permohonan Seminar Proposal</a>
							  </div>";
		echo "<hr>
		<ul><h3>Tanda Persetujuan dan Permohonan Seminar Hasil</h3></ul>
 							<div class=\"form-actions\">
 								<a class=\"btn btn-primary\" href='#' onclick=\"fnSetujuHasil()\">Tanda Persetujuan Seminar Hasil</a>
								<a class=\"btn btn-primary\" href='#' onclick=\"fnMohonHasil()\">Cetak Permohonan Seminar Hasil</a>
							  </div>";
        echo "<hr>
		<ul><h3>Tanda Persetujuan dan Permohonan Ujian Tesis</h3></ul>
 							<div class=\"form-actions\">
 								<a class=\"btn btn-primary\" href='#' onclick=\"fnSetujuUjian()\">Tanda Persetujuan Ujian</a>
								<a class=\"btn btn-primary\" href='#' onclick=\"fnMohonUjian()\">Cetak Permohonan Ujian</a>
							  </div>";

		echo "<hr />
<ul><h3>Edit Data</h3></ul>
		<form class='form-horizontal' enctype='multipart/form-data' method=post action='?mnux=jur/tesis.form'>
<input type=hidden name='gos' value='SAV'>
<input type='hidden' value='$MhswID' name='MhswID'>
<label class='control-label'>Judul TA/Skripsi/Tesis</label><div class='controls'><input type=text Name='Judul' value='$w[Judul]' size=50>
<br><sup>Huruf Besar Setiap Awal Kata dan sesuaikan dengan Standar EYD.</sup></div>
<label class='control-label'>Alamat</label><div class='controls'><input type=text Name='Alamat' value='$mhs[Alamat]' size=50>
<br></div>
<label class='control-label'>Telp. / Handphone</label><div class='controls'><input type=text Name='Handphone' value='$mhs[Handphone]' size=50>
<br></div>
<label class='control-label'>Instansi Asal</label><div class='controls'><input type=text Name='Instansi' value='$mhs[Instansi]' size=50>
<br></div>
<label class='control-label'>Jabatan</label><div class='controls'><input type=text Name='Jabatan' value='$mhs[Jabatan]' size=50>
<br></div>
 							<div class=\"form-actions\">
								<button type=\"submit\" class=\"btn btn-primary\">Update Judul TA/Skripsi/Tesis</button>
								<button class=\"btn\" type=button onclick=\"location.href='?mnux=loginprc&gos=berhasil'\">Batal</button>
							  </div></form>"; 
	}
	}
}

function SAV($KRSID) {
	global $MhswID;
	$w 	= GetFields('ta', "MhswID", $MhswID, "*");
	$Judul = sqling($_POST['Judul']);
	$Pembimbing1 = sqling($_POST['Pembimbing1']);
	$Pembimbing2 = sqling($_POST['Pembimbing2']);

	$Handphone = GetSetVar('Handphone');
	$Alamat = GetSetVar('Alamat');
	$Instansi = GetSetVar('Instansi');
	$Jabatan = GetSetVar('Jabatan');

	if (empty($w['MhswID'])){
	$mhs = GetFields('mhsw', "MhswID", $MhswID, "ProdiID, ProgramID");
	$TahunID = GetaField('tahun', "NA='N' AND ProdiID='$mhs[ProdiID]' AND ProgramID='$mhs[ProgramID]' AND KodeID", KodeID, "TahunID"); 
	$s = "INSERT INTO ta(KRSID, TahunID, MhswID, KodeID, Judul, TglDaftar, Lulus, Pembimbing_1, Pembimbing_2, LoginBuat, TanggalBuat) values
			('$KRSID','$TahunID','$MhswID','".KodeID."','$Judul', now(), 'N', '$Pembimbing1','$Pembimbing2', '$_SESSION[_Login]', now())";
	$r = _query($s);
	}
	else {
		$s = "update ta set Judul='$Judul',TahunID='$TahunID' where MhswID='$MhswID' and NA='N' ";
		$r = _query($s);

		$upd 	= _query("update mhsw set Handphone='$Handphone', Alamat='$Alamat', Instansi='$Instansi', Jabatan='$Jabatan',LoginEdit='$_SESSION[_Login]', TanggalEdit=now() where MhswID='$MhswID'");
	}
	//echo $s;
	BerhasilSimpan('?mnux=jur/tesis.form',100);
}

function ctkSuratPersetujuan($KRSID) {
	global $MhswID;
	$mhsw = GetFields('mhsw', "MhswID", $MhswID, '*');
	$Identitas = GetFields('identitas', "Kode", KodeID,'*');
	$DataProdi = GetFields('prodi', "ProdiID", $mhsw['ProdiID'],"*");
	$DataFakultas = GetFields('fakultas',"FakultasID",$DataProdi['FakultasID'], "*");
	$ta = GetFields('ta', "NA='N' AND MhswID", $MhswID, "*, date_format(TanggalBuat,'%Y-%m-%d') as Tanggal");
	
	echo "<html><head><title>Pengajuan TA/Skripsi/Tesis ".$mhsw['Nama']." - NPM ".$mhsw['MhswID']."</title>";
			echo "<style>
			body {
				margin-left: 0 px;
				font-family: Arial, Tahoma, Verdana, Arial;
				font-size: 13px;
			}
			td {
				font-size: 13px;
				font-family: Arial, Tahoma, Verdana, Arial;
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
				font-family: Arial, Verdana, Arial;
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
				font-family: Arial, Tahoma, Verdana, Arial;
				font-weight: bold;
			}
			.isisurat {
				font-size: 13px;
				font-family: Arial, Tahoma, Verdana, Arial;
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

			<div style='width:650px; padding-left:20px; text-align:center; background: #ffffff;'>
			<br /><br />

			<div class='judulsurat' style='text-align:center'>PENGAJUAN JUDUL DAN PEMBIMBING ".($DataFakultas['FakultasID']=='08'? "TESIS":"TUGAS AKHIR/SKRIPSI")."</div>
			<br /><br />

			<div style='text-align:Right'>Padang, ".TanggalFormat(date('Y-m-d'))."</div>
			<br />

			<div style='text-align:Left'>
			Kepada <br />
			Yth. Ketua Program Studi ".str_replace(" Dan ", " dan ",ucwords(strtolower($DataProdi['Nama'])))."<br />
			".($DataFakultas['FakultasID']=='08'? "Program":"Fakultas")." ".str_replace(" Dan ", " dan ",ucwords(strtolower($DataFakultas['Nama'])))."<br />
			".ucwords(strtolower($Identitas['Nama']))."<br />
			Padang
			</div>

			<br /><br />

			<div class='isisurat' style='text-align:Justify'>
			Dengan hormat,
			<br /><br />
			Saya yang bertanda tangan di bawah ini :

			<br /><br />

			<div style='padding-left:50px'>
			<table cellspacing='0' cellpadding='0' style='width:100%;'>
			<tr>
			<td width='10%'>Nama</td>
			<td width='2%'>:</td>
			<td width='80%'><b>".$mhsw['Nama']."</b></td>
			</tr>
			<tr style='height:40px'>
			<td>NPM</td>
			<td>:</td>
			<td>".$mhsw['MhswID']."</td>
			</tr>
			</table>
			</div>
			<br />

			Dengan ini mengajukan permohonan penunjukan pembimbing ".($DataFakultas['FakultasID']=='08'? "Tesis":"Tugas Akhir/Skripsi")." dengan judul :
			<br />
			<strong>".$ta['Judul']."</strong>
			<br /><br />
			Sebagai pertimbangan bagi Bapak/Ibu kami mengusulkan pembimbing sebagai berikut :

			<br /><br />

			<div style='padding-left:50px'>
			<table cellspacing='0' cellpadding='0' style='width:100%;'>
			<tr style='height:40px'>
			<td width='25%'>1. Pembimbing I</td>
			<td width='2%'>:</td>
			<td width='70%'><b>".$ta['Pembimbing_1']."</b></td>
			</tr>
			<tr>
			<td width='25%'>2. Pembimbing II</td>
			<td width='2%'>:</td>
			<td width='70%'><b>".$ta['Pembimbing_2']."</b></td>
			</tr>
			</table>
			<br />
			<strong>Pembimbing yang disetujui :</strong><br />
			<table cellspacing='0' cellpadding='0' style='width:100%;'>
			<tr style='height:40px'>
			<td width='25%'>1. Pembimbing I</td>
			<td width='5%'>:</td>
			<td width='70%'></td>
			</tr>
			<tr>
			<td width='25%'>2. Pembimbing II</td>
			<td width='5%'>:</td>
			<td width='70%'></td>
			</tr>
			</table>
			<br />
			</div>
			Demikian kami sampaikan, atas perhatian dan pertimbangan Bapak/Ibu kami ucapkan terima kasih.
			</div>
			<br />

			<div style='text-align:left; padding-left:400px'>Yang mengajukan,<br />
			<br /><br /><br /><br /><br />
			<b><u>".$mhsw['Nama']."</u></b>

			</div>
			<br /><br />

			<div class='catatan' style='text-align:left; width:100%; padding-right:300px'><u>Catatan</u>
			<br />Mahasiswa bersangkutan agar membawa Surat ini ke Tata Usaha ".($DataFakultas['FakultasID']=='08'? "Program":"Fakultas")." ".str_replace(" Dan ", " dan ",ucwords(strtolower($DataFakultas['Nama'])))." untuk perubahan status TA/Skripsi/Tesis pada Portal ".ucwords(strtolower($Identitas['Nama']))."</div>

			<div class='onlyscreen' style='text-align:center'>
			<form>
			<input type='button' value='Klik Untuk Mencetak Surat Permohonan TA/Skripsi/Tesis' onClick='window.print()' style='font: 11px Tahoma,Verdana,Arial;' />
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
			  function fnMohonProposal() {
				var _rnd = randomString();
				lnk = "jur/ta.permohonan.seminar.proposal.php?_rnd="+_rnd+"&ui=win";
				win2 = window.open(lnk, "", "width=700, height=550,left=200,top=0, scrollbars");
				if (win2.opener == null) childWindow.opener = self;
			  }
			  function fnMohonHasil() {
				var _rnd = randomString();
				lnk = "jur/ta.permohonan.seminar.hasil.php?_rnd="+_rnd+"&ui=win";
				win2 = window.open(lnk, "", "width=700, height=550,left=200,top=0, scrollbars");
				if (win2.opener == null) childWindow.opener = self;
			  }
			  function fnMohonUjian() {
				var _rnd = randomString();
				lnk = "jur/ta.permohonan.ujian.php?_rnd="+_rnd+"&ui=win";
				win2 = window.open(lnk, "", "width=700, height=550,left=200,top=0, scrollbars");
				if (win2.opener == null) childWindow.opener = self;
			  }
			  function fnSetujuProposal() {
				var _rnd = randomString();
				lnk = "jur/ta.setuju.seminar.proposal.php?_rnd="+_rnd+"&ui=win";
				win2 = window.open(lnk, "", "width=700, height=550,left=200,top=0, scrollbars");
				if (win2.opener == null) childWindow.opener = self;
			  }
			  function fnSetujuHasil() {
				var _rnd = randomString();
				lnk = "jur/ta.setuju.seminar.hasil.php?_rnd="+_rnd+"&ui=win";
				win2 = window.open(lnk, "", "width=700, height=550,left=200,top=0, scrollbars");
				if (win2.opener == null) childWindow.opener = self;
			  }
			  function fnSetujuUjian() {
				var _rnd = randomString();
				lnk = "jur/ta.setuju.ujian.php?_rnd="+_rnd+"&ui=win";
				win2 = window.open(lnk, "", "width=700, height=550,left=200,top=0, scrollbars");
				if (win2.opener == null) childWindow.opener = self;
			  }
		</script><?php
 }