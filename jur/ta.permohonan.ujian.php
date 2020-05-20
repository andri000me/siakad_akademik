<?php		session_start();		
    include_once "../dwo.lib.php";
  	include_once "../db.mysql.php";
  	include_once "../connectdb.php";
  	include_once "../parameter.php";
  	include_once "../cekparam.php";
	$MhswID = GetSetVar('MhswID');
	$Handphone = GetSetVar('Handphone');
	$Alamat = GetSetVar('Alamat');
	$Instansi = GetSetVar('Instansi');
	$Jabatan = GetSetVar('Jabatan');
		if($ta = GetFields("ta", "MhswID", $MhswID, "*"))
		{
			$upd 	= _query("update mhsw set Handphone='$Handphone', Alamat='$Alamat', Instansi='$Instansi', Jabatan='$Jabatan',LoginEdit='$_SESSION[_Login]', TanggalEdit=now() where MhswID='$MhswID'");
			$rowpemb1 	= GetaField('ta t left outer join dosen d on d.Login=t.Pembimbing', "t.NA='N' and t.MhswID", $MhswID, "concat(d.Gelar1, ' ', d.Nama,', ', d.Gelar)");
			$rowpemb2 	= GetaField('tadosen t left outer join dosen d on d.Login=t.DosenID', "t.Tipe=0 and t.TAID", $ta['TAID'], "concat(d.Gelar1,' ',d.Nama, ', ', d.Gelar)");
			
			$Data		= GetFields("mhsw m left outer join prodi p on p.ProdiID=m.ProdiID
											left outer join fakultas f on f.FakultasID=p.FakultasID",
									"m.MhswID", $MhswID, "f.Nama as Fakultas, f.JabatanSKPembimbing, f.PejabatSKPembimbing, p.Nama as Jurusan, p.Jurusan as Jurusan2, f.Email as _Email, f.Website as _Website, m.*, f.FakultasID as FID");
			$Identitas		= GetFields('identitas',"Kode",KodeID,'*');

					echo "<html>
					<head>
					<title>Surat Permohonan Ujian ".$Data['Nama']." (NPM ".$Data['MhswID'].")</title>
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
					<td valign='top'>Hal</td>
					<td valign='top'>:</td>
					<td><b><i>Permohonan ujian ".($Data['FID']=='08'? "tesis":"akhir")."</i></b>
					</td>
					<td width='43%' style='text-align:right'>".$waktu_aju."</td>
					</tr>

					<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td colspan='2'>&nbsp;</td>
					</tr>

					<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td colspan='2'>&nbsp;</td>
					</tr>

					<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td colspan='2'>&nbsp;</td>
					</tr>

					<tr>
					<td colspan='4'>Kepada</td>
					</tr>

					<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td colspan='2'>&nbsp;</td>
					</tr>

					<tr>
					<td colspan='4'>Yth. <b>Bapak/Ibu ".($Data['FID']=='08'? "Direktur Program":"Dekan Fakultas")." ".$Data['Fakultas']."</b></td>
					</tr>";

						echo "<tr>
						<td colspan='4'>".$Identitas['Nama']."</td>
						</tr>";


					echo "<tr>
					<td colspan='4'>di</td>
					</tr>

					<tr>
					<td colspan='4'>".$Identitas['Kota']."</td>
					</tr>

					<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td colspan='2'>&nbsp;</td>
					</tr>

					<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td colspan='2'>&nbsp;</td>
					</tr>

				
					<tr>
					<td colspan='4' style='text-align:Justify'>Saya yang bertanda tangan di bawah ini:</td>
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
					<td valign='top'>Alamat</td>
					<td valign='top'>:</td>
					<td>".$Data['Alamat']."</td>
					</tr>
					<tr>
					<td valign='top'>Handphone</td>
					<td valign='top'>:</td>
					<td>".$Data['Handphone']."</td>
					</tr>
					<tr>
					<td valign='top'>Instansi Asal</td>
					<td valign='top'>:</td>
					<td>".$Instansi."</td>
					</tr>
					<tr>
					<td valign='top'>Jabatan</td>
					<td valign='top'>:</td>
					<td>".$Jabatan."</td>
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
					<td colspan='4' style='text-align:Justify'>Dengan ini mengajukan permohonan untuk melaksanakan ".($Data['FID']=='08'? "Ujian Tesis":"Ujian Akhir")." :</td>
					</tr>

					<tr>
					<td colspan='4'>
					<div style='padding-left:50px;'>
					<table cellspacing='5' cellpadding='0' style='width:90%;'>
					<tr>
					<td width='35%' valign=top>Judul ".($Data['FID']=='08'? "Tesis":"Skripsi/TA")."</td>
					<td width='2%' valign=top>:</td>
					<td width='65%' valign=top>".$ta['Judul']."</td>
					</tr>
					<tr>
					<td>Pembimbing I</td>
					<td>:</td>
					<td>".$rowpemb1."</td>
					</tr>
					<tr>
					<td>Pembimbing II</td>
					<td>:</td>
					<td>".$rowpemb2."</td>
					</tr>
					<td>Penguji I (diisi Prodi)</td>
					<td>:</td>
					<td></td>
					</tr>
					<tr>
					<td>Penguji II (diisi Prodi)</td>
					<td>:</td>
					<td></td>
					</tr>
					<tr>
					<td>Tanggal Ujian (diisi Prodi)</td>
					<td>:</td>
					<td></td>
					</tr>
					<tr>
					<td>Jam (diisi Prodi)</td>
					<td>:</td>
					<td></td>
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
					<td colspan='4' style='text-align:Justify'>Sebagai bahan pertimbangan penetapan Surat Keputusan (SK) pelaksanaan ".($Data['FID']=='08'? "Ujian Tesis":"Ujian Akhir").", saya lampirkan:
					<br>
					<ol>
					<li>Tanda persetujuan ujian dari pembimbing</li>
					<li>Kartu Konsultasi</li>
					<li>Foto kopi Lembar Hasil Studi (LHS) semester 1 sampai semester terakhir</li>
					<li>Tanda bukti pelunasan uang kuliah</li>
					<li>Foto kopi sertifikat TOEFL dengan skor minimal 400</li>
					<li>Foto kopi ".($Data['FID']=='08'? "Tesis":"Skripsi/TA")." 1 (satu) rangkap</li>
					".($Data['FID']=='08'? "<li>Foto kopi artikel dari tesis yang disetujui kedua pembimbing":"")."
					<li>Foto kopi ijazah terakhir</li>
					<li>Isian formulir buku wisuda</li>
					<li>Isian formulir foto untuk ijazah</li>
					<li>Pas photo ukuran 4x6 sebanyak 4 (empat) lembar (hitam putih pakai jas Almamater)</li>
					<li>Pas photo ukuran 3x4 sebanyak 4 (empat) lembar (berwarna pakai jas Almamater)</li>
					<li>Pas photo ukuran 3x4 sebanyak 4 (empat) lembar (hitam putih pakai jas Almamater)</li>
					<li>Pas photo ukuran 2x3 sebanyak 4 (empat) lembar (hitam putih pakai jas Almamater)</li>
					</ol>
					</td>
					</tr>

					<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td colspan='2'>&nbsp;</td>
					</tr>

					<tr>
					<td colspan='4' style='text-align:Justify'>Demikian disampaikan, atas perhatian dan kerjasamanya diucapkan terima kasih.</td>
					</tr>

					</table>

					<br /><br /><br />

					<div align=left>Mengetahui,<br>
					<div style='width:400px; float:left'>Ketua Program Studi</div>
					<span style='text-align:left; margin-left:40px'>Mahasiswa,</span>
					<br /><br /><br /><br /><br />
					<div style='width:400px; float:left'>.....................</div>
					<span style='text-align:left; margin-left:40px'>.....................</span>

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