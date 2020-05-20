<?php session_start(); 

	include_once "../../dwo.lib.php";
  	include_once "../../db.mysql.php";
  	include_once "../../connectdb.php";
  	include_once "../../parameter.php";
  	include_once "../../cekparam.php";
  	
  	//die('berhasil '. $_GET['MhswID'].' - '.$_GET['ProdiID'].' - '.$_GET['Bobot'].' - '.$_GET['MKID']);
  	cekHakAkses();
	// Parameter
	$MhswID = sqling($_GET['MhswID']);
	$MKID = sqling($_GET['MKID']);
	$Bobot = sqling($_GET['Bobot']);
	$KRSID = sqling($_GET['KRSID']);

  	$s = "DELETE FROM krs where KRSID='$KRSID' and TahunID='KLIRING' and MhswID='$MhswID'";
  	$r = _query($s);

  	$s = "UPDATE mhsw SET KurikulumID='$_SESSION[_kliringKurikulum]' where MhswID='$MhswID'";
  	$r = _query($s);

  	$k = GetFields('krs',"KRSID",$KRSID,"MKKode,Nama,GradeNilai");

  	if($Bobot != 'NA'){
		$nilai = GetFields('nilai','Bobot', $Bobot, "*");
		$mk = GetFields('mk','MKID',$MKID,"*");
		$s = "INSERT into krs (KodeID,TahunID,MhswID,GradeNilai,BobotNilai,NilaiAkhir,MKID,MKKode,Nama,SKS,EvaluasiDosen,Tinggi, Final,  NA, Sah, TanggalBuat,LoginBuat,
								Setara, SetaraKode, SetaraGrade, SetaraNama)
							values ('".KodeID."',
									'KLIRING',
									'$MhswID',
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
									'$_SESSION[_Login]-KRNEW',
									'Y','$k[MKKode]','$k[GradeNilai]','$k[Nama]'
									)";
		$r = _query($s);
		$id = mysql_insert_id();
		$s = "INSERT INTO koreksinilai (Tanggal,TahunID,SK,Perihal,KRSID,MhswID,MKID,GradeLama,GradeNilai,Pejabat,Modul,LoginBuat,TglBuat)
									values
									(now(),
									'KLIRING',
									'ybs',
									'Input MK dan Nilai menggunakan Modul Kliring Nilai',
									'$id',
									'$MhswID',
									'$mk[MKID]',
									'-',
									'$nilai[Nama]',
									'$_SESSION[_Nama]',
									'KliringNilaiNew',
									'$_SESSION[_Login]',
									now())";
		$r = _query($s);
	}else{
		$mk = GetFields('mk','MKID',$MKID,"*");
		$s = "UPDATE krs set NA='Y',LoginEdit='$_SESSION[_Login]',TanggalEdit=now() where MhswID='$MhswID' and KRSID='$KRSID'";
		$r = _query($s);
		//echo $s;
		$s = "INSERT INTO koreksinilai (Tanggal,TahunID,SK,Perihal,KRSID,MhswID,MKID,GradeLama,GradeNilai,Pejabat,Modul,LoginBuat,TglBuat)
									values
									(now(),
									'KLIRING',
									'ybs',
									'Hapus MK dan Nilai menggunakan Modul Kliring Nilai',
									'$id',
									'$MhswID',
									'$mk[MKID]',
									'-',
									'E',
									'E',
									'KliringNilaiNew',
									'$_SESSION[_Login]',
									now())";
		$r = _query($s);
	}
echo "OK!";
function cekHakAkses(){
	//'ekoalvares','yaddi1','ika','jonny','elfida','desy aryanti','sudirman'
	$arrAkses = array('auth0rized','rini','cintya','adhia','nasril','ramli','afrizal','sekjurarsitektur','kajurbdp','kajurpsp');
  $key = array_search($_SESSION['_Login'], $arrAkses);
  //if ($key === false)
    //die("restricted access");
}