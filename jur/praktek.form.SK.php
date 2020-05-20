<?php session_start();
	/* 	Author	: Arisal Yanuarafi
		Start	: 15 Oktober 2013 3:19 AM
	*/
	
	include_once "../dwo.lib.php";
  	include_once "../db.mysql.php";
  	include_once "../connectdb.php";
  	include_once "../parameter.php";
  	include_once "../cekparam.php";
	$MhswID = GetSetVar('MhswID');
		if($praktek = GetFields("praktekkerja", "NA='N' AND MhswID", $MhswID, "*"))
		{
			$rowpemb1 	= GetaField('dosen', "Login", $praktek['Pembimbing1'], "concat(Gelar1,' ',Nama, ', ', Gelar)");
			$rowpemb2 	= GetaField('dosen', "Login", $praktek['Pembimbing2'], "concat(Gelar1,' ',Nama, ', ', Gelar)");

			$Data		= GetFields("mhsw m left outer join prodi p on p.ProdiID=m.ProdiID
											left outer join fakultas f on f.FakultasID=p.FakultasID",
									"m.MhswID", $MhswID, "f.Nama as Fakultas, f.JabatanSKPembimbing, f.PejabatSKPembimbing, p.Nama as Jurusan, f.Email as _Email, f.Website as _Website, m.*");
			$NamaPT		= GetaField('identitas',"Kode",KodeID,'Nama');
			$Tahun		= substr($praktek['TahunID'],0,4)."/".(substr($praktek['TahunID'],0,4)+1);
			$Semester	= (substr($praktek['TahunID'],-1)=='1')? "Ganjil" : "Genap";
			echo "<html><head><title>SK Penunjukan Pembimbing KP/PKL/PL ".$Data['Nama']." - NPM ".$Data['MhswID']."</title>";
			echo "<style>
			body {
				margin-left: 0 px;
				font-family: Arial, Georgia, Tahoma, Arial;
				font-size: 15px;
			}
			td {
				font-size: 15px;
				font-family: Arial, Georgia, Tahoma, Verdana, Arial;
			}
			.logo {
				font-size: 15px;
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
				font-size: 11px;
				font-family: Tahoma, Arial, Verdana;
			}
			.tabel {
				text-align:center;
				border-top:2px solid #000000;
				border-bottom:1px dotted #000000;
			}
			.judulsurat {
				font-size: 18px;
				font-family: Arial, Georgia, Tahoma, Verdana, Arial;
				font-weight: bold;
			}
			.judulsurat2 {
				font-size: 15px;
				font-family: Arial, Georgia, Tahoma, Verdana, Arial;
				font-weight: bold;
			}
			.isisurat {
				font-size: 14px;
				font-family: Arial, Georgia, Tahoma, Verdana, Arial;
			}
			.footer {
				color: #ffffff;
			}
			</style>
			<style media='print'>
			.onlyscreen {
				display: none;
			}
			</style>
			</head>";

			$waktu_cetak = TanggalFormat(date('Y-m-d'));
			
			echo "<body>
			<center>
			<div style='width:650px; text-align:center; background: #ffffff;'>

			<table style='width:100%'>
			<tr>
			<td width='10%' class='alamat' style='text-align:left;' valign='top'><img src='../img/ubh_logo.png' width='100' height='98' border='0' alt='Cetak Bukti' title='Cetak Bukti'></td>
			<td width='90%' valign='top' class='alamat' style='text-align:left; padding-left:10px'>
			<div class='logo'>".strtoupper(GetaField('identitas', 'Kode', KodeID, 'Yayasan'))."</div>
			<div class='kampus'>".strtoupper(GetaField('identitas', 'Kode', KodeID, 'Nama'))."</div>
			<div class='fakultas'>FAKULTAS ".strtoupper($Data['Fakultas'])."</div>
			<div class='alamat'>Email: <font color='#0000ff'>".strtolower($Data['_Email'])."</font> Website: <font color='#0000ff'>".strtolower($rowfak4['_Website'])."</font></div>
			</td>
			</tr>
			</table>

			<table cellspacing='0' cellpadding='0' style='width:100%;'>
			<tr>
			<td class='tabel' style='width:650px; height:2px'><img src='../img/transparant.png' height='1' border='0'></td>
			</tr>
			</table>
			<br />

			<div class='judulsurat' style='text-align:center'>KEPUTUSAN DEKAN FAKULTAS ".strtoupper($Data['Fakultas'])."<br />
			<u>".strtoupper($NamaPT)."</u></div>
			<div style='text-align:center'>Nomor: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
			<br /><br />

			<div class='judulsurat2' style='text-align:center'>Tentang<br /><br />
			PENUNJUKAN PEMBIMBING KP/PKL/PL MAHASISWA<br />
			FAKULTAS ".strtoupper($Data['Fakultas'])." ".strtoupper($NamaPT)."<br />
			SEMESTER ".strtoupper($Semester)." TAHUN AKADEMIK ".$Tahun."</div>
			<br /><br />

			<div style='text-align:center'>Dekan Fakultas ".ucwords(strtolower($Data['Fakultas']))." ".ucwords(strtolower($NamaPT)).",</div>
			<br /><br />

			<table cellspacing='0' cellpadding='2' style='width:100%;'>
			<tr>
			<td width='15%' valign='top'><b>Menimbang</b></td>
			<td width='5%' valign='top'>:</td>
			<td width='3%' valign='top'>a.</td>
			<td width='77%' valign='top' style='text-align:Justify'>bahwa untuk kelancaran pelaksanaan KP/PKL/PL bagi mahasiswa;</td>
			</tr>
			<tr>
			<td valign='top' style='padding-top:5px'>&nbsp;</td>
			<td valign='top' style='padding-top:5px'>&nbsp;</td>
			<td valign='top' style='padding-top:5px'>b.</td>
			<td valign='top' style='padding-top:5px; text-align:Justify'>bahwa untuk itu perlu menunjuk staf pengajar Fakultas ".ucwords(strtolower($Data['Fakultas']))." ".ucwords(strtolower($NamaPT))." sebagai pembimbing;</td>
			</tr>
			<tr>
			<td valign='top' style='padding-top:5px'>&nbsp;</td>
			<td valign='top' style='padding-top:5px'>&nbsp;</td>
			<td valign='top' style='padding-top:5px'>c.</td>
			<td valign='top' style='padding-top:5px; text-align:Justify'>bahwa untuk itu perlu dikeluarkan Keputusan Dekan Fakultas ".ucwords(strtolower($Data['Fakultas']))." ".ucwords(strtolower($NamaPT)).".</td>
			</tr>
			</table>
			<br /><br />

			<table cellspacing='0' cellpadding='2' style='width:100%;'>
			<tr>
			<td width='15%' valign='top'><b>Mengingat</b></td>
			<td width='5%' valign='top'>:</td>
			<td width='3%' valign='top'>1.</td>
			<td width='77%' valign='top' style='text-align:Justify'>Undang-Undang Nomor 12 Tahun 2012 tentang Sistem Pendidikan Nasional;</td>
			</tr>
			<tr>
			<td valign='top' style='padding-top:5px'>&nbsp;</td>
			<td valign='top' style='padding-top:5px'>&nbsp;</td>
			<td valign='top' style='padding-top:5px'>2.</td>
			<td valign='top' style='padding-top:5px; text-align:Justify'>Peraturan Pemerintah RI Nomor 4 Tahun 2014 tentang Penyelenggaraan Pendidikan Tinggi dan Pengelolaan Pendidikan Tinggi;</td>
			</tr>
			<tr>
			<td valign='top' style='padding-top:5px'>&nbsp;</td>
			<td valign='top' style='padding-top:5px'>&nbsp;</td>
			<td valign='top' style='padding-top:5px'>3.</td>
			<td valign='top' style='padding-top:5px; text-align:Justify'>Akta ".ucwords(strtolower(GetaField('identitas',"Kode", KodeID,"Yayasan")))." tanggal 20 Nopember 1996 jo Keputusan Dirjen Dikti Depdikbud Nomor 45/DIKTI/Kep/1997 tanggal 14 Maret 1997 tentang Perubahan Nama Badan Penyelenggara ".ucwords(strtolower($NamaPT)).";</td>
			</tr>
			<tr>
			<td valign='top' style='padding-top:5px'>&nbsp;</td>
			<td valign='top' style='padding-top:5px'>&nbsp;</td>
			<td valign='top' style='padding-top:5px'>4.</td>
			<td valign='top' style='padding-top:5px; text-align:Justify'>Surat Keputusan Rektor ".ucwords(strtolower($NamaPT))." Nomor 1 tanggal 28 Desember 2015 tentang Penyelenggaraan Akademik Universitas Bung Hatta;</td>
			</tr>
			<tr>
			<td valign='top' style='padding-top:5px'>&nbsp;</td>
			<td valign='top' style='padding-top:5px'>&nbsp;</td>
			<td valign='top' style='padding-top:5px'>5.</td>
			<td valign='top' style='padding-top:5px; text-align:Justify'>Peraturan Badan Pengurus Yayasan Pendidikan Bung Hatta Nomor: 003/SK/YPBH/XI-2015 tentang Statuta Universitas Bung Hatta;</td>
			</tr>
			</table>
			<br /><br />

			<div class='isisurat' style='text-align:center'><b>Memutuskan</b></div>
			<br />

			<table cellspacing='0' cellpadding='2' style='width:100%;'>
			<tr>
			<td width='15%' valign='top'><b>Menetapkan</b></td>
			<td width='5%' valign='top'>:</td>
			<td width='80%' valign='top' style='text-align:Justify'>&nbsp;</td>
			</tr>
			<tr>
			<td valign='top'><b>Pertama</b></td>
			<td valign='top'>:</td>
			<td valign='top' style='text-align:Justify'>Menunjuk staf pengajar yang tersebut dibawah ini:<br /></td>
			</tr>

			<tr>
			<td valign='top'>&nbsp;</td>
			<td valign='top'>&nbsp;</td>
			<td valign='top' style='text-align:Justify'>1. &nbsp;&nbsp;<b>".$rowpemb1."</b> sebagai Pembimbing I</td>
			</tr>";
			if ($rowpemb1 != $rowpemb2) {
				echo "
				<tr>
				<td valign='top'>&nbsp;</td>
				<td valign='top'>&nbsp;</td>
				<td valign='top' style='text-align:Justify'>2. &nbsp;&nbsp;<b>".$rowpemb2."</b> sebagai Pembimbing II</td>
				</tr>";
			}
			echo "

			<tr>
			<td valign='top'>&nbsp;</td>
			<td valign='top'>&nbsp;</td>
			<td valign='top' style='text-align:Justify'>&nbsp;</td>
			</tr>

			<tr>
			<td valign='top'>&nbsp;</td>
			<td valign='top'>&nbsp;</td>
			<td valign='top' style='text-align:Justify'>Sebagai pembimbing mahasiswa yang tersebut dibawah ini:</td>
			</tr>

			<tr>
			<td valign='top'>&nbsp;</td>
			<td valign='top'>&nbsp;</td>
			<td valign='top' style='text-align:Justify'>
			<table style='width:95%'>
			<tr>
			<td width='20%'>Nama</td>
			<td width='3%'>:</td>
			<td width='77%'><b>".$Data['Nama']."</b></td>
			</tr>
			<tr>
			<td width='20%'>NPM</td>
			<td width='3%'>:</td>
			<td width='77%'>".$Data['MhswID']."</td>
			</tr>
			<tr>
			<td width='20%'>Jurusan</td>
			<td width='3%'>:</td>
			<td width='77%'>".ucwords(strtolower($Data['Jurusan']))."</td>
			</tr>
			<tr>
			<td width='20%'>Fakultas</td>
			<td width='3%'>:</td>
			<td width='77%'>".ucwords(strtolower($Data['Fakultas']))."</td>
			</tr>
			</table>
			</td>
			</tr>

			<tr>
			<td valign='top'>&nbsp;</td>
			<td valign='top'>&nbsp;</td>
			<td valign='top' style='text-align:Justify'>&nbsp;</td>
			</tr>

			<tr>
			<td valign='top'><b>Kedua</b></td>
			<td valign='top'>:</td>
			<td valign='top' style='text-align:Justify'>Kepada staf pengajar yang ditunjuk agar dapat melaksanakan tugasnya sebagai pembimbing dengan sebaik-baiknya;</td>
			</tr>

			<tr>
			<td valign='top'>&nbsp;</td>
			<td valign='top'>&nbsp;</td>
			<td valign='top' style='text-align:Justify'>&nbsp;</td>
			</tr>

			<tr>
			<td valign='top'><b>Ketiga</b></td>
			<td valign='top'>:</td>
			<td valign='top' style='text-align:Justify'>Surat Keputusan ini mulai berlaku sejak tanggal ditetapkan dengan ketentuan apabila dikemudian hari terdapat kekeliruan dalam penetapannya akan diadakan perubahan dan perbaikan kembali sebagaimana mestinya.</td>
			</tr>
			</table>
			<br /><br /><br />";

			echo "<table cellspacing='0' cellpadding='0' style='width:100%;'>
			<tr>
			<td width='55%' valign='top'>&nbsp;</td>
			<td width='20%' valign='top'>Ditetapkan di</td>
			<td width='3%' valign='top'>:</td>
			<td width='22%' valign='top'>Padang</td>
			</tr>
			<tr>
			<td style='padding-top:5px' valign='top'>&nbsp;</td>
			<td style='padding-top:5px; border-bottom:1px solid #000' valign='top'>Pada tanggal</td>
			<td style='padding-top:5px; border-bottom:1px solid #000' valign='top'>:</td>
			<td style='padding-top:5px; border-bottom:1px solid #000' valign='top'>".$waktu_cetak."</td>
			</tr>
			<tr>
			<td style='padding-top:25px' valign='top'>&nbsp;</td>
			<td style='padding-top:25px; valign='top'>".$Data['JabatanSKPembimbing'].",</td>
			<td style='padding-top:25px; valign='top'>&nbsp;</td>
			<td style='padding-top:25px; valign='top'>&nbsp;</td>
			</tr>
			<tr>
			<td style='padding-top:55px' valign='top'>&nbsp;</td>
			<td style='padding-top:55px; valign='top' colspan='3'><b>".$Data['PejabatSKPembimbing']."</b></td>
			</tr>
			</table>
			<br /><br /><br />

			<div style='text-align:Justify'>Tembusan disampaikan kepada:
			<ol><li>Bapak Rektor ".ucwords(strtolower($NamaPT))."
			<li>Saudara Ketua Jurusan ".ucwords(strtolower($Data['Jurusan']))."
			<li>Yang bersangkutan.
			<li>Arsip.
			</div>

			<script>
			window.print();
			</script>

			<div class='onlyscreen' style='text-align:center'>
			<form>
			<input type='button' value='Klik Untuk Mencetak SK Pembimbing' onClick='window.print()' style='font: 11px Tahoma,Verdana,Arial;' />
			</form>
			</div>

			</div>
			</center>
			</body>
			</html>";
		}
	