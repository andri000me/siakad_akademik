<?php
session_start();
//	Author: Arisal Yanuarafi
//	17 September 2013
  
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
$aktif = GetaField('mhsw', "MhswID", $MhswID, 'StatusMhswID');
if ($aktif == 'A'){
	RandomStringScript();
	Scriptnya();
	$tahun =  GetSetVar('TahunAkd');
	$gos		= (empty($_REQUEST['gos']))? 'TampilkanForm' : $_REQUEST['gos'];
	$gos();
}
else die(errorMsg("Tidak Aktif","Anda tidak tercatat sebagai mahasiswa aktif pada Semester ini.<br><br>
									Opsi: Jika anda merasa ini adalah kesalahan, hubungi jurusan."));

function TampilkanForm(){
	global $MhswID;
	TampilkanJudul('Cetak Surat Keterangan Aktif Kuliah');
	$s = "SELECT TahunID from khs where MhswID='".$MhswID."' and StatusMhswID='A' AND TahunID not like 'Tra%' order by TahunID DESC limit 2";
	$r = _query($s); $thn='<option></option>';
	while ($w = _fetch_array($r)) {
		if ($_SESSION['TahunAkd']==$w['TahunID']) $thn .= "<option value='$w[TahunID]' selected>$w[TahunID]</option>";
		else $thn .= "<option value='$w[TahunID]'>$w[TahunID]</option>";
	}
	echo "<div>Tahun Akademik: <form method=post action=?><select name='TahunAkd' onchange=\"this.form.submit()\">$thn</select></form>
	<br><input type=button value='Untuk Ayah' onclick=\"javascript:tampilkan('0')\"> <input type=button value='Untuk Ibu' onclick=\"javascript:tampilkan('1')\"></div>
	<div id='formKu'></div>";
}
function SAV() {
	global $MhswID;
	//$w 	= GetFields('praktekkerja', "MhswID", $MhswID, "*");
	//if (empty($w['MhswID'])){
		$Nama = sqling($_POST['Nama']);
		$NIP = sqling($_POST['NIP']);
		$PangkatGol = sqling($_POST['PangkatGol']);
		$Instansi = sqling($_POST['Instansi']);
		$d = sqling($_POST['d']);
		if ($d==0) {
			$s = "update mhsw set NamaAyah='$Nama', NIPAyah='$NIP', PangkatGolAyah='$PangkatGol', InstansiAyah='$Instansi' where
					MhswID = '$MhswID'";
			$r = _query($s);
			echo "<script type='text/javascript'>window.location='$_SESSION[mnux].php?dd2=0&gos=CetakSurat'</script>";
		}
		if ($d==1) {
			$s = "update mhsw set NamaIbu='$Nama', NIPIbu='$NIP', PangkatGolIbu='$PangkatGol', InstansiIbu='$Instansi' where
					MhswID = '$MhswID'";
			$r = _query($s);
			echo "<script type='text/javascript'>window.location='mhsw/surat.keterangan.php?dd2=1&gos=CetakSurat'</script>";
		}
	//}
	//echo $s;
	
}

function CetakSurat(){	
	global $MhswID;
	$dd2 = $_GET['dd2'];
	$mhs = GetFields('mhsw', "MhswID", $MhswID, '*');
	$Identitas = GetFields('identitas', "Kode", KodeID,'*');
	$DataProdi = GetFields('prodi', "ProdiID", $mhs['ProdiID'],"*");
	$DataFakultas = GetFields('fakultas',"FakultasID",$DataProdi['FakultasID'], "*");
	$praktek = GetFields('praktekkerja', "NA='N' AND MhswID", $MhswID, "*, date_format(TanggalBuat,'%Y-%m-%d') as Tanggal");
	$Tahun = $_SESSION['TahunAkd'];
		echo "<html>
		<head>
		<title>Surat Pernyataan Masih Kuliah ".$mhs['Nama']." (NPM ".$mhs['MhswID'].")</title>
		<style>
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
			font-size: 16px;
			font-family: Georgia, Tahoma, Verdana, Arial;
			font-weight: bold;
		}
		.isisurat {
			font-size: 13px;
			font-family: Georgia, Tahoma, Verdana, Arial;
		}
		.catatan {
			font-size: 10px;
			font-family: 'Book Antiqua', Georgia, Tahoma, Verdana, Arial;
		}
		</style>
		<style media='print'>
		.onlyscreen {
			display: none;
		}
		</style>";
		echo "</head>";

		$waktu_aju = TanggalFormat(date('Y-m-d'));

		echo "<body>
		<center>
		<div style='width:650px; text-align:center; background: #ffffff;'>

		<table style='width:100%'>
		<tr>
		<td width='10%' class='alamat' style='text-align:left;' valign='top'><img src='../img/logo.jpg' width='100' height='98' border='0' alt='Cetak Bukti' title='Cetak Bukti'></td>
		<td width='90%' valign='top' class='alamat' style='text-align:left; padding-left:10px'>
		<div class='logo'>".strtoupper($Identitas['Yayasan'])."</div>
		<div class='kampus'>".strtoupper($Identitas['Nama'])."</div>
		<div class='fakultas'>FAKULTAS ".strtoupper($DataFakultas['Nama'])."</div>
		<div class='alamat'>".$DataFakultas['Alamat']."<br />
		Email: <font color='#0000ff'>".strtolower($DataFakultas['Email'])."</font> Website: <font color='#0000ff'>".strtolower($DataFakultas['Website'])."</font>
		</div></td>
		</tr>
		</table>

		<table cellspacing='0' cellpadding='0' style='width:100%;'>
		<tr>
		<td class='tabel' style='width:550px; height:2px'><img src='../img/transparant.png' height='1' border='0'></td>
		</tr>
		</table>

		<div style='height:5px'><img src='../img/transparant.png' height='5' border='0'></div>

		<table cellspacing='2' cellpadding='2' style='width:100%; border:1px solid #000000'>
		<tr>
		<td class='catatan'>Surat Keputusan Menteri Keuangan<br />
		dan Badan Administrasi Kepegawaian Negara<br />
		<table style='width:100%'>
		<tr>
		<td class='catatan' style='width:10%'>Nomor:</td><td class='catatan' style='width:90%'>0272/KU-02/1982</td>
		</tr>
		<tr>
		<td class='catatan'>Nomor:</td><td class='catatan'>505/KM.02/1982</td>
		</tr>
		<tr>
		<td class='catatan'>Tanggal:</td><td class='catatan'>24 Agustus 1982</td>
		</tr>
		</table></td>
		<td style='border-left:1px solid #000000'><div style='text-align:center' class='judulsurat'>SURAT PERNYATAAN MASIH KULIAH<br />
		<u>DI ".strtoupper($Identitas['Nama'])."</u></div>
		<div style='padding-left:60px'>Nomor:</div></td>
		</tr>
		</table>

		<div style='text-align:Justify; width:100%'>
		<br />Yang bertanda tangan dibawah ini, $DataFakultas[JabatanSuratAktif] Fakultas ".ucwords(strtolower($DataFakultas['Nama']))." ".ucwords(strtolower($Identitas['Nama'])).", menerangkan bahwa:<br /><br />

		<div style='padding-left:50px'>
		<table cellspacing='0' cellpadding='0' style='width:600px;'>
		<tr>
		<td width='200px'>Nama</td>
		<td width='25px'>:</td>
		<td width='375px'><b>".$mhs['Nama']."</b></td>
		</tr>
		<tr>
		<td>Nomor Pokok Mahasiswa</td>
		<td>:</td>
		<td>".$mhs['MhswID']."</td>
		</tr>
		<tr>
		<td>Tempat / Tgl Lahir</td>
		<td>:</td>
		<td>".ucfirst($mhs['TempatLahir'])." / ";

		if($mhs['TanggalLahir']=="0000-00-00"){
			echo "";
		}else{
			echo TanggalFormat($mhs['TanggalLahir']);
		}

		echo "</td>
		</tr>
		<tr>
		<td valign='top'>Alamat</td>
		<td valign='top'>:</td>
		<td style='text-align:Justify'>".$mhs['Alamat']."</td>
		</tr>
		<tr>
		<td valign='top'>Jurusan</td>
		<td valign='top'>:</td>
		<td>".$DataProdi['Nama']."</td>
		</tr>
		<tr>
		<td valign='top'>Program Studi</td>
		<td valign='top'>:</td>
		<td>".$DataProdi['Nama']."</td>
		</tr>
		<tr>
		<td valign='top'>Semester / Tahun Akademik</td>
		<td valign='top'>:</td>
		<td>Semester ".Semester($Tahun)." Tahun Akademik ".Tahun($Tahun)."</td>
		</tr>
		</table>
		</div>

		<br />
		<div style='padding-left:50px'>Anak dari:</div><br />

		<div style='padding-left:50px'>
		<table cellspacing='0' cellpadding='0' style='width:600px'>
		<tr>
		<td width='200px'>Nama</td>
		<td width='25px'>:</td>
		<td width='375px'>".str_replace("\'", "'",(($dd2==0) ? $mhs['NamaAyah'] : $mhs['NamaIbu']))."</td>
		</tr>
		<tr>
		<td>NIP/NRP/NIK/No.Pening</td>
		<td>:</td>
		<td>".(($dd2==0) ? $mhs['NIPAyah'] : $mhs['NIPIbu'])."</td>
		</tr>
		<tr>
		<td>Pangkat/Golongan</td>
		<td>:</td>
		<td>".(($dd2==0) ? $mhs['PangkatGolAyah'] : $mhs['PangkatGolIbu'])."</td>
		</tr>
		<tr>
		<td valign='top'>Instansi</td>
		<td valign='top'>:</td>
		<td style='text-align:Justify'>".(($dd2==0) ? $mhs['InstansiAyah'] : $mhs['InstansiIbu'])."</td>
		</tr>
		</table>
		</div>

		<br /><br />
		Adalah mahasiswa Fakultas ".ucwords(strtolower($DataFakultas['Nama']))." yang terdaftar pada Semester ".Semester($Tahun)." Tahun Akademik ".Tahun($Tahun).".<br /><br />

		Surat Pernyataan ini tidak berlaku sebagai bukti pembayaran uang kuliah.<br /><br />

		Demikianlah surat pernyataan ini dibuat untuk dipergunakan sebagaimana mestinya.

		<br /><br /><br /><br /><br />

		<div style='text-align:left; padding-left:410px'>".ucwords(strtolower($DataFakultas['Kota'])).", ".$waktu_aju."<br />
		".$DataFakultas['JabatanSuratAktif'].",<br />
		<br /><br /><br /><br />
		".$DataFakultas['PejabatSuratAktif']."
		</div>";

		echo "</table>
		<br />
		<script>
		window.print();
		</script>

		<div class='onlyscreen' style='text-align:center'>
		<form>
		<input type='button' value='Cetak Surat Kuliah' onClick='window.print()' style='font: 11px Tahoma,Verdana,Arial;' />
		</form>
		</div>
		</div>

		</center>
		</body>
		</html>";
}
function Scriptnya() {
	global $MhswID;
 	?><script type='text/javascript'>
			  function fnCetak(d) {
				var _rnd = randomString();
				lnk = "<?=$_SESSION['mnux']?>.php?gos=CetakSurat&dd2="+d+"&_rnd="+_rnd+"&ui=win";
				win2 = window.open(lnk, "", "width=700, height=550,left=200,top=0, scrollbars");
				if (win2.opener == null) childWindow.opener = self;
			  }
			  function createRequestObject()
			{
			var ro;
			var browser = navigator.appName;
				if(browser == "Microsoft Internet Explorer")
				{
					ro = new ActiveXObject("Microsoft.XMLHTTP");
				}
				else
				{
					ro = new XMLHttpRequest();
				}
			return ro;
			}

				var xmlhttp = createRequestObject();
				function tampilkan(pilih)
				{
					if (!pilih) return;
						xmlhttp.open('get', 'mhsw/ajx/surat.keterangan.ajx.php?d='+pilih+"&MhswID=<?=$MhswID?>", true);
						xmlhttp.onreadystatechange = function()
					{
					if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
						document.getElementById("formKu").innerHTML = xmlhttp.responseText;
						return false;
					}
					xmlhttp.send(null);
				}
		</script><?php
 }