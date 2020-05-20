<?php		session_start();		
    include_once "../dwo.lib.php";
  	include_once "../db.mysql.php";
  	include_once "../connectdb.php";
  	include_once "../parameter.php";
  	include_once "../cekparam.php";
		$KompreID = GetSetVar('KompreID');
		$kompre = GetFields("kompre", "KompreID", $KompreID, "*");
		$MhswID = $kompre['MhswID'];

		if($ta = GetFields("ta", "MhswID", $MhswID, "*"))
		{
			$rowpemb1 	= GetaField('dosen', "NA='N' AND Login", $ta['pembimbing'], "concat(Gelar1,' ',Nama, ', ', Gelar)");
			
			$Data		= GetFields("mhsw m left outer join prodi p on p.ProdiID=m.ProdiID
											left outer join fakultas f on f.FakultasID=p.FakultasID",
									"m.MhswID", $MhswID, "f.Nama as Fakultas, f.JabatanSKPembimbing, f.PejabatSKPembimbing, p.Pejabat as KaProdi, p.Nama as Jurusan, p.Jurusan as Jurusan2, f.Email as _Email, f.Website as _Website, m.*, f.FakultasID as FID");
			$Identitas	= GetFields('identitas',"Kode",KodeID,'*');

					echo "<html>
					<head>
					<title>Undangan Ujian Akhir ".$Data['Nama']." (NPM ".$Data['MhswID'].")</title>
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

					<table cellspacing='0' cellpadding='0' style='width:100%;'>

					<tr>
					<td valign='top'>Nomor</td>
					<td valign='top'>:</td>
					<td>
					</td>
					<td width='43%' style='text-align:right'>".$waktu_aju."</td>
					</tr>
					<tr>
					<td valign='top'>Lampiran</td>
					<td valign='top'>: -</td>
					<td>
					</td>
					<td width='43%' style='text-align:right'></td>
					</tr>
					<tr>
					<td valign='top'>Hal</td>
					<td valign='top'>: <b><i>Jadwal Ujian ".($Data['FID']=='08'? "Tesis":"Skripsi/TA")."</i></b></td>
					<td>
					</td>
					<td width='43%' style='text-align:right'></td>
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
					<td colspan='4'>Kepada Yth.
					<br>
					</td>
					</tr>

					<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td colspan='2'>&nbsp;</td>
					</tr>

					<tr>
					<td valign=top>Sdr.</td>
					<td valign=top><ol>";
					$s1 = "SELECT m.Nama as Jabatan, concat(d.Gelar1, ' ', d.Nama,', ', d.Gelar) as Penguji from komprematauji m 
					left outer join kompredosen k on k.KompreMataUjiID=m.KompreMataUjiID
					left outer join kompre r on r.KompreID=k.KompreID
					left outer join dosen d on d.Login=k.DosenID
					where m.ProdiID='$Data[ProdiID]'
					AND r.KompreID='$KompreID'
					group by m.KodeKompre order by m.KodeKompre";
			$r1 = _query($s1);
			while ($w1 = _fetch_array($r1)){
					echo "<li>$w1[Penguji]</li>";
					}
					echo "</ol>
					</tr>";

					echo "		
					<tr>
					<td colspan='4' style='text-align:Justify'>
					Dosen Pembimbing dan Penguji Ujian ".($Data['FID']=='08'? "Tesis":"Skripsi/TA")." Program Studi $Data[Jurusan] ".($Data['FID']=='08'? "Program":"Fakultas")." $Data[Fakultas] ".$Identitas['Nama'].".
					<br>Di tempat
					</td>
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
					<td colspan='4' style='text-align:Justify'>
					Dengan hormat,<br><br>
					Bersama ini kami mengundang Saudara untuk menghadiri Ujian ".($Data['FID']=='08'? "Tesis":"Skripsi/TA")." pada :
					</td>
					</tr>

					<tr>
					<td colspan='4'>
					<div style='padding-left:50px;'>
					<table cellspacing='5' cellpadding='0' style='width:90%;'>
					<tr>
					<td width='35%'>Hari/Tanggal</td>
					<td width='2%'>:</td>
					<td width='65%'>".GetaField('hari','HariID',date('w', strtotime($kompre['TanggalUjian'])),"Nama")." / ".TanggalFormat($kompre['TanggalUjian'])."</td>
					</tr>
					<tr>
					<td>Tempat</td>
					<td>:</td>
					<td>Gedung ".($Data['FID']=='08'? "Program":"Fakultas")." $Data[Fakultas]</td>
					</tr>
					<tr>
					<td>Pukul</td>
					<td>:</td>
					<td>".substr($kompre['JamMulai'],0,5)." s/d ".substr($kompre['JamSelesai'],0,5)."</td>
					</tr>

					</table>
					</div>
					</td>
					</tr>

					<tr>
					<td colspan='4' style='text-align:Justify'>
					untuk mahasiswa program studi $Data[Jurusan] atas nama:
					</td>
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
					<td valign=top>Judul ".($Data['FID']=='08'? "Tesis":"Skripsi/TA")."</td>
					<td valign=top>:</td>
					<td>".$ta['Judul']."</td>
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
					<td colspan='4' style='text-align:Justify'>Demikian disampaikan, atas perhatian dan kerjasamanya diucapkan terima kasih.</td>
					</tr>

					</table>

					<br /><br /><br />

					<div align=left><br>
					<div style='text-align:left; margin-left:350px'>Ketua Program Studi $Data[Jurusan],</div>
					<br /><br /><br /><br /><br />
					<div style='text-align:left; margin-left:350px'><b>$Data[KaProdi]</b></div>

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