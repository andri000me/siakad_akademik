<?php
session_start();

  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
  $PMBID = GetSetVar('PMBID');
  $rowcmhs = GetFields('pmb', "PMBID", $PMBID, "*, left(TanggalEdit,10) as TanggalEdit");
  $PrID = GetaField('aplikan', "AplikanID", $rowcmhs['AplikanID'],"PresenterID");
  $oprs = GetaField('karyawan', "Login", $_SESSION['_Login'],"Nama");
  $MhswID =  $rowcmhs['MhswID'];
  $rowmhs = GetFields('mhsw', "MhswID", $MhswID, "*");
echo "<html>
	<head>
	<title>Kartu Mahasiswa Sementara ".$rowmhs['Nama']."</title>
	<style type='text/css'>
	body { 
		margin-left: 0 px;
		font-family:  Arial, Tahoma, Verdana;
		font-size: 13px;
		padding: 0px;
	}
	td {
		font-family: Arial, Tahoma, Verdana;
	}
	.logo {
		font-size: 13px;
		font-family: Arial, Tahoma, Verdana;
		font-weight: bold;
		color: #000000;
	}
	.kampus {
		font-size: 18px;
		font-family: Arial, Georgia, Tahoma, Verdana, Arial;
		font-weight: bold;
		color: #0000ff;
	}
	.alamat {
		font-size: 10px;
		font-family: Tahoma, Arial, Verdana;
	}
	.tabel {
		text-align:center;
		border-top:1px solid #000000;
	}
	.tabelbawah {
		border-top:1px solid #000000;
	}
	.catatan {
		padding-top:10px;
		font-size: 10px;
		font-family: Arial, Tahoma, Verdana;
		padding: 0px;
	}
	.tabel_bawah {
		font-family: Tahoma, Arial, Verdana;
		border:1px solid #000000;
	}
	.ttd {
		font-size: 14px;
		font-family: Arial, Tahoma, Verdana;
		padding: 4px;
	}
	.ttd2 {
		font-size: 13px;
		font-family: Arial, Tahoma, Verdana;
		padding-left: 3px;
	}
	.tabel_dasar {
		font-size: 13px;
		font-family: Arial, Tahoma, Verdana;
		padding: 2px;
		border-top:1px solid #000;
		border-left:1px solid #000;
		text-align:center;
	}
	.tabel_dasar2 {
		font-size: 13px;
		font-family: Arial, Tahoma, Verdana;
		padding: 2px;
		border-top:1px solid #000;
		border-left:1px solid #000;
		border-bottom:1px solid #000;
		text-align:center;
	}
	.tabel_kanan {
		font-size: 13px;
		font-family: Arial, Tahoma, Verdana;
		padding: 2px;
		border-top:1px solid #000;
		border-left:1px solid #000;
		border-right:1px solid #000;
		text-align:center;
	}
	.tabel_kanan2 {
		font-size: 13px;
		font-family: Arial, Tahoma, Verdana;
		padding: 2px;
		border:1px solid #000;
		text-align:center;
	}
	.h4 {
		font-family: Arial, Tahoma, Verdana;
		color: #000;
		font-size: 16px;
		font-weight:bold;
		padding-top:5px;
		padding-bottom:20px;
	}
	.fborder {
		border-collapse: collapse;
		border: 1px solid #ccc;
		margin-bottom: 10px;
	}
	</style>
	<style media='print'>
	.onlyscreen {
		display: none;
	}
	</style>
	</head>";

	$rowjur = GetFields('prodi', 'ProdiID', $rowmhs['ProdiID'],'*');
	$rowfak = GetFields('fakultas', 'FakultasID', $rowjur['FakultasID'],'*');
	$rowuniv = GetFields('identitas', 'Kode', KodeID,'*');

	echo "<body>
	<center>
	<div style='width:700px; text-align:center; background: #ffffff;'>

	<table cellspacing='4' cellpadding='0' style='width:95%;'>
	<tr>
	<td class='tabel_bawah' width='50%' valign='top'>

	<table style='border-bottom:1px solid #000000'>
	<tr>
	<td valign='top' width='10%'><img src='../img/logo.jpg' width='60' height='59' border='0' alt='' title=''></td>
	<td valign='top' width='90%' style='text-align:center'><div class='logo'>".strtoupper($rowuniv['Yayasan'])."</div>
	<div class='kampus'>".strtoupper($rowuniv['Nama'])."</div>
	<div class='alamat'>".ucwords(strtolower($rowuniv['Alamat']))." ".ucwords(strtolower($rowuniv['Kota']))."<br />Website: <font color='#0000ff'>".strtolower($rowuniv['Website'])."</font>
	</div>
	</td>
	</tr>
	</table>

	<div class='h4' style='text-align:center'>KARTU MAHASISWA SEMENTARA</div>

	<table width='100%'>
	<tr>
	<td valign='top' width='10%'><img src='../foto/".$rowcmhs['cmhs_foto']."' height='90' border='0' alt='' title=''></td>
	<td valign='top' width='90%' style='text-align:center'>

	<table>
	<tr>
	<td class='ttd' valign='top' width='20%'>Nama</td>
	<td class='ttd' valign='top' width='80%'>: <b>".$rowmhs['Nama']."</b>
	</td>
	</tr>
	<tr>
	<td class='ttd' valign='top' width='20%'>NPM</td>
	<td class='ttd' valign='top' width='80%'>: <span style='font-size:15px'><b>".$rowmhs['MhswID']."</span></b>
	</td>
	</tr>
	</table>

	</td>
	</tr>
	</table>
	
	</td>
	<td class='tabel_bawah' width='50%' valign='top'>

	<table width='100%'>
	<tr>
	<td class='ttd2' valign='top' width='29%'>Nomor Tes</td>
	<td class='ttd2' valign='top' width='1%'>:</td>
	<td class='ttd2' valign='top' width='70%'><b>".$rowcmhs['AplikanID']."</b>
	</td>
	</tr>
	<tr>
	<td class='ttd2' valign='top' width='29%'>Fakultas</td>
	<td class='ttd2' valign='top' width='1%'>:</td>
	<td class='ttd2' valign='top' width='70%'><b>Fakultas ".ucwords(strtolower($rowfak['Nama']))."</b>
	</td>
	</tr>";

		echo "<tr>
		<td class='ttd2' valign='top' width='29%'>Program Studi</td>
		<td class='ttd2' valign='top' width='1%'>:</td>
		<td class='ttd2' valign='top' width='70%'><b>".ucwords(strtolower($rowjur['Nama']))."</b>
		</td>
		</tr>";

	echo "</table>

	<div class='ttd2' style='padding-left:100px; padding-top:30px; font-size:11px'>Padang, ".TanggalFormat(date('Y-m-d'))."<br />
	Rektor,<br /><br />
	DTO<br /><br />
	".$rowuniv['Pejabat']."
	</div>

	</td>
	</tr>
	</table>

	<p>&nbsp;</p>";
	$TanggalMasuk = ($rowmhs['TanggalMasuk']=='0000-00-00')? $rowcmhs['TanggalEdit'] : $rowmhs['TanggalMasuk'];
	echo "<table cellspacing='0' cellpadding='0' width='95%'>
	<tr>
	<td class='tabel_dasar'>Foto</td>
	<td class='tabel_dasar'>Nomor Tes</td>
	<td class='tabel_dasar'>NPM</td>
	<td class='tabel_dasar'>Nama</td>
	<td class='tabel_dasar'>Program Studi</td>
	<td class='tabel_dasar'>Fakultas</td>
	<td class='tabel_dasar'>Tgl Daftar</td>
	<td class='tabel_kanan'>Operator</td>
	</tr>
	<tr>
	<td class='tabel_dasar2'><img src='../foto/".$rowcmhs['Foto']."' height='90' border='0' alt='' title=''></td>
	<td class='tabel_dasar2'>".$rowcmhs['PMBID']."</td>
	<td class='tabel_dasar2'>".$rowmhs['MhswID']."</td>
	<td class='tabel_dasar2'>".ucwords(strtolower($rowmhs['Nama']))."</td>
	<td class='tabel_dasar2'>".ucwords(strtolower($rowjur['Nama']))."</td>
	<td class='tabel_dasar2'>".ucwords(strtolower($rowfak['Nama']))."</td>
	<td class='tabel_dasar2'>".$TanggalMasuk."</td>
	<td class='tabel_kanan2'>".$oprs."</td>
	</tr>
	</table>

	<div class='onlyscreen' style='text-align:center'>
	<form>
	<br />
	<input type='button' value='Cetak KTM Sementara' onClick='window.print()' style='font: 11px Tahoma,Verdana,Arial;' />
	</form>
	</div>

	</div>
	</div>
	</center>
	</body>
	</html>";
?>