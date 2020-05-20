<?php
session_start();
  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
header("Content-type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename=krs-semester-".$_SESSION['TahunID']."-".$_SESSION['ProdiID'].".xls");
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
    	<h2>Laporan SKS Semester Mahasiswa</h2>
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
        <th>NPM</th>
        <th>Nama</th>
        <th>Matakuliah/Nilai</th>
        <th>Total<br>SKS</th>
    </tr>
<?php
	$s = "SELECT k.MhswID, m.Nama as _Nama, k.Nama as Matakuliah, k.SKS, m.StatusAwalID from krs k left outer join mhsw m on m.MhswID=k.MhswID where
			m.ProdiID = '$_SESSION[ProdiID]' and k.TahunID = '$_SESSION[TahunID]' Group by k.MhswID order by m.TahunID,k.MhswID,k.Nama";
	$r = _query($s);$n=0;
	while ($w = _fetch_array($r)) {
		$n++;
		echo "<tr>
						<td>$n</td>
						<td class='txt'>$w[MhswID]</td>
						<td>$w[_Nama]</td>";

		$s1 = "SELECT k.MhswID, k.GradeNilai,m.Nama as _Nama, k.Nama as Matakuliah, k.SKS, m.StatusAwalID from krs k left outer join mhsw m on m.MhswID=k.MhswID where
			m.ProdiID = '$_SESSION[ProdiID]' and k.TahunID = '$_SESSION[TahunID]' and k.MhswID='$w[MhswID]' order by m.TahunID,k.MhswID,k.Nama";
		$r1 = _query($s1);$n=0;
		echo "<td>";$sks = 0;$nilai='';
		while ($w1 = _fetch_array($r1)) {
			$sks += $w1['SKS'];
			echo "$w1[Matakuliah] ($w1[SKS] SKS / $w1[GradeNilai]) |";
		}
		echo "</td><td>$sks</td></tr>";
	}
					
					
					
					
