<?php
  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";

$gelombang = GetaField('pmbperiod', "KodeID='".KodeID."' and NA", 'N', "PMBPeriodID");
$tahap = "Gel2";
$namafile = "daftar-cama-lulus-reguler-$gelombang.xls";
header("Content-type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename=$namafile");
header("Expires:0");
header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
header('Content-Transfer-Encoding: text'); 
header("Pragma: public");
?>
<style>
table,td,th{font-family:'Trebuchet MS'; vertical-align:top;mso-number-format:'\@';}
th{background:#CCCCCC; font-weight:bold; text-transform:uppercase;}
</style>
<?php

echo "<table><tr><td align=left colspan=5><h2>Lampiran 1. Daftar Calon Mahasiswa Universitas Bung Hatta yang Dinyatakan Lulus Seleksi Gelombang II Reguler</h2></td></tr></table>
<table border=1><tr>
		<th align=center>PRODI</th>
		<th align=center>Username</th>
		<th align=center>No.Daftar</th>
		<th align=center>Nama</th>	
		</tr>";
$s="select pmb.*, a.NilaiRapor,a.NilaiSekolah from pmb left outer join aplikan a on a.AplikanID = pmb.AplikanID and a.PMBID = pmb.PMBID where pmb.PMBPeriodID='$gelombang' and pmb.NA='N' AND a.Hint='$tahap' and pmb.MhswID is NULL and pmb.LulusUjian='Y' order by pmb.LulusUjian desc,pmb.ProdiID,pmb.Nama";
$r=_query($s);
$n=0;
while ($w=_fetch_array($r)) {
$n++;
$hp = $w['Telepon'].'/'.$w['Handphone'];
$prodi = GetFields('prodi',"ProdiID",$w['ProdiID'],'Nama,JenjangID,KodeLama');
$w['Nama'] = str_replace("'", "`", $w['Nama']);

echo "<tr>
		<td align=center>$prodi[KodeLama]</td>
		<td>$w[AplikanID]</td>
		<td>$w[PMBID]</td>
		<td>".strtoupper($w['Nama'])."</td>
	</tr>";
		}
echo "</table>";
?>