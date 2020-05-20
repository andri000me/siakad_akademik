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
		if($praktek = GetFields("praktekkerja", "MhswID", $MhswID, "*"))
		{
			if ($surat = GetFields('praktekkerja_surat', "MhswID", $MhswID, "*")) {
				$upd 	= _query("update praktekkerja_surat set Tujuan='$Tujuan', cq='$cq', Instansi='$Instansi', Kota='$Kota',XEdit=(XEdit+1) where MhswID='$MhswID'");
			}
			else {
				$ins = _query("INSERT into praktekkerja_surat (MhswID,Tujuan,cq,Instansi,Kota,LoginBuat,TanggalBuat,XEdit) values
								('$MhswID','$Tujuan','$cq','$Instansi','$Kota','$_SESSION[_Login]',now(),0)");
			}
			$surat = GetFields('praktekkerja_surat', "MhswID", $MhswID, "*");
			$rowpemb1 	= GetaField('dosen', "NA='N' AND Login", $praktek['Pembimbing1'], "concat(Gelar1,' ',Nama, ', ', Gelar)");
			$rowpemb2 	= GetaField('dosen', "NA='N' AND Login", $praktek['Pembimbing2'], "concat(Gelar1,' ',Nama, ', ', Gelar)");
			
			$Data		= GetFields("mhsw m left outer join prodi p on p.ProdiID=m.ProdiID
											left outer join fakultas f on f.FakultasID=p.FakultasID",
									"m.MhswID", $MhswID, "f.Nama as Fakultas, f.JabatanSKPembimbing, f.PejabatSKPembimbing, p.Nama as Jurusan, p.Jurusan as Jurusan2, f.Email as _Email, f.Website as _Website, m.*");
			$Identitas		= GetFields('identitas',"Kode",KodeID,'*');

					echo "<html>
					<head>
					<title>Surat Pengantar KP/PKL/PL ".$Data['Nama']." (NPM ".$Data['MhswID'].")</title>
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
					<div class='fakultas'>FAKULTAS ".strtoupper($Data['Fakultas'])."</div>
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
					<td colspan='2'><b><i>Permohonan izin melakukan<br />
					Kerja Praktek/Tugas Akhir (KP/TA)</i></b>
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
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td colspan='2'>Kepada</td>
					</tr>

					<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td colspan='2'>&nbsp;</td>
					</tr>

					<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td colspan='2'>Yth. <b>".$surat['Tujuan']." ".($surat['cq'] ? $surat['Instansi'] : "")."</b></td>
					</tr>";

					if($surat['cq'])
					{
						echo "<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td colspan='2'>c.q <b>".$surat['cq']."</b></td>
						</tr>";
					}

					if($surat['Instansi'])
					{
						echo "<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td colspan='2'>".$surat['Instansi']."</td>
						</tr>";
					}

					echo "<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td colspan='2'>di</td>
					</tr>

					<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td colspan='2'>".$surat['Kota']."</td>
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
					<td colspan='2'>Dengan hormat,</td>
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
					<td colspan='2' style='text-align:Justify'>Dalam rangka menyelesaikan program pendidikan Sarjana/Diploma di Fakultas ".ucwords(strtolower($Data['Fakultas']))." ".ucwords(strtolower($Identitas['Nama'])).", dengan ini kami mengajukan permohonan kepada Bapak/Ibu, agar dapat memberi izin melakukan <b>Kerja Praktek/Tugas Akhir (KP/TA)</b> kepada mahasiswa kami yang tersebut di bawah ini:</td>
					</tr>

					<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td colspan='2'>&nbsp;</td>
					</tr>

					<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td colspan='2'>
					<div style='padding-left:50px;'>
					<table cellspacing='5' cellpadding='0' style='width:90%;'>
					<tr>
					<td width='45%'>Nama</td>
					<td width='5%'>:</td>
					<td width='50%'><b>".$Data['Nama']."</b></td>
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
					<td>Fakultas</td>
					<td>:</td>
					<td>".ucwords(strtolower($Data['Fakultas']))."</td>
					</tr>
					<tr>
					<td valign='top'>Lama Melakukan KP/TA</td>
					<td valign='top'>:</td>
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
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td colspan='2' style='text-align:Justify'>Untuk terlaksananya kegiatan tersebut, mahasiswa kami bersedia mematuhi ketentuan yang berlaku.</td>
					</tr>

					<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td colspan='2'>&nbsp;</td>
					</tr>

					<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td colspan='2' style='text-align:Justify'>Demikianlah kami sampaikan, atas perhatian dan bantuannya kami ucapkan terima kasih.</td>
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
					<script>
					window.print();
					</script>

					<div class='onlyscreen' style='text-align:center'>
					<form>
					<input type='button' value='Cetak Surat Pengantar KP' onClick='window.print()' style='font: 11px Tahoma,Verdana,Arial;' />
					</form>
					</div>
					</div>

					</center>
					</body>
					</html>";
		}