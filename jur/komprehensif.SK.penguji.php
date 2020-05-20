<?php session_start();error_reporting(E_ALL);
	/* 	Author	: Arisal Yanuarafi
		Start	: 8 November 2013 9:19 AM
	*/
	
	include_once "../dwo.lib.php";
  	include_once "../db.mysql.php";
  	include_once "../connectdb.php";
  	include_once "../parameter.php";
  	include_once "../cekparam.php";
  		$KompreID = GetSetVar('KompreID');
		$kompre = GetFields("kompre", "KompreID", $KompreID, "*");
		$MhswID = $kompre['MhswID'];
		//echo $MhswID;
		if($ta = GetFields("ta", "NA='N' AND MhswID", $kompre['MhswID'], "*"))
		{
			$rowpemb1 	= GetaField('dosen', "NA='N' AND Login", $ta['pembimbing'], "concat(Gelar1,' ',Nama, ', ', Gelar)");
			//$rowpemb2 	= GetaField('ta t left outer join dosen d on d.Login=t.DosenID', "t.Tipe=0 and t.TAID", $ta['KompreID'], "concat(d.Gelar1,' ',d.Nama, ', ', d.Gelar)");
			

			$Data		= GetFields("mhsw m left outer join prodi p on p.ProdiID=m.ProdiID
											left outer join fakultas f on f.FakultasID=p.FakultasID",
									"m.MhswID", $MhswID, "f.Nama as Fakultas, f.JabatanSKPembimbing, f.PejabatSKPembimbing, p.Nama as Jurusan, f.Email as _Email, f.Website as _Website, m.*, f.FakultasID as FID");
			$NamaPT		= GetaField('identitas',"Kode",KodeID,'Nama');
			$Tahun		= substr($ta['TahunID'],0,4)."/".(substr($ta['TahunID'],0,4)+1);
			$Semester	= (substr($ta['TahunID'],-1)=='1')? "Ganjil" : "Genap";
			echo "<html><head><title>SK Penunjukan Penguji ".($Data['FID']=='08'? "Tesis":"TA/Skripsi")." ".$Data['Nama']." - NPM ".$Data['MhswID']."</title>";
			echo "<style>
			body {
				margin-left: 0 px;
				font-family: Trebuchet MS, Georgia, Tahoma, Arial;
				font-size: 15px;
			}
			td {
				font-size: 15px;
				font-family: Trebuchet MS, Georgia, Tahoma, Verdana, Arial;
			}
			.logo {
				font-size: 15px;
				font-family: Trebuchet MS, Tahoma, Verdana, Arial;
				font-weight: bold;
				color: #0000ff;
			}
			.kampus {
				font-size: 16px;
				font-family: Trebuchet MS, Georgia, Tahoma, Verdana, Arial;
				font-weight: bold;
				color: #0000ff;
			}
			.fakultas {
				font-size: 18px;
				font-family: Trebuchet MS, Verdana, Arial;
				font-weight: bold;
				color: #0000ff;
			}
			.alamat {
				font-size: 11px;
				font-family: Trebuchet MS, Arial, Verdana;
			}
			.tabel {
				text-align:center;
				border-top:2px solid #000000;
				border-bottom:1px dotted #000000;
			}
			.judulsurat {
				font-size: 18px;
				font-family: Trebuchet MS, Georgia, Tahoma, Verdana, Arial;
				font-weight: bold;
			}
			.judulsurat2 {
				font-size: 15px;
				font-family: Trebuchet MS, Georgia, Tahoma, Verdana, Arial;
				font-weight: bold;
			}
			.isisurat {
				font-size: 14px;
				font-family: Trebuchet MS, Georgia, Tahoma, Verdana, Arial;
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
			<div class='fakultas'>".($Data['FID']=='08'? "PROGRAM":"FAKULTAS")." ".strtoupper($Data['Fakultas'])."</div>
			<div class='alamat'>Email: <font color='#0000ff'>".strtolower($Data['_Email'])."</font> Website: <font color='#0000ff'>".strtolower($Data['_Website'])."</font></div>
			</td>
			</tr>
			</table>

			<table cellspacing='0' cellpadding='0' style='width:100%;'>
			<tr>
			<td class='tabel' style='width:650px; height:2px'><img src='../img/transparant.png' height='1' border='0'></td>
			</tr>
			</table>
			<br />

			<div class='judulsurat' style='text-align:center'>KEPUTUSAN ".($Data['FID']=='08'? "DIREKTUR PROGRAM":"DEKAN FAKULTAS")." ".strtoupper($Data['Fakultas'])."<br />
			<u>".strtoupper($NamaPT)."</u></div>
			<div style='text-align:center'>Nomor: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
			<br /><br />

			<div class='judulsurat2' style='text-align:center'>Tentang<br /><br />
			PENUNJUKAN PEMBIMBING TIM PENGUJI UJIAN ".($Data['FID']=='08'? "TESIS":"SKRIPSI/TA")."<br />
			".($Data['FID']=='08'? "PROGRAM":"FAKULTAS")." ".strtoupper($Data['Fakultas'])." ".strtoupper($NamaPT)."<br />
			SEMESTER ".strtoupper($Semester)." TAHUN AKADEMIK ".$Tahun."</div>
			<br /><br />

			<div style='text-align:center'>".($Data['FID']=='08'? "Direktur Program":"Dekan Fakultas")." ".ucwords(strtolower($Data['Fakultas']))." ".ucwords(strtolower($NamaPT)).",</div>
			<br /><br />

			<table cellspacing='0' cellpadding='2' style='width:100%;'>
			<tr>
			<td width='15%' valign='top'><b>Menimbang</b></td>
			<td width='5%' valign='top'>:</td>
			<td width='3%' valign='top'>a.</td>
			<td width='77%' valign='top' style='text-align:Justify'>bahwa untuk pelaksanaan ujian ".($Data['FID']=='08'? "Tesis":"Skripsi/TA")." Mahasiswa ".($Data['FID']=='08'? "Program":"Fakultas")." ".ucwords(strtolower($Data['Fakultas']))." ".ucwords(strtolower($NamaPT))." perlu ditunjuk tim penguji;</td>
			</tr>
			<tr>
			<td valign='top' style='padding-top:5px'>&nbsp;</td>
			<td valign='top' style='padding-top:5px'>&nbsp;</td>
			<td valign='top' style='padding-top:5px'>b.</td>
			<td valign='top' style='padding-top:5px; text-align:Justify'>bahwa untuk keperluan tersebut perlu ditetapkan keputusan ".($Data['FID']=='08'? "Direktur Program":"Dekan Fakultas")." ".ucwords(strtolower($Data['Fakultas']))." ".ucwords(strtolower($NamaPT)).";</td>
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
			<td valign='top' style='text-align:Justify'>Menunjuk dan menetapkan nama-nama dosen tim penguji ujian ".($Data['FID']=='08'? "Tesis":"Skripsi/TA")." Program Studi ".$Data['Jurusan']." ".($Data['FID']=='08'? "Program":"Fakultas")." ".ucwords(strtolower($Data['Fakultas']))." ".ucwords(strtolower($NamaPT))." bagi mahasiswa:<br /></td>
			</tr>
			<tr>
			<td valign='top'>&nbsp;</td>
			<td valign='top'>&nbsp;</td>
			<td valign='top' style='text-align:Justify'>
			<table style='width:95%'>
			<tr>
			<td width='20%'>Nama</td>
			<td width='3%'>:</td>
			<td width='77%'><b>".ucwords(strtolower($Data['Nama']))."</b></td>
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
			<td width='20%'>".($Data['FID']=='08'? "Program":"Fakultas")." </td>
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
			<td valign='top'>&nbsp;</td>
			<td valign='top'>&nbsp;</td>
			<td valign='top' style='text-align:Justify'>Dengan susunan tim penguji sebagai berikut :</td>
			</tr>
			
			<tr>
			<td valign='top'>&nbsp;</td>
			<td valign='top'>&nbsp;</td>
			<td valign='top' style='text-align:Justify'>
			<table style='width:95%'>
			";
			$s1 = "SELECT m.Nama as Jabatan, concat(d.Gelar1, ' ', d.Nama,', ', d.Gelar) as Penguji from komprematauji m 
					left outer join kompredosen k on k.KompreMataUjiID=m.KompreMataUjiID
					left outer join kompre r on r.KompreID=k.KompreID
					left outer join dosen d on d.Login=k.DosenID
					where m.ProdiID='$Data[ProdiID]'
					AND r.KompreID='$KompreID'
					group by m.KodeKompre order by m.KodeKompre";
			$r1 = _query($s1);
			while ($w1 = _fetch_array($r1)){
			echo "
			<tr>
			<td width='25%'>$w1[Jabatan]</td>
			<td width='3%'>:</td>
			<td width='70%'><b>".$w1['Penguji']."</b></td>
			</tr>";
			}
			echo "
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
			<td valign='top' style='text-align:Justify'>Tim Penguji mempunyai tugas dan wewenang sebagai berikut :
			<ol>
			<li>Melaksanakan ujian tesis sesuai disiplin ilmu yang terkait.
			<li>Memberikan saran perbaikan terhadap proposal yang diuji.
			<li>Memutuskan layak tidak layak proposal peserta untuk dijadikan tesis/penelitian.
			<li>Membuat Berita Acara tentang ujian tesis mahasiswa yang bersangkutan.
			</ol>
			</td>
			</tr>

			<tr>
			<td valign='top'>&nbsp;</td>
			<td valign='top'>&nbsp;</td>
			<td valign='top' style='text-align:Justify'>&nbsp;</td>
			</tr>

			<tr>
			<td valign='top'><b>Ketiga</b></td>
			<td valign='top'>:</td>
			<td valign='top' style='text-align:Justify'>Segala biaya yang ditimbulkan karena adanya Keputusan ini dibebankan kepada anggaran ".($Data['FID']=='08'? "Program":"Fakultas")." ".ucwords(strtolower($Data['Fakultas']))." ".ucwords(strtolower($NamaPT)).";</td>
			</tr>

			<tr>
			<td valign='top'>&nbsp;</td>
			<td valign='top'>&nbsp;</td>
			<td valign='top' style='text-align:Justify'>&nbsp;</td>
			</tr>

			<tr>
			<td valign='top'><b>Keempat</b></td>
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
			<td style='padding-top:5px' valign='top'>&nbsp;</td>
			<td style='padding-top:5px; valign='top'>".$Data['JabatanSKPembimbing'].",</td>
			<td style='padding-top:5px; valign='top'>&nbsp;</td>
			<td style='padding-top:5px; valign='top'>&nbsp;</td>
			</tr>
			<tr>
			<td style='padding-top:95px' valign='top'>&nbsp;</td>
			<td style='padding-top:95px; valign='top' colspan='3'><b>".$Data['PejabatSKPembimbing']."</b></td>
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
			<input type='button' value='Klik Untuk Mencetak SK' onClick='window.print()' style='font: 11px Tahoma,Verdana,Arial;' />
			</form>
			</div>

			</div>
			</center>
			</body>
			</html>";
		}
	