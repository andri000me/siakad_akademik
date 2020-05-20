<?php  
// BY. ARISAL YANUARAFI, START 2 JULI 2013
error_reporting(E_ALL);
  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb2.php";

  $ProdiID = GetSetVar('prd');
  $ProgramID = GetSetVar('prg');
  $TahunID = GetSetVar('thn');

$nmfile = strtolower(str_replace(" ","_","$ProdiID -$ProgramID-$TahunID.xls"));
header("Content-type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename=$nmfile");
header("Expires:0");
header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
header("Pragma: public");
?>
<style>
table,font { font-family:'Calibri'; line-height:100%; }
td {vertical-align:top}
.header,ttl{ font-family:'Calibri'; font-size:14px; line-height:90%; }
.garis {height:0px; line-height:0px;}
.text{
  mso-number-format:"\@";/*force text*/
}
</style>
<?php
echo "<table border=0>
  	<tr>
		<td colspan=2><font size=3>Prodi</td>
		<td colspan=8><font size=3>: ".GetaField('prodi', "ProdiID", $ProdiID, "Nama")."</td>
		</tr>
		<tr>
		<td colspan=2><font size=3>Program</td>
		<td colspan=8><font size=3>: $ProgramID</td>
		</tr>
		<tr>
		<td colspan=2><font size=3>Tahun</td>
		<td colspan=8><font size=3>: $TahunID</td>
		<tr>
		</tr>
		</tr></table>";
  echo "<table border=1>
  		<tr>
			<th rowspan=2>No.</th>
			<th rowspan=2>Nama</th>
			<th rowspan=2>Matakuliah</th>
			<th rowspan=2>Responden</th>
			<th colspan=4>Uraian</th>
			<th rowspan=2>Rata-rata</th>
			<th rowspan=2>Keterangan</th>
			<th colspan=10>Rekap Penilaian Mahasiswa</th>
		</tr>
		<tr>
			<th>Perencanaan Perkuliahan</th>
			<th>Keterampilan Mengajar</th>
			<th>Suasana Pembelajaran</th>
			<th>Kedisiplinan</th>
			<th>-</th>
			<th>E</th>
			<th>D</th>
			<th>C</th>
			<th>C+</th>
			<th>B-</th>
			<th>B</th>
			<th>B+</th>
			<th>A-</th>
			<th>A</th>
		</tr>";
  $s = "SELECT j.DosenID, d.Nama as DSN, j.Nama as MK, j.JadwalID from jadwal j left outer join dosen d on d.Login=j.DosenID where j.ProdiID='$ProdiID' and j.ProgramID='$ProgramID' and j.TahunID='$TahunID' order by d.Nama";
  $r = _query($s); $n =0;
   while ($w = _fetch_array($r)) {
	   $n++;
	   //$Responden = GetaField('dosenevaluasi_hasil', "ButirID='1' and JadwalID", $w['JadwalID'], "COUNT(LoginBuat)")+0; // Cek ulang
	   $Responden = GetaField('dosen_evaluasi_rekap', "JadwalID", $w['JadwalID'], "nMhsw")+0;
	    /*$A = GetaField('dosenevaluasi_hasil', 
				"ButirID in (1,2) and Jawaban='Y' and JadwalID", $w['JadwalID'], "(COUNT(ButirID)/$Responden)/2");
		$B = GetaField('dosenevaluasi_hasil', 
				"ButirID in (3,4,5,6,7,8) and Jawaban='Y' and JadwalID", $w['JadwalID'], "(COUNT(ButirID)/$Responden)/6");
		$C= GetaField('dosenevaluasi_hasil',"ButirID in (11,12,13,14,15,16,17,18,19,20,21,22,23,24,25) and  JadwalID", $w['JadwalID'],"(SUM(Jawaban)*0.25/$Responden)/15");
		$D = GetaField('dosenevaluasi_hasil', 
				"ButirID in (9,10,26,27,28) and JadwalID", $w['JadwalID'], "(SUM(Jawaban)*0.25/$Responden)/5");
		*/
		
		$A = GetaField('dosen_evaluasi_rekap d', 
				"d.JadwalID", $w['JadwalID'], "((d.n1+d.n2)/2)/$Responden")+0;
		$B = GetaField('dosen_evaluasi_rekap d', 
				"d.JadwalID", $w['JadwalID'], "((d.n3+d.n4+d.n5+d.n6+d.n7+d.n8)/6)/$Responden")+0;
		$C = GetaField('dosen_evaluasi_rekap d', 
				"d.JadwalID", $w['JadwalID'], "((d.n11+d.n12+d.n13+d.n14+d.n15+d.n16+d.n17+d.n18+d.n19+d.n20+d.n21+d.n22+d.n23+d.n24+d.n25)/15)/$Responden")+0;
		$D = GetaField('dosen_evaluasi_rekap d', 
				"d.JadwalID", $w['JadwalID'], "((d.n9+d.n10+d.n26+d.n27+d.n28)/5)/$Responden")+0;

		$Rata = ($A + $B + $C + $D)/4;
		$Keterangan = GradeDosen($Rata);

  echo "<tr>
  			<td>$n</td>
			<td>$w[DSN]</td>
			<td>$w[MK]</td>
			<td align=center>$Responden</td>
			<td>".number_format($A,2)."</td>
			<td>".number_format($B,2)."</td>
			<td>".number_format($C,2)."</td>
			<td>".number_format($D,2)."</td>
			<td>".number_format($Rata,2)."</td>
			<td>".$Keterangan."</td>
		";
		$s1 = "SELECT GradeNilai from krs where JadwalID='$w[JadwalID]'";
  $r1 = _query($s1); $_A=0;$_Am=0;$_Bp=0;$_Bm=0;$_B=0;$_Cp=0;$_C=0;$_D=0;$_E=0;$_m=0;
  while ($w1 = _fetch_array($r1)){
  	if ($w1['GradeNilai']=='A') $_A++;
  	if ($w1['GradeNilai']=='A-') $_Am++;
  	if ($w1['GradeNilai']=='B+') $_Bp++;
  	if ($w1['GradeNilai']=='B') $_B++;
  	if ($w1['GradeNilai']=='B-') $_Bm++;
  	if ($w1['GradeNilai']=='C+') $_Cp++;
  	if ($w1['GradeNilai']=='C') $_C++;
  	if ($w1['GradeNilai']=='D') $_D++;
  	if ($w1['GradeNilai']=='E') $_E++;
  	if ($w1['GradeNilai']=='-') $_m++;
  }
  echo "	<td>$_m</td>
			<td>$_E</td>
			<td>$_D</td>
			<td>$_C</td>
			<td>$_Cp</td>
			<td>$_Bm</td>
			<td>$_B</td>
			<td>$_Bp</td>
			<td>$_Am</td>
			<td>$_A</td>
			</tr>";
   }
  echo "</table>";
function GradeDosen($nilai){
	if ($nilai<2.5) {
		$huruf = "Kurang";
	}elseif ($nilai >= 2.5 && $nilai < 3){
		$huruf = "Cukup";
	}elseif ($nilai >= 3 && $nilai < 3.5) {
		$huruf = "Baik";	
	} elseif ($nilai >= 3.5){
		$huruf = "Sangat Baik";	
	}
	return $huruf;
}