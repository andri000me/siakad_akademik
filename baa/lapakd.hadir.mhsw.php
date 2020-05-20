<?php
session_start();
  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
header("Content-type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename=rekap-kehadiran-mhsw-".$_SESSION['TahunID']."-".$_SESSION['ProdiID'].".xls");
header("Expires:0");
header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
header("Pragma: public");

// *** Parameters ***
$TahunID = GetSetVar('TahunID');
$ProdiID = GetSetVar('ProdiID');

$prodi = GetFields('prodi', "ProdiID", $_SESSION['ProdiID'],"*");
$Fakultas = GetaField('fakultas', "FakultasID", $prodi['FakultasID'],"Nama");
?><!-- 	
Author	: Arisal Yanuarafi
Start	: 06 November 2013 -->
<style>
.txt{
  mso-number-format:"\@";/*force text*/
}
</style>
    	<h2>Rata-rata Kehadiran Mahasiswa</h2>
<table>
    <tr>
    	<td>Fakultas</td>
        <td>: <?php echo $Fakultas?></td>
   </tr>
   		<td>Program Studi</td>
        <td>: <?php echo $prodi['Nama']?></td>
	</tr>
</table>
<table border=1>
	<tr>
    	<th>No.</th>
        <th>Dosen</th>
        <th>Matakuliah</th>
        <th>Kelas</th>
        <th>Kehadiran Dosen</th>
        <th>Kehadiran Mhsw</th>
    </tr>
<?php
	$s = "SELECT j.Nama,j.Kehadiran,j.JadwalID,d.Nama as _DSN,k.Nama as _KLS from jadwal j
			LEFT OUTER JOIN dosen d on d.Login=j.DosenID
			LEFT OUTER JOIN kelas k on k.KelasID=j.NamaKelas where
			j.ProdiID = '$_SESSION[ProdiID]' and j.TahunID = '$_SESSION[TahunID]' order by j.Nama";
	$r = _query($s);$n=0;
	while ($w = _fetch_array($r)) {
		$n++;
		$hdr_mhsw = GetaField('krs',"JadwalID",$w['JadwalID'],"SUM(_Presensi)/COUNT(MhswID)");
		//$_hdr_mhsw = $hdr_mhsw / $w['Kehadiran'];
		$_hdr_mhsw = ($hdr_mhsw / $w['Kehadiran']) * 100;
		echo "<tr>
						<td>$n</td>
						<td class='txt'>$w[_DSN]</td>
						<td>$w[Nama]</td>
						<td>$w[_KLS]</td>
						<td>$w[Kehadiran]</td>
						<td>".number_format($_hdr_mhsw,0)." %</td>
				</tr>";
	}
					
					
					
					
