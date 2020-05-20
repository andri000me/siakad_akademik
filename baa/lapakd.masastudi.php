<?php
session_start();
  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
header("Content-type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename=ipk-lulusan-".$_SESSION['TahunID']."-".$_SESSION['ProdiID'].".xls");
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
    	<h2>Laporan Masa Studi</h2>
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
        <th>Masa Studi</th>
        <th>Status Awal</th>
    </tr>
<?php
	$whr = (!empty($ProdiID) ? "m.ProdiID = '$_SESSION[ProdiID]' and" : "");
	$s = "SELECT k.MhswID, m.Nama, m.ProdiID, p.JenjangID, m.TahunID, left(k.TahunID,4) as Tamat,m.StatusAwalID
        from wisudawan k 
        left outer join mhsw m on m.MhswID=k.MhswID
        left outer join prodi p on p.ProdiID=m.ProdiID
        where
			$whr k.TahunID like '$TahunID%' and m.Nama!='' group by k.MhswID order by m.TahunID desc,k.MhswID";
	$r = _query($s);$n=0;
	while ($w = _fetch_array($r)) {
		$n++;
    $Semester = substr($TahunID, 4,1);
    $Awal     = substr($w['TahunID'], 0,4);
    $MasaStudi = $w['Tamat']-$Awal;
    $MasaStudi = $MasaStudi.($Semester == 2 ? ",5":"");
		echo "<tr>
					<td>$n</td>
					<td class='txt'>$w[MhswID]</td>
					<td>$w[Nama]</td>
          <td>$MasaStudi</td>
          <td>$w[StatusAwalID]</td>
				</tr>";
	}
					
					
					
					
