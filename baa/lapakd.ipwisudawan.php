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
    	<h2>Laporan IPK Wisudawan</h2>
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
        <th>Total SKS</th>
        <th>IPK</th>
        <th>Status Awal</th>
    </tr>
<?php
	$whr = (!empty($ProdiID) ? "m.ProdiID = '$_SESSION[ProdiID]' and" : "");
	$s = "SELECT k.MhswID, m.Nama, m.ProdiID, p.JenjangID, m.StatusAwalID, m.IPK, m.TotalSKS
        from wisudawan k 
        left outer join mhsw m on m.MhswID=k.MhswID
        left outer join prodi p on p.ProdiID=m.ProdiID
        where
			$whr k.TahunID like '$TahunID%' and m.Nama!='' group by k.MhswID order by m.TahunID,k.MhswID";
	$r = _query($s);$n=0;
	while ($w = _fetch_array($r)) {
		$s1 = "SELECT MAX(k.BobotNilai) as BobotNilai,m.SKS from krs k,mk m,kurikulum u where
                    k.NA='N'   
                    AND  m.MKKode=k.MKKode 
                    AND u.KurikulumID=m.KurikulumID 
                    AND u.Nama like 'Kurikulum Default' 
                    AND k.BobotNilai > 0
                    AND k.Tinggi = '*'
                    AND m.NA='N'
                    AND k.MhswID='$w[MhswID]' group by k.Nama";
    $r1 = _query($s1);$bobot=0;$sks=0;
    while ($w1 = _fetch_array($r1)) {
        $bobot += $w1['BobotNilai']*$w1['SKS'];
        $sks += $w1['SKS'];
    }
    $ipk = $bobot/$sks;
    $ipk = number_format($ipk,2,".", ",");
      $n++;
		echo "<tr>
					<td>$n</td>
					<td class='txt'>$w[MhswID]</td>
					<td>$w[Nama]</td>
					<td>".($w['TotalSKS']==0 ? $sks:$w['TotalSKS'])."</td>
					<td>".$w['IPK']."</td>
          <td>$w[StatusAwalID]</td>
				</tr>";
	}
					
					
					
					
