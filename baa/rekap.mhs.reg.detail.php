<?php
  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb2.php";
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
.text{
  mso-number-format:"\@";/*force text*/
}
</style>
<?php

session_start();
	
  $ProdiID = sqling($_GET['prd']);
  $TahunAwal = GetSetVar('TahunAwal');
  $TahunID = sqling($_GET['thn']);
  $ThnAkd = sqling($_GET['thnakd']);
  $PrgID = sqling($_GET['prg']);
  $StatAwal = GetSetVar('StatusAwalID');
   $whr = array();
  //cek bayar
  $_SESSION['_Bayar'] = ($_GET['byr'] == 'Y')? 'Y' : 'N';
   	if ($_SESSION['_Bayar'] == 'Y')   $whr[] = "((khs.Bayar>0) or mhsw.StatusAwalID='M' or mhsw.StatusAwalID='S' or mhsw.ProgramID='M' or mhsw.ProgramID='P' or p.FakultasID='08')";
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
	//cek Status Awal
	if (!empty($StatAwal)) $whr[] = "mhsw.StatusAwalID='$StatAwal'";
	//cek program
	if (!empty($PrgID)) $whr[] = "mhsw.ProgramID='$PrgID'";
	//cek tahun akademik
	if (!empty($ThnAkd)) $whr[] = "khs.TahunID like '$ThnAkd%'";
		
		  	$_whr = implode(' and ', $whr);
 			$_whr = (empty($_whr))? '' : ' and ' . $_whr;
 if (!empty($TahunID)) {
  if ($TahunID=='k2000') {
    $s = "SELECT a.Nama as _AsalSekolah,khs.MhswID, mhsw.TahunID as _THN, mhsw.Nama, khs.SKS, mhsw.ProdiID,  mhsw.ProgramID, mhsw.Kelamin, khs.Biaya, khs.Bayar,khs.Potongan,s.Nama as Status,p.Nama as PRD
			FROM `khs` , mhsw left outer join statusawal s on s.StatusAwalID=mhsw.StatusAwalID
      left outer join prodi p on p.ProdiID=mhsw.ProdiID
      left outer join asalsekolah a on a.SekolahID = mhsw.AsalSekolah
			WHERE mhsw.MhswID = khs.MhswID
			$_whr
			AND mhsw.`TahunID` < $TahunAwal order by p.FakultasID,p.ProdiID,khs.MhswID";

	}
	else {
  $s = "SELECT a.Nama as _AsalSekolah,khs.MhswID, mhsw.TahunID as _THN, mhsw.Nama,mhsw.ProdiID,  mhsw.ProgramID, khs.SKS,mhsw.Kelamin, khs.Biaya, khs.Bayar,khs.Potongan,s.Nama as Status,p.Nama as PRD
			FROM `khs` , mhsw left outer join statusawal s on s.StatusAwalID=mhsw.StatusAwalID
      left outer join prodi p on p.ProdiID=mhsw.ProdiID
      left outer join asalsekolah a on a.SekolahID = mhsw.AsalSekolah
			WHERE mhsw.MhswID = khs.MhswID
			$_whr
			AND mhsw.`TahunID` LIKE '$TahunID%' order by p.FakultasID,p.ProdiID,khs.MhswID";
			}
}
else
{
  $s = "SELECT a.Nama as _AsalSekolah,khs.MhswID, mhsw.TahunID as _THN, mhsw.ProdiID, mhsw.ProgramID, mhsw.Nama, khs.SKS,mhsw.Kelamin, khs.Biaya, khs.Bayar,khs.Potongan,s.Nama as Status,p.Nama as PRD
			FROM `khs` , mhsw left outer join statusawal s on s.StatusAwalID=mhsw.StatusAwalID
      left outer join prodi p on p.ProdiID=mhsw.ProdiID
      left outer join asalsekolah a on a.SekolahID = mhsw.AsalSekolah
			WHERE mhsw.MhswID = khs.MhswID
			$_whr
			order by p.FakultasID,p.ProdiID,khs.MhswID";
			}


  $r = _query($s);
  echo "<table border=1 align=center><tr>
  <td><b>No.</td><td>
  <b>No. BP</td>
  <td><b>Nama</td>
  <td>Kelamin</td><td>
  <b>Jurusan</td>
  <td><b>Status Awal</b></td>
  <td><b>Program</b></td>
  <td><b>Asal SMA</b></td>
  <td width=50 align=center><b>SKS</td>
  <td width=80 align=center><b>Biaya</td>
  <td width=80 align=center><b>Bayar</td>
  <td width=80 align=center><b>Potongan</td></tr>";
  $n=0;
  while ($w = _fetch_array($r)) {
  $n++;
  echo "<tr>
  <td align=center>$n</td>
  <td class=text>$w[MhswID]</td>
  <td>$w[Nama]</td>
  <td>$w[Kelamin]</td>
  <td>$w[PRD]</td>
  <td>".$w['Status']."</td>
  <td>".$w['ProgramID']."</td>
  <td>".$w['_AsalSekolah']."</td>
  <td align=center>$w[SKS]</td>
  <td align=right>$w[Biaya]</td>
  <td align=right>$w[Bayar]</td>
  <td align=right>$w[Potongan]</td>
  <td align=center>$w[_THN]</td></tr>";
  }
  ?>

