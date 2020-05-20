<?php
TampilkanJudul('Proses Data Evaluasi Dosen');
$gos = (empty($_REQUEST['gos']))? "TampilkanPilihan" : $_REQUEST['gos'];
$gos();

function TampilkanPilihan(){
	  $s = "Select TahunID, Nama from dosenevaluasi";
	  $r = _query($s);
	  while ($w = _fetch_array($r)) {
	  if ($_SESSION['prcevTahunID']==$w['TahunID']) {
	  $optTahun .= "<option value='$w[TahunID]' selected>$w[TahunID] - $w[Nama]</option>";
	  }
	  else $optTahun .= "<option value='$w[TahunID]'>$w[TahunID]  - $w[Nama]</option>";
	  }
	  // Prodi
	  $s = "Select ProdiID, Nama from prodi where NA='N'";
	  $r = _query($s);
	  while ($w = _fetch_array($r)) {
	  if ($_SESSION['prcevProdiID']==$w['ProdiID']) {
	  $optProdi .= "<option value='$w[ProdiID]' selected>$w[Nama]</option>";
	  }
	  else $optProdi .= "<option value='$w[ProdiID]'>$w[Nama]</option>";
	  }
	echo "<form action=? method='post'>
	<table width=600 class=box align=center>
	<input type='hidden' value='Proses' name='gos'>
	<tr><td class=inp>Tahun Evaluasi:</td>
		<td class='ul1'><select name='prcevTahunID'>$optTahun</td></tr>
	<tr><td class=inp>Prodi:</td>
		<td class='ul1'><select name='prcevProdiID'>$optProdi</td></tr>
	<tr><td class=ul1 colspan=2 align=center><input type='submit' value='Proses'></td></tr></table>";
}

function Proses(){
	$TahunID = GetSetVar('prcevTahunID');
	$ProdiID = GetSetVar('prcevProdiID');
$End = GetaField('jadwal', "ProdiID='$_SESSION[prcevProdiID]' and TahunID", $_SESSION['prcevTahunID'],"COUNT(JadwalID)");
$_SESSION['start'] = 0;
$_SESSION['end'] = $End;
BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=ProsesArray", 700);
}
function ProsesArray(){
	$Proses = $_SESSION['start']+10;
	while ($_SESSION['start'] < $Proses) {
	$JadwalID = GetaField('jadwal', "ProdiID='$_SESSION[prcevProdiID]' and TahunID", $_SESSION['prcevTahunID'], "JadwalID", "Order by JadwalID", '', "limit $_SESSION[start],1");
	$cek = GetaField('dosen_evaluasi_nilai', "JadwalID", $JadwalID, "JadwalID");
	if (empty($cek)){
		$insert = _query('INSERT INTO dosen_evaluasi_nilai (JadwalID) value('.$JadwalID.')');	
	}
	$s = "SELECT * from dosenevaluasi_butir";
  	$r = _query($s);
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
		$UPDATE = _query("UPDATE dosen_evaluasi_nilai set n$w[ButirID]='$Nilai' where JadwalID = '$JadwalID'");
	}
	$_SESSION['start']++;
	}
	if ($_SESSION['start']>=$_SESSION['end']) {
		BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=TampilkanPilihan", 5000);
	}else{
		BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=ProsesArray", 100, "<br />Memproses data ke $_SESSION[start] dari $_SESSION[end]");
	}
}