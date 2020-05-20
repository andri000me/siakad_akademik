<?php
// Author : Arisal Yanuarafi
// 17 Januari 2014
error_reporting(0);
$cek = ($_SESSION['_LevelID']=='120'? "":die('Anda tidak berhak mengakses modul ini.'));
$TahunID = GetaField('dosenevaluasi', "NA='N' AND KodeID", KodeID, 'TahunID');
if (!empty($_POST['JadwalID']) && isset($_POST['JadwalID']) && !empty($TahunID)) {
	$cek = '';
	$insert = array(); $IPBuat = $_SERVER['REMOTE_ADDR'];
	$s3 = "SELECT * from dosenevaluasi_butir where TahunID='$TahunID'";
	$r3 = _query($s3);
	while ($w3 = _fetch_array($r3)){
		$cek .= (empty($_POST[$w3['ButirID']]))? "BLANK":"";
		if (!empty($_POST[$w3['ButirID']])){
			$insert[]= "INSERT INTO dosenevaluasi_hasil (JadwalID,ButirID,Jawaban,IPBuat,LoginBuat,TanggalBuat)
						values('".$_POST['JadwalID']."', '".$w3['ButirID']."', '".$_POST[$w3['ButirID']]."',
						'".$IPBuat."','".$_SESSION['_Login']."',now())";
		}
	}
	$insJadwal = _query("INSERT IGNORE into dosen_evaluasi_rekap(JadwalID) value ('$_POST[JadwalID]')");
	if (empty($cek)) {
		foreach($insert as $id){
			_query($id);
		}
		$updQuery = '';
		for ($x = 1; $x <= 8; $x++) {
			$updQuery .= ($_POST[$x]=='Y') ? "n$x=(n$x + 3.57),":"n$x=(n$x + 0),";
		}
		for ($x = 9; $x <= 28; $x++) {
			$updQuery .= "n$x=(n$x + ($_POST[$x]*0.25)*3.57),";
		}
		$update = _query("UPDATE dosen_evaluasi_rekap set 
							$updQuery nMhsw=(nMhsw+1)
							where
							JadwalID='".$_POST['JadwalID']."'");
		$update = _query("UPDATE krs set EvaluasiDosen='Y' where MhswID='".$_SESSION['_Login']."' AND
							JadwalID='".$_POST['JadwalID']."' AND TahunID='".$TahunID."'");
	}
	unset($_POST);
	$_POST['JadwalID']='';
	echo "<script>window.location='evaluasidosen';</script>";
}
TampilkanJudul("Evaluasi Kinerja Dosen Terhadap Proses Pembelajaran");

$BelumEvaluasi = GetaField("krs", "TahunID='".$TahunID."' AND NA='N' AND EvaluasiDosen='N' AND MhswID", $_SESSION['_Login'],"count(KRSID)");
if ($BelumEvaluasi < 1) { echo "<script>window.location='lhs'</script>"; }
// Matakuliah yang akan disurvey
$optMK = GetOption2('krs', 'Nama', 'Nama', '', "TahunID='".$TahunID."' and EvaluasiDosen='N' AND MhswID='".$_SESSION['_Login']."'", "JadwalID");

$text = '';
$text .= "<h3>Petunjuk Pengisian</h3>
		<ul>
			<li>Berikan penilaian se-objektif mungkin, karena jawaban Anda akan memberikan kontribusi positif terhadap perbaikan
				pelaksanaan perkuliahan selanjutnya</li>
			<li>Mengingat pentingnya informasi ini untuk meningkatkan kualitas proses pembelajaran, mohon isi dengan yang sebenar-
				benarnya. Kerahasiaan data Anda terjamin, <b>setiap penilaian tidak akan mencantumkan identitas penilai</b>.</li>
			<li>Evaluasi dosen dilakukan untuk masing-masing matakuliah yang Anda ambil semester berjalan.</li>
		</ul>";
$text .= "<form method='post' action='?' onSubmit=\"javascript:return CheckForm(this);\">";
$s = "SELECT * from dosenevaluasi_kategori order by KategoriID";
$r = _query($s);
$text .= "<b>Pilih Matakuliah yang akan dinilai:</b> <select name='JadwalID'>".$optMK."</select><br><br>";
while ($w = _fetch_array($r)){
	$s2 = "SELECT * from dosenevaluasi_butir where KategoriID='$w[KategoriID]' order by ButirID";
	$r2 = _query($s2);
	$text .= "<b>$w[Nama]</b><br /><ol>";
	while ($w2 = _fetch_array($r2)){
		$text .= "<li>".$w2['Pertanyaan']."<br />";
		$ckform .= (empty($ckform))? $w2['ButirID']:",".$w2['ButirID']; 
		if ($w2['JenisJawaban']=='D'){
			$text .= "<input type=radio value='Y' name='".$w2['ButirID']."'> Ya <input type=radio value='N' name='".$w2['ButirID']."'> Tidak";
		}
		  elseif ($w2['JenisJawaban']=='G'){
			$text .= "<input type=radio value='1' name='".$w2['ButirID']."'> Tidak Sesuai/Tidak Puas/Kurang <br>
			 		<input type=radio value='2' name='".$w2['ButirID']."'> Kurang Sesuai/Kurang Puas/Jarang/Cukup <br>
					<input type=radio value='3' name='".$w2['ButirID']."'> Sesuai/Puas/Sering/Baik <br>
					<input type=radio value='4' name='".$w2['ButirID']."'> Sangat Sesuai/Sangat Puas/Selalu/Sangat Baik <br>
					";
		}
			
	}
	$text .= "</ol>";
	
}
$text .= "<p><input type=submit value='Simpan Penilaian'></form>
";
CheckFormScript('JadwalID');
echo $text;