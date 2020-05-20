<?php  error_reporting(0);
// BY. ARISAL YANUARAFI, START 2 JULI 2013
  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb2.php";
  
  $JadwalID = GetSetVar('id');
  $jdwl = GetFields("jadwal j left outer join dosen d on d.Login=j.DosenID
  								left outer join prodi p on p.ProdiID=j.ProdiID
  								left outer join hari h on h.HariID=j.HariID", "JadwalID",$JadwalID, "j.TahunID, concat(d.Gelar1,' ',d.Nama,', ',d.Gelar) as DSN,j.Nama as MK,j.JamMulai,j.JamSelesai, j.JumlahMhsw,h.Nama as Harinya, d.Nama as NMDosen, p.Nama as PRD");
$nmfile = strtolower(str_replace(" ","_","$jdwl[NMDosen]-$jdwl[MK]-$jdwl[TahunID].xls"));
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
  
  $s = "SELECT * from dosenevaluasi_butir";
  $r = _query($s);
  echo "<table border=0>
  	<tr>
		<td colspan=2><font size=3>Nama Dosen</td>
		<td colspan=8><font size=3>: $jdwl[DSN]</td>
		</tr>
		<tr>
		<td colspan=2><font size=3>Matakuliah</td>
		<td colspan=8><font size=3>: $jdwl[MK]</td>
		</tr>
		<tr>
		<td colspan=2><font size=3>Hari/Jam</td>
		<td colspan=8><font size=3>: $jdwl[Harinya], $jdwl[JamMulai] - $jdwl[JamSelesai]</td>
		</tr>
		<tr>
		<td colspan=2><font size=3>Jumlah Mhsw/Tahun</td>
		<td colspan=8><font size=3>: $jdwl[JumlahMhsw] / $jdwl[TahunID] </td>
		</tr>
		<td colspan=2><font size=3>Prodi</td>
		<td colspan=8><font size=3>: $jdwl[PRD] </td>
		</tr></table>
		<br />
		<table border=0>
		<tr><td colspan=10><font size=+2><b>Rapor Dosen</b></font></td></tr></table>";
		$Responden = GetaField('dosen_evaluasi_rekap', "JadwalID", $JadwalID, "nMhsw")+0;
		/*$A = GetaField('dosenevaluasi_hasil d', 
				"d.ButirID in (1,2) and d.Jawaban='Y' and d.JadwalID", $JadwalID, "(COUNT(d.ButirID)/$Responden)/2")*3.57;
		$B = GetaField('dosenevaluasi_hasil d', 
				"d.ButirID in (3,4,5,6,7,8) and d.Jawaban='Y' and d.JadwalID", $JadwalID, "(COUNT(d.ButirID)/$Responden)/6")*3.57;
		$C= GetaField('dosenevaluasi_hasil d',"d.ButirID in (11,12,13,14,15,16,17,18,19,20,21,22,23,24,25) and  d.JadwalID", $JadwalID,"(SUM(d.Jawaban)*0.25/$Responden)/15")*3.57;
		$D = GetaField('dosenevaluasi_hasil d', 
				"d.ButirID in (9,10,26,27,28) and d.JadwalID", $JadwalID, "(SUM(d.Jawaban)*0.25/$Responden)/5")*3.57; */

		$A = GetaField('dosen_evaluasi_rekap d', 
				"d.JadwalID", $JadwalID, "((d.n1+d.n2)/2)/$Responden")+0;
		$B = GetaField('dosen_evaluasi_rekap d', 
				"d.JadwalID", $JadwalID, "((d.n3+d.n4+d.n5+d.n6+d.n7+d.n8)/6)/$Responden")+0;
		$C = GetaField('dosen_evaluasi_rekap d', 
				"d.JadwalID", $JadwalID, "((d.n11+d.n12+d.n13+d.n14+d.n15+d.n16+d.n17+d.n18+d.n19+d.n20+d.n21+d.n22+d.n23+d.n24+d.n25)/15)/$Responden")+0;
		$D = GetaField('dosen_evaluasi_rekap d', 
				"d.JadwalID", $JadwalID, "((d.n9+d.n10+d.n26+d.n27+d.n28)/5)/$Responden")+0;
  	echo "<table border=1>
	<tr>
		<th colspan=2><font size=3>Uraian</th>
		<th colspan=4>Nilai Angka</th>
		<th colspan=4>Nilai Huruf</th>
	</tr>
	<tr>
		<td colspan=2><font size=3>A. Perencanaan Perkuliahan</td>
		<td colspan=4 align=center><font size=3>".number_format($A,2)."</td>
		<td colspan=4>".GradeDosen($A)."</td>
		</tr>
	<tr>
		<td colspan=2><font size=3>B. Keterampilan Mengajar</td>
		<td colspan=4 align=center><font size=3>".number_format($B,2)."</td>
		<td colspan=4>".GradeDosen($B)."</td>
		</tr>
	<tr>
		<td colspan=2><font size=3>C. Suasana Pembelajaran</td>
		<td colspan=4 align=center><font size=3>".number_format($C,2)."</td>
		<td colspan=4>".GradeDosen($C)."</td>
		</tr>
	<tr>
		<td colspan=2><font size=3>D. Kedisiplinan</td>
		<td colspan=4 align=center><font size=3>".number_format($D,2)."</td>
		<td colspan=4>".GradeDosen($D)."</td>
		</tr>
	<tr>
		<th colspan=2><font size=3>Rata-rata</th>
		<th colspan=4 align=center><font size=3>".number_format(($A + $B + $C + $D)/4,2)."</th>
		<th colspan=4>".GradeDosen(($A + $B + $C + $D)/4)."</th>
		</tr>
	</table>
	<br /><br />
	<font size='+2'><b>Detail Penilaian</b></font>
	<table border=1>
  			<tr>
				<th rowspan=2 width=20>No.</th>
				<th rowspan=2 width=500>Pertanyaan</th>
				<th colspan=6 align=center>Jawaban</th>
				<th rowspan=2 width=20>Kode</th>
				<th rowspan=2 width=20>Nilai</th>
			</tr>
			<tr>
				<th>Ya</th>
				<th>Tidak</th>
				<th>Kurang</th>
				<th>Cukup</th>
				<th>Baik</th>
				<th>Sangat Baik</th>
			</tr>";
	$no = 0;
	$cek = GetaField('dosen_evaluasi_nilai', "JadwalID", $JadwalID, "JadwalID");
	if (empty($cek)){
		$insert = _query('INSERT INTO dosen_evaluasi_nilai (JadwalID) value('.$JadwalID.')');	
	}
  while ($w = _fetch_array($r)) {
  	$s2 = "SELECT SUM(IF(Jawaban = 'Y',1,0)) as Y,
					SUM(IF(Jawaban = 'N',1,0)) as N,
					SUM(IF(Jawaban = '1',1,0)) as n1,
					SUM(IF(Jawaban = '2',1,0)) as n2,
					SUM(IF(Jawaban = '3',1,0)) as n3,
					SUM(IF(Jawaban = '4',1,0)) as n4
	 from dosenevaluasi_hasil where ButirID='".$w['ButirID']."' and JadwalID=".$JadwalID;
	$r2 = _query($s2); $Y=0;$N=0;$Kurang=0;$Cukup=0;$Baik=0;$SangatBaik=0;
	while ($w2 = _fetch_array($r2)) {
		if ($w['JenisJawaban']=='D') {
			$Y = ($w2['Y']);
			$N = ($w2['N']);
			$Kurang = '-';
			$Cukup = '-';
			$Baik = '-';
			$SangatBaik = '-';
		}
		elseif ($w['JenisJawaban']=='G') {
			$Y = '-';
			$N = '-';
			$Kurang = ($w2['n1']);
			$Cukup = ($w2['n2']);
			$Baik = ($w2['n3']);
			$SangatBaik = ($w2['n4']);
		}
	}
	$no++;
	echo "<tr>
				<td width=20>".$no."</td>
				<td width=500>".$w['Pertanyaan']."</td>
				<td align=center>".$Y."</td>
				<td align=center>".$N."</td>
				<td align=center>".$Kurang."</td>
				<td align=center>".$Cukup."</td>
				<td align=center>".$Baik."</td>
				<td align=center>".$SangatBaik."</td>
				<td align=center>".$w['Kelompok']."</td>";
	$J = $Y + $N + 0;
		if ($w['JenisJawaban']=='D') { 
			$Nilai = ($Y * 3.57)/$J;
		}
		elseif ($w['JenisJawaban']=='G'){
			$J = $Kurang + $Cukup + $Baik + $SangatBaik + 0;
			$nD = $Kurang * 0.25 * 3.57;
			$nC = $Cukup * 0.5 * 3.57;
			$nB = $Baik * 0.75 * 3.57;
			$nA = $SangatBaik * 1 * 3.57;
			$SUM = $nA + $nB + $nC +$nD + 0;
			$Nilai = $SUM / $J;
		}
		echo "<td align=center>".number_format($Nilai,2)."</td>
			</tr>";
		//$UPDATE = _query("UPDATE dosen_evaluasi_nilai set n$w[ButirID]='$Nilai' where JadwalID = '$JadwalID'");
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