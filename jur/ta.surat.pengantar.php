<?php		session_start();		
    include_once "../dwo.lib.php";
  	include_once "../db.mysql.php";
  	include_once "../connectdb.php";
  	include_once "../parameter.php";
  	include_once "../cekparam.php";
	$MhswID = GetSetVar('MhswID');
	$Tujuan = GetSetVar('Tujuan');
	$cq = GetSetVar('cq');
	$Instansi = GetSetVar('Instansi');
	$Kota = GetSetVar('Kota');
		if($ta = GetFields("ta", "MhswID", $MhswID, "*"))
		{
			if ($surat = GetFields('ta_surat', "MhswID", $MhswID, "*")) {
				$upd 	= _query("update ta_surat set Tujuan='$Tujuan', cq='$cq', Instansi='$Instansi', Kota='$Kota',XEdit=(XEdit+1),LoginBuat='$_SESSION[_Login]', TanggalBuat=now() where MhswID='$MhswID'");
			}
			else {
				$ins = _query("INSERT into ta_surat (MhswID,Tujuan,cq,Instansi,Kota,LoginBuat,TanggalBuat,XEdit) values
								('$MhswID','$Tujuan','$cq','$Instansi','$Kota','$_SESSION[_Login]',now(),0)");
			}
			$surat = GetFields('ta_surat', "MhswID", $MhswID, "*");
			$rowpemb1 	= GetaField('dosen', "NA='N' AND Login", $ta['Pembimbing1'], "concat(Gelar1,' ',Nama, ', ', Gelar)");
			$rowpemb2 	= GetaField('tadosen t left outer join dosen d on d.Login=t.DosenID', "t.TAID", $ta['TAID'], "concat(d.Gelar1,' ',d.Nama, ', ', d.Gelar)");
			
			$Data		= GetFields("mhsw m left outer join prodi p on p.ProdiID=m.ProdiID
											left outer join fakultas f on f.FakultasID=p.FakultasID",
									"m.MhswID", $MhswID, "f.Nama as Fakultas, f.JabatanSKPembimbing, f.PejabatSKPembimbing, p.Nama as Jurusan, p.Jurusan as Jurusan2, f.Email as _Email, f.Website as _Website, m.*, f.FakultasID as FID");
			$Identitas		= GetFields('identitas',"Kode",KodeID,'*');

					echo "<html>
					<head>
					<title>Surat Pengantar Penelitian ".$Data['Nama']." (NPM ".$Data['MhswID'].")</title>
					<style>
					body {
						margin-left: 0 px;
						font-family: Georgia, Tahoma, Verdana, Arial;
						font-size: 14px;
					}
					td {
						font-size: 14px;
						font-family: Georgia, Tahoma, Verdana, Arial;
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
						font-size: 12px;
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
						font-size: 14px;
						font-family: Georgia, Tahoma, Verdana, Arial;
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
					<td width='10%' class='alamat' style='text-align:left;' valign='top'><img src='../img/ubh_logo.png' width='100' height='98' border='0' alt='-' title='-'></td>
					<td width='90%' valign='top' class='alamat' style='text-align:left; padding-left:10px'>
					<div class='logo'>".strtoupper($Identitas['Yayasan'])."</div>
					<div class='kampus'>".strtoupper($Identitas['Nama'])."</div>
					<div class='fakultas'>".($Data['FID']=='08'? "PROGRAM":"FAKULTAS")." ".strtoupper($Data['Fakultas'])."</div>
					<div class='alamat'>Email: <font color='#0000ff'>".strtolower($Data['_Email'])."</font> Website: <font color='#0000ff'>".strtolower($Data['_Website'])."</font></div></td>
					</tr>
					</table>

					<table cellspacing='0' cellpadding='0' style='width:100%;'>
					<tr>
					<td class='tabel' style='width:100%; height:2px'><img src='../img/transparant.png' height='1' border='0'></td>
					</tr>
					</table>

					<div style='height:5px'><img src='../img/transparant.png' height='5' border='0'></div>

					<table cellspacing='0' cellpadding='0' style='width:100%;'>
					<tr>
					<td width='12%'>Nomor</td>
					<td width='2%'>:</td>
					<td width='43%'>&nbsp;</td>
					<td width='43%' style='text-align:right'>".$waktu_aju."</td>
					</tr>

					<tr>
					<td>Lampiran</td>
					<td>:</td>
					<td colspan='2'>-</td>
					</tr>

					<tr>
					<td valign='top'>Hal</td>
					<td valign='top'>:</td>
					<td colspan='2'><b><i>Permohonan izin melakukan Penelitian</i></b>
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
					<td colspan='4'>Yth. <b>".$surat['Tujuan']." ".($surat['cq'] ? $surat['Instansi'] : "")."</b></td>
					</tr>";

					if($surat['cq'])
					{
						echo "<tr>
						<td colspan='4'>c.q <b>".$surat['cq']."</b></td>
						</tr>";
					}

					if($surat['Instansi'])
					{
						echo "<tr>
						<td colspan='4'>".$surat['Instansi']."</td>
						</tr>";
					}

					echo "<tr>
					<td colspan='4'>di</td>
					</tr>

					<tr>
					<td colspan='4'>".$surat['Kota']."</td>
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
					<td colspan='4'>Dengan hormat,</td>
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
					<td colspan='4' style='text-align:Justify'>Dalam rangka menyelesaikan program pendidikan ".($Data['FID']=='08'? "Pascasarjana (S2) di Program":"Sarjana (S1) di Fakultas")." ".ucwords(strtolower($Data['Fakultas']))." ".ucwords(strtolower($Identitas['Nama'])).", dengan ini kami mengajukan permohonan kepada Bapak/Ibu, agar dapat memberi izin melakukan <b>penelitian</b> kepada mahasiswa kami yang tersebut di bawah ini:</td>
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
					<td>Jurusan</td>
					<td>:</td>
					<td>".ucwords(strtolower($Data['Jurusan']))."</td>
					</tr>
					<tr>
					<td>".($Data['FID']=='08'? "Program":"Fakultas")."</td>
					<td>:</td>
					<td>".ucwords(strtolower($Data['Fakultas']))."</td>
					</tr>
					<tr>
					<td valign='top'>Judul Penelitian</td>
					<td valign='top'>:</td>
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
					<td colspan='4' style='text-align:Justify'>Untuk terlaksananya kegiatan tersebut, mahasiswa kami bersedia mematuhi ketentuan yang berlaku.</td>
					</tr>

					<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td colspan='2'>&nbsp;</td>
					</tr>

					<tr>
					<td colspan='4' style='text-align:Justify'>Demikianlah kami sampaikan, atas perhatian dan bantuannya kami ucapkan terima kasih.</td>
					</tr>

					</table>

					<br /><br /><br /><br /><br />

					<div style='text-align:left; padding-left:450px'>".$Data['JabatanSKPembimbing'].",<br />
					<br /><br /><br /><br />
				".$Data['PejabatSKPembimbing']."
					</div>

					<div style='text-align:left'>
					<u>Tembusan Yth</u>:
					<ol>
					<li>Rektor ".ucwords(strtolower($Identitas['Nama']))."
					<li>Ketua ".(empty($Data['Jurusan2']) ? "Program Studi" : "Jurusan")." ".ucwords(strtolower($Data['Jurusan']))."
					<li>Arsip
					</ol>
					</div>
	
					<div class='onlyscreen' style='text-align:center'>
					<form>
					<input type='button' value='Cetak Surat Pengantar' onClick='window.print()' style='font: 11px Tahoma,Verdana,Arial;' />
					</form>
					</div>
					</div>

					</center>
					</body>
					</html>";
		}