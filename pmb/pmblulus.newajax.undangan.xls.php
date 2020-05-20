<?php
  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";

$gelombang = GetaField('pmbperiod', "KodeID='".KodeID."' and NA", 'N', "PMBPeriodID");
$namafile = "daftar-cama-lulus-undangan-$gelombang.xls";
header("Content-type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename=$namafile");
header("Expires:0");
header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
header('Content-Transfer-Encoding: text'); 
header("Pragma: public");
?>
<style>
table,td,th{font-family:'Trebuchet MS'; vertical-align:top;}
th{background:#CCCCCC; font-weight:bold; text-transform:uppercase;}
</style>
<?php

echo "<table><tr><td align=left colspan=5><h2>Lampiran 1. Daftar Calon Mahasiswa Universitas Bung Hatta yang Dinyatakan Lulus Seleksi Gelombang II Jalur Undangan</h2></td></tr></table>
<table border=1><tr>
		<th align=center>PRODI</th>
		<th align=center>No.Daftar</th>
		<th align=center>Nama</th>
		<th align=center>Handphone</th>
		</tr>";
$s="select * from pmb where PMBPeriodID='$gelombang' and LulusUjian='Y' and AplikanID not like '14%' order by ProdiID, AplikanID";
$r=_query($s);
$n=0;
while ($w=_fetch_array($r)) {
$n++;
$hp = $w['Telepon'].'/'.$w['Handphone'];
$prodi = GetFields('prodi',"ProdiID",$w['ProdiID'],'Nama,JenjangID,KodeLama');
$jenjang = GetaField('jenjang',"JenjangID",$prodi['JenjangID'],'Nama');
$sekolah = GetaField('asalsekolah',"SekolahID",$w['AsalSekolah'],'Nama');

echo "<tr>
		<td align=center>$prodi[KodeLama]</td>
		<td>$w[AplikanID]</td>
		<td>".strtoupper($w['Nama'])."</td>
		<td>$w[Handphone]</td>
	</tr>";
		}
echo "</table>";
?>