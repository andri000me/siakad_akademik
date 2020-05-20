<?php 
// Rekap Dosen Mengajar
// Author 	: Arisal Yanuarafi
// Start 	: 17 Juni 2012
   include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";

    $_SESSION['ThnAkd'] = $_GET['thnakd'];
  	$_SESSION['ProgramID']= $_GET['prg'];
	$_SESSION['ProdiID']= $_GET['prd'];
	$_SESSION['MKID'] = $_GET['mk'];
	$_SESSION['nds'] = $_GET['nds'];
	
	// Filter 
	if (!empty($_SESSION['ThnAkd']))   $whr[] = "j.TahunID='$_SESSION[ThnAkd]'";
	if (!empty($_SESSION['ProgramID']))   $whr[] = "j.ProgramID='$_SESSION[ProgramID]'";
	if (!empty($_SESSION['ProdiID']))   $whr[] = "j.ProdiID='$_SESSION[ProdiID]'";
	if (!empty($_SESSION['nds']))   $whr[] = "D.Nama='$_SESSION[nds]'";

		
		$_whr = implode(' and ', $whr);
 		$_whr = (empty($_whr))? '' : ' and ' . $_whr;

header("Content-type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename=rekap-dosen-mengajar-$_SESSION[ProdiID]-$_SESSION[ThnAkd]-$_SESSION[ProgramID]");
header("Expires:0");
header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
header("Pragma: public");

					$s = "SELECT DISTINCT(j.JadwalID),j.ProdiID,j.RuangID,D.NIDN, D.Nama as DOS, D.Gelar,D.Gelar1,m.Sesi,p.Nama as JUR,j.MKKode,j.JumlahMhsw,j.NamaKelas,k.Nama as KELAS,
					j.Nama as MAT, concat(h.Nama,' <sup>',left(j.JamMulai,5),' - ',left(j.JamSelesai,5)) as HAR,r.Nama as PRO,j.Kehadiran as Presensi, j.SKS as Kehadiran
					FROM `jadwal` j left outer join mk m on m.MKID=j.MKID
					left outer join kelas k on k.KelasID=j.NamaKelas, dosen D, prodi p, hari h,program r,jenjang n
					WHERE 
					p.ProdiID = j.ProdiID
					AND r.ProgramID = j.ProgramID
					AND n.JenjangID = p.JenjangID
					AND h.HariID = j.HariID
					AND D.Login = j.DosenID
					$_whr
					ORDER BY D.Nama,j.ProdiID,j.ProgramID,j.HariID,j.JamMulai";
	$r		=_query($s);

	?>
  <style>
table,font { font-family:'Trebuchet MS'; line-height:100%;mso-number-format:"\@"; }
.header{ font-family:'Trebuchet MS'; font-size:15px; line-height:90%; }
.garis {height:0px; line-height:0px;}
.text {
</style>
<table border=0>
<tr><td class=header align="center" colspan="12"><strong> BIRO ADMINISTRASI AKADEMIK & KEMAHASISWAAN (BAAK)<BR />
					REKAP DOSEN MENGAJAR <?php 
					$jur = GetFields('prodi',"ProdiID",$_SESSION['ProdiID'],'Nama,JenjangID');
					$jenjang = GetaField('jenjang',"JenjangID",$jur['JenjangID'],'Nama');
					echo $jur[Nama].' '.$jenjang; ?><br>
					<?php $thn=GetaField('tahun',"TahunID",$_SESSION['ThnAkd'],'Nama');
					echo $thn;  ?> 
                    </strong></td></tr>
 </table>
	<table class="bsc" border="1" width="900">
    <tr>
    <th class="ttl" align="center">No.</th>
    <th class="ttl" align="center" >Prodi</th>
    <th class="ttl" align="center" >Smst</th>
    <th class="ttl" align="center" >NIDN</th>
    <th class="ttl" align="center">Nama Dosen</th>
     <th class="ttl" align="center">Kelas</th>
     <th class="ttl" align="center">Peserta</th>
     <th class="ttl" align="center">Hari</th>
     <th class="ttl" align="center">Ruang</th>
    <th class="ttl" align="center">Kode MK</th>
    <th class="ttl" align="center">Nama Kelas</th>
    <th class="ttl" align="center">SKS</th>
    <th class="ttl" align="center">Pertemuan</th>
    
  </tr>
    <?php 
	$Dosen = '';
	$SKS = 0; $Kehadiran = 0; $row = 0;
	while ($w=_fetch_array($r)) {
	$n++;
	if ($_REQUEST['Periode']=='Y') {
	$whrPeriode = "and Tanggal >= '$_REQUEST[Mulai]' and Tanggal <= '$_REQUEST[Selesai]'";
	$w['Presensi'] = GetaField('presensi',"Tanggal >= '$_REQUEST[Mulai]' and Tanggal <= '$_REQUEST[Selesai]' and JadwalID",$w['JadwalID'],"count(PresensiID)");
	}
	$NIDN =(string)$w[NIDN];
	// buat jumlah sks dan pertemuan
	if (($Dosen != $w['DOS']) and (!empty($Dosen))) {
			echo "<tr>
					<td colspan='11' align=right bgcolor=#cccccc><font color=#0066FF><b>TOTAL SKS DAN PERTEMUAN</b></font></td>
					<td align=center bgcolor=#cccccc><font color=#0066FF><b>$SKS</b></font></td>
					<td align=center bgcolor=#cccccc><font color=#0066FF><b>$Kehadiran</b></font></td>
					</tr>";
		$SKS = 0; $Kehadiran = 0; $row = 0;
	}
			echo "<tr><td class=inp align=center>$n</td>
			<td align=left>$w[ProdiID]</td>
			<td align=left>$w[Sesi]</td>
			<td align=left>$NIDN</td>
			<td class=ul1>$w[Gelar1] $w[DOS] <sup>$w[Gelar]</sup></td>
			<td class=ul1>$w[KELAS]</td>
			<td class=ul1 align=center>$w[JumlahMhsw]</td>
			<td class=inp>$w[HAR]</td>
			<td class=inp>$w[RuangID]</td>
			<td class=ul1>$w[MKKode]</td>
			<td class=ul1>$w[MAT]</td>
			<td align=right align=center>$w[Kehadiran]</td>
			<td align=right align=center>$w[Presensi]</td>
			";

	$Dosen = $w['DOS'];	
	$SKS += $w['Kehadiran'];
	$Kehadiran += $w['Presensi'];
	$row++;
	
	$s3 = "SELECT p.Pertemuan, p.Tanggal,p.JamMulai, d.Nama from presensi p left outer join dosen d on d.Login=p.DosenID where 
			p.JadwalID='$w[JadwalID]' $whrPeriode order by p.Pertemuan";
	$r3 = _query($s3);
	while ($w3 = _fetch_array($r3)) {
		echo "<td>Pertemuan $w3[Pertemuan]<br>$w3[Tanggal] $w3[JamMulai]<br>$w3[Nama]</td>";
	}
	
	echo "</tr>";
	} 
				echo "<tr>
					<td colspan='11' align=right bgcolor=#cccccc><font color=#0066FF><b>TOTAL SKS DAN PERTEMUAN</b></font></td>
					<td align=center bgcolor=#cccccc><font color=#0066FF><b>$SKS</b></font></td>
					<td align=center bgcolor=#cccccc><font color=#0066FF><b>$Kehadiran</b></font></td>
					</tr>";

	
	?>
	</tr></table>
