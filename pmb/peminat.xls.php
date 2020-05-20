<?php
  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";

$gelombang = GetaField('pmbperiod', "KodeID='".KodeID."' and NA", 'N', "PMBPeriodID");
$namafile = "daftar-aplikan.xls";
header("Content-type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename=$namafile");
header("Expires:0");
header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
header('Content-Transfer-Encoding: text'); 
header("Pragma: public");
?>
<style>
table,td,th{font-family:'Trebuchet MS'; vertical-align:middle;}
th{background:#CCCCCC; font-weight:bold; text-transform:uppercase;}
</style>
<?php

echo "<table><tr><td align=center colspan=9><h2>Daftar Aplikan Gelombang $gelombang</h2></td></tr></table>
<table border=1><tr><th align=center>No</th><th align=center>No. Aplikan</th><th align=center>Nama</th>
		<th align=center>Pilihan1</th>
		<th align=center>Pilihan2</th>
		<th align=center>Jurusan</th>
		<th align=center>Program</th>
		<th align=center>Asal Sekolah</th>
		<th align=center>Nilai Sekolah</th>
		<th align=center>Nilai Rapor</th>
		<th align=center>Handphone</th>
		<th align=center>Alamat</th>
		<th align=center>Status</th></tr>";
$s="select a.*,p1.Nama as Pilihan_1, p2.Nama as Pilihan_2, p.Pilihan1, p.Pilihan2 from aplikan a 
		left outer join pmb p on p.AplikanID=a.AplikanID and p.PMBID=a.PMBID
		left outer join prodi p1 on p1.ProdiID=p.Pilihan1
		left outer join prodi p2 on p2.ProdiID=p.Pilihan2
		where a.PMBPeriodID='$gelombang' and a.NA='N' order by a.AplikanID";
$r=_query($s);
$n=0;
while ($w=_fetch_array($r)) {
$n++;
$hp = $w['Telepon'].'/'.$w['Handphone'];
$prodi = GetFields('prodi',"ProdiID",$w['ProdiID'],'Nama,JenjangID');
$program = GetaField('program',"ProgramID",$w['ProgramID'],'Nama');
$jenjang = GetaField('jenjang',"JenjangID",$prodi['JenjangID'],'Nama');
$status = GetaField('statusaplikan',"StatusAplikanID",$w['StatusAplikanID'],'Nama');
$sekolah = GetaField('asalsekolah',"SekolahID",$w['AsalSekolah'],'Nama');
if (empty($sekolah)) {
$sekolah = GetaField('perguruantinggi',"PerguruanTinggiID",$w['AsalSekolah'],'Nama');
}
echo "<tr><td align=center>$n.</td>
		<td>$w[AplikanID]</td>
		<td>$w[Nama]</td>
		<td>$w[Pilihan1] - $w[Pilihan_1]</td>
		<td>$w[Pilihan2] - $w[Pilihan_2]</td>
		<td>$prodi[Nama] $jenjang</td>
		<td>$program</td>
		<td>$sekolah</td>
		<td align=center>$w[NilaiSekolah]</td>
		<td align=center>$w[NilaiRapor]</td>
		<td>$hp</td>
		<td>$w[Alamat]</td>
		<td><strong>$w[StatusAplikanID]</strong> ($status)</td></tr>";
		}
echo "</table>";
?>