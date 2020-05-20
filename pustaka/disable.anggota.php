<?php 
session_start();
	/* 	Author	: Arisal Yanuarafi
		Start	: 17 Agustus 2014  */
	include_once "../dwo.lib.php";
  	include_once "../db.mysql.php";
  	include_once "../connectdb.php";
  	include_once "../parameter.php";
  	include_once "../cekparam.php";

$s = "SELECT AnggotaID,NA from pustaka_anggota";
$r = _query($s); $c = 0;
while ($w = _fetch_array($r)) {
	$StatusMhswID = GetaField('mhsw',"MhswID", $w['AnggotaID'],"StatusMhswID");
	$Password = GetaField('mhsw',"MhswID", $w['AnggotaID'],"Password");
	if ($StatusMhswID!='A' && $w['NA']=='N') {
	$update = _query("UPDATE pustaka_anggota set NA='Y', StatusMhswID='$StatusMhswID',InstitusiID='MHS' where AnggotaID='$w[AnggotaID]'");
	$update = _query("UPDATE app_pustaka1.member set is_pending=1, expire_date='2014-01-01' where member_id='$w[AnggotaID]'");
	$c++;
	}
	elseif ($StatusMhswID=='A' && $w['NA']=='Y') {
	$expire = date('Y-m-d', strtotime('+6 months'));
	$update = _query("UPDATE pustaka_anggota set NA='N' , StatusMhswID='$StatusMhswID',InstitusiID='MHS' where AnggotaID='$w[AnggotaID]'");
	$update = _query("UPDATE app_pustaka1.member set is_pending=0, expire_date='$expire', mpasswd = '$Password' where member_id='$w[AnggotaID]'");
	$c++;
	}
}
echo number_format($c,0);