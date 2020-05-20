<?php		session_start();		
    include_once "../dwo.lib.php";
  	include_once "../db.mysql.php";
  	include_once "../connectdb.php";
  	include_once "../parameter.php";
  	include_once "../cekparam.php";
	$MhswID = ($_SESSION['_LevelID']!='120' ? GetSetVar('MhswID'): $_SESSION['_Login']);
	
		if($ta = GetFields("ta", "MhswID", $MhswID, "*"))
		{
			$rowpemb1 	= GetaField('ta t left outer join dosen d on d.Login=t.Pembimbing', "t.NA='N' and t.MhswID", $MhswID, "concat(d.Gelar1, ' ', d.Nama,', ', d.Gelar)");
			$rowpemb2 	= GetaField('tadosen t left outer join dosen d on d.Login=t.DosenID', "t.Tipe=0 and t.TAID", $ta['TAID'], "concat(d.Gelar1,' ',d.Nama, ', ', d.Gelar)");
			
			$Data		= GetFields("mhsw m left outer join prodi p on p.ProdiID=m.ProdiID
											left outer join fakultas f on f.FakultasID=p.FakultasID
											left outer join jenjang j on j.JenjangID=p.JenjangID",
									"m.MhswID", $MhswID, "f.Nama as Fakultas, f.JabatanSKPembimbing, f.PejabatSKPembimbing, p.Nama as Jurusan, p.Jurusan as Jurusan2, f.Email as _Email, f.Website as _Website, m.*, f.FakultasID as FID, j.Keterangan as Gelar");
			$Identitas		= GetFields('identitas',"Kode",KodeID,'*');

					echo "<html>
					<head>
					<title>Tanda Persetujuan Ujian ".$Data['Nama']." (NPM ".$Data['MhswID'].")</title>
					<style>
					body {
						margin-left: 0 px;
						font-family: Trebuchet MS, Tahoma, Verdana, Arial;
						font-size: 14px;
					}
					td {
						font-size: 14px;
						font-family: Trebuchet MS, Tahoma, Verdana, Arial;
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
						font-size: 12px;
						font-family: Trebuchet MS, Arial, Verdana;
					}
					.tabel {
						text-align:center;
						border-top:2px solid #000000;
						border-bottom:1px dotted #000000;
					}
					.judulsurat {
						font-size: 16px;
						font-family: Trebuchet MS, Tahoma, Verdana, Arial;
						font-weight: bold;
					}
					.isisurat {
						font-size: 14px;
						font-family: Trebuchet MS, Tahoma, Verdana, Arial;
					}
					.catatan {
						font-size: 12px;
						font-family: 'Book Antiqua', Georgia, Tahoma, Verdana, Arial;
					}
					</style>
					<style media='print'>
					.onlyscreen {
						display: none;
					}
					</style>
					</head>";

					$waktu_aju = TanggalFormat(date("Y-m-d"));

					echo "<body>
					<center>
					<div style='width:650px; text-align:center; background: #ffffff;'>

					

					<table cellspacing='0' cellpadding='0' style='width:100%;'>

					<tr>
					<td colspan='4'><h3 align=center>TANDA PERSETUJUAN UJIAN ".($Data['FID']=='08'? "TESIS":"SKRIPSI/TA")."</td>
					</tr>";

						echo "<tr>
						<td colspan='4'></td>
						</tr>";


					echo "<tr>
					<td colspan='4'></td>
					</tr>

					<tr>
					<td colspan='4'>Kami yang bertanda tangan di bawah ini adalah Pembimbing ".($Data['FID']=='08'? "Tesis":"Skripsi/TA")." dari :</td>
					</tr>

					<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td colspan='2'>&nbsp;</td>
					</tr>

					<tr>
					<td colspan='4'>
					<div style='padding-left:50px;'>
					<table cellspacing='5' cellpadding='0' style='width:90%;'>
					<tr>
					<td width='35%'>Nama</td>
					<td width='2%'>:</td>
					<td width='65%'><b>".ucwords(strtolower($Data['Nama']))."</b></td>
					</tr>
					<tr>
					<td>Nomor Pokok Mahasiswa</td>
					<td>:</td>
					<td>".$Data['MhswID']."</td>
					</tr>
					<tr>
					<td>Program Studi</td>
					<td>:</td>
					<td>".ucwords(strtolower($Data['Jurusan']))."</td>
					</tr>
					<tr>
					<td valign=top>Judul ".($Data['FID']=='08'? "Tesis":"Skripsi/TA")."</td>
					<td valign=top>:</td>
					<td valign=top>".$ta['Judul']."</td>
					</tr>
					</table>
					</div>
					</td>
					</tr>

					<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td colspan='2'>&nbsp;</td>
					</tr>

					<tr>
					<td colspan='4' style='text-align:Justify'>Dengan ini menyatakan telah menyetujui ".($Data['FID']=='08'? "Tesis":"Skripsi/TA")." mahasiswa tersebut untuk diuji guna memperoleh gelar ".$Data['Gelar']." pada ".($Data['FID']=='08'? "Program":"Fakultas")." ".$Data['Fakultas']." ".$Identitas['Nama'].".</td>
					</tr>

					<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td colspan='2'>&nbsp;</td>
					</tr>

					<tr>
					<td colspan='4' style='text-align:Justify'>Demikian persetujuan ini diberikan untuk dapat dipergunakan sebagaimana mestinya.</td>
					</tr>

					</table>

					<br /><br /><br />

					<div align=left>
					<div style='width:400px; float:left'>&nbsp;</div>
					<span style='text-align:left; margin-left:40px'>Padang, ".$waktu_aju."<br></span>
					<div style='width:400px; float:left'>Pembimbing II</div>
					<span style='text-align:left; margin-left:40px'>Pembimbing I,</span>
					<br /><br /><br /><br /><br /><br />
					<div style='width:400px; float:left'>".$rowpemb2."</div>
					<span style='text-align:left; margin-left:40px'>".$rowpemb1."</span>

				</div>
					<div class='onlyscreen' style='text-align:center'>
					<form>
					<input type='button' value='Cetak Surat' onClick='window.print()' style='font: 11px Tahoma,Verdana,Arial;' />
					</form>
					</div>
					</div>

					</center>
					</body>
					</html>";
		}