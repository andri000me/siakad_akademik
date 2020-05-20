<?php		session_start();
	/* 	Author	: Arisal Yanuarafi
		Start	: 20 DES 2013 14:45 AM
	*/
	
	include_once "../dwo.lib.php";
  	include_once "../db.mysql.php";
  	include_once "../connectdb.php";
  	include_once "../parameter.php";
  	include_once "../cekparam.php";	
	$JadwalID = GetSetVar('jid');
	$ProdiID 	= GetaField('jadwal', "JadwalID", $JadwalID, "ProdiID");
	$DataProdi	= GetFields('prodi', "NA='N' AND ProdiID", $ProdiID, "*");
	
	$Identitas 		= GetFields("identitas", "Kode", KodeID, "*");
	$jdwl 			= GetFields("jadwal j left outer join dosen d on d.Login=j.DosenID
								left outer join kelas kl on kl.KelasID=j.NamaKelas", "j.NA='N' AND j.JadwalID", $JadwalID, "j.*,d.Nama as DSN,d.Gelar1,d.Gelar,kl.Nama as Kelas");
	$DataFakultas 	= GetFields("fakultas", "NA='N' AND FakultasID", $DataProdi['FakultasID'], "*");
	$Jenjang		= GetFields("jenjang", "NA='N' AND JenjangID", $DataProdi['JenjangID'], "Nama, Keterangan");
	$Semester 		= GetaField('tahun', "TahunID", $jdwl['TahunID'],'Nama');
	$header .= " 

			<div class='logo' style='text-align:center'>Daftar Hadir Ujian Akhir Semester</div>
			<br />
			<table cellspacing='0' cellpadding='0' style='width:100%'>
			<tr>
			<td class='judul' style='width:15%'>Periode</td>
			<td class='judul' style='width:1%'>:</td>
			<td class='judul' style='width:40%'><b>$Semester</b></td>
			<td class='judul' style='width:44%' colspan='2'>Hari Ujian:...........................................</td>
			</tr>
			<tr>
			<td class='judul' style='width:15%'>Fakultas</td>
			<td class='judul' style='width:1%'>:</td>
			<td class='judul' style='width:40%'>".ucwords(strtolower($DataFakultas['Nama']))."</td>
			<td class='judul' style='width:44%' colspan='2'>Tanggal Ujian:.....................................</td>
			</tr>
			<tr>
			<td class='judul' style='width:15%'>"."Program Studi"."</td>
			<td class='judul' style='width:1%'>:</td>
			<td class='judul' style='width:40%'>".ucwords(strtolower($DataProdi['Nama']))."</td>
			<td class='judul' style='width:44%' colspan='2'>Jadwal Ujian:.......................................</td>
			</tr>
			<tr>
			<td class='judul' colspan='3'>&nbsp;</td>
			<td class='judul' colspan='2'>Gedung/Lokal:.....................................</td>
			</tr>
			<tr>
			<td class='judul' colspan='5'>".$jdwl['MKKode']." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$jdwl['Nama']." (".$jdwl['SKS']." SKS) - Kelas ".(empty($jdwl['Kelas'])? $jdwl['NamaKelas']:$jdwl['Kelas'])."</td>
			</tr>
			<tr>
			<td class='judul' colspan='5'>Dosen: <b>".($jdwl['Gelar1'] ? $jdwl['Gelar1']." " : "") . $jdwl['DSN'] . ($jdwl['Gelar'] ? ", ".$jdwl['Gelar'] : "");

			$header .= "</b></td>
			</tr>
			</table>";

			$header .= "<table class='tabel' cellspacing='0' cellpadding='0' style='width:100%'>
			<tr>
			<td style='text-align:center; padding:5px; width:3%'>No</td>
			<td style='text-align:center; padding:5px; width:16%'>NPM</td>
			<td style='text-align:center; padding:5px; width:27%'>Nama</td>
			<td style='text-align:center; padding:5px; width:18%'>Tanda Tangan</td>
			<td style='text-align:center; padding:5px; width:18%'>Kehadiran</td>
			<td style='text-align:center; padding:5px; width:18%'>Nilai (Angka)</td>
			</tr>";

			$footer .= "<br /><br />
			
			<table style='border:0px; width:100%'>
			<tr>
			<td style='border:0px; padding:0px; width:55%'>Padang, ________________</td>
			<td style='border:0px; padding:0px; width:20%'>&nbsp;</td>
			<td style='border:0px; padding:0px; width:25%'>Pengawas</td>
			<tr>
			<tr>
			<td style='border:0px; padding:0px; width:55%'>Dosen Pengampu Matakuliah</td>
			<td style='border:0px; padding:0px; width:20%'>&nbsp;</td>
			<td style='border:0px; padding:0px; width:25%'>&nbsp;</td>
			<tr>
			<tr>
			<td style='border:0px; padding-top: 30px; width:55%'><b>".($jdwl['Gelar1'] ? $jdwl['Gelar1']." " : "") . $jdwl['DSN'] . ($jdwl['Gelar'] ? ", ".$jdwl['Gelar'] : "")."</b></td>
			<td style='border:0px; padding-top: 30px; width:20%'>&nbsp;</td>
			<td style='border:0px; padding-top: 30px; width:25%;'>_______________</td>
			<tr>
			<table>
		";

			$footer .="</div>
			</center>
			</body>
			</html>";
			
            echo "<html>
			<head>
			<title>Absensi Ujian Akhir Semester $dd1 $dd2</title>
			<style>
			body {
				font-family: 'Courier New', Arial, Tahoma, Verdana;
				font-size: 11px;
				padding: 0px;
				background: #ffffff;
			}
			div, td,select {
				font-family: 'Courier New', Arial, Tahoma, Verdana;
				font-size: 11px;
				background: #ffffff;
			}
			.break { page-break-before: always; }
			td {
				padding: 2px;
				border:1px solid #000000;
			}
			.tabel {
				
			}
			.tabelbawah {
				border-top:1px solid #000000;
			}
			.logo {
				font-size: 14px;
				font-family: Tahoma, Tahoma, Verdana;
				font-weight: bold;
				color: #000000;
			}
			.judul {
				font-family: Tahoma, Arial, Verdana;
				font-size: 12px;
				color: #000000;
				border:0px;
			}
			</style>
			<style media='print'>
			.onlyscreen {
				display: none;
			}
			</style>";
			
			echo "</head>

			<body>
			<center>
			<div style='width:630px; text-align:center; background: #ffffff;'>

			";

			echo "

			<div class='onlyscreen' style='text-align:center'>
			<a href='javascript:void(0);' onClick='window.print()' ><img src='../img/printer.gif' border='0' alt='Cetak' title='Cetak' /></a><br />
			Jumlah Data Per Lembar<form method=post action=?>
			<select name='jml_pst' class='tbox' onchange=\"this.form.submit()\">
			<option value=\"\">- atur -</option>
			<option value=\"15\">15 Mahasiswa</option>
			<option value=\"20\">20 Mahasiswa</option>
			<option value=\"25\">25 Mahasiswa</option>
			<option value=\"30\">30 Mahasiswa</option>
			<option value=\"35\">35 Mahasiswa</option>
			<option value=\"40\">40 Mahasiswa</option>
			<option value=\"45\">45 Mahasiswa</option>
			</select></form>
			</div>";

			echo "<div class='onlyscreen'><a href='#' onclick=\"javascript:window.close(); return false\">Tutup Jendela</a><br /><br /></div>";

			$counter1 = $from+1;
			if (isset($_POST['jml_pst'])) {
				$s = "select k.MhswID, upper(m.Nama) as Nama, m.TahunID as ThnMasuk, h.TahunID, p.JenjangID,m.ProgramID,h.Biaya,h.Potongan,h.Bayar,h.Tarik
				   from khs h, krs k
					  left outer join mhsw m on m.MhswID = k.MhswID and m.KodeID = '".KodeID."'
					  left outer join prodi p on p.ProdiID = m.ProdiID
					where k.JadwalID = '$jdwl[JadwalID]'
					AND h.MhswID=k.MhswID
					AND h.TahunID=k.TahunID
					Group By k.MhswID
					order by k.MhswID";
				$r = _query($s);
				$nr = _num_rows($r);
				$maxentryperpage = sqling($_POST['jml_pst'])+0;
				$maxentryoflastpage = 33;
				$pages = floor($nr/$maxentryperpage);
				$lastpageentry = $nr%$maxentryperpage;
				if($lastpageentry == 0)
				{	$pages -= 1;
					$lastpageentry = $maxentryperpage;
				}
				$totalpage = $pages;
				if($lastpageentry > $maxentryoflastpage) $totalpage += 2;
				else $totalpage += 1;

// Buat semua halaman tanpa footer
for($i = 0; $i< $pages; $i++)
{ 	$start = $i*$maxentryperpage;
			echo $header;
	$s1 = "select k.MhswID, upper(m.Nama) as Nama, m.TahunID as ThnMasuk, h.TahunID, p.JenjangID,m.ProgramID,h.Biaya,h.Potongan,h.Bayar,h.Tarik
   from khs h, krs k
      left outer join mhsw m on m.MhswID = k.MhswID and m.KodeID = '".KodeID."'
	  left outer join prodi p on p.ProdiID = m.ProdiID
    where k.JadwalID = '$jdwl[JadwalID]'
	AND h.MhswID=k.MhswID
	AND h.TahunID=k.TahunID
	Group By k.MhswID
    order by k.MhswID
		limit $start, $maxentryperpage";
	$r1 = _query($s1);
			while($rowkrs = _fetch_array($r1))
				{

					echo "<tr>
					<td style='text-align:right'>".$counter1.".</td>
					<td>".$rowkrs['MhswID']."</td>
					<td>".ucwords(strtolower($rowkrs['Nama']))."</td>
					<td style='padding:5px'>&nbsp;</td>
					<td style='padding:5px'>&nbsp;</td>
					<td style='padding:5px'>&nbsp;</td>
					</tr>";

					$counter1++;
				}
				echo "</table>";
				echo $footer;
				echo "<p class=\"break\"></p>";
				
				
}

//Buat halaman terakhir dengan footer
$start = $i*$maxentryperpage;

if($lastpageentry > $maxentryoflastpage)
{	
				echo $header;
	$s1 = "select k.MhswID, upper(m.Nama) as Nama, m.TahunID as ThnMasuk, h.TahunID, p.JenjangID,m.ProgramID,h.Biaya,h.Potongan,h.Bayar,h.Tarik
    from khs h, krs k
      left outer join mhsw m on m.MhswID = k.MhswID and m.KodeID = '".KodeID."'
	  left outer join prodi p on p.ProdiID = m.ProdiID
    where k.JadwalID = '$jdwl[JadwalID]'
	AND h.MhswID=k.MhswID
	AND h.TahunID=k.TahunID
	Group By k.MhswID
    order by k.MhswID
		limit $start, $maxentryperpage";
	$r1 = _query($s1);
	while($rowkrs = _fetch_array($r1))
				{
					echo "<tr>
					<td style='text-align:right'>".$counter1.".</td>
					<td>".$rowkrs['MhswID']."</td>
					<td>".ucwords(strtolower($rowkrs['Nama']))."</td>
					<td style='padding:5px'>&nbsp;</td>
					<td style='padding:5px'>&nbsp;</td>
					<td style='padding:5px'>&nbsp;</td>
					</tr>";

					$counter1++;
				}
                echo "</table>";

echo $footer;
}
else
{	
			echo $header;
	$s1 = "select k.MhswID, upper(m.Nama) as Nama, m.TahunID as ThnMasuk, h.TahunID, p.JenjangID,m.ProgramID,h.Biaya,h.Potongan,h.Bayar,h.Tarik
    from khs h,krs k
      left outer join mhsw m on m.MhswID = k.MhswID and m.KodeID = '".KodeID."'
	  left outer join prodi p on p.ProdiID = m.ProdiID
    where k.JadwalID = '$jdwl[JadwalID]'
	AND h.MhswID=k.MhswID
	AND h.TahunID=k.TahunID
	Group By k.MhswID
    order by k.MhswID
		limit $start, $maxentryperpage";

	$r1 = _query($s1);
	
	while($rowkrs = _fetch_array($r1))
				{
					echo "<tr>
					<td style='text-align:right'>".$counter1.".</td>
					<td>".$rowkrs['MhswID']."</td>
					<td>".ucwords(strtolower($rowkrs['Nama']))."</td>
					<td style='padding:5px'>&nbsp;</td>
					<td style='padding:5px'>&nbsp;</td>
					<td style='padding:5px'>&nbsp;</td>
					</tr>";

					$counter1++;
				}
                echo "</table>";
			echo $footer;
			exit;
		}
}
			
		

