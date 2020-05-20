<?php
session_start();
	/* 	Author	: Arisal Yanuarafi
		Start	: 15 Jan 2014  */
	
	include_once "../dwo.lib.php";
  	include_once "../db.mysql.php";
  	include_once "../connectdb.php";
  	include_once "../parameter.php";
  	include_once "../cekparam.php";
if ($_POST['gos']=='SAV' && $_SESSION['_LevelID']='100') {
	$MhswID = sqling($_POST['MhswID']);
	$KHSID	= sqling($_POST['KHSID']);
	$Status = sqling($_POST['Status']);
	$Alasan = FixQuotes($_POST['Alasan']);
	$Alasan = sqling($Alasan);
	//ResetTagihan($MhswID);
	if (!empty($MhswID) && !empty($KHSID) && !empty($Status)) {
		$s = "UPDATE khs set SetujuPA='".$Status."', ValidasiKe=(ValidasiKe+1), Alasan='".$Alasan."', LoginEdit='".$_SESSION['_Login']."', TanggalEdit=now() , KonfirmasiKRS='N'
		where KHSID='".$KHSID."' AND MhswID='".$MhswID."'";
		$r = _query($s);
		//echo $s;
	}
	if ($Status != 'Y') { 
		$s = "UPDATE khs set KonfirmasiKRS='N',SetujuPA='".$Status."' where KHSID='".$KHSID."' AND MhswID='".$MhswID."'";
		$r = _query($s);
		//echo $s;
	}
}

function ResetTagihan($MhswID){
	$thn = GetFields("tahun","TahunID not like 'Tra%' and NA='N'  AND KodeID", KodeID, "max(TahunID) as TahunID");
	$SKS = GetaField('krs', "MhswID='".$MhswID."' AND TahunID", $thn, "sum(SKS)");
		$update = 'UPDATE khs SET KonfirmasiAktif="N",SKS="'.$SKS.'" WHERE MhswID="'.$MhswID.'" AND TahunID="'.$thn.'"';
		$delete_bipot = 'DELETE FROM bipotmhsw WHERE MhswID="'.$MhswID.'" AND Dibayar=0 AND TahunID="'.$thn.'" AND BIPOTNamaID not in (14,3,16,12,13,17)';
		$delete_bipot2 = 'DELETE FROM bipotmhsw2 WHERE MhswID="'.$MhswID.'" AND flag="0" AND BayarMhswID=""  AND TahunID="'.$thn.'" AND BIPOTNamaID not in (14,3,16,12,13,17)';
		if (_query($update)) $rc = mysql_affected_rows(); echo "Sudah bisa menghitung Tagihan Kembali<br>";
		if (_query($delete_bipot)) $rc = mysql_affected_rows(); echo "Menghapus BIPOTMhsw sebanyak ".$rc." rekord<br>";
		if (_query($delete_bipot2)) $rc = mysql_affected_rows(); echo "Menghapus BIPOTMhsw2 sebanyak ".$rc." rekord<br>";
}
//echo $s;
echo "<script>window.location='portal?mnux=dosen/pa'</script>";