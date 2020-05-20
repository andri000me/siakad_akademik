<?php
session_start();
  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
header("Content-type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename=ipsemester-".$_SESSION['TahunID']."-".$_SESSION['ProdiID'].".xls");
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
    	<h2>Laporan IP Semester Mahasiswa</h2>
<table>
    <tr>
    	<td>Fakultas</td>
        <td>: <?php echo $Fakultas?></td>
   </tr>
   		<td>Program Studi</td>
        <td>: <?php echo $prodi['Nama']?></td>
	</tr>
</table>
<table>
	<tr>
    	<th>No.</th>
        <th>NPM</th>
        <th>Nama</th>
        <th>SKS Semester</th>
        <th>IP Semester</th>
        <th>IPK</th>
    </tr>
<?php
	$whr = (!empty($_SESSION['ProdiID']) ? " and m.ProdiID = '$_SESSION[ProdiID]'":"");
	$s = "SELECT k.MhswID, m.Nama, h.IP from krs k left outer join mhsw m on m.MhswID=k.MhswID
			Left outer join khs h on h.KHSID=k.KHSID where
			k.TahunID = '$_SESSION[TahunID]' and k.SKS > 0 $whr group by k.MhswID order by m.TahunID,k.MhswID";
	$r = _query($s);$n=0;
	while ($w = _fetch_array($r)) {
		$Semester = GetFields('krs', "TahunID='$_SESSION[TahunID]' AND MhswID", $w['MhswID'], "sum(SKS) as _SKS, (sum(BobotNilai*SKS)/sum(SKS)) as IPS");
		if ($Semester['IPS'] > 0){
			$n++;
		echo "<tr>
					<td>$n</td>
					<td class='txt'>$w[MhswID]</td>
					<td>$w[Nama]</td>
					<td>$Semester[_SKS]</td>
					<td>".number_format($Semester[IPS],2)."</td>
					<td>$w[IP]</td>
				</tr>";
		}
	}