<?php session_start(); 
	include_once "../../dwo.lib.php";
  	include_once "../../db.mysql.php";
  	include_once "../../connectdb.php";
  	include_once "../../parameter.php";
  	include_once "../../cekparam.php";
if ($_SESSION['_LevelID']==42 || $_SESSION['_LevelID']==40 || $_SESSION['_LevelID']==1 || $_SESSION['_LevelID']==20) {
	if ($_GET['field'] == 'UPDATE') {
		if ($_GET['data'] == 'X') {
			$s = "UPDATE krs set NA='Y', 
				LoginEdit='$_SESSION[_Login]-KLN',TanggalEdit=now() where KRSID='".$_GET['whr1']."' and MhswID='".$_GET['whr2']."' ";
			$r = _query($s);
		}
		elseif ($_GET['data'] == 'N') {
			$s = "UPDATE krs set NA='N', 
				LoginEdit='$_SESSION[_Login]-KLN',TanggalEdit=now() where KRSID='".$_GET['whr1']."' and MhswID='".$_GET['whr2']."' ";
			$r = _query($s);
		}
		else {
		$krs = GetFields('krs',"KRSID", $_GET['whr1'], "*");
		$nilai = GetFields('nilai','Bobot', $_GET['data'], "*");
		$mk = GetFields('mk','MKID',$krs['MKID'],"*");
		$s = "UPDATE krs set GradeNilai = '".$nilai['Nama']."', BobotNilai = '".$nilai['Bobot']."', NilaiAkhir='".$nilai['NilaiMin']."', 
				LoginEdit='$_SESSION[_Login]-KLN',TanggalEdit=now(),Final='Y',EvaluasiDosen='Y' where KRSID='".$_GET['whr1']."' and MhswID='".$_GET['whr2']."'";
		$r = _query($s);
		//echo $s;
		
		$s = "INSERT INTO koreksinilai (Tanggal,TahunID,SK,Perihal,KRSID,MhswID,MKID,GradeLama,GradeNilai,Pejabat,Modul,LoginBuat,TglBuat)
									values
									(now(),
									'$krs[TahunID]',
									'ybs',
									'Perubahan Nilai menggunakan Modul Kliring Nilai',
									'$krs[KRSID]',
									'$_GET[whr2]',
									'$mk[MKID]',
									'-',
									'$nilai[Nama]',
									'$_SESSION[_Nama]',
									'KliringNilai',
									'$_SESSION[_Login]',
									now())";
		$r = _query($s);
		}
	}
	elseif ($_GET['field'] == 'INSERT') {
		$nilai = GetFields('nilai','Bobot', $_GET['data'], "*");
		$mk = GetFields('mk','MKID',$_GET['whr1'],"*");
		$s = "INSERT into krs (KodeID,TahunID,MhswID,GradeNilai,BobotNilai,NilaiAkhir,MKID,MKKode,Nama,SKS,EvaluasiDosen,Tinggi, Final,  NA, Sah, TanggalBuat,LoginBuat)
							values ('".KodeID."',
									'Transfer',
									'$_GET[whr2]',
									'$nilai[Nama]',
									'$nilai[Bobot]',
									'$nilai[NilaiMin]',
									'$mk[MKID]',
									'$mk[MKKode]',
									'$mk[Nama]',
									'$mk[SKS]',
									'Y',
									'*', 
									'Y', 
									'N', 
									'Y', 
									now(),
									'$_SESSION[_Login]-KLN')";
		$r = _query($s);
		$id = mysql_insert_id();
		$s = "INSERT INTO koreksinilai (Tanggal,TahunID,SK,Perihal,KRSID,MhswID,MKID,GradeLama,GradeNilai,Pejabat,Modul,LoginBuat,TglBuat)
									values
									(now(),
									'Transfer',
									'ybs',
									'Input MK dan Nilai menggunakan Modul Kliring Nilai',
									'$id',
									'$_GET[whr2]',
									'$mk[MKID]',
									'-',
									'$nilai[Nama]',
									'$_SESSION[_Nama]',
									'KliringNilai',
									'$_SESSION[_Login]',
									now())";
		$r = _query($s);
	}
}