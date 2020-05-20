<?php
  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";

header("Content-type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename=rekap-mhs.xls");
header("Expires:0");
header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
header("Pragma: public");
?><!-- 	
Author	: Arisal Yanuarafi
Start	: 28 Mei 2012 -->

<style>
table,font { font-family:Trebuchet MS; line-height:100%; }
.header{ font-family:Times; font-size:32px; line-height:90%; }
.garis {height:0px; line-height:0px;}
</style>
<?php



  $ProdiID = $_GET['prd'];
  $TahunID = $_GET['thn'];
  $ThnAkd = $_GET['thnakd'];
  $PrgID = $_GET['prg'];
   $whr = array();
  //cek bayar
  $_SESSION['_Bayar'] = ($_GET['byr'] == 'Y')? 'Y' : 'N';
   	if ($_SESSION['_Bayar'] == 'Y')   $whr[] = "(khs.Bayar>0 or khs.Potongan>0)";
	//cek krs	
	$_SESSION['_KRS'] = $_GET['krs'];
		if ($_SESSION['_KRS']==1) 
		{ 
		$whr[]= "khs.SKS>0";
		}
		elseif ($_SESSION['_KRS']==2) 
		{ 
		$whr[] = "khs.SKS=0";
		}
	//cek prodi
	if (!empty($ProdiID)) $whr[] = "mhsw.ProdiID='$ProdiID'";
	//cek program
	if (!empty($PrgID)) $whr[] = "mhsw.ProgramID='$PrgID'";
	//cek tahun akademik
	if (!empty($ThnAkd)) $whr[] = "khs.TahunID='$ThnAkd'";
		
		  	$_whr = implode(' and ', $whr);
 			$_whr = (empty($_whr))? '' : ' and ' . $_whr;
 if (!empty($TahunID)) {
  if ($TahunID=='k2000') {
    $s = "SELECT khs.MhswID, mhsw.Nama, khs.SKS, khs.Biaya, khs.Bayar,khs.Potongan
			FROM `khs` , mhsw
			WHERE mhsw.MhswID = khs.MhswID
			$_whr
			AND mhsw.`TahunID` < 2000 order by khs.MhswID";

	}
	else {
  $s = "SELECT khs.MhswID, mhsw.Nama, khs.SKS, khs.Biaya, khs.Bayar,khs.Potongan
			FROM `khs` , mhsw
			WHERE mhsw.MhswID = khs.MhswID
			$_whr
			AND mhsw.`TahunID` LIKE '$TahunID%' order by khs.MhswID";
			}
}
else
{
  $s = "SELECT khs.MhswID, mhsw.Nama, khs.SKS, khs.Biaya, khs.Bayar,khs.Potongan
			FROM `khs` , mhsw
			WHERE mhsw.MhswID = khs.MhswID
			$_whr
			order by khs.MhswID";
			}


  $r = _query($s);
  echo "<table border=1 align=center><tr>
  <td><b>No.</td><td><b>No. BP</td><td><b>Nama</td><td><b>Asal Sekolah</b><td width=50 align=center><b>SKS</td><td width=80 align=center><b>Biaya</td><td width=80 align=center><b>Bayar</td><td width=80 align=center><b>Potongan</td></tr>";
  $n=0;
  while ($w = _fetch_array($r)) {
  $n++;
  echo "<tr><td align=center>$n</td><td>$w[MhswID]</td><td>$w[Nama]</td><td>$w[AsalSekolah]<td align=center>$w[SKS]</td><td align=right>$w[Biaya]</td><td align=right>$w[Bayar]</td><td align=right>$w[Potongan]</td></tr>";
  }
  ?>

